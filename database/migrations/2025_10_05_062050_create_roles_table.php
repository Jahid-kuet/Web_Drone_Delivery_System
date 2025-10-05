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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('level', [
                'super_admin',
                'admin',
                'manager',
                'operator',
                'staff',
                'user'
            ]);
            $table->json('capabilities')->nullable(); // JSON array of capabilities
            $table->boolean('is_system_role')->default(false); // Cannot be deleted
            $table->boolean('is_active')->default(true);
            $table->integer('max_users')->nullable(); // Limit number of users with this role
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['level', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
