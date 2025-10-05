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
        Schema::create('medical_supplies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description');
            $table->enum('category', [
                'blood_products',
                'medicines',
                'vaccines',
                'surgical_instruments',
                'emergency_supplies',
                'diagnostic_kits',
                'medical_devices'
            ]);
            $table->enum('type', ['liquid', 'solid', 'fragile', 'temperature_sensitive']);
            $table->decimal('weight_kg', 8, 3); // Weight in kilograms
            $table->decimal('volume_ml', 10, 2)->nullable(); // Volume in milliliters
            $table->json('dimensions')->nullable(); // JSON: {length, width, height} in cm
            $table->integer('quantity_available')->default(0);
            $table->integer('minimum_stock_level')->default(0);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->json('storage_requirements')->nullable(); // JSON: temperature, humidity, etc.
            $table->json('handling_instructions')->nullable(); // Special handling requirements
            $table->boolean('requires_cold_chain')->default(false);
            $table->decimal('temperature_min', 5, 2)->nullable(); // Celsius
            $table->decimal('temperature_max', 5, 2)->nullable(); // Celsius
            $table->boolean('is_hazardous')->default(false);
            $table->boolean('is_controlled_substance')->default(false);
            $table->enum('priority_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Additional flexible data
            $table->timestamps();
            
            // Indexes
            $table->index(['category', 'type']);
            $table->index(['quantity_available', 'minimum_stock_level']);
            $table->index(['expiry_date', 'is_active']);
            $table->index('priority_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_supplies');
    }
};
