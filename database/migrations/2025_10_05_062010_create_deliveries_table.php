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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_request_id')->constrained('delivery_requests')->onDelete('cascade');
            $table->foreignId('drone_id')->nullable()->constrained('drones')->onDelete('set null');
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('assigned_pilot_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('delivery_number')->unique();
            $table->enum('status', [
                'scheduled',
                'preparing',
                'loaded',
                'departed',
                'in_transit',
                'approaching_destination',
                'landed',
                'delivered',
                'returning',
                'completed',
                'failed',
                'cancelled',
                'emergency_landed'
            ])->default('scheduled');
            $table->datetime('scheduled_departure_time');
            $table->datetime('actual_departure_time')->nullable();
            $table->datetime('estimated_arrival_time');
            $table->datetime('actual_arrival_time')->nullable();
            $table->datetime('delivery_completed_time')->nullable();
            $table->json('pickup_coordinates');
            $table->json('delivery_coordinates');
            $table->json('current_coordinates')->nullable();
            $table->decimal('current_altitude_m', 8, 2)->nullable();
            $table->decimal('current_speed_kmh', 8, 2)->nullable();
            $table->decimal('distance_remaining_km', 8, 3)->nullable();
            $table->integer('estimated_time_remaining_minutes')->nullable();
            $table->decimal('total_distance_km', 8, 3);
            $table->json('route_waypoints')->nullable(); // Planned route coordinates
            $table->json('weather_conditions')->nullable();
            $table->decimal('fuel_battery_level_start', 5, 2)->nullable();
            $table->decimal('fuel_battery_level_current', 5, 2)->nullable();
            $table->decimal('fuel_battery_level_end', 5, 2)->nullable();
            $table->json('cargo_manifest'); // Detailed list of items being delivered
            $table->decimal('total_cargo_weight_kg', 8, 3);
            $table->text('special_handling_notes')->nullable();
            $table->text('pilot_notes')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->json('incidents')->nullable(); // Any incidents during delivery
            $table->string('delivery_confirmation_signature')->nullable();
            $table->json('delivery_photos')->nullable(); // Photos of delivery
            $table->boolean('requires_return_trip')->default(false);
            $table->json('return_cargo')->nullable();
            $table->enum('delivery_rating', [1, 2, 3, 4, 5])->nullable();
            $table->text('delivery_feedback')->nullable();
            $table->decimal('delivery_cost', 10, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'scheduled_departure_time']);
            $table->index(['drone_id', 'status']);
            $table->index(['hospital_id', 'delivery_completed_time']);
            $table->index('delivery_number');
            $table->index(['estimated_arrival_time', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
