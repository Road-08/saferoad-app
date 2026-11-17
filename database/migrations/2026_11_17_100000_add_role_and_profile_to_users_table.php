<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 30)->default('masyarakat')->after('password');
            }
            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 25)->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('phone_number');
            }
            if (! Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['perempuan', 'laki-laki', 'lainnya', 'tidak_menyebutkan'])->nullable()->after('avatar');
            }
            if (! Schema::hasColumn('users', 'gender_consent')) {
                $table->boolean('gender_consent')->default(false)->after('gender');
            }
            if (! Schema::hasColumn('users', 'ktp_path')) {
                $table->string('ktp_path')->nullable()->after('gender_consent');
            }
            if (! Schema::hasColumn('users', 'identity_status')) {
                $table->enum('identity_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified')->after('ktp_path');
            }
            if (! Schema::hasColumn('users', 'identity_verified_at')) {
                $table->timestamp('identity_verified_at')->nullable()->after('identity_status');
            }
            if (! Schema::hasColumn('users', 'identity_rejection_note')) {
                $table->text('identity_rejection_note')->nullable()->after('identity_verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach ([
                'role',
                'phone_number',
                'avatar',
                'gender',
                'gender_consent',
                'ktp_path',
                'identity_status',
                'identity_verified_at',
                'identity_rejection_note',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
