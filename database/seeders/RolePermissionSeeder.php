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
            'manage users', // General permission to access user management
            
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

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create or get Admin role and assign all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // Create or get Petugas role and assign limited permissions
        $petugasRole = Role::firstOrCreate(['name' => 'Petugas', 'guard_name' => 'web']);
        $petugasPermissions = [
            'view dashboard',
            'view devices', // View only
            'view alerts',
            'resolve alerts',
            'view reports',
            'view profile',
            'update profile',
            'update password',
        ];
        $petugasRole->syncPermissions($petugasPermissions);

        // Create a default admin user (only if not exists)
        $adminUser = \App\Models\User::firstOrCreate([
            'email' => 'admin@sttwastukancana.ac.id',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
        }

        // Create a default petugas user (only if not exists)
        $petugasUser = \App\Models\User::firstOrCreate([
            'email' => 'petugas@sttwastukancana.ac.id',
        ], [
            'name' => 'Petugas User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        if (!$petugasUser->hasRole('Petugas')) {
            $petugasUser->assignRole('Petugas');
        }
    }
}
