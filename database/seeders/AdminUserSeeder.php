<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', Role::ADMIN)->firstOrFail();

        $adminEmail = env('ADMIN_EMAIL', 'admin@bombayfashion.com');
        $adminName = env('ADMIN_NAME', 'Owner - Bombay Fashion');
        $adminPassword = env('ADMIN_PASSWORD', 'Admin@Bombay123!');

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'role_id' => $adminRole->id,
                'name' => $adminName,
                'phone' => '+919876543210',
                'password' => Hash::make($adminPassword),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
