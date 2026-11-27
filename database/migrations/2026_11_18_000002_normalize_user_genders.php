<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->whereNotNull('gender')->get()->each(function ($user) {
            $raw = strtolower(trim((string) $user->gender));
            $gender = match ($raw) {
                'p', 'female', 'wanita', 'perempuan' => 'perempuan',
                'l', 'male', 'pria', 'laki-laki', 'laki' => 'laki-laki',
                default => null,
            };

            if ($gender !== null && $gender !== $user->gender) {
                DB::table('users')->where('id', $user->id)->update(['gender' => $gender]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation needed
    }
};
