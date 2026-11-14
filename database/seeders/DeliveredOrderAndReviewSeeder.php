<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeliveredOrderAndReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customerRole = Role::firstOrCreate(['name' => 'user'], ['display_name' => 'Customer']);

        // 1. Create a verified patron account
        $customer = User::firstOrCreate(
            ['email' => 'customer@bombayfashion.com'],
            [
                'name' => 'Simranjit Kaur',
                'phone' => '+91 98141 23456',
                'password' => Hash::make('Customer@Bombay123!'),
                'role_id' => $customerRole->id,
                'is_active' => true,
            ]
        );

        $admin = User::where('email', 'admin@bombayfashion.com')->first();

        // 2. Find key products
        $shirt = Product::where('slug', 'midnight-navy-mandarin-collar-linen-shirt')->first();
        $saree = Product::where('slug', 'like', '%saree%')->first();
        $trousers = Product::where('slug', 'like', '%chino%')->orWhere('slug', 'like', '%trouser%')->first();

        // 3. Create a delivered order for Simranjit Kaur
        if ($shirt) {
            $order1 = Order::firstOrCreate(
                ['order_number' => 'BF-ORD-2026-DEL1'],
                [
                    'user_id' => $customer->id,
                    'customer_name' => 'Simranjit Kaur',
                    'customer_email' => 'customer@bombayfashion.com',
                    'customer_phone' => '+91 98141 23456',
                    'shipping_address' => [
                        'name' => 'Simranjit Kaur',
                        'phone' => '+91 98141 23456',
                        'address_line1' => 'House 42, Circular Road',
                        'address_line2' => 'Near Medical College',
                        'city' => 'Amritsar',
                        'state' => 'Punjab',
                        'pincode' => '143001',
                    ],
                    'subtotal' => 1599.00,
                    'discount_amount' => 0.00,
                    'tax_amount' => 79.95,
                    'shipping_charge' => 0.00,
                    'grand_total' => 1678.95,
                    'payment_method' => 'cod',
                    'payment_status' => 'paid',
                    'order_status' => 'delivered',
                    'delivered_at' => now()->subDays(3),
                    'created_at' => now()->subDays(5),
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order1->id, 'product_id' => $shirt->id],
                [
                    'product_name' => $shirt->name,
                    'product_sku' => $shirt->sku ?? 'BF-0006',
                    'variant_details' => 'Size: 40 | Color: Navy',
                    'unit_price' => 1599.00,
                    'quantity' => 1,
                    'total_price' => 1599.00,
                ]
            );

            // Seed verified review with details and photo
            $primaryImg = $shirt->primaryImage?->image_url ?? $shirt->images->first()?->image_url;
            Review::firstOrCreate(
                ['product_id' => $shirt->id, 'user_id' => $customer->id],
                [
                    'order_id' => $order1->id,
                    'rating' => 5,
                    'title' => 'Superior Linen Fabric & Sharp Collar Cut',
                    'comment' => 'Received this delivered to our home in Amritsar within 24 hours. The linen weave has a rich textured feel and does not irritate in warm weather. Stitching on the mandarin collar is flawless.',
                    'fit_feedback' => 'true_to_size',
                    'quality_rating' => 'exceptional',
                    'verified_purchase' => true,
                    'is_approved' => true,
                    'images' => $primaryImg ? [
                        ['image_url' => $primaryImg, 'public_id' => 'sample_shirt_rev1']
                    ] : null,
                    'created_at' => now()->subDays(2),
                ]
            );
        }

        // 4. Create delivered order for saree if exists
        if ($saree) {
            $order2 = Order::firstOrCreate(
                ['order_number' => 'BF-ORD-2026-DEL2'],
                [
                    'user_id' => $customer->id,
                    'customer_name' => 'Simranjit Kaur',
                    'customer_email' => 'customer@bombayfashion.com',
                    'customer_phone' => '+91 98141 23456',
                    'shipping_address' => [
                        'name' => 'Simranjit Kaur',
                        'phone' => '+91 98141 23456',
                        'address_line1' => 'House 42, Circular Road',
                        'city' => 'Amritsar',
                        'state' => 'Punjab',
                        'pincode' => '143001',
                    ],
                    'subtotal' => 4500.00,
                    'discount_amount' => 0.00,
                    'tax_amount' => 225.00,
                    'shipping_charge' => 0.00,
                    'grand_total' => 4725.00,
                    'payment_method' => 'razorpay',
                    'payment_status' => 'paid',
                    'order_status' => 'delivered',
                    'delivered_at' => now()->subDays(4),
                    'created_at' => now()->subDays(7),
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $order2->id, 'product_id' => $saree->id],
                [
                    'product_name' => $saree->name,
                    'product_sku' => $saree->sku ?? 'BF-0001',
                    'variant_details' => 'Color: Crimson Red',
                    'unit_price' => 4500.00,
                    'quantity' => 1,
                    'total_price' => 4500.00,
                ]
            );

            $sareeImg = $saree->primaryImage?->image_url ?? $saree->images->first()?->image_url;
            Review::firstOrCreate(
                ['product_id' => $saree->id, 'user_id' => $customer->id],
                [
                    'order_id' => $order2->id,
                    'rating' => 5,
                    'title' => 'Exquisite Traditional Zari Craftsmanship',
                    'comment' => 'Bought this Banarasi saree for a family wedding in Amritsar. The silk fall and gold zari pallu look royal and authentic. Truly authentic quality as promised by Bombay Fashion.',
                    'fit_feedback' => 'true_to_size',
                    'quality_rating' => 'exceptional',
                    'verified_purchase' => true,
                    'is_approved' => true,
                    'images' => $sareeImg ? [
                        ['image_url' => $sareeImg, 'public_id' => 'sample_saree_rev1']
                    ] : null,
                    'created_at' => now()->subDays(3),
                ]
            );
        }

        // 5. Also grant a delivered order to the Admin account for the Chino Trousers (or Shirt)
        // so Admin can immediately test writing/uploading photos
        if ($admin && ($trousers ?? $shirt)) {
            $testProd = $trousers ?? $shirt;
            $orderAdmin = Order::firstOrCreate(
                ['order_number' => 'BF-ORD-2026-ADM1'],
                [
                    'user_id' => $admin->id,
                    'customer_name' => 'Store Administrator',
                    'customer_email' => 'admin@bombayfashion.com',
                    'customer_phone' => '+91 98765 43210',
                    'shipping_address' => [
                        'name' => 'Store Administrator',
                        'phone' => '+91 98765 43210',
                        'address_line1' => 'Mahila College Road',
                        'city' => 'Amritsar',
                        'state' => 'Punjab',
                        'pincode' => '143001',
                    ],
                    'subtotal' => 1299.00,
                    'discount_amount' => 0.00,
                    'tax_amount' => 64.95,
                    'shipping_charge' => 0.00,
                    'grand_total' => 1363.95,
                    'payment_method' => 'cod',
                    'payment_status' => 'paid',
                    'order_status' => 'delivered',
                    'delivered_at' => now()->subDay(),
                    'created_at' => now()->subDays(3),
                ]
            );

            OrderItem::firstOrCreate(
                ['order_id' => $orderAdmin->id, 'product_id' => $testProd->id],
                [
                    'product_name' => $testProd->name,
                    'product_sku' => $testProd->sku ?? 'BF-0010',
                    'variant_details' => 'Size: 32',
                    'unit_price' => 1299.00,
                    'quantity' => 1,
                    'total_price' => 1299.00,
                ]
            );
        }
    }
}
