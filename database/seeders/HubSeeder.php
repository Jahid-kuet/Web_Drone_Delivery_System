<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hub;
use App\Models\HubInventory;
use App\Models\MedicalSupply;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Khulna Central Hub
        $khulnaCentral = Hub::create([
            'name' => 'Khulna Central Medical Hub',
            'code' => 'HUB-KHL-001',
            'hub_type' => 'medical_depot',
            'address' => 'Khulna Medical College Road, Khanjahan Ali',
            'city' => 'Khulna',
            'division' => 'Khulna',
            'district' => 'Khulna',
            'postal_code' => '9100',
            'latitude' => 22.8456,
            'longitude' => 89.5403,
            'contact_person' => 'Dr. Abdullah Rahman',
            'phone' => '+880-41-761020',
            'email' => 'khulna.hub@dronedelivery.bd',
            'operating_hours' => json_encode([
                'monday' => '24 hours',
                'tuesday' => '24 hours',
                'wednesday' => '24 hours',
                'thursday' => '24 hours',
                'friday' => '24 hours',
                'saturday' => '24 hours',
                'sunday' => '24 hours',
            ]),
            'storage_capacity_cubic_meters' => 150,
            'has_cold_storage' => true,
            'cold_storage_temp_min' => 2.0,
            'cold_storage_temp_max' => 8.0,
            'cold_storage_capacity_liters' => 500,
            'drone_charging_stations' => 10,
            'drone_parking_bays' => 20,
            'has_maintenance_facility' => true,
            'has_weather_station' => true,
            'is_active' => true,
            'is_24_7' => true,
            'notes' => 'Primary distribution hub for Khulna Division. Equipped with advanced cold chain storage for blood and vaccines.',
        ]);

        // Create Jessore Distribution Hub
        $jessoreHub = Hub::create([
            'name' => 'Jessore District Hub',
            'code' => 'HUB-JES-001',
            'hub_type' => 'distribution_center',
            'address' => 'Jessore Sadar Hospital Complex',
            'city' => 'Jessore',
            'division' => 'Khulna',
            'district' => 'Jessore',
            'postal_code' => '7400',
            'latitude' => 23.1634,
            'longitude' => 89.2182,
            'contact_person' => 'Mohammad Karim',
            'phone' => '+880-421-68291',
            'email' => 'jessore.hub@dronedelivery.bd',
            'operating_hours' => json_encode([
                'monday' => '06:00-22:00',
                'tuesday' => '06:00-22:00',
                'wednesday' => '06:00-22:00',
                'thursday' => '06:00-22:00',
                'friday' => '06:00-22:00',
                'saturday' => '06:00-22:00',
                'sunday' => '08:00-18:00',
            ]),
            'storage_capacity_cubic_meters' => 80,
            'has_cold_storage' => true,
            'cold_storage_temp_min' => 2.0,
            'cold_storage_temp_max' => 8.0,
            'cold_storage_capacity_liters' => 200,
            'drone_charging_stations' => 6,
            'drone_parking_bays' => 12,
            'has_maintenance_facility' => false,
            'has_weather_station' => false,
            'is_active' => true,
            'is_24_7' => false,
            'notes' => 'Secondary hub serving Jessore district.',
        ]);

        // Create Satkhira Hub
        $satkhiraHub = Hub::create([
            'name' => 'Satkhira District Hub',
            'code' => 'HUB-SAT-001',
            'hub_type' => 'distribution_center',
            'address' => 'Satkhira Sadar Hospital Area',
            'city' => 'Satkhira',
            'division' => 'Khulna',
            'district' => 'Satkhira',
            'postal_code' => '9400',
            'latitude' => 22.7185,
            'longitude' => 89.0700,
            'contact_person' => 'Fatema Begum',
            'phone' => '+880-471-62345',
            'email' => 'satkhira.hub@dronedelivery.bd',
            'operating_hours' => json_encode([
                'monday' => '08:00-20:00',
                'tuesday' => '08:00-20:00',
                'wednesday' => '08:00-20:00',
                'thursday' => '08:00-20:00',
                'friday' => '08:00-20:00',
                'saturday' => '08:00-20:00',
                'sunday' => '08:00-14:00',
            ]),
            'storage_capacity_cubic_meters' => 60,
            'has_cold_storage' => true,
            'cold_storage_temp_min' => 2.0,
            'cold_storage_temp_max' => 8.0,
            'cold_storage_capacity_liters' => 150,
            'drone_charging_stations' => 4,
            'drone_parking_bays' => 8,
            'has_maintenance_facility' => false,
            'has_weather_station' => false,
            'is_active' => true,
            'is_24_7' => false,
            'notes' => 'Serving Satkhira and coastal areas.',
        ]);

        $this->command->info('✅ Khulna hubs created successfully!');

        // Seed initial inventory for Khulna Central Hub
        $this->seedInventory($khulnaCentral);
        $this->seedInventory($jessoreHub);
        $this->seedInventory($satkhiraHub);

        $this->command->info('✅ Hub inventories seeded successfully!');
    }

    /**
     * Seed inventory for a hub
     */
    private function seedInventory(Hub $hub)
    {
        // Get all medical supplies
        $supplies = MedicalSupply::all();

        if ($supplies->isEmpty()) {
            $this->command->warn('⚠️ No medical supplies found. Please seed medical supplies first.');
            return;
        }

        foreach ($supplies as $supply) {
            // Determine quantities based on hub type
            $isMainHub = $hub->code === 'HUB-KHL-001';
            
            $quantity = $isMainHub ? rand(100, 500) : rand(50, 200);
            $minStock = $isMainHub ? 50 : 20;
            $maxStock = $isMainHub ? 1000 : 500;
            $reorderQty = $isMainHub ? 200 : 100;
            $reorderPoint = $isMainHub ? 100 : 50;

            HubInventory::create([
                'hub_id' => $hub->id,
                'medical_supply_id' => $supply->id,
                'quantity_available' => $quantity,
                'minimum_stock_level' => $minStock,
                'maximum_stock_level' => $maxStock,
                'reorder_quantity' => $reorderQty,
                'reorder_point' => $reorderPoint,
                'needs_cold_storage' => in_array($supply->category, ['blood', 'vaccine', 'insulin']),
                'storage_temperature_celsius' => in_array($supply->category, ['blood', 'vaccine', 'insulin']) ? 4.0 : null,
                'last_restocked_date' => now()->subDays(rand(1, 30)),
                'last_restock_quantity' => $reorderQty,
                'notes' => "Initial stock for {$hub->name}",
            ]);
        }

        $this->command->info("  → Inventory created for {$hub->name}");
    }
}

