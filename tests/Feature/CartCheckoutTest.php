<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\SettingSeeder::class);

        $userRole = Role::where('name', Role::USER)->first();
        $this->customer = User::factory()->create([
            'role_id' => $userRole->id,
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Sarees',
            'slug' => 'sarees',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Chanderi Silk Saree',
            'slug' => 'chanderi-silk-saree',
            'sku' => 'BF-SAR-001',
            'price' => 2500.00,
            'discount_price' => 2200.00,
            'season' => 'all_season',
            'stock' => 15,
            'is_active' => true,
        ]);

        // Basic settings
        Setting::set('store_name', 'Bombay Fashion', 'text', 'store');
        Setting::set('cod_enabled', true, 'boolean', 'payment');
        Setting::set('cod_max_limit', 10000.0, 'number', 'payment');
        Setting::set('tax_rate', 5.0, 'number', 'tax');
        Setting::set('shipping_charge', 50.0, 'number', 'shipping');
        Setting::set('free_shipping_threshold', 999.0, 'number', 'shipping');
    }

    public function test_can_add_product_to_cart(): void
    {
        $response = $this->from(route('cart.index'))
            ->actingAs($this->customer)
            ->post(route('cart.add'), [
                'product_id' => $this->product->id,
                'quantity' => 2,
            ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    public function test_can_view_cart_page(): void
    {
        $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->get(route('cart.index'));
        $response->assertStatus(200);
        $response->assertSee('Chanderi Silk Saree');
        $response->assertSee('₹2,200.00');
    }

    public function test_can_place_cash_on_delivery_order(): void
    {
        // 1. Add to cart
        $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $initialStock = $this->product->fresh()->stock;

        // 2. Process checkout with Cash on Delivery
        $response = $this->actingAs($this->customer)->post(route('checkout.process'), [
            'name' => 'Manpreet Singh',
            'phone' => '9812345678',
            'address_line1' => 'Opposite Khalsa College, GT Road',
            'city' => 'Amritsar',
            'state' => 'Punjab',
            'pincode' => '143002',
            'payment_method' => 'cod',
            'customer_notes' => 'Please deliver between 10am to 2pm',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Manpreet Singh',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'confirmed',
        ]);

        // 3. Verify stock was decremented by 2
        $this->assertEquals($initialStock - 2, $this->product->fresh()->stock);

        // 4. Verify Stock audit log was recorded
        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $this->product->id,
            'quantity_change' => -2,
        ]);
    }

    public function test_customer_can_download_invoice(): void
    {
        // Add item and place order
        $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($this->customer)->post(route('checkout.process'), [
            'name' => 'Manpreet Singh',
            'phone' => '9812345678',
            'address_line1' => 'GT Road',
            'city' => 'Amritsar',
            'state' => 'Punjab',
            'pincode' => '143002',
            'payment_method' => 'cod',
        ]);

        $order = \App\Models\Order::first();
        $this->assertNotNull($order);

        $response = $this->actingAs($this->customer)->get(route('orders.invoice', $order->order_number));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_customer_can_view_order_details(): void
    {
        $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($this->customer)->post(route('checkout.process'), [
            'name' => 'Manpreet Singh',
            'phone' => '9812345678',
            'address_line1' => 'GT Road',
            'city' => 'Amritsar',
            'state' => 'Punjab',
            'pincode' => '143002',
            'payment_method' => 'cod',
        ]);

        $order = \App\Models\Order::first();
        $this->assertNotNull($order);

        $response = $this->actingAs($this->customer)->get(route('orders.show', $order->order_number));
        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }
}
