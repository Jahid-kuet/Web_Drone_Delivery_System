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
        // Add hub relationships to drones table
        Schema::table('drones', function (Blueprint $table) {
            $table->foreignId('home_hub_id')->nullable()->after('name')->constrained('hubs')->nullOnDelete();
            $table->foreignId('current_hub_id')->nullable()->after('home_hub_id')->constrained('hubs')->nullOnDelete();
        });

        // Add hub relationship to deliveries table
        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreignId('pickup_hub_id')->nullable()->after('drone_id')->constrained('hubs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drones', function (Blueprint $table) {
            $table->dropForeign(['home_hub_id']);
            $table->dropForeign(['current_hub_id']);
            $table->dropColumn(['home_hub_id', 'current_hub_id']);
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['pickup_hub_id']);
            $table->dropColumn('pickup_hub_id');
        });
    }
};
