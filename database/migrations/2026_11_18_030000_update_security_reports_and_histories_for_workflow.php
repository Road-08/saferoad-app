<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah kolom status menjadi VARCHAR(50) agar menerima status baru
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE security_reports MODIFY status VARCHAR(50) NOT NULL DEFAULT 'pending_validation'");
        } else {
            Schema::table('security_reports', function (Blueprint $table) {
                $table->string('status', 50)->default('pending_validation')->change();
            });
        }

        // 2. Petakan status data lama ke status baru
        DB::table('security_reports')->whereIn('status', ['submitted', 'admin_review'])->update(['status' => 'pending_validation']);
        DB::table('security_reports')->where('status', 'officer_check')->update(['status' => 'forwarded_to_security']);
        DB::table('security_reports')->where('status', 'map_updated')->update(['status' => 'handled']);

        // Petakan juga status di histories
        DB::table('security_report_histories')->whereIn('status', ['submitted', 'admin_review'])->update(['status' => 'pending_validation']);
        DB::table('security_report_histories')->where('status', 'officer_check')->update(['status' => 'forwarded_to_security']);
        DB::table('security_report_histories')->where('status', 'map_updated')->update(['status' => 'handled']);

        // 3. Tambah kolom baru di security_reports
        Schema::table('security_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('security_reports', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('description');
            }
            if (!Schema::hasColumn('security_reports', 'forwarded_at')) {
                $table->timestamp('forwarded_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('security_reports', 'forwarded_by')) {
                $table->foreignId('forwarded_by')->nullable()->after('forwarded_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('security_reports', 'handled_at')) {
                $table->timestamp('handled_at')->nullable()->after('security_confirmed_at');
            }
            if (!Schema::hasColumn('security_reports', 'incident_at')) {
                $table->timestamp('incident_at')->nullable();
            }
            if (!Schema::hasColumn('security_reports', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false);
            }
            if (!Schema::hasColumn('security_reports', 'inspection_note')) {
                $table->text('inspection_note')->nullable();
            }
            if (!Schema::hasColumn('security_reports', 'needs_action')) {
                $table->boolean('needs_action')->nullable();
            }
            if (!Schema::hasColumn('security_reports', 'action_note')) {
                $table->text('action_note')->nullable();
            }
            if (!Schema::hasColumn('security_reports', 'inspected_by')) {
                $table->foreignId('inspected_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('security_reports', 'security_confirmed_at')) {
                $table->timestamp('security_confirmed_at')->nullable();
            }
        });

        // 4. Tambah kolom actor_id dan actor_role di security_report_histories
        Schema::table('security_report_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('security_report_histories', 'actor_id')) {
                $table->foreignId('actor_id')->nullable()->after('note')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('security_report_histories', 'actor_role')) {
                $table->string('actor_role', 50)->nullable()->after('actor_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('security_report_histories', function (Blueprint $table) {
            if (Schema::hasColumn('security_report_histories', 'actor_id')) {
                $table->dropForeign(['actor_id']);
                $table->dropColumn('actor_id');
            }
            if (Schema::hasColumn('security_report_histories', 'actor_role')) {
                $table->dropColumn('actor_role');
            }
        });

        Schema::table('security_reports', function (Blueprint $table) {
            if (Schema::hasColumn('security_reports', 'forwarded_by')) {
                $table->dropForeign(['forwarded_by']);
                $table->dropColumn('forwarded_by');
            }
            if (Schema::hasColumn('security_reports', 'admin_note')) {
                $table->dropColumn('admin_note');
            }
            if (Schema::hasColumn('security_reports', 'forwarded_at')) {
                $table->dropColumn('forwarded_at');
            }
            if (Schema::hasColumn('security_reports', 'handled_at')) {
                $table->dropColumn('handled_at');
            }
        });
    }
};
