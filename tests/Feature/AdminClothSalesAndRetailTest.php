<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminClothSalesAndRetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'display_name' => 'Administrator']);
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'display_name' => 'Staff Member']);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'role_id' => $staffRole->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_cloth_sales_breakdown(): void
    {
        $category = Category::create([
            'name' => 'Bridal Sarees',
            'slug' => 'bridal-sarees',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pure Banarasi Katan Silk Saree',
            'slug' => 'pure-banarasi-katan-silk-saree',
            'sku' => 'BF-SAR-001',
            'price' => 12500,
            'discount_price' => 9999,
            'stock' => 15,
            'fabric' => 'Pure Silk',
            'season' => 'all_season',
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'BF-ORD-TEST-001',
            'customer_name' => 'Harpreet Kaur',
            'customer_email' => 'harpreet@example.com',
            'customer_phone' => '9876543210',
            'shipping_address' => ['address' => 'Amritsar'],
            'subtotal' => 9999,
            'grand_total' => 10499,
            'tax_amount' => 500,
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'payment_method' => 'razorpay',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'variant_details' => 'Color: Maroon, Size: Free Size',
            'unit_price' => 9999,
            'quantity' => 2,
            'total_price' => 19998,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.cloth-sales'));

        $response->assertStatus(200);
        $response->assertSee('Cloth &amp; Garment Sales Breakdown', false);
        $response->assertSee('Pure Banarasi Katan Silk Saree');
        $response->assertSee('2 pcs');
    }

    public function test_admin_can_export_cloth_sales_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export-cloth-sales'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_access_category_and_fabric_analytics(): void
    {
        $catResponse = $this->actingAs($this->admin)->get(route('admin.reports.categories'));
        $catResponse->assertStatus(200);
        $catResponse->assertSee('Category Sales Breakdown');

        $fabResponse = $this->actingAs($this->admin)->get(route('admin.reports.fabrics'));
        $fabResponse->assertStatus(200);
        $fabResponse->assertSee('Fabric Sales');
    }

    public function test_admin_can_access_inventory_reorder_and_dead_stock(): void
    {
        $reorderResponse = $this->actingAs($this->admin)->get(route('admin.inventory.reorder'));
        $reorderResponse->assertStatus(200);
        $reorderResponse->assertSee('Low Stock');

        $deadResponse = $this->actingAs($this->admin)->get(route('admin.inventory.dead-stock'));
        $deadResponse->assertStatus(200);
        $deadResponse->assertSee('Dead Stock');
    }

    public function test_admin_and_staff_can_access_pos_terminal(): void
    {
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.pos.index'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Walk-in POS Billing Counter');

        $staffResponse = $this->actingAs($this->staff)->get(route('admin.pos.index'));
        $staffResponse->assertStatus(200);
        $staffResponse->assertSee('Walk-in POS Billing Counter');

        $historyResponse = $this->actingAs($this->admin)->get(route('admin.pos.history'));
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee('Showroom Counter Sales History');
    }

    public function test_admin_can_access_wishlist_and_abandoned_carts_and_tax(): void
    {
        $wishResponse = $this->actingAs($this->admin)->get(route('admin.reports.wishlist'));
        $wishResponse->assertStatus(200);
        $wishResponse->assertSee('Customer Wishlist Demand Insights');

        $cartResponse = $this->actingAs($this->admin)->get(route('admin.reports.abandoned-carts'));
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee('Abandoned Cart Recovery');

        $taxResponse = $this->actingAs($this->admin)->get(route('admin.reports.tax'));
        $taxResponse->assertStatus(200);
        $taxResponse->assertSee('GST & Invoicing Tax Report');

        $custResponse = $this->actingAs($this->admin)->get(route('admin.reports.customers'));
        $custResponse->assertStatus(200);
        $custResponse->assertSee('Top Customers & Spending Insights');
    }

    public function test_pos_checkout_creates_order_and_updates_stock_logs(): void
    {
        $category = Category::create([
            'name' => 'Punjabi Suits',
            'slug' => 'punjabi-suits',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Designer Chanderi Silk Suit',
            'slug' => 'designer-chanderi-silk-suit',
            'sku' => 'BF-SUIT-001',
            'price' => 4500,
            'stock' => 20,
            'fabric' => 'Chanderi Silk',
            'season' => 'all_season',
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'size' => '40',
            'color' => 'Peach',
            'sku' => 'BF-SUIT-001-40-PCH',
            'additional_price' => 0,
            'stock' => 10,
            'is_active' => true,
        ]);

        $checkoutData = [
            'customer_name' => 'Simran Kaur',
            'customer_phone' => '9812345678',
            'payment_method' => 'cash',
            'discount_amount' => 100,
            'cash_tendered' => 5000,
            'items' => [
                [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->staff)->postJson(route('admin.pos.checkout'), $checkoutData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Simran Kaur',
            'customer_phone' => '9812345678',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'type' => 'order_placed',
            'quantity_change' => -1,
        ]);

        $variant->refresh();
        $this->assertEquals(9, $variant->stock);
    }

    public function test_admin_and_staff_can_access_operations_hub(): void
    {
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.hub'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Store Operations Hub');
        $adminResponse->assertSee('Showroom POS');
        $adminResponse->assertSee('Kitna &amp; Kon Sa Cloth Bikha', false);

        $staffResponse = $this->actingAs($this->staff)->get(route('admin.hub'));
        $staffResponse->assertStatus(200);
        $staffResponse->assertSee('Store Operations Hub');
    }
}



