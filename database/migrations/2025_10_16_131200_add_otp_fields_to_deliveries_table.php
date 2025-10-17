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
        Schema::table('deliveries', function (Blueprint $table) {
            // OTP for delivery confirmation
            $table->string('delivery_otp', 6)->nullable()->after('delivery_completed_time');
            $table->timestamp('otp_generated_at')->nullable()->after('delivery_otp');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_generated_at');
            $table->timestamp('otp_verified_at')->nullable()->after('otp_expires_at');
            $table->string('otp_verified_by')->nullable()->after('otp_verified_at'); // Name of person who verified
            
            // Delivery proof (skip delivery_notes as it already exists)
            $table->string('delivery_photo_path')->nullable()->after('otp_verified_by');
            $table->string('recipient_name')->nullable()->after('delivery_photo_path');
            $table->string('recipient_phone')->nullable()->after('recipient_name');
            $table->string('recipient_signature_path')->nullable()->after('recipient_phone');
            
            // Verification status
            $table->boolean('is_verified')->default(false)->after('recipient_signature_path');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            
            // Index for quick OTP lookup
            $table->index('delivery_otp');
            $table->index(['delivery_otp', 'otp_expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex(['delivery_otp', 'otp_expires_at']);
            $table->dropIndex(['delivery_otp']);
            
            $table->dropColumn([
                'delivery_otp',
                'otp_generated_at',
                'otp_expires_at',
                'otp_verified_at',
                'otp_verified_by',
                'delivery_photo_path',
                'recipient_name',
                'recipient_phone',
                'recipient_signature_path',
                'is_verified',
                'verified_at',
            ]);
        });
    }
};
