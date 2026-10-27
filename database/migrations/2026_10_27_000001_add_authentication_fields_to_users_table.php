<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender', 32)
                ->nullable()
                ->after('password');

            $table->boolean('gender_statistics_consent')
                ->default(false)
                ->after('gender');

            $table->string('role', 32)
                ->default('user')
                ->index()
                ->after('gender_statistics_consent');

            $table->string('verification_status', 32)
                ->default('pending')
                ->index()
                ->after('role');

            $table->string('verified_gender', 32)
                ->nullable()
                ->after('verification_status');

            $table->string('ktp_path')
                ->nullable()
                ->after('verified_gender');

            $table->timestamp('verified_at')
                ->nullable()
                ->after('ktp_path');

            $table->text('verification_note')
                ->nullable()
                ->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['verification_status']);

            $table->dropColumn([
                'gender',
                'gender_statistics_consent',
                'role',
                'verification_status',
                'verified_gender',
                'ktp_path',
                'verified_at',
                'verification_note',
            ]);
        });
    }
};
