<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Role;
use App\Models\StaffAdvance;
use App\Models\StaffLeave;
use App\Models\StaffSalary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffHrAndPayrollTest extends TestCase
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
            'email' => 'admin@bombayfashion.com',
            'is_active' => true,
            'salary' => 50000,
        ]);

        $this->staff = User::factory()->create([
            'role_id' => $staffRole->id,
            'name' => 'Simran Kaur',
            'email' => 'simran@bombayfashion.com',
            'is_active' => true,
            'salary' => 25000,
            'designation' => 'Senior Bridal Consultant',
            'bank_name' => 'HDFC Bank Amritsar',
            'bank_account_no' => '50100987654321',
            'bank_ifsc' => 'HDFC0001234',
            'upi_id' => 'simran@okhdfcbank',
            'weekly_off' => 'Tuesday',
            'shift_timings' => '10:00 AM - 08:30 PM',
        ]);
    }

    public function test_admin_can_access_salary_register_and_generate_payroll(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.staff.salaries.index', ['month' => now()->format('Y-m')]));
        $response->assertStatus(200);
        $response->assertSee('Staff Salary &amp; Payroll Register', false);
        $response->assertSee('Simran Kaur');

        // Generate payroll
        $generateResponse = $this->actingAs($this->admin)->post(route('admin.staff.salaries.generate'), [
            'month' => now()->format('Y-m'),
            'user_id' => $this->staff->id,
            'bonus_incentive' => 2000,
        ]);

        $generateResponse->assertRedirect();
        $this->assertDatabaseHas('staff_salaries', [
            'user_id' => $this->staff->id,
            'month' => now()->format('Y-m'),
            'bonus_incentive' => 2000,
            'payment_status' => 'pending',
        ]);
    }

    public function test_admin_can_record_salary_payment_and_view_payslip(): void
    {
        $salary = StaffSalary::create([
            'user_id' => $this->staff->id,
            'month' => now()->format('Y-m'),
            'base_salary' => 25000,
            'working_days' => 30,
            'days_present' => 28,
            'days_absent' => 0,
            'days_half' => 0,
            'paid_leaves' => 2,
            'bonus_incentive' => 1000,
            'deductions' => 0,
            'net_salary' => 26000,
            'payment_status' => 'pending',
        ]);

        // Record payment
        $payResponse = $this->actingAs($this->admin)->patch(route('admin.staff.salaries.payment', $salary->id), [
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'UTR9817293812',
            'payment_date' => now()->toDateString(),
            'notes' => 'NEFT transfer cleared',
        ]);

        $payResponse->assertRedirect();
        $salary->refresh();
        $this->assertEquals('paid', $salary->payment_status);
        $this->assertEquals('bank_transfer', $salary->payment_method);
        $this->assertEquals('UTR9817293812', $salary->payment_reference);

        // View official printable payslip
        $slipResponse = $this->actingAs($this->admin)->get(route('admin.staff.salaries.payslip', $salary->id));
        $slipResponse->assertStatus(200);
        $slipResponse->assertSee('BOMBAY FASHION');
        $slipResponse->assertSee('Salary Slip');
        $slipResponse->assertSee('Simran Kaur');
        $slipResponse->assertSee('₹26,000.00');
    }

    public function test_admin_can_apply_and_approve_staff_leave_and_syncs_attendance(): void
    {
        $leaveResponse = $this->actingAs($this->admin)->get(route('admin.staff.leaves.index'));
        $leaveResponse->assertStatus(200);
        $leaveResponse->assertSee('Staff Vacation &amp; Leaves', false);

        // Apply leave
        $startDate = now()->toDateString();
        $endDate = now()->addDays(2)->toDateString();

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.staff.leaves.store'), [
            'user_id' => $this->staff->id,
            'leave_type' => 'festival',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => 'Diwali celebrations with family in Ludhiana',
            'status' => 'approved',
            'admin_notes' => 'Approved by Store Owner',
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('staff_leaves', [
            'user_id' => $this->staff->id,
            'leave_type' => 'festival',
            'days_count' => 3,
            'status' => 'approved',
        ]);

        // Verify attendance records automatically synced
        $attendance = Attendance::where('user_id', $this->staff->id)->first();
        $this->assertNotNull($attendance);
        $this->assertEquals('leave', $attendance->status);
        $this->assertEquals('admin_leave_approval', $attendance->verification_method);
    }

    public function test_admin_can_disburse_advance_and_settle(): void
    {
        $advResponse = $this->actingAs($this->admin)->get(route('admin.staff.advances.index'));
        $advResponse->assertStatus(200);
        $advResponse->assertSee('Staff Salary Advance (पेशगी खाता)', false);

        // Disburse advance
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.staff.advances.store'), [
            'user_id' => $this->staff->id,
            'amount' => 5000,
            'advance_date' => now()->toDateString(),
            'reason' => 'Emergency medical expenses for mother',
            'notes' => 'Cash issued from drawer',
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('staff_advances', [
            'user_id' => $this->staff->id,
            'amount' => 5000,
            'status' => 'pending',
        ]);

        $advance = StaffAdvance::where('user_id', $this->staff->id)->first();

        // Settle advance
        $settleResponse = $this->actingAs($this->admin)->patch(route('admin.staff.advances.settle', $advance->id));
        $settleResponse->assertRedirect();
        $advance->refresh();
        $this->assertEquals('cash_repaid', $advance->status);
    }

    public function test_staff_role_cannot_access_staff_salaries(): void
    {
        $response = $this->actingAs($this->staff)->get(route('admin.staff.salaries.index'));
        $response->assertStatus(403);
    }
}
