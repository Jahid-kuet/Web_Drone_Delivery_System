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
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('requested_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('request_number')->unique(); // Auto-generated request number
            $table->enum('priority', ['low', 'medium', 'high', 'critical', 'emergency'])->default('medium');
            $table->enum('status', [
                'pending',
                'approved',
                'assigned',
                'in_transit',
                'delivered',
                'cancelled',
                'rejected'
            ])->default('pending');
            $table->json('medical_supplies')->nullable(); // Array of supply IDs and quantities
            $table->decimal('total_weight_kg', 8, 3);
            $table->decimal('total_volume_ml', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->enum('urgency_level', ['routine', 'urgent', 'emergency', 'life_threatening']);
            $table->datetime('requested_delivery_time');
            $table->datetime('latest_acceptable_time')->nullable();
            $table->json('pickup_location')->nullable(); // GPS coordinates, address
            $table->json('delivery_location'); // GPS coordinates, address
            $table->text('special_instructions')->nullable();
            $table->json('handling_requirements')->nullable(); // Temperature, fragile, etc.
            $table->boolean('requires_signature')->default(true);
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_email')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->datetime('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'priority']);
            $table->index(['hospital_id', 'status']);
            $table->index(['urgency_level', 'requested_delivery_time']);
            $table->index('request_number');
            $table->index('requested_delivery_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_requests');
    }
};
