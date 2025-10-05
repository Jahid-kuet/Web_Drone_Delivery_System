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
        Schema::create('drones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->string('serial_number')->unique();
            $table->string('registration_number')->unique();
            $table->enum('status', [
                'available',
                'assigned',
                'in_flight',
                'maintenance',
                'charging',
                'offline',
                'emergency'
            ])->default('available');
            $table->enum('type', [
                'medical_transport',
                'emergency_response',
                'blood_delivery',
                'pharmaceutical',
                'multi_purpose'
            ]);
            $table->decimal('max_payload_kg', 8, 3); // Maximum payload in kilograms
            $table->decimal('max_range_km', 8, 2); // Maximum range in kilometers
            $table->decimal('max_altitude_m', 8, 2); // Maximum altitude in meters
            $table->decimal('max_speed_kmh', 8, 2); // Maximum speed in km/h
            $table->integer('battery_life_minutes'); // Battery life in minutes
            $table->decimal('current_battery_level', 5, 2)->default(100.00); // Battery percentage
            $table->json('gps_coordinates')->nullable(); // Current GPS coordinates
            $table->decimal('current_altitude_m', 8, 2)->nullable();
            $table->decimal('current_speed_kmh', 8, 2)->nullable();
            $table->string('firmware_version')->nullable();
            $table->json('sensors')->nullable(); // Available sensors (GPS, cameras, temperature, etc.)
            $table->boolean('has_camera')->default(false);
            $table->boolean('has_temperature_control')->default(false);
            $table->boolean('has_emergency_parachute')->default(false);
            $table->decimal('temperature_min_celsius', 5, 2)->nullable();
            $table->decimal('temperature_max_celsius', 5, 2)->nullable();
            $table->string('operator_license_required')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_due')->nullable();
            $table->integer('total_flight_hours')->default(0);
            $table->integer('total_deliveries')->default(0);
            $table->json('flight_restrictions')->nullable(); // Weather, altitude, etc.
            $table->string('insurance_policy_number')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Additional flexible data
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'type']);
            $table->index(['current_battery_level', 'status']);
            $table->index(['next_maintenance_due', 'is_active']);
            $table->index('last_maintenance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drones');
    }
};
