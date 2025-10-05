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
        Schema::create('drone_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drone_id')->constrained('drones')->onDelete('cascade');
            $table->foreignId('delivery_id')->constrained('deliveries')->onDelete('cascade');
            $table->foreignId('assigned_by_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('assignment_status', [
                'assigned',
                'accepted',
                'rejected',
                'in_progress',
                'completed',
                'cancelled',
                'failed'
            ])->default('assigned');
            $table->datetime('assigned_at');
            $table->datetime('accepted_at')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->text('assignment_notes')->nullable();
            $table->text('pilot_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('completion_notes')->nullable();
            $table->decimal('estimated_duration_minutes', 8, 2);
            $table->decimal('actual_duration_minutes', 8, 2)->nullable();
            $table->decimal('estimated_distance_km', 8, 3);
            $table->decimal('actual_distance_km', 8, 3)->nullable();
            $table->decimal('estimated_battery_usage', 5, 2);
            $table->decimal('actual_battery_usage', 5, 2)->nullable();
            $table->json('pre_flight_checklist')->nullable();
            $table->json('post_flight_report')->nullable();
            $table->enum('priority_override', ['low', 'medium', 'high', 'critical'])->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['drone_id', 'assignment_status']);
            $table->index(['delivery_id', 'assignment_status']);
            $table->index(['assigned_at', 'assignment_status']);
            $table->index('assigned_by_user_id');
            
            // Unique constraint to prevent multiple assignments for same delivery
            $table->unique(['delivery_id', 'assignment_status'], 'unique_active_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drone_assignments');
    }
};
