<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => Role::ADMIN,
                'display_name' => 'Store Owner / Administrator',
                'description' => 'Full administrative access to manage all aspects of Bombay Fashion store.',
            ],
            [
                'name' => Role::STAFF,
                'display_name' => 'Store Staff',
                'description' => 'Access to products, stock updates, orders fulfillment, and customer returns.',
            ],
            [
                'name' => Role::USER,
                'display_name' => 'Customer',
                'description' => 'Standard customer account for browsing, wishlist, cart, and placing orders.',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
