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
        Schema::table('security_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('security_reports', 'incident_at')) {
                $table->timestamp('incident_at')->nullable();
            }
            if (!Schema::hasColumn('security_reports', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('security_reports', function (Blueprint $table) {
            if (Schema::hasColumn('security_reports', 'incident_at')) {
                $table->dropColumn('incident_at');
            }
            if (Schema::hasColumn('security_reports', 'is_anonymous')) {
                $table->dropColumn('is_anonymous');
            }
        });
    }
};
