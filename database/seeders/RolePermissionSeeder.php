<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Dashboard permissions
            'view dashboard',
            
            // Device permissions
            'view devices',
            'create devices',
            'edit devices',
            'delete devices',
            
            // Alert permissions
            'view alerts',
            'resolve alerts',
            
            // User management permissions (Admin only)
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Report permissions
            'view reports',
            'generate reports',
            
            // Application settings permissions (Admin only)
            'view settings',
            'edit settings',
            
            // Profile permissions
            'view profile',
            'update profile',
            'update password',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Admin role and assign all permissions
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Create Petugas role and assign limited permissions
        $petugasRole = Role::create(['name' => 'Petugas']);
        $petugasRole->givePermissionTo([
            'view dashboard',
            'view devices', // View only
            'view alerts',
            'resolve alerts',
            'view reports',
            'view profile',
            'update profile',
            'update password',
        ]);

        // Create a default admin user
        $adminUser = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@sttwastukancana.ac.id',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('Admin');

        // Create a default petugas user
        $petugasUser = \App\Models\User::create([
            'name' => 'Petugas User',
            'email' => 'petugas@sttwastukancana.ac.id',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $petugasUser->assignRole('Petugas');
    }
}
