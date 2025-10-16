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
        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('hub_type', ['warehouse', 'distribution_center', 'medical_depot'])->default('warehouse');
            $table->string('address');
            $table->string('city');
            $table->string('division'); // Dhaka, Chittagong, Khulna, etc.
            $table->string('district')->nullable();
            $table->string('postal_code');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('contact_person');
            $table->string('phone');
            $table->string('email');
            $table->json('operating_hours')->nullable();
            
            // Storage capacity
            $table->integer('storage_capacity_cubic_meters')->default(100);
            $table->boolean('has_cold_storage')->default(false);
            $table->decimal('cold_storage_temp_min', 5, 2)->nullable();
            $table->decimal('cold_storage_temp_max', 5, 2)->nullable();
            $table->integer('cold_storage_capacity_liters')->nullable();
            
            // Drone facilities
            $table->integer('drone_charging_stations')->default(4);
            $table->integer('drone_parking_bays')->default(8);
            $table->boolean('has_maintenance_facility')->default(false);
            $table->boolean('has_weather_station')->default(false);
            
            // Operational
            $table->boolean('is_active')->default(true);
            $table->boolean('is_24_7')->default(false);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubs');
    }
};
