<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Sign In');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertSee('Join Bombay Fashion');
    }

    public function test_new_customer_can_register(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Simran Kaur',
            'email' => 'simran@example.com',
            'phone' => '9876543210',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'simran@example.com',
            'name' => 'Simran Kaur',
        ]);
        $response->assertRedirect(route('home'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $userRole = Role::where('name', Role::USER)->first();
        $user = User::factory()->create([
            'role_id' => $userRole->id,
            'email' => 'test@bombayfashion.com',
            'password' => bcrypt('CorrectPassword123!'),
            'is_active' => true,
        ]);

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'WrongPassword',
        ]);

        $this->assertGuest();
    }

    public function test_suspended_user_cannot_login(): void
    {
        $userRole = Role::where('name', Role::USER)->first();
        $user = User::factory()->create([
            'role_id' => $userRole->id,
            'email' => 'suspended@bombayfashion.com',
            'password' => bcrypt('Password123!'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_logout(): void
    {
        $userRole = Role::where('name', Role::USER)->first();
        $user = User::factory()->create([
            'role_id' => $userRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }
}
