<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom pada tabel security_reports jika belum ada
        Schema::table('security_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('security_reports', 'photo_path')) {
                $table->string('photo_path', 255)->nullable()->after('description');
            }
            if (! Schema::hasColumn('security_reports', 'inspection_status')) {
                $table->string('inspection_status', 50)->default('waiting_verification')->after('status');
            }
            if (! Schema::hasColumn('security_reports', 'inspection_evidence')) {
                $table->string('inspection_evidence', 255)->nullable()->after('action_note');
            }
        });

        // 2. Buat tabel safety_score_logs untuk audit riwayat perubahan skor
        if (! Schema::hasTable('safety_score_logs')) {
            Schema::create('safety_score_logs', function (Blueprint $table) {
                $table->id();
                $table->string('target_type', 30)->default('area'); // 'area', 'route'
                $table->string('target_identifier', 255); // lat,lng atau route_id
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->decimal('radius_km', 6, 3)->nullable()->default(0.500);
                $table->decimal('score_before', 5, 2);
                $table->decimal('score_after', 5, 2);
                $table->decimal('score_change', 5, 2);
                $table->string('trigger_event', 100); // new_report, report_verified, report_handled, report_unproven, safe_condition, facility_change
                $table->foreignId('security_report_id')->nullable()->constrained('security_reports')->nullOnDelete();
                $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('actor_role', 50)->nullable();
                $table->json('details')->nullable();
                $table->timestamps();

                $table->index(['latitude', 'longitude']);
                $table->index('trigger_event');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_score_logs');

        Schema::table('security_reports', function (Blueprint $table) {
            if (Schema::hasColumn('security_reports', 'photo_path')) {
                $table->dropColumn('photo_path');
            }
            if (Schema::hasColumn('security_reports', 'inspection_status')) {
                $table->dropColumn('inspection_status');
            }
            if (Schema::hasColumn('security_reports', 'inspection_evidence')) {
                $table->dropColumn('inspection_evidence');
            }
        });
    }
};
