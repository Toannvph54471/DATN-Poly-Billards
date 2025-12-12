<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmployeeShift;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoCheckoutEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically check out employees whose shift has ended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-checkout process...');

        $now = now();
        $bufferMinutes = 30; // Wait 30 mins after shift end before auto checkout

        // Get active employee shifts where the shift end time (plus buffer) has passed
        $activeShifts = EmployeeShift::where('status', EmployeeShift::STATUS_ACTIVE)
            ->with(['shift', 'employee'])
            ->get();

        $count = 0;

        foreach ($activeShifts as $employeeShift) {
            if (!$employeeShift->shift) continue;

            $shiftEndTimeString = $employeeShift->shift->end_time;
            
            // Construct Shift End DateTime
            // Note: Shift might span midnight.
            
            // Assuming shift_date is the date the shift started.
            $shiftEnd = Carbon::parse($employeeShift->shift_date->format('Y-m-d') . ' ' . $shiftEndTimeString);
            
            // Start time for reference
            $shiftStart = Carbon::parse($employeeShift->shift_date->format('Y-m-d') . ' ' . $employeeShift->shift->start_time);

            if ($shiftEnd->lt($shiftStart)) {
                // Shift spans to next day
                $shiftEnd->addDay();
            }

            // Check if we passed end time + buffer
            if ($now->gt($shiftEnd->copy()->addMinutes($bufferMinutes))) {
                
                $this->info("Auto checking out {$employeeShift->employee->name}...");

                DB::transaction(function() use ($employeeShift, $shiftEnd) {
                    
                    // 1. Update Attendance
                    $attendance = Attendance::where('employee_id', $employeeShift->employee_id)
                        ->whereNull('check_out')
                        ->latest('check_in') // Assume last checkin
                        ->first();

                    if ($attendance) {
                        $attendance->check_out = $shiftEnd; // Set checkout time to Shift End Time (not now)
                        
                        $checkIn = Carbon::parse($attendance->check_in);
                        $attendance->total_minutes = $checkIn->diffInMinutes($shiftEnd);
                        $attendance->note = ($attendance->note ? $attendance->note . "\n" : "") . "System Auto Checkout";
                        
                        $attendance->save();
                        
                        \App\Models\ActivityLog::log('auto_checkout', "System auto checkout for {$employeeShift->employee->name}", ['attendance_id' => $attendance->id]);
                    }

                    // 2. Update EmployeeShift
                    $employeeShift->update([
                        'actual_end_time' => $shiftEnd,
                        'total_hours' => $employeeShift->actual_start_time->diffInHours($shiftEnd),
                        'status' => EmployeeShift::STATUS_COMPLETED,
                        'notes' => ($employeeShift->notes ? $employeeShift->notes . "\n" : "") . "System Auto Checkout"
                    ]);
                });

                $count++;
            }
        }

        $this->info("Processed {$count} auto-checkouts.");
    }
}
