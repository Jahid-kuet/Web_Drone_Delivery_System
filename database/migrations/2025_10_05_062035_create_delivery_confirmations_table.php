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
        Schema::create('delivery_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->onDelete('cascade');
            $table->foreignId('hospital_id')->constrained('hospitals')->onDelete('cascade');
            $table->foreignId('confirmed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('confirmation_number')->unique();
            $table->enum('confirmation_method', [
                'digital_signature',
                'sms_code',
                'email_confirmation',
                'biometric',
                'photo_verification',
                'automated_sensor'
            ]);
            $table->datetime('confirmed_at');
            $table->json('delivered_items'); // List of items actually delivered with quantities
            $table->json('missing_items')->nullable(); // Items that were supposed to be delivered but missing
            $table->json('damaged_items')->nullable(); // Items that arrived damaged
            $table->enum('delivery_condition', [
                'excellent',
                'good',
                'acceptable',
                'poor',
                'damaged'
            ]);
            $table->enum('overall_rating', [1, 2, 3, 4, 5]);
            $table->text('feedback')->nullable();
            $table->text('issues_reported')->nullable();
            $table->string('recipient_name');
            $table->string('recipient_title')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('digital_signature_path')->nullable();
            $table->json('confirmation_photos')->nullable(); // Photos of received items
            $table->json('temperature_log')->nullable(); // Temperature readings during transport
            $table->decimal('delivery_latitude', 10, 8);
            $table->decimal('delivery_longitude', 11, 8);
            $table->json('packaging_condition')->nullable(); // Condition of packaging
            $table->boolean('all_items_received')->default(true);
            $table->boolean('customer_satisfied')->default(true);
            $table->text('additional_notes')->nullable();
            $table->json('quality_checks')->nullable(); // Any quality verification checks
            $table->string('confirmation_code')->nullable(); // SMS/Email confirmation code
            $table->boolean('requires_follow_up')->default(false);
            $table->text('follow_up_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['delivery_id', 'confirmed_at']);
            $table->index(['hospital_id', 'confirmed_at']);
            $table->index('confirmation_number');
            $table->index(['overall_rating', 'customer_satisfied']);
            $table->index(['confirmed_at', 'delivery_condition']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_confirmations');
    }
};
