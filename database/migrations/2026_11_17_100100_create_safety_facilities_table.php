<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['cctv', 'street_light', 'security_post']);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('address')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_facilities');
    }
};
