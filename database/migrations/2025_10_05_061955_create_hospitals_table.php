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
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Hospital identification code
            $table->enum('type', [
                'general_hospital',
                'specialized_hospital',
                'clinic',
                'emergency_center',
                'blood_bank',
                'diagnostic_center',
                'pharmacy',
                'research_facility'
            ]);
            $table->text('address');
            $table->string('city');
            $table->string('state_province');
            $table->string('postal_code');
            $table->string('country');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('primary_phone');
            $table->string('emergency_phone')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('license_number')->unique();
            $table->date('license_expiry_date');
            $table->json('operating_hours')->nullable(); // JSON format for different days
            $table->json('emergency_hours')->nullable(); // 24/7 emergency availability
            $table->json('specializations')->nullable(); // Array of medical specializations
            $table->integer('bed_capacity')->nullable();
            $table->boolean('has_emergency_department')->default(false);
            $table->boolean('has_blood_bank')->default(false);
            $table->boolean('has_pharmacy')->default(false);
            $table->boolean('has_laboratory')->default(false);
            $table->boolean('has_helicopter_pad')->default(false);
            $table->boolean('has_drone_landing_pad')->default(false);
            $table->json('drone_landing_coordinates')->nullable(); // Specific coordinates for drone landing
            $table->decimal('drone_landing_altitude_m', 8, 2)->nullable();
            $table->json('delivery_preferences')->nullable(); // Preferred delivery times, special instructions
            $table->enum('priority_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('accepts_emergency_deliveries')->default(true);
            $table->json('approved_supply_categories')->nullable(); // Which supply categories they can receive
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('backup_contact_name')->nullable();
            $table->string('backup_contact_phone')->nullable();
            $table->text('special_instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'is_active']);
            $table->index(['city', 'state_province']);
            $table->index(['priority_level', 'accepts_emergency_deliveries']);
            $table->index(['latitude', 'longitude']);
            $table->index('license_expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
