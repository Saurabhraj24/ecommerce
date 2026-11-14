<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database strictly according to requirements:
     * - ONLY Roles, 1 Admin account, base Categories, Store Settings, and Policies
     * - NO fake products, users, reviews, or counters.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            SettingSeeder::class,
            PageSeeder::class,
            BannerSeeder::class,
            ProductSeeder::class,
            DeliveredOrderAndReviewSeeder::class,
        ]);
    }
}
