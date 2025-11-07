<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class EmployeeShiftsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('employee_shifts')->truncate();
        
        $employee = Employee::where('employee_code', 'EMP001')->first();
        $shiftSang = Shift::where('name', 'Ca Sáng')->first();

        if ($employee && $shiftSang) {
            DB::table('employee_shifts')->insert([
                'employee_id' => $employee->id,
                'shift_id' => $shiftSang->id,
                'shift_date' => today(),
                'status' => 'Scheduled',
            ]);
            DB::table('employee_shifts')->insert([
                'employee_id' => $employee->id,
                'shift_id' => $shiftSang->id,
                'shift_date' => today()->addDay(),
                'status' => 'Scheduled',
            ]);
        }
    }
}