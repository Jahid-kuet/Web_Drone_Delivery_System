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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->foreignId('granted_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('granted_at')->default(now());
            $table->boolean('is_active')->default(true);
            $table->json('conditions')->nullable(); // Additional conditions for this permission grant
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate role-permission assignments
            $table->unique(['role_id', 'permission_id'], 'unique_role_permission');
            
            // Indexes
            $table->index(['role_id', 'is_active']);
            $table->index(['permission_id', 'is_active']);
            $table->index('granted_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
