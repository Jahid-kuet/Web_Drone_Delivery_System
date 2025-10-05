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
        Schema::create('delivery_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->onDelete('cascade');
            $table->foreignId('drone_id')->constrained('drones')->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude_m', 8, 2);
            $table->decimal('speed_kmh', 8, 2);
            $table->decimal('heading_degrees', 5, 2); // 0-360 degrees
            $table->decimal('battery_level', 5, 2);
            $table->enum('flight_mode', [
                'manual',
                'autopilot',
                'gps_guided',
                'return_to_home',
                'emergency',
                'hovering'
            ]);
            $table->enum('tracking_status', [
                'normal',
                'warning',
                'critical',
                'emergency',
                'offline'
            ])->default('normal');
            $table->json('sensor_data')->nullable(); // Temperature, humidity, etc.
            $table->json('weather_data')->nullable(); // Wind speed, weather conditions
            $table->decimal('signal_strength', 5, 2)->nullable();
            $table->boolean('gps_lock')->default(true);
            $table->integer('satellites_visible')->nullable();
            $table->json('system_alerts')->nullable(); // Any system warnings or alerts
            $table->json('cargo_status')->nullable(); // Cargo integrity sensors
            $table->decimal('estimated_arrival_time_minutes', 8, 2)->nullable();
            $table->decimal('distance_to_destination_km', 8, 3);
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at'); // Actual time of GPS reading
            $table->timestamps();
            
            // Indexes
            $table->index(['delivery_id', 'recorded_at']);
            $table->index(['drone_id', 'recorded_at']);
            $table->index(['latitude', 'longitude']);
            $table->index(['tracking_status', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_tracking');
    }
};
