<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah user dengan email yang diinginkan sudah ada
        $adminUser = User::where('email', 'admin@wastukancana.ac.id')->first();
        
        if (!$adminUser) {
            // Buat user admin baru
            $adminUser = User::create([
                'name' => 'Admin User',
                'email' => 'admin@wastukancana.ac.id',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            
            echo "Admin user created successfully with email: admin@wastukancana.ac.id\n";
        } else {
            echo "Admin user with email admin@wastukancana.ac.id already exists\n";
        }
        
        // Tetapkan role Admin (baik user baru maupun yang sudah ada)
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole && $adminUser) {
            // Hapus semua role yang ada dulu
            $adminUser->syncRoles([]);
            // Tambahkan role Admin
            $adminUser->assignRole($adminRole);
            echo "Admin role assigned to user with email: admin@wastukancana.ac.id\n";
        } else {
            echo "Admin role could not be assigned\n";
        }
    }
}