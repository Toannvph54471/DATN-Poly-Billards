<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Attendance;
use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Services\WorkingTimeCalculator;
use Carbon\Carbon;

class WorkingTimeCalculatorTest extends TestCase
{
    public function test_normal_shift_capped()
    {
        // Arrange
        // Shift 08:00 - 17:00
        $shift = new Shift([
            'start_time' => '08:00:00', 
            'end_time' => '17:00:00'
        ]);
        $empShift = new EmployeeShift([
            'shift_date' => today(),
        ]);
        $empShift->setRelation('shift', $shift);

        // Check in early 07:30, Out late 17:30
        $attendance = new Attendance([
            'check_in' => today()->setTime(7, 30),
            'check_out' => today()->setTime(17, 30),
            'status' => 'Present'
        ]);

        // Act
        $result = WorkingTimeCalculator::calculate($attendance, $empShift);

        // Assert
        // Should start at 08:00 and end at 17:00 = 9 hours = 540 minutes
        $this->assertEquals(540, $result['total_minutes']);
        $this->assertEquals(9, $result['total_hours']);
        $this->assertEquals('08:00', $result['payroll_start']->format('H:i'));
        $this->assertEquals('17:00', $result['payroll_end']->format('H:i'));
    }

    public function test_late_checkin_not_approved()
    {
        // Shift 08:00 - 17:00
        $shift = new Shift(['start_time' => '08:00:00', 'end_time' => '17:00:00']);
        $empShift = new EmployeeShift(['shift_date' => today()]);
        $empShift->setRelation('shift', $shift);

        // Late 08:30
        $attendance = new Attendance([
            'check_in' => today()->setTime(8, 30),
            'check_out' => today()->setTime(17, 00),
            'status' => 'Late'
        ]);

        $result = WorkingTimeCalculator::calculate($attendance, $empShift);

        // 8:30 -> 17:00 = 8.5 hours = 510 minutes
        $this->assertEquals(510, $result['total_minutes']);
        $this->assertEquals(8.5, $result['total_hours']);
        $this->assertEquals('08:30', $result['payroll_start']->format('H:i'));
    }

    public function test_late_checkin_approved()
    {
        // Shift 08:00 - 17:00
        $shift = new Shift(['start_time' => '08:00:00', 'end_time' => '17:00:00']);
        $empShift = new EmployeeShift(['shift_date' => today()]);
        $empShift->setRelation('shift', $shift);

        // Late 08:30 Approved
        $attendance = new Attendance([
            'check_in' => today()->setTime(8, 30),
            'check_out' => today()->setTime(17, 00),
            'status' => 'Late',
            'approval_status' => 'approved'
        ]);

        $result = WorkingTimeCalculator::calculate($attendance, $empShift);

        // Approved -> Count from Shift Start (08:00)
        // 08:00 -> 17:00 = 9 hours = 540 minutes
        $this->assertEquals(540, $result['total_minutes']);
        $this->assertEquals('08:00', $result['payroll_start']->format('H:i'));
    }

    public function test_early_checkout()
    {
        // Shift 08:00 - 17:00
        $shift = new Shift(['start_time' => '08:00:00', 'end_time' => '17:00:00']);
        $empShift = new EmployeeShift(['shift_date' => today()]);
        $empShift->setRelation('shift', $shift);

        // Early Leave 16:00
        $attendance = new Attendance([
            'check_in' => today()->setTime(8, 00),
            'check_out' => today()->setTime(16, 00),
            'status' => 'Present'
        ]);

        $result = WorkingTimeCalculator::calculate($attendance, $empShift);

        // 08:00 -> 16:00 = 8 hours = 480 minutes
        $this->assertEquals(480, $result['total_minutes']);
        $this->assertEquals(60, $result['early_minutes']); // 16:00 to 17:00
    }
    
    public function test_admin_checkout_simulated()
    {
        // Shift 08:00 - 17:00
        $shift = new Shift(['start_time' => '08:00:00', 'end_time' => '17:00:00']);
        $empShift = new EmployeeShift(['shift_date' => today()]);
        $empShift->setRelation('shift', $shift);

        // Check in 08:00. No checkout yet.
        $attendance = new Attendance([
            'check_in' => today()->setTime(8, 00),
            'check_out' => null
        ]);
        
        // Admin force checkout at 17:30 (Late)
        $checkoutTime = today()->setTime(17, 30);
        
        $result = WorkingTimeCalculator::calculate($attendance, $empShift, $checkoutTime);
        
        // Should cap at 17:00
        $this->assertEquals(540, $result['total_minutes']);
        // Check payroll_end
        $this->assertEquals('17:00', $result['payroll_end']->format('H:i'));
    }
}
