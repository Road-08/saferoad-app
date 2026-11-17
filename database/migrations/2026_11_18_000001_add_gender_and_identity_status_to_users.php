<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add gender column if not exists
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('nik_hash');
            }
            // Add identity_status column if not exists
            if (!Schema::hasColumn('users', 'identity_status')) {
                $table->string('identity_status')->default('unverified')->after('gender');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'identity_status']);
        });
    }
};
?>