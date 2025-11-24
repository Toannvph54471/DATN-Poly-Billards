<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_monthly_salary_with_multiplier()
    {
        // Create user and employee
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'name' => 'Test Employee',
            'phone' => '0123456789',
            'salary_rate' => 100000, // 100k/hour
            'status' => 'Active'
        ]);

        // Create shifts
        $normalShift = Shift::create([
            'name' => 'Normal Shift',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'salary_multiplier' => 1.0
        ]);

        $nightShift = Shift::create([
            'name' => 'Night Shift',
            'start_time' => '22:00:00',
            'end_time' => '06:00:00',
            'salary_multiplier' => 1.5
        ]);

        // Assign shifts to employee
        // Normal shift: 8 hours * 100k * 1.0 = 800k
        EmployeeShift::create([
            'employee_id' => $employee->id,
            'shift_id' => $normalShift->id,
            'shift_date' => Carbon::now()->format('Y-m-d'),
            'actual_start_time' => Carbon::now()->setTime(8, 0),
            'actual_end_time' => Carbon::now()->setTime(16, 0),
            'status' => 'Completed',
            'total_hours' => 8
        ]);

        // Night shift: 8 hours * 100k * 1.5 = 1,200k
        EmployeeShift::create([
            'employee_id' => $employee->id,
            'shift_id' => $nightShift->id,
            'shift_date' => Carbon::now()->addDay()->format('Y-m-d'),
            'actual_start_time' => Carbon::now()->addDay()->setTime(22, 0),
            'actual_end_time' => Carbon::now()->addDay()->addHours(8),
            'status' => 'Completed',
            'total_hours' => 8
        ]);

        // Calculate payroll
        $service = new PayrollService();
        $result = $service->calculateMonthlySalary($employee->id, Carbon::now()->format('Y-m'));

        // Total should be 800k + 1200k = 2,000,000
        $this->assertEquals(2000000, $result['total_amount']);
        $this->assertEquals(16, $result['total_hours']);
    }
}
