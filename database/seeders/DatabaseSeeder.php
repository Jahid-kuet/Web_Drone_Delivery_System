<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
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
        $hospital = \App\Models\Hospital::firstOrCreate(['email' => 'contact@cityhospital.com'], [
            'name' => 'City General Hospital',
            'code' => 'CGH001',
            'type' => 'general_hospital',
            'address' => '123 Medical Center Drive',
            'city' => 'New York',
            'state_province' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'latitude' => 40.7580,
            'longitude' => -73.9855,
            'primary_phone' => '+1 (555) 123-4567',
            'emergency_phone' => '+1 (555) 911-0000',
            'website' => 'https://cityhospital.com',
            'license_number' => 'LIC-NY-' . time(),
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

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📧 Default Users:');
        $this->command->info('   Admin: admin@drone.com / password123');
        $this->command->info('   Operator: operator@drone.com / password123');
        $this->command->info('   Hospital: hospital@drone.com / password123');
    }
}
