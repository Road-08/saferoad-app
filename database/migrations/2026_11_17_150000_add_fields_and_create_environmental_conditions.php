<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom ke security_reports
        Schema::table('security_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('security_reports', 'severity')) {
                $table->enum('severity', ['rendah', 'sedang', 'tinggi'])->default('sedang')->after('description');
            }
            if (! Schema::hasColumn('security_reports', 'incident_at')) {
                $table->timestamp('incident_at')->nullable()->after('severity');
            }
            if (! Schema::hasColumn('security_reports', 'validation_status')) {
                $table->enum('validation_status', ['pending', 'verified', 'rejected'])->default('pending')->after('status');
            }
            if (! Schema::hasColumn('security_reports', 'is_simulation')) {
                $table->boolean('is_simulation')->default(false)->after('validation_status');
            }
        });

        // 2. Tambah kolom ke safety_facilities
        Schema::table('safety_facilities', function (Blueprint $table) {
            if (! Schema::hasColumn('safety_facilities', 'condition')) {
                $table->enum('condition', ['aktif', 'rusak', 'tidak_aktif'])->default('aktif')->after('status');
            }
            if (! Schema::hasColumn('safety_facilities', 'verification_status')) {
                $table->enum('verification_status', ['verified', 'unverified', 'pending'])->default('verified')->after('condition');
            }
            if (! Schema::hasColumn('safety_facilities', 'is_simulation')) {
                $table->boolean('is_simulation')->default(false)->after('verification_status');
            }
        });

        // 3. Buat tabel environmental_conditions
        if (! Schema::hasTable('environmental_conditions')) {
            Schema::create('environmental_conditions', function (Blueprint $table) {
                $table->id();
                $table->string('location_name');
                $table->decimal('latitude', 10, 7);
                $table->decimal('longitude', 10, 7);
                $table->enum('activity_level', ['ramai', 'sedang', 'sepi'])->default('sedang');
                $table->enum('lighting_condition', ['terang', 'cukup', 'minim', 'gelap'])->default('cukup');
                $table->timestamp('observed_at')->nullable();
                $table->enum('validation_status', ['pending', 'verified', 'rejected'])->default('verified');
                $table->boolean('is_simulation')->default(false);
                $table->timestamps();

                $table->index(['latitude', 'longitude']);
                $table->index('validation_status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('environmental_conditions');

        Schema::table('safety_facilities', function (Blueprint $table) {
            $cols = ['condition', 'verification_status', 'is_simulation'];
            $drop = array_filter($cols, fn ($c) => Schema::hasColumn('safety_facilities', $c));
            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });

        Schema::table('security_reports', function (Blueprint $table) {
            $cols = ['severity', 'incident_at', 'validation_status', 'is_simulation'];
            $drop = array_filter($cols, fn ($c) => Schema::hasColumn('security_reports', $c));
            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
