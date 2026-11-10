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
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('report_id');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('location_id')->constrained('locations', 'location_id');
            $table->string('category');
            $table->dateTime('event_time');
            $table->text('description');
            $table->string('status')->default('menunggu_validasi')->index();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
