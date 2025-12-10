<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class HourlyPayrollTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed Roles
        if (\App\Models\Role::count() == 0) {
            \App\Models\Role::forceCreate(['id' => 1, 'name' => 'admin', 'slug' => 'admin']);
            \App\Models\Role::forceCreate(['id' => 4, 'name' => 'employee', 'slug' => 'employee']);
        }
        
        // Create Admin User
        $this->admin = User::factory()->create(['role_id' => 1]); // Assuming 1 is Admin
        
        // Create Employee User
        $user = User::factory()->create();

        $this->employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'name' => 'Test Employee',
            'email' => 'test@example.com',
            'phone' => '0123456789',
            'salary_type' => 'hourly',
            'hourly_rate' => 25000,
            'start_date' => now(),
            'status' => 'Active'
        ]);
    }

    protected function assignShift($date, $startTime = '08:00:00')
    {
        $shift = \App\Models\Shift::create([
            'name' => 'Morning Shift',
            'start_time' => $startTime,
            'end_time' => Carbon::parse($startTime)->addHours(9)->format('H:i:s'),
            'wage' => 0 // Not used anymore
        ]);

        \App\Models\EmployeeShift::create([
            'employee_id' => $this->employee->id,
            'shift_id' => $shift->id,
            'shift_date' => $date,
            'status' => 'scheduled'
        ]);
    }

    public function test_check_in_on_time()
    {
        // Mock time: 07:55
        $date = '2025-11-01';
        Carbon::setTestNow(Carbon::parse("$date 07:55:00"));
        
        $this->assignShift($date, '08:00:00');
        
        $token = $this->employee->generateQrToken();

        $response = $this->postJson('/api/attendance/check-in', [
            'qr_token' => $token
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('attendance', [
            'employee_id' => $this->employee->id,
            'status' => 'Present',
            'late_minutes' => 0
        ]);
    }

    public function test_check_in_late_requires_reason()
    {
        // Mock time: 08:20 (Late > 15 mins)
        $date = '2025-11-01';
        Carbon::setTestNow(Carbon::parse("$date 08:20:00"));
        
        $this->assignShift($date, '08:00:00');
        
        $token = $this->employee->generateQrToken();

        $response = $this->postJson('/api/attendance/check-in', [
            'qr_token' => $token
        ]);

        $response->assertStatus(403)
            ->assertJson(['status' => 'LATE_REASON_REQUIRED']);
    }

    public function test_submit_late_reason()
    {
        // Mock time: 08:20
        $date = '2025-11-01';
        Carbon::setTestNow(Carbon::parse("$date 08:20:00"));
        
        $this->assignShift($date, '08:00:00');
        
        $token = $this->employee->generateQrToken();

        $response = $this->postJson('/api/attendance/submit-late-reason', [
            'qr_token' => $token,
            'reason' => 'Traffic jam'
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('attendance', [
            'employee_id' => $this->employee->id,
            'status' => 'Late',
            'late_minutes' => 20,
            'late_reason' => 'Traffic jam',
            'approval_status' => 'pending'
        ]);
    }

    public function test_check_out_calculates_hours()
    {
        // 1. Check In at 08:00
        $date = '2025-11-01';
        Carbon::setTestNow(Carbon::parse("$date 08:00:00"));
        
        $this->assignShift($date, '08:00:00');

        $attendance = Attendance::create([
            'employee_id' => $this->employee->id,
            'check_in' => now(),
            'status' => 'Present'
        ]);

        // 2. Check Out at 17:00 (9 hours = 540 minutes)
        Carbon::setTestNow(Carbon::parse("$date 17:00:00"));
        
        $token = $this->employee->generateQrToken();

        $response = $this->postJson('/api/attendance/check-out', [
            'qr_token' => $token
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('attendance', [
            'id' => $attendance->id,
            'total_minutes' => 540
        ]);
    }

    public function test_payroll_calculation()
    {
        // Create 2 attendance records for Nov 2025
        // Day 1: 8 hours (480 mins)
        Attendance::create([
            'employee_id' => $this->employee->id,
            'check_in' => Carbon::parse('2025-11-01 08:00:00'),
            'check_out' => Carbon::parse('2025-11-01 16:00:00'),
            'total_minutes' => 480,
            'status' => 'Present',
            'approval_status' => 'none'
        ]);

        // Day 2: 4 hours (240 mins)
        Attendance::create([
            'employee_id' => $this->employee->id,
            'check_in' => Carbon::parse('2025-11-02 08:00:00'),
            'check_out' => Carbon::parse('2025-11-02 12:00:00'),
            'total_minutes' => 240,
            'status' => 'Present',
            'approval_status' => 'none'
        ]);

        // Total: 12 hours * 25,000 = 300,000
        
        $response = $this->actingAs($this->admin)->postJson('/api/payroll/generate', [
            'employee_id' => $this->employee->id,
            'month' => '2025-11',
            'bonus' => 50000,
            'penalty' => 0
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payrolls', [
            'employee_id' => $this->employee->id,
            'period' => '2025-11',
            'total_hours' => 12,
            'hourly_rate' => 25000,
            'base_salary' => 300000,
            'bonus' => 50000,
            'final_amount' => 350000
        ]);
    }
}
