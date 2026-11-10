<?php

namespace Tests\Feature\Api;

use App\Models\Location;
use App\Models\Report;
use App\Models\Role;
use App\Models\SecurityFacility;
use App\Models\User;
use App\Services\SafetyScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RouteScoreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_routes_with_more_active_facilities_score_higher_and_safe_points_do_not_change_score(): void
    {
        $route = $this->routePayload();
        $routeStart = $route['coordinates'][0];
        $routeEnd = $route['coordinates'][1];
        $this->createFacility('penerangan', $routeStart[0] + 0.001, $routeStart[1]);
        $this->createFacility('cctv', $routeStart[0] + 0.001, $routeStart[1]);
        $this->createFacility('pos_keamanan', $routeStart[0] + 0.001, $routeStart[1]);

        $fewFacilitiesScore = $this->postJson('/api/routes/score', ['routes' => [$route]])
            ->assertOk()
            ->json('data.0.score');

        for ($index = 0; $index <= 10; $index++) {
            $longitude = $routeStart[0] + (($routeEnd[0] - $routeStart[0]) * $index / 10);
            $this->createFacility('penerangan', $longitude, $routeStart[1]);
            $this->createFacility('cctv', $longitude, $routeStart[1]);
            $this->createFacility('pos_keamanan', $longitude, $routeStart[1]);
        }

        $manyFacilitiesScore = $this->postJson('/api/routes/score', ['routes' => [$route]])
            ->assertOk()
            ->json('data.0.score');

        $this->assertGreaterThan($fewFacilitiesScore, $manyFacilitiesScore);

        $this->createFacility('titik_aman', $routeStart[0] + 0.001, $routeStart[1]);
        $scoreWithSafePoint = $this->postJson('/api/routes/score', ['routes' => [$route]])
            ->assertOk()
            ->json('data.0.score');

        $this->assertSame($manyFacilitiesScore, $scoreWithSafePoint);
    }

    public function test_facilities_needing_maintenance_are_not_available_factors(): void
    {
        $coordinates = $this->routePayload()['coordinates'];
        $this->createFacility('cctv', $coordinates[0][0], $coordinates[0][1], 'perlu_perawatan');

        $response = $this->postJson('/api/routes/score', ['routes' => [$this->routePayload()]]);

        $response->assertOk()
            ->assertJsonPath('data.0.factors.2.key', 'cctv')
            ->assertJsonPath('data.0.factors.2.available', false)
            ->assertJsonPath('data.0.factors.2.score', null);
    }

    public function test_only_forwarded_reports_lower_the_hazard_factor(): void
    {
        $this->createCoreFacilities();
        $location = Location::factory()->create([
            'longitude' => 112.725,
            'latitude' => -7.314,
        ]);

        $baseline = $this->postJson('/api/routes/score', ['routes' => [$this->shortRoutePayload()]])
            ->assertOk()
            ->json('data.0.factors.0.score');

        $reporter = $this->createReporter();
        foreach (['menunggu_validasi', 'perlu_perbaikan', 'ditangani', 'ditolak'] as $status) {
            Report::factory()->for($reporter)->for($location)->create([
                'status' => $status,
                'category' => 'kecelakaan',
            ]);
        }

        $ignoredReportsScore = $this->postJson('/api/routes/score', ['routes' => [$this->shortRoutePayload()]])
            ->assertOk()
            ->json('data.0.factors.0.score');

        $this->assertSame($baseline, $ignoredReportsScore);

        Report::factory()->for($reporter)->for($location)->create([
            'status' => 'diteruskan_ke_keamanan',
            'category' => 'kecelakaan',
        ]);

        $forwardedReportScore = $this->postJson('/api/routes/score', ['routes' => [$this->shortRoutePayload()]])
            ->assertOk()
            ->json('data.0.factors.0.score');

        $this->assertLessThan($baseline, $forwardedReportScore);
    }

    public function test_new_report_lowers_hazard_score_more_than_an_old_report_of_the_same_category(): void
    {
        $this->travelTo(now()->setDate(2026, 1, 15)->startOfDay());
        $this->createCoreFacilities();
        $reporter = $this->createReporter();
        $location = Location::factory()->create([
            'longitude' => 112.725,
            'latitude' => -7.314,
        ]);

        Report::factory()->for($reporter)->for($location)->create([
            'status' => 'diteruskan_ke_keamanan',
            'category' => 'kecelakaan',
            'event_time' => now(),
        ]);
        $newReportScore = $this->postJson('/api/routes/score', ['routes' => [$this->shortRoutePayload()]])
            ->assertOk()
            ->json('data.0.factors.0.score');

        Report::query()->delete();
        Report::factory()->for($reporter)->for($location)->create([
            'status' => 'diteruskan_ke_keamanan',
            'category' => 'kecelakaan',
            'event_time' => now()->subDays(365),
        ]);
        $oldReportScore = $this->postJson('/api/routes/score', ['routes' => [$this->shortRoutePayload()]])
            ->assertOk()
            ->json('data.0.factors.0.score');

        $this->assertLessThan($oldReportScore, $newReportScore);
    }

    public function test_level_is_aman_when_the_score_is_exactly_eighty(): void
    {
        $this->assertHazardScoreAtThreshold(40, 80, 'aman');
    }

    public function test_level_is_sedang_when_the_score_is_exactly_sixty(): void
    {
        $this->assertHazardScoreAtThreshold(80, 60, 'sedang');
    }

    public function test_area_without_data_returns_null_score_and_data_insufficient_note(): void
    {
        $response = $this->postJson('/api/routes/score', ['routes' => [$this->routePayload()]]);

        $response->assertOk()
            ->assertJsonPath('data.0.score', null)
            ->assertJsonPath('data.0.level', 'data_belum_cukup')
            ->assertJsonPath('data.0.note', 'Data keamanan di sekitar rute ini belum cukup.');
    }

    public function test_score_is_null_when_available_core_factors_are_below_the_configured_minimum(): void
    {
        $coordinates = $this->routePayload()['coordinates'];
        $this->createFacility('cctv', $coordinates[0][0], $coordinates[0][1]);

        $response = $this->postJson('/api/routes/score', ['routes' => [$this->routePayload()]]);

        $response->assertOk()
            ->assertJsonPath('data.0.score', null)
            ->assertJsonPath('data.0.level', 'data_belum_cukup')
            ->assertJsonPath('data.0.note', 'Data keamanan di sekitar rute ini belum cukup.');
    }

    public function test_hazard_is_one_hundred_when_other_area_data_exists_without_forwarded_reports_near_the_route(): void
    {
        $coordinates = $this->routePayload()['coordinates'];
        $this->createFacility('cctv', $coordinates[0][0], $coordinates[0][1]);

        $response = $this->postJson('/api/routes/score', ['routes' => [$this->routePayload()]]);

        $response->assertOk()
            ->assertJsonPath('data.0.factors.0.key', 'hazard')
            ->assertJsonPath('data.0.factors.0.available', true)
            ->assertJsonPath('data.0.factors.0.score', 100);
    }

    public function test_available_factor_weights_sum_to_one_and_null_crowd_is_unavailable(): void
    {
        $this->createCoreFacilities();

        $response = $this->postJson('/api/routes/score', ['routes' => [$this->routePayload()]]);
        $response->assertOk();
        $factors = $response->json('data.0.factors');
        $availableWeights = array_sum(array_column(array_filter($factors, fn (array $factor): bool => $factor['available']), 'weight'));

        $this->assertEqualsWithDelta(1.0, $availableWeights, 0.0001);
        $response->assertJsonPath('data.0.factors.4.key', 'crowd')
            ->assertJsonPath('data.0.factors.4.available', false)
            ->assertJsonPath('data.0.factors.4.weight', 0);
    }

    public function test_rejects_empty_route_list_with_indonesian_422(): void
    {
        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => []]),
            'routes',
            'Daftar rute wajib diisi.',
        );
    }

    public function test_rejects_more_than_maximum_routes_with_indonesian_422(): void
    {
        $routes = array_fill(0, (int) config('safety.limits.max_routes') + 1, $this->routePayload());

        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => $routes]),
            'routes',
            'Jumlah rute melebihi batas maksimum.',
        );
    }

    public function test_rejects_route_with_fewer_than_two_points_with_indonesian_422(): void
    {
        $route = $this->routePayload();
        $route['coordinates'] = [[$route['coordinates'][0][0], $route['coordinates'][0][1]]];

        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => [$route]]),
            'routes.0.coordinates',
            'Setiap rute harus memiliki minimal dua titik koordinat.',
        );
    }

    public function test_rejects_more_than_maximum_points_with_indonesian_422(): void
    {
        config(['safety.limits.max_points_per_route' => 2]);
        $route = $this->routePayload();
        $route['coordinates'][] = [112.726, -7.314];

        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => [$route]]),
            'routes.0.coordinates',
            'Jumlah titik koordinat melebihi batas maksimum.',
        );
    }

    public function test_rejects_longitude_outside_geographic_range_with_indonesian_422(): void
    {
        $route = $this->routePayload();
        $route['coordinates'][0][0] = 180.01;

        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => [$route]]),
            'routes.0.coordinates.0.0',
            'Longitude harus berada antara -180 dan 180.',
        );
    }

    public function test_rejects_latitude_outside_geographic_range_with_indonesian_422(): void
    {
        $route = $this->routePayload();
        $route['coordinates'][0][1] = -90.01;

        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => [$route]]),
            'routes.0.coordinates.0.1',
            'Latitude harus berada antara -90 dan 90.',
        );
    }

    public function test_rejects_coordinate_that_is_not_a_pair_with_indonesian_422(): void
    {
        $route = $this->routePayload();
        $route['coordinates'][0] = [112.725];

        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => [$route]]),
            'routes.0.coordinates.0',
            'Setiap titik harus berisi tepat dua angka: longitude dan latitude.',
        );
    }

    public function test_rejects_non_numeric_coordinate_with_indonesian_422(): void
    {
        $route = $this->routePayload();
        $route['coordinates'][0][1] = 'bukan-angka';

        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => [$route]]),
            'routes.0.coordinates.0.1',
            'Latitude harus berupa angka.',
        );
    }

    public function test_rejects_extra_route_keys_with_indonesian_422(): void
    {
        $route = [...$this->routePayload(), 'unknown' => 'not-allowed'];

        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => [$route]]),
            'routes.0',
            'Setiap rute harus berupa objek dengan id dan coordinates.',
        );
    }

    public function test_rejects_numeric_route_id_with_indonesian_422(): void
    {
        $route = [...$this->routePayload(), 'id' => 123];

        $this->assertValidationError(
            $this->postJson('/api/routes/score', ['routes' => [$route]]),
            'routes.0.id',
            'ID rute harus berupa teks.',
        );
    }

    public function test_response_has_ordered_factors_one_disclaimer_and_no_reporter_data(): void
    {
        $this->createCoreFacilities();
        $reporter = $this->createReporter([
            'name' => 'Private Reporter Name',
            'email' => 'private.reporter@example.test',
        ]);
        Report::factory()->for($reporter)->create([
            'status' => 'diteruskan_ke_keamanan',
            'admin_note' => 'Private administrator note',
            'description' => 'Private reporter description',
        ]);

        $response = $this->postJson('/api/routes/score', [
            'routes' => [
                [...$this->routePayload(), 'id' => 'route-a'],
                [...$this->routePayload(), 'id' => 'route-b'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id', 'score', 'level', 'level_label', 'note',
                    'factors' => [[ 'key', 'label', 'score', 'weight', 'available', 'note' ]],
                ]],
                'disclaimer',
            ]);
        $json = $response->getContent();
        $this->assertSame(1, substr_count($json, '"disclaimer"'));
        $this->assertStringNotContainsString('user_id', $json);
        $this->assertStringNotContainsString('admin_note', $json);
        $this->assertStringNotContainsString('email', $json);
        $this->assertStringNotContainsString('private.reporter@example.test', $json);
        $this->assertStringNotContainsString('Private Reporter Name', $json);
        $this->assertStringNotContainsString('Private reporter description', $json);

        $this->assertSame(['route-a', 'route-b'], array_column($response->json('data'), 'id'));
        $this->assertSame(
            ['hazard', 'lighting', 'cctv', 'security_post', 'crowd'],
            array_column($response->json('data.0.factors'), 'key'),
        );
        $this->assertArrayNotHasKey('disclaimer', $response->json('data.0'));
        $this->assertArrayNotHasKey('disclaimer', $response->json('data.0.factors.0'));
    }

    public function test_service_query_count_does_not_grow_with_number_of_facilities(): void
    {
        $coordinates = $this->routePayload()['coordinates'];
        foreach (['penerangan', 'cctv', 'pos_keamanan'] as $type) {
            $this->createFacility($type, $coordinates[0][0], $coordinates[0][1]);
        }

        $database = DB::connection();
        $database->enableQueryLog();
        $database->flushQueryLog();
        app(SafetyScoreService::class)->score([$this->routePayload()]);
        $fewFacilityQueryCount = count($database->getQueryLog());

        for ($index = 0; $index < 15; $index++) {
            foreach (['penerangan', 'cctv', 'pos_keamanan'] as $type) {
                $this->createFacility($type, $coordinates[0][0], $coordinates[0][1]);
            }
        }

        $database->flushQueryLog();
        app(SafetyScoreService::class)->score([$this->routePayload()]);
        $manyFacilityQueryCount = count($database->getQueryLog());

        $this->assertSame($fewFacilityQueryCount, $manyFacilityQueryCount);
    }

    public function test_uses_weight_hazard_from_security_parameters_when_table_is_available(): void
    {
        if (! Schema::hasTable('security_parameters')) {
            $this->markTestSkipped('Tabel security_parameters milik SR-30 belum tersedia pada skema branch ini.');
        }

        $columns = Schema::getColumnListing('security_parameters');
        $this->assertContains('weight_hazard', $columns, 'Skema SR-30 harus menyediakan kolom weight_hazard untuk test ini.');

        $row = DB::table('security_parameters')->first();
        $this->assertNotNull($row, 'Test memerlukan satu baris parameter keamanan yang sudah ada.');
        $weightHazard = (float) $row->weight_hazard;
        $this->assertGreaterThan(0, $weightHazard);

        $parameters = app(\App\Services\SafetyParameters::class);
        $this->assertSame($weightHazard, (float) $parameters->get('weights.hazard'));
    }

    private function assertHazardScoreAtThreshold(float $penalty, int $expectedScore, string $expectedLevel): void
    {
        config([
            'safety.weights' => [
                'hazard' => 1,
                'lighting' => 0,
                'cctv' => 0,
                'security_post' => 0,
                'crowd' => 0,
            ],
            'safety.hazard_penalty_per_unit' => $penalty,
            'safety.min_available_factors' => 3,
        ]);
        $this->createCoreFacilities();
        $reporter = $this->createReporter();
        $location = Location::factory()->create([
            'longitude' => 112.725,
            'latitude' => -7.314,
        ]);
        Report::factory()->for($reporter)->for($location)->create([
            'status' => 'diteruskan_ke_keamanan',
            'category' => 'kecelakaan',
            'event_time' => now(),
        ]);

        $response = $this->postJson('/api/routes/score', ['routes' => [$this->shortRoutePayload()]])
            ->assertOk();

        $response->assertJsonPath('data.0.score', $expectedScore)
            ->assertJsonPath('data.0.level', $expectedLevel);
    }

    /** @return array{id: string|null, coordinates: array<int, array{0: float, 1: float}>} */
    private function routePayload(): array
    {
        return [
            'id' => null,
            'coordinates' => [[112.725, -7.314], [112.727, -7.314]],
        ];
    }

    /** @return array{id: string|null, coordinates: array<int, array{0: float, 1: float}>} */
    private function shortRoutePayload(): array
    {
        return [
            'id' => null,
            'coordinates' => [[112.725, -7.314], [112.72501, -7.314]],
        ];
    }

    private function createFacility(string $type, float $longitude, float $latitude, string $status = 'aktif'): SecurityFacility
    {
        $location = Location::factory()->create([
            'longitude' => $longitude,
            'latitude' => $latitude,
        ]);

        return SecurityFacility::factory()->for($location)->create([
            'facility_type' => $type,
            'status' => $status,
        ]);
    }

    private function createCoreFacilities(): void
    {
        foreach (['penerangan', 'cctv', 'pos_keamanan'] as $type) {
            $this->createFacility($type, 112.725, -7.314);
        }
    }

    /** @param  array<string, mixed>  $attributes */
    private function createReporter(array $attributes = []): User
    {
        $role = Role::factory()->create();

        return User::factory()->create([
            ...$attributes,
            'role_id' => $role->role_id,
        ]);
    }

    private function assertValidationError(TestResponse $response, string $attribute, string $message): void
    {
        $response->assertUnprocessable()
            ->assertJsonValidationErrors($attribute);

        $this->assertSame($message, $response->json('errors')[$attribute][0]);
    }
}
