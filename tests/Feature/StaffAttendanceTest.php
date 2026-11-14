<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\SettingSeeder::class);

        $adminRole = Role::where('name', Role::ADMIN)->first();
        $staffRole = Role::where('name', Role::STAFF)->first();

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'name' => 'Admin User',
            'email' => 'admin@bombayfashion.com',
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'role_id' => $staffRole->id,
            'name' => 'Simranjit Kaur',
            'email' => 'simran.staff@bombayfashion.com',
            'phone' => '9814123456',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_attendance_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.attendance.index'));
        $response->assertStatus(200);
        $response->assertSee('Showroom Staff Attendance');
    }

    public function test_admin_can_open_attendance_kiosk()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.attendance.kiosk'));
        $response->assertStatus(200);
        $response->assertSee('20-Second Dynamic Rolling QR');
    }

    public function test_live_token_api_returns_dynamic_qr_payload()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.attendance.live-token'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'token',
            'qr_payload',
            'qr_image_url',
            'seconds_remaining',
            'window',
        ]);
    }

    public function test_staff_can_punch_attendance_with_valid_dynamic_qr_token()
    {
        $tokenResponse = $this->actingAs($this->admin)->get(route('admin.attendance.live-token'));
        $token = $tokenResponse->json('token');

        // Staff scans QR while authenticated
        $response = $this->actingAs($this->staff)->get(route('attendance.scan', ['token' => $token]));
        $response->assertStatus(200);
        $response->assertSee('Attendance Logged');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->staff->id,
        ]);

        $attendance = Attendance::where('user_id', $this->staff->id)->first();
        $this->assertNotNull($attendance);
        $this->assertTrue(in_array($attendance->status, ['present', 'late']));
        $this->assertEquals(date('Y-m-d'), $attendance->date->format('Y-m-d'));
        $this->assertNotNull($attendance->punch_in_at);
        $this->assertNotNull($attendance->device_id);
    }

    public function test_one_mobile_one_staff_policy_blocks_proxy_punching()
    {
        $staffRole = Role::where('name', Role::STAFF)->first();
        $secondStaff = User::factory()->create([
            'role_id' => $staffRole->id,
            'name' => 'Harpreet Singh',
            'email' => 'harpreet.staff@bombayfashion.com',
            'phone' => '9876543210',
            'is_active' => true,
        ]);

        $tokenResponse = $this->actingAs($this->admin)->get(route('admin.attendance.live-token'));
        $token = $tokenResponse->json('token');

        $sharedDeviceId = 'mobile_device_iphone_punjab_01';

        // Staff 1 punches on this mobile device
        $res1 = $this->actingAs($this->staff)
            ->withCookie('bf_attendance_device_token', $sharedDeviceId)
            ->get(route('attendance.scan', ['token' => $token, 'client_device_id' => $sharedDeviceId]));
        $res1->assertStatus(200);
        $res1->assertSee('Attendance Logged');

        $att1 = Attendance::where('user_id', $this->staff->id)->first();
        $this->assertEquals($sharedDeviceId, $att1->device_id);

        // Staff 2 tries to punch using the SAME mobile device
        $res2 = $this->actingAs($secondStaff)
            ->withCookie('bf_attendance_device_token', $sharedDeviceId)
            ->get(route('attendance.scan', ['token' => $token, 'client_device_id' => $sharedDeviceId]));

        $res2->assertStatus(200);
        $res2->assertSee('Device Restriction');
        $res2->assertSee('This mobile device has already recorded attendance today for another staff member');
        $res2->assertSee($this->staff->name);

        // Verify Staff 2 attendance was NOT created
        $this->assertDatabaseMissing('attendances', [
            'user_id' => $secondStaff->id,
        ]);
    }
}
