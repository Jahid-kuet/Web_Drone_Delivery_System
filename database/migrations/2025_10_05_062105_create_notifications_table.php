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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('type'); // delivery_update, emergency_alert, system_notification
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional notification data
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('channel', [
                'in_app',
                'email',
                'sms',
                'push',
                'webhook'
            ])->default('in_app');
            $table->boolean('is_read')->default(false);
            $table->datetime('read_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->datetime('sent_at')->nullable();
            $table->string('reference_type')->nullable(); // Model type (Delivery, Drone, etc.)
            $table->unsignedBigInteger('reference_id')->nullable(); // Model ID
            $table->json('recipients')->nullable(); // For broadcast notifications
            $table->text('action_url')->nullable(); // URL for notification action
            $table->datetime('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_read']);
            $table->index(['type', 'priority']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['created_at', 'priority']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
