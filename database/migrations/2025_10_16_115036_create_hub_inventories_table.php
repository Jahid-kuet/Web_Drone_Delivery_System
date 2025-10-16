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
        Schema::create('hub_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_supply_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_available')->default(0);
            $table->integer('minimum_stock_level')->default(10);
            $table->integer('maximum_stock_level')->default(500);
            $table->integer('reorder_quantity')->default(50);
            $table->integer('reorder_point')->default(20);
            $table->boolean('needs_cold_storage')->default(false);
            $table->decimal('storage_temperature_celsius', 5, 2)->nullable();
            $table->date('last_restocked_date')->nullable();
            $table->integer('last_restock_quantity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['hub_id', 'medical_supply_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_inventories');
    }
};
