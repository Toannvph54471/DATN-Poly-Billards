<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Services\PayrollService;
use Carbon\Carbon;

class PayrollCalculationTest extends TestCase
{
    protected $employee;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create Admin
        $this->admin = User::first() ?? User::factory()->create(['name' => 'Admin Test', 'email' => 'admin_test_'.time().'@example.com', 'role' => 'admin']);
        
        // Create Employee
        $this->employee = Employee::create([
            'name' => 'Payroll Test Employee ' . time(),
            'code' => 'PTE' . time(),
            'email' => 'pte' . time() . '@example.com',
            'phone' => '0999999999',
            'hourly_rate' => 50000
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->employee) {
            Attendance::where('employee_id', $this->employee->id)->delete();
            Payroll::where('employee_id', $this->employee->id)->delete();
            $this->employee->delete();
        }
        // Admin cleanup skipped if reusing existing
        parent::tearDown();
    }

    public function test_late_fee_calculation()
    {
        // 1. Arrange: Create 2 Late attendances (unapproved) and 1 On-time
        $month = now()->format('Y-m');
        
        // Late 1
        Attendance::create([
            'employee_id' => $this->employee->id,
            'check_in' => now()->startOfMonth()->addDays(1)->setTime(8, 30), // Late
            'check_out' => now()->startOfMonth()->addDays(1)->setTime(17, 30),
            'total_minutes' => 540, // 9 hours
            'status' => 'Late',
            'approval_status' => 'pending' // Not approved
        ]);

        // Late 2
        Attendance::create([
            'employee_id' => $this->employee->id,
            'check_in' => now()->startOfMonth()->addDays(2)->setTime(9, 00), // Late
            'check_out' => now()->startOfMonth()->addDays(2)->setTime(18, 00),
            'total_minutes' => 540,
            'status' => 'Late',
            'approval_status' => 'rejected' // Rejected -> Should NOT count for pay or late fee? Service logic says where('approval_status', '!=', 'rejected'). So this attendance is IGNORED completely.
        ]);
        
        // Late 3 (Approved)
        Attendance::create([
            'employee_id' => $this->employee->id,
            'check_in' => now()->startOfMonth()->addDays(3)->setTime(8, 30),
            'check_out' => now()->startOfMonth()->addDays(3)->setTime(17, 30),
            'total_minutes' => 540,
            'status' => 'Late',
            'approval_status' => 'approved' // Approved -> Should NOT charge late fee.
        ]);

        // On Time
        Attendance::create([
            'employee_id' => $this->employee->id,
            'check_in' => now()->startOfMonth()->addDays(4)->setTime(7, 50),
            'check_out' => now()->startOfMonth()->addDays(4)->setTime(17, 00),
            'total_minutes' => 550,
            'status' => 'Present',
            'approval_status' => 'none'
        ]);
        
        // Expected:
        // Late Count: 1 (The first pending one. Reject is ignored. Approved is waived).
        // Late Fee: 20,000 * 1 = 20,000.
        // Total Hours: 9 (Late 1) + 9 (Late 3) + 9.16 (On Time) = 27.16 hours. (Rejected is ignored).

        // 2. Act: Calculate Salary via Service
        $service = new PayrollService();
        $payroll = $service->createPayroll($this->employee->id, $month);

        // 3. Assert
        $this->assertEquals(1, $payroll->late_count, 'Late count should be 1');
        $this->assertEquals(20000, $payroll->late_penalty, 'Late penalty should be 20,000');
        
        // Verify Total Hours roughly
        $this->assertTrue($payroll->total_hours > 20);
    }

    public function test_admin_can_update_deductions_bonus()
    {
        $month = now()->format('Y-m');

        // Create initial payroll
        $service = new PayrollService();
        $payroll = $service->createPayroll($this->employee->id, $month);
        
        $initialAmount = $payroll->final_amount;

        // Act: Update via Controller (Simulation)
        $response = $this->actingAs($this->admin)
            ->postJson("/api/payroll/generate", [ // Or recalculate route
                'employee_id' => $this->employee->id,
                'month' => $month,
                'bonus' => 100000,
                'deductions' => 50000, // Manually input fine
                'notes' => 'Test Adjustment'
            ]);

        $response->assertStatus(200);
        
        $payroll->refresh();
        
        $this->assertEquals(100000, $payroll->bonus);
        $this->assertEquals(50000, $payroll->deductions); // Check if mapped correctly
        
        // Check Final Calculation
        // Final = Base + Bonus (100k) - Deductions (50k) - LatePenalty (0)
        // Delta = +50k
        $this->assertEquals($initialAmount + 50000, $payroll->final_amount);
    }
}
