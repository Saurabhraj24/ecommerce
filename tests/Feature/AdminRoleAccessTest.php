<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $staffUser;
    protected User $customerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\SettingSeeder::class);

        $adminRole = Role::where('name', Role::ADMIN)->first();
        $staffRole = Role::where('name', Role::STAFF)->first();
        $userRole = Role::where('name', Role::USER)->first();

        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'email' => 'admin@bombayfashion.com',
            'is_active' => true,
        ]);

        $this->staffUser = User::factory()->create([
            'role_id' => $staffRole->id,
            'email' => 'staff@bombayfashion.com',
            'is_active' => true,
        ]);

        $this->customerUser = User::factory()->create([
            'role_id' => $userRole->id,
            'email' => 'customer@bombayfashion.com',
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_admin_panel(): void
    {
        $response = $this->actingAs($this->customerUser)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_staff_can_access_dashboard_and_products(): void
    {
        $response = $this->actingAs($this->staffUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->staffUser)->get(route('admin.products.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->staffUser)->get(route('admin.orders.index'));
        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_settings_staff_or_reports(): void
    {
        // Staff cannot view store settings
        $response = $this->actingAs($this->staffUser)->get(route('admin.settings.index'));
        $response->assertStatus(403);

        // Staff cannot view staff management
        $response = $this->actingAs($this->staffUser)->get(route('admin.staff.index'));
        $response->assertStatus(403);

        // Staff cannot view revenue reports
        $response = $this->actingAs($this->staffUser)->get(route('admin.reports.index'));
        $response->assertStatus(403);
    }

    public function test_admin_has_full_access(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->adminUser)->get(route('admin.settings.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->adminUser)->get(route('admin.staff.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reports.index'));
        $response->assertStatus(200);
    }
}
