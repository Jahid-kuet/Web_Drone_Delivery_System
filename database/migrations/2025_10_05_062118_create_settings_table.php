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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json, array
            $table->string('group')->default('general'); // general, drone, delivery, notification
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Can be accessed by frontend
            $table->boolean('is_editable')->default(true); // Can be modified via admin panel
            $table->text('validation_rules')->nullable(); // Laravel validation rules
            $table->json('options')->nullable(); // For dropdown/select settings
            $table->string('default_value')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('requires_restart')->default(false); // App restart needed for changes
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['group', 'sort_order']);
            $table->index(['is_public', 'is_editable']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
