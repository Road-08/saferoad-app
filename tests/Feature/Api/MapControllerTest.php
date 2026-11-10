<?php

namespace Tests\Feature\Api;

use App\Models\Location;
use App\Models\Report;
use App\Models\Role;
use App\Models\SecurityFacility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_facilities_returns_all_features_without_filters(): void
    {
        SecurityFacility::factory()->count(3)->create();

        $response = $this->getJson('/api/map/facilities');

        $response->assertOk()
            ->assertJsonPath('type', 'FeatureCollection')
            ->assertJsonCount(3, 'features');
    }

    public function test_facilities_filters_by_type(): void
    {
        SecurityFacility::factory()->create(['facility_type' => 'cctv']);
        SecurityFacility::factory()->create(['facility_type' => 'penerangan']);
        SecurityFacility::factory()->create(['facility_type' => 'pos_keamanan']);

        $response = $this->getJson('/api/map/facilities?type=cctv,penerangan');

        $response->assertOk()
            ->assertJsonCount(2, 'features')
            ->assertJsonPath('features.0.properties.type', 'cctv')
            ->assertJsonPath('features.1.properties.type', 'penerangan');
    }

    public function test_facilities_filters_points_within_bbox(): void
    {
        $insideLocation = Location::factory()->create([
            'latitude' => -7.314,
            'longitude' => 112.725,
        ]);
        $outsideLocation = Location::factory()->create([
            'latitude' => -7.314,
            'longitude' => 112.75,
        ]);

        SecurityFacility::factory()->for($insideLocation)->create();
        SecurityFacility::factory()->for($outsideLocation)->create();

        $response = $this->getJson('/api/map/facilities?bbox=112.72,-7.32,112.73,-7.31');

        $response->assertOk()
            ->assertJsonCount(1, 'features')
            ->assertJsonPath('features.0.geometry.coordinates', [112.725, -7.314]);
    }

    public function test_hazards_only_returns_forwarded_reports(): void
    {
        $reporter = $this->createReporter();
        $location = Location::factory()->create();
        $forwardedReport = Report::factory()
            ->for($reporter)
            ->for($location)
            ->create(['status' => 'diteruskan_ke_keamanan']);

        foreach (['menunggu_validasi', 'perlu_perbaikan', 'ditangani', 'ditolak'] as $status) {
            Report::factory()->for($reporter)->for($location)->create(['status' => $status]);
        }

        $response = $this->getJson('/api/map/hazards');

        $response->assertOk()
            ->assertJsonCount(1, 'features')
            ->assertJsonPath('features.0.properties.id', $forwardedReport->report_id);
    }

    public function test_hazard_and_point_responses_omit_reporter_and_internal_fields(): void
    {
        $reporter = $this->createReporter([
            'name' => 'Reporter Private Name',
            'email' => 'reporter-private@example.test',
        ]);
        $report = Report::factory()->for($reporter)->create([
            'status' => 'diteruskan_ke_keamanan',
            'admin_note' => 'Internal review note',
        ]);
        $facility = SecurityFacility::factory()->create();

        $responses = [
            $this->getJson('/api/map/hazards')->assertOk(),
            $this->getJson('/api/map/points/hazard/'.$report->report_id)->assertOk(),
            $this->getJson('/api/map/points/facility/'.$facility->facility_id)->assertOk(),
        ];

        foreach ($responses as $response) {
            $json = $response->getContent();

            $this->assertStringNotContainsString('user_id', $json);
            $this->assertStringNotContainsString('admin_note', $json);
            $this->assertStringNotContainsString('reporter-private@example.test', $json);
            $this->assertStringNotContainsString('Reporter Private Name', $json);
        }
    }

    public function test_hazard_point_with_inactive_status_returns_404(): void
    {
        $report = Report::factory()->create(['status' => 'menunggu_validasi']);

        $this->getJson('/api/map/points/hazard/'.$report->report_id)
            ->assertNotFound();
    }

    public function test_invalid_bbox_returns_indonesian_422(): void
    {
        $this->getJson('/api/map/facilities?bbox=not-a-bbox')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('bbox')
            ->assertJsonPath('errors.bbox.0', 'Format bbox harus minLng,minLat,maxLng,maxLat dengan angka koordinat yang valid.');
    }

    public function test_invalid_facility_type_returns_indonesian_422(): void
    {
        $this->getJson('/api/map/facilities?type=cctv,unknown')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('type')
            ->assertJsonPath('errors.type.0', 'Filter type hanya boleh berisi cctv, penerangan, pos_keamanan, atau titik_aman yang dipisahkan koma.');
    }

    public function test_map_features_use_geojson_point_coordinates_in_longitude_latitude_order(): void
    {
        $facilityLocation = Location::factory()->create([
            'latitude' => -7.3123456,
            'longitude' => 112.7234567,
        ]);
        SecurityFacility::factory()->for($facilityLocation)->create();

        $hazardLocation = Location::factory()->create([
            'latitude' => -7.3134567,
            'longitude' => 112.7245678,
        ]);
        Report::factory()->for($this->createReporter())->for($hazardLocation)->create([
            'status' => 'diteruskan_ke_keamanan',
        ]);

        $facilityResponse = $this->getJson('/api/map/facilities')->assertOk();
        $hazardResponse = $this->getJson('/api/map/hazards')->assertOk();

        $facilityResponse->assertJsonPath('features.0.type', 'Feature')
            ->assertJsonPath('features.0.geometry.type', 'Point')
            ->assertJsonPath('features.0.geometry.coordinates', [112.7234567, -7.3123456]);
        $hazardResponse->assertJsonPath('features.0.type', 'Feature')
            ->assertJsonPath('features.0.geometry.type', 'Point')
            ->assertJsonPath('features.0.geometry.coordinates', [112.7245678, -7.3134567]);
    }

    public function test_overview_combines_facilities_and_forwarded_hazards(): void
    {
        SecurityFacility::factory()->create(['facility_type' => 'cctv']);
        Report::factory()->for($this->createReporter())->create([
            'category' => 'pelecehan',
            'status' => 'diteruskan_ke_keamanan',
        ]);
        Report::factory()->create(['status' => 'ditolak']);

        $response = $this->getJson('/api/map/overview');

        $response->assertOk()
            ->assertJsonPath('type', 'FeatureCollection')
            ->assertJsonCount(2, 'features')
            ->assertJsonPath('features.0.properties.type', 'cctv')
            ->assertJsonPath('features.1.properties.category', 'pelecehan');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createReporter(array $attributes = []): User
    {
        $role = Role::factory()->create();

        return User::factory()->create([
            ...$attributes,
            'role_id' => $role->role_id,
        ]);
    }
}
