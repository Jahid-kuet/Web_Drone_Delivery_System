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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('module'); // e.g., 'drones', 'deliveries', 'hospitals'
            $table->enum('action', [
                'create',
                'read',
                'update',
                'delete',
                'approve',
                'assign',
                'monitor',
                'report'
            ]);
            $table->string('resource')->nullable(); // Specific resource within module
            $table->boolean('is_system_permission')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('conditions')->nullable(); // Additional conditions for permission
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['module', 'action']);
            $table->index('slug');
            $table->index(['is_active', 'is_system_permission']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
