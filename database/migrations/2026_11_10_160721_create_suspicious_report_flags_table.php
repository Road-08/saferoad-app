
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suspicious_report_flags', function (Blueprint $table) {
            $table->id();

            $table->foreignId('security_report_id')
                ->constrained('security_reports')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['security_report_id', 'user_id'],
                'unique_user_suspicious_report_flag'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suspicious_report_flags');
    }
};
