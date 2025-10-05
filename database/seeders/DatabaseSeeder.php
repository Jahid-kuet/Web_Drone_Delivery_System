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

        // Create Hospital Admin
        $hospitalAdmin = User::firstOrCreate(['email' => 'hospital@drone.com'], [
            'name' => 'Hospital Administrator',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $hospitalAdmin->roles()->sync([$hospitalAdminRole->id]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📧 Default Users:');
        $this->command->info('   Admin: admin@drone.com / password123');
        $this->command->info('   Operator: operator@drone.com / password123');
        $this->command->info('   Hospital: hospital@drone.com / password123');
    }
}
