<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
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

        $category = Category::create([
            'name' => 'Shirts',
            'slug' => 'shirts',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Linen Shirt',
            'slug' => 'linen-shirt',
            'sku' => 'BF-SHIRT-01',
            'price' => 1500.00,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_submit_review(): void
    {
        $res = $this->post(route('reviews.store', $this->product->id), [
            'rating' => 5,
            'comment' => 'Excellent piece of clothing',
        ]);

        $res->assertRedirect(route('login'));
    }

    public function test_user_without_delivered_order_cannot_review(): void
    {
        $res = $this->actingAs($this->customer)->post(route('reviews.store', $this->product->id), [
            'rating' => 5,
            'comment' => 'I have not bought it yet',
        ]);

        $res->assertSessionHas('error');
    }

    public function test_user_with_delivered_order_can_submit_review_with_details_and_photos(): void
    {
        Storage::fake('public');

        // Create delivered order
        $order = Order::create([
            'order_number' => 'TEST-ORD-' . uniqid(),
            'user_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_phone' => '9876543210',
            'shipping_address' => ['address' => 'Amritsar'],
            'subtotal' => 1500,
            'grand_total' => 1500,
            'order_status' => 'delivered',
            'payment_status' => 'paid',
            'delivered_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => 'SKU-TEST',
            'unit_price' => 1500,
            'quantity' => 1,
            'total_price' => 1500,
        ]);

        $photo = UploadedFile::fake()->image('customer_fit.jpg', 600, 600);

        $res = $this->actingAs($this->customer)->post(route('reviews.store', $this->product->id), [
            'rating' => 5,
            'title' => 'Fantastic fitting and fabric',
            'comment' => 'The fabric feels pure and stitching is top class. Fits true to size!',
            'fit_feedback' => 'true_to_size',
            'quality_rating' => 'exceptional',
            'images' => [$photo],
        ]);

        $res->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'rating' => 5,
            'title' => 'Fantastic fitting and fabric',
            'fit_feedback' => 'true_to_size',
            'quality_rating' => 'exceptional',
            'verified_purchase' => true,
        ]);

        // Verify reviewing a second time updates the existing review (only 1 review per product)
        $resUpdate = $this->actingAs($this->customer)->post(route('reviews.store', $this->product->id), [
            'rating' => 4,
            'title' => 'Updated review title',
            'comment' => 'Still great after 3 washes.',
            'fit_feedback' => 'true_to_size',
            'quality_rating' => 'good',
        ]);

        $resUpdate->assertSessionHas('success');
        $this->assertEquals(1, Review::where('user_id', $this->customer->id)->where('product_id', $this->product->id)->count());
    }
}
