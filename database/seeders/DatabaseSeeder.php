<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Hospital;
use App\Models\MedicalSupply;
use App\Models\Drone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            ['name' => 'view-deliveries', 'slug' => 'view-deliveries', 'module' => 'deliveries', 'description' => 'View deliveries'],
            ['name' => 'create-deliveries', 'slug' => 'create-deliveries', 'module' => 'deliveries', 'description' => 'Create deliveries'],
            ['name' => 'view-drones', 'slug' => 'view-drones', 'module' => 'drones', 'description' => 'View drones'],
            ['name' => 'create-drones', 'slug' => 'create-drones', 'module' => 'drones', 'description' => 'Create drones'],
            ['name' => 'view-hospitals', 'slug' => 'view-hospitals', 'module' => 'hospitals', 'description' => 'View hospitals'],
            ['name' => 'create-hospitals', 'slug' => 'create-hospitals', 'module' => 'hospitals', 'description' => 'Create hospitals'],
            ['name' => 'view-users', 'slug' => 'view-users', 'module' => 'users', 'description' => 'View users'],
            ['name' => 'create-users', 'slug' => 'create-users', 'module' => 'users', 'description' => 'Create users'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], [
            'name' => 'Administrator',
            'description' => 'Full system access',
            'level' => 'admin',
            'is_system_role' => true,
        ]);

        $operatorRole = Role::firstOrCreate(['slug' => 'drone_operator'], [
            'name' => 'Drone Operator',
            'description' => 'Drone flight operations',
            'level' => 'operator',
            'is_system_role' => true,
        ]);

        $hospitalAdminRole = Role::firstOrCreate(['slug' => 'hospital_admin'], [
            'name' => 'Hospital Admin',
            'description' => 'Hospital administration',
            'level' => 'manager',
            'is_system_role' => true,
        ]);

        $hospitalStaffRole = Role::firstOrCreate(['slug' => 'hospital_staff'], [
            'name' => 'Hospital Staff',
            'description' => 'Hospital staff member',
            'level' => 'staff',
            'is_system_role' => true,
        ]);

        // Assign all permissions to admin role
        $adminRole->permissions()->sync(Permission::all());

        // Create a sample hospital first
                // Create Primary Hospital (Bangladesh)
        $hospital = Hospital::firstOrCreate(['code' => 'SQUH001'], [
            'name' => 'Square Hospital Dhaka',
            'code' => 'SQUH001',
            'email' => 'info@squarehospital.com',
            'type' => 'general_hospital',
            'address' => '18/F, Bir Uttam Qazi Nuruzzaman Sarak, West Panthapath',
            'city' => 'Dhaka',
            'state_province' => 'Dhaka Division',
            'postal_code' => '1205',
            'country' => 'Bangladesh',
            'latitude' => 23.7557,
            'longitude' => 90.3872,
            'primary_phone' => '+880-2-8159457',
            'emergency_phone' => '+880-2-8159458',
            'website' => 'https://squarehospital.com',
            'license_number' => 'LIC-BD-DHK-' . time(),
            'license_expiry_date' => now()->addYears(5),
            'bed_capacity' => 500,
            'has_emergency_department' => true,
            'has_drone_landing_pad' => true,
            'is_active' => true,
            'is_verified' => true,
        ]);

        // Create Admin User
        $admin = User::firstOrCreate(['email' => 'admin@drone.com'], [
            'name' => 'System Administrator',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $admin->roles()->sync([$adminRole->id]);

        // Create Drone Operator
        $operator = User::firstOrCreate(['email' => 'operator@drone.com'], [
            'name' => 'John Operator',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $operator->roles()->sync([$operatorRole->id]);

        // Create Hospital Admin (with hospital_id)
        $hospitalAdmin = User::firstOrCreate(['email' => 'hospital@drone.com'], [
            'name' => 'Hospital Administrator',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'hospital_id' => $hospital->id,  // Assign hospital
            'status' => 'active',
        ]);
        $hospitalAdmin->roles()->sync([$hospitalAdminRole->id]);

        // Create Medical Supplies
        $medicalSupplies = [
            [
                'name' => 'Blood Bag (Type O-)',
                'code' => 'BLOOD-O-NEG',
                'description' => 'Blood bag containing O- blood type, universal donor',
                'category' => 'blood_products',
                'type' => 'liquid',
                'weight_kg' => 0.5,
                'volume_ml' => 450.00,
                'quantity_available' => 50,
                'minimum_stock_level' => 20,
                'unit_price' => 150.00,
                'requires_cold_chain' => true,
                'temperature_min' => 2.00,
                'temperature_max' => 6.00,
                'is_active' => true,
            ],
            [
                'name' => 'Insulin Vials',
                'code' => 'MED-INSULIN-100',
                'description' => 'Insulin medication for diabetes treatment',
                'category' => 'medicines',
                'type' => 'temperature_sensitive',
                'weight_kg' => 0.1,
                'volume_ml' => 10.00,
                'quantity_available' => 100,
                'minimum_stock_level' => 30,
                'unit_price' => 50.00,
                'requires_cold_chain' => true,
                'temperature_min' => 2.00,
                'temperature_max' => 8.00,
                'is_active' => true,
            ],
            [
                'name' => 'Surgical Masks (Box of 50)',
                'code' => 'PPE-MASK-50',
                'description' => 'Disposable surgical masks for medical use',
                'category' => 'emergency_supplies',
                'type' => 'solid',
                'weight_kg' => 0.2,
                'quantity_available' => 200,
                'minimum_stock_level' => 50,
                'unit_price' => 25.00,
                'requires_cold_chain' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Epinephrine Auto-Injector',
                'code' => 'MED-EPI-AUTO',
                'description' => 'Emergency epinephrine auto-injector for severe allergic reactions',
                'category' => 'medicines',
                'type' => 'temperature_sensitive',
                'weight_kg' => 0.05,
                'quantity_available' => 30,
                'minimum_stock_level' => 10,
                'unit_price' => 300.00,
                'requires_cold_chain' => true,
                'temperature_min' => 15.00,
                'temperature_max' => 25.00,
                'is_active' => true,
            ],
            [
                'name' => 'Sterile Bandages Pack',
                'code' => 'SUPP-BAND-PACK',
                'description' => 'Sterile bandages and dressings for wound care',
                'category' => 'emergency_supplies',
                'type' => 'solid',
                'weight_kg' => 0.3,
                'quantity_available' => 150,
                'minimum_stock_level' => 40,
                'unit_price' => 15.00,
                'requires_cold_chain' => false,
                'is_active' => true,
            ],
            [
                'name' => 'COVID-19 Vaccine Vials',
                'code' => 'VAC-COVID-10',
                'description' => 'COVID-19 vaccine vials requiring ultra-cold storage',
                'category' => 'vaccines',
                'type' => 'temperature_sensitive',
                'weight_kg' => 0.08,
                'volume_ml' => 5.00,
                'quantity_available' => 80,
                'minimum_stock_level' => 25,
                'unit_price' => 75.00,
                'requires_cold_chain' => true,
                'temperature_min' => -20.00,
                'temperature_max' => -10.00,
                'is_active' => true,
            ],
            [
                'name' => 'Nitrile Gloves (Box of 100)',
                'code' => 'PPE-GLOVE-100',
                'description' => 'Nitrile examination gloves, latex-free',
                'category' => 'emergency_supplies',
                'type' => 'solid',
                'weight_kg' => 0.4,
                'quantity_available' => 120,
                'minimum_stock_level' => 30,
                'unit_price' => 20.00,
                'requires_cold_chain' => false,
                'is_active' => true,
            ],
            [
                'name' => 'IV Fluids (Saline Solution)',
                'code' => 'MED-IV-SALINE-1L',
                'description' => '1L sterile saline solution for IV administration',
                'category' => 'medicines',
                'type' => 'liquid',
                'weight_kg' => 1.0,
                'volume_ml' => 1000.00,
                'quantity_available' => 75,
                'minimum_stock_level' => 20,
                'unit_price' => 10.00,
                'requires_cold_chain' => false,
                'is_active' => true,
            ],
        ];

        foreach ($medicalSupplies as $supply) {
            \App\Models\MedicalSupply::firstOrCreate(['code' => $supply['code']], $supply);
        }

        // Create Drones
        $drones = [
            [
                'name' => 'Drone Alpha',
                'serial_number' => 'DRN-ALPHA-001',
                'registration_number' => 'REG-ALPHA-001',
                'model' => 'MediDrone X1',
                'status' => 'available',
                'type' => 'medical_transport',
                'max_payload_kg' => 5.0,
                'max_range_km' => 50.0,
                'max_altitude_m' => 500.0,
                'max_speed_kmh' => 80.0,
                'battery_life_minutes' => 90,
                'current_battery_level' => 100.00,
                'total_flight_hours' => 250,
                'total_deliveries' => 150,
                'last_maintenance_date' => now()->subMonths(1),
                'next_maintenance_due' => now()->addMonths(2),
                'has_temperature_control' => true,
                'has_camera' => true,
                'has_emergency_parachute' => true,
                'temperature_min_celsius' => 2.0,
                'temperature_max_celsius' => 8.0,
                'insurance_policy_number' => 'INS-DRONE-' . rand(1000, 9999),
                'insurance_expiry_date' => now()->addYear(),
                'is_active' => true,
            ],
            [
                'name' => 'Drone Beta',
                'serial_number' => 'DRN-BETA-002',
                'registration_number' => 'REG-BETA-002',
                'model' => 'MediDrone X2',
                'status' => 'in_flight',
                'type' => 'blood_delivery',
                'max_payload_kg' => 7.0,
                'max_range_km' => 75.0,
                'max_altitude_m' => 600.0,
                'max_speed_kmh' => 100.0,
                'battery_life_minutes' => 120,
                'current_battery_level' => 65.00,
                'total_flight_hours' => 180,
                'total_deliveries' => 95,
                'last_maintenance_date' => now()->subMonths(2),
                'next_maintenance_due' => now()->addMonth(),
                'has_temperature_control' => true,
                'has_camera' => true,
                'has_emergency_parachute' => true,
                'temperature_min_celsius' => 2.0,
                'temperature_max_celsius' => 8.0,
                'insurance_policy_number' => 'INS-DRONE-' . rand(1000, 9999),
                'insurance_expiry_date' => now()->addYear(),
                'is_active' => true,
            ],
            [
                'name' => 'Drone Gamma',
                'serial_number' => 'DRN-GAMMA-003',
                'registration_number' => 'REG-GAMMA-003',
                'model' => 'MediDrone Pro',
                'status' => 'charging',
                'type' => 'pharmaceutical',
                'max_payload_kg' => 10.0,
                'max_range_km' => 100.0,
                'max_altitude_m' => 800.0,
                'max_speed_kmh' => 120.0,
                'battery_life_minutes' => 150,
                'current_battery_level' => 45.00,
                'total_flight_hours' => 95,
                'total_deliveries' => 50,
                'last_maintenance_date' => now()->subWeeks(2),
                'next_maintenance_due' => now()->addMonths(4),
                'has_temperature_control' => true,
                'has_camera' => true,
                'has_emergency_parachute' => true,
                'temperature_min_celsius' => -20.0,
                'temperature_max_celsius' => 8.0,
                'insurance_policy_number' => 'INS-DRONE-' . rand(1000, 9999),
                'insurance_expiry_date' => now()->addYear(),
                'is_active' => true,
            ],
            [
                'name' => 'Drone Delta',
                'serial_number' => 'DRN-DELTA-004',
                'registration_number' => 'REG-DELTA-004',
                'model' => 'MediDrone X1',
                'status' => 'maintenance',
                'type' => 'multi_purpose',
                'max_payload_kg' => 5.0,
                'max_range_km' => 50.0,
                'max_altitude_m' => 500.0,
                'max_speed_kmh' => 80.0,
                'battery_life_minutes' => 90,
                'current_battery_level' => 20.00,
                'total_flight_hours' => 450,
                'total_deliveries' => 280,
                'last_maintenance_date' => now()->subDays(2),
                'next_maintenance_due' => now()->addMonths(3),
                'has_temperature_control' => false,
                'has_camera' => true,
                'has_emergency_parachute' => true,
                'insurance_policy_number' => 'INS-DRONE-' . rand(1000, 9999),
                'insurance_expiry_date' => now()->addMonths(6),
                'is_active' => true,
            ],
        ];

        $droneAlpha = \App\Models\Drone::firstOrCreate(['serial_number' => $drones[0]['serial_number']], $drones[0]);
        $droneBeta = \App\Models\Drone::firstOrCreate(['serial_number' => $drones[1]['serial_number']], $drones[1]);
        $droneGamma = \App\Models\Drone::firstOrCreate(['serial_number' => $drones[2]['serial_number']], $drones[2]);
        $droneDelta = \App\Models\Drone::firstOrCreate(['serial_number' => $drones[3]['serial_number']], $drones[3]);

        // Create more hospitals
                $moreHospitals = [
            [
                'name' => 'Dhaka Medical College Hospital',
                'code' => 'DMCH002',
                'type' => 'specialized_hospital',
                'email' => 'info@dhakamedical.gov.bd',
                'address' => 'Bakshi Bazar, Secretariat Road',
                'city' => 'Dhaka',
                'state_province' => 'Dhaka Division',
                'postal_code' => '1000',
                'country' => 'Bangladesh',
                'latitude' => 23.7261,
                'longitude' => 90.3967,
                'primary_phone' => '+880-2-8626812',
                'emergency_phone' => '+880-2-8626813',
                'website' => 'https://dhakamedical.gov.bd',
                'license_number' => 'LIC-BD-DHK-' . (time() + 1),
                'license_expiry_date' => now()->addYears(5),
                'bed_capacity' => 600,
                'has_emergency_department' => true,
                'has_drone_landing_pad' => true,
                'is_active' => true,
                'is_verified' => true,
            ],
            [
                'name' => 'Chittagong Medical College Hospital',
                'code' => 'CMCH003',
                'type' => 'general_hospital',
                'email' => 'info@chittagongmedical.gov.bd',
                'address' => 'Panchlaish, K.B. Fazlul Kader Road',
                'city' => 'Chittagong',
                'state_province' => 'Chittagong Division',
                'postal_code' => '4203',
                'country' => 'Bangladesh',
                'latitude' => 22.3475,
                'longitude' => 91.8123,
                'primary_phone' => '+880-31-619821',
                'emergency_phone' => '+880-31-619822',
                'website' => 'https://chittagongmedical.gov.bd',
                'license_number' => 'LIC-BD-CTG-' . (time() + 2),
                'license_expiry_date' => now()->addYears(5),
                'bed_capacity' => 450,
                'has_emergency_department' => true,
                'has_drone_landing_pad' => true,
                'is_active' => true,
                'is_verified' => true,
            ],
        ];

        foreach ($moreHospitals as $hospitalData) {
            \App\Models\Hospital::firstOrCreate(['code' => $hospitalData['code']], $hospitalData);
        }

        // Create Delivery Requests
        $deliveryRequests = [
            [
                'hospital_id' => $hospital->id,
                'requested_by_user_id' => $hospitalAdmin->id,
                'request_number' => 'REQ-' . date('Ymd') . '-001',
                'priority' => 'high',
                'status' => 'pending',
                'medical_supplies' => json_encode([
                    ['name' => 'Blood Bag (Type O-)', 'quantity' => 5, 'code' => 'BLOOD-O-NEG']
                ]),
                'total_weight_kg' => 2.5,
                'total_volume_ml' => 2250.0,
                'description' => 'Emergency blood supply needed',
                'urgency_level' => 'emergency',
                'requested_delivery_time' => now()->addHours(2),
                'latest_acceptable_time' => now()->addHours(4),
                'pickup_location' => json_encode([
                    'address' => 'Central Medical Warehouse, Dhanmondi',
                    'city' => 'Dhaka',
                    'latitude' => 23.7461,
                    'longitude' => 90.3742
                ]),
                'delivery_location' => json_encode([
                    'address' => $hospital->address,
                    'city' => $hospital->city,
                    'latitude' => $hospital->latitude,
                    'longitude' => $hospital->longitude
                ]),
                'special_instructions' => 'Handle with extreme care. Temperature critical.',
                'requires_signature' => true,
                'recipient_name' => 'Dr. Sarah Johnson',
                'recipient_phone' => '+1 (555) 123-4567',
            ],
            [
                'hospital_id' => $hospital->id,
                'requested_by_user_id' => $hospitalAdmin->id,
                'request_number' => 'REQ-' . date('Ymd') . '-002',
                'priority' => 'medium',
                'status' => 'approved',
                'approved_by_user_id' => $admin->id,
                'approved_at' => now()->subHours(1),
                'medical_supplies' => json_encode([
                    ['name' => 'Insulin Vials', 'quantity' => 10, 'code' => 'MED-INSULIN-100']
                ]),
                'total_weight_kg' => 1.0,
                'description' => 'Regular insulin supply',
                'urgency_level' => 'routine',
                'requested_delivery_time' => now()->addHours(6),
                'pickup_location' => json_encode([
                    'address' => 'Pharmacy Depot, Gulshan',
                    'city' => 'Dhaka',
                    'latitude' => 23.7806,
                    'longitude' => 90.4193
                ]),
                'delivery_location' => json_encode([
                    'address' => $hospital->address,
                    'city' => $hospital->city,
                    'latitude' => $hospital->latitude,
                    'longitude' => $hospital->longitude
                ]),
                'requires_signature' => true,
                'recipient_name' => 'Nurse Emily Davis',
                'recipient_phone' => '+1 (555) 234-5678',
            ],
        ];

        foreach ($deliveryRequests as $requestData) {
            \App\Models\DeliveryRequest::firstOrCreate(
                ['request_number' => $requestData['request_number']], 
                $requestData
            );
        }

        // ===========================
        // CREATE DELIVERIES (Bangladesh Locations)
        // ===========================
        $deliveryRequest1 = \App\Models\DeliveryRequest::where('request_number', 'REQ-' . date('Ymd') . '-001')->first();
        $deliveryRequest2 = \App\Models\DeliveryRequest::where('request_number', 'REQ-' . date('Ymd') . '-002')->first();

        $deliveries = [
            [
                'delivery_request_id' => $deliveryRequest1 ? $deliveryRequest1->id : null,
                'drone_id' => $droneAlpha->id,
                'hospital_id' => $hospital->id,
                'assigned_pilot_id' => $operator->id,
                'delivery_number' => 'DEL-' . date('Ymd') . '-001',
                'status' => 'in_transit',
                'scheduled_departure_time' => now()->subMinutes(20),
                'actual_departure_time' => now()->subMinutes(18),
                'estimated_arrival_time' => now()->addMinutes(12),
                'pickup_coordinates' => json_encode([
                    'address' => 'Central Medical Warehouse, Dhanmondi',
                    'city' => 'Dhaka',
                    'latitude' => 23.7461,
                    'longitude' => 90.3742
                ]),
                'delivery_coordinates' => json_encode([
                    'address' => $hospital->address,
                    'city' => $hospital->city,
                    'latitude' => $hospital->latitude,
                    'longitude' => $hospital->longitude
                ]),
                'current_coordinates' => json_encode([
                    'latitude' => 23.7520,
                    'longitude' => 90.3820
                ]),
                'current_altitude_m' => 120.5,
                'current_speed_kmh' => 65.3,
                'distance_remaining_km' => 2.8,
                'estimated_time_remaining_minutes' => 12,
                'total_distance_km' => 8.5,
                'route_waypoints' => json_encode([
                    ['lat' => 23.7461, 'lng' => 90.3742],
                    ['lat' => 23.7500, 'lng' => 90.3800],
                    ['lat' => 23.7557, 'lng' => 90.3872]
                ]),
                'weather_conditions' => json_encode([
                    'temperature' => 28,
                    'humidity' => 75,
                    'wind_speed' => 12,
                    'conditions' => 'Partly Cloudy'
                ]),
                'fuel_battery_level_start' => 100.00,
                'fuel_battery_level_current' => 78.50,
                'cargo_manifest' => json_encode([
                    ['item' => 'Blood Bag (Type O-)', 'quantity' => 5, 'weight_kg' => 2.5]
                ]),
                'total_cargo_weight_kg' => 2.5,
                'special_handling_notes' => 'Temperature sensitive - maintain 2-8°C',
                'pilot_notes' => 'Clear weather, smooth flight',
                'delivery_cost' => 850.00,
            ],
            [
                'delivery_request_id' => $deliveryRequest2 ? $deliveryRequest2->id : null,
                'drone_id' => $droneBeta->id,
                'hospital_id' => $hospital->id,
                'assigned_pilot_id' => $operator->id,
                'delivery_number' => 'DEL-' . date('Ymd') . '-002',
                'status' => 'completed',
                'scheduled_departure_time' => now()->subHours(2),
                'actual_departure_time' => now()->subHours(2)->addMinutes(5),
                'estimated_arrival_time' => now()->subHours(1)->subMinutes(25),
                'actual_arrival_time' => now()->subHours(1)->subMinutes(30),
                'delivery_completed_time' => now()->subHours(1)->subMinutes(20),
                'pickup_coordinates' => json_encode([
                    'address' => 'Pharmacy Depot, Gulshan',
                    'city' => 'Dhaka',
                    'latitude' => 23.7806,
                    'longitude' => 90.4193
                ]),
                'delivery_coordinates' => json_encode([
                    'address' => $hospital->address,
                    'city' => $hospital->city,
                    'latitude' => $hospital->latitude,
                    'longitude' => $hospital->longitude
                ]),
                'current_coordinates' => json_encode([
                    'latitude' => $hospital->latitude,
                    'longitude' => $hospital->longitude
                ]),
                'total_distance_km' => 6.2,
                'route_waypoints' => json_encode([
                    ['lat' => 23.7806, 'lng' => 90.4193],
                    ['lat' => 23.7650, 'lng' => 90.3950],
                    ['lat' => 23.7557, 'lng' => 90.3872]
                ]),
                'weather_conditions' => json_encode([
                    'temperature' => 26,
                    'humidity' => 70,
                    'wind_speed' => 8,
                    'conditions' => 'Clear'
                ]),
                'fuel_battery_level_start' => 100.00,
                'fuel_battery_level_end' => 85.00,
                'cargo_manifest' => json_encode([
                    ['item' => 'Insulin Vials', 'quantity' => 10, 'weight_kg' => 1.0]
                ]),
                'total_cargo_weight_kg' => 1.0,
                'special_handling_notes' => 'Refrigerated items - handle with care',
                'pilot_notes' => 'Successful delivery, no issues',
                'delivery_notes' => 'Delivered to pharmacy reception, signed by Nurse Emily Davis',
                'delivery_confirmation_signature' => 'Emily Davis',
                'delivery_rating' => 5,
                'delivery_feedback' => 'Fast and professional service',
                'delivery_cost' => 650.00,
            ],
        ];

        foreach ($deliveries as $deliveryData) {
            if (isset($deliveryData['delivery_request_id']) && $deliveryData['delivery_request_id']) {
                \App\Models\Delivery::firstOrCreate(
                    ['delivery_number' => $deliveryData['delivery_number']], 
                    $deliveryData
                );
            }
        }

        $this->command->info('✅ Database seeded successfully with comprehensive test data!');
        $this->command->info('');
        $this->command->info('📧 Default Users:');
        $this->command->info('   Admin: admin@drone.com / password123');
        $this->command->info('   Operator: operator@drone.com / password123');
        $this->command->info('   Hospital: hospital@drone.com / password123');
        $this->command->info('');
        $this->command->info('📊 Test Data Created:');
        $this->command->info('   • 8 Medical Supplies');
        $this->command->info('   • 4 Drones (various statuses)');
        $this->command->info('   • 3 Hospitals (Bangladesh locations)');
        $this->command->info('   • 2 Delivery Requests');
        $this->command->info('   • 2 Active Deliveries (in_transit, completed)');
        $this->command->info('');
        $this->command->info('🚁 Delivery Status:');
        $this->command->info('   • DEL-' . date('Ymd') . '-001: In Transit (Drone Alpha)');
        $this->command->info('   • DEL-' . date('Ymd') . '-002: Completed (Drone Beta)');
    }
}
