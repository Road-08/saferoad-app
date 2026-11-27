<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danger_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('danger_level', ['tinggi', 'sedang', 'rendah'])->default('sedang');
            $table->string('category', 100);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('radius_meters')->default(150);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index('danger_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danger_zones');
    }
};
