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
        Schema::create('security_facilities', function (Blueprint $table) {
            $table->bigIncrements('facility_id');
            $table->foreignId('location_id')->constrained('locations', 'location_id');
            $table->string('facility_type')->index();
            $table->string('facility_name');
            $table->text('description')->nullable();
            $table->string('status')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_facilities');
    }
};
