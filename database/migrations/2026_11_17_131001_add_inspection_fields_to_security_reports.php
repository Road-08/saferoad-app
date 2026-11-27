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
            $table->text('inspection_note')->nullable();
        $table->boolean('needs_action')->nullable();
        $table->text('action_note')->nullable();
        $table->foreignId('inspected_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('security_confirmed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('security_reports', function (Blueprint $table) {
            $table->text('inspection_note')->nullable();
        $table->boolean('needs_action')->nullable();
        $table->text('action_note')->nullable();
        $table->foreignId('inspected_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('security_confirmed_at')->nullable();
        });
    }
};
