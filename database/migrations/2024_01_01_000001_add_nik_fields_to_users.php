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
            $table->string('nik_hash')->nullable()->unique()->after('email');
            $table->string('identity_method')->nullable()->after('nik_hash');
            // Ensure gender_consent column exists (may already exist)
            if (! Schema::hasColumn('users', 'gender_consent')) {
                $table->boolean('gender_consent')->default(false)->after('identity_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik_hash', 'identity_method', 'gender_consent']);
        });
    }
};
?>