<?php

use App\Models\Employee;
use App\Models\User;
use App\Models\Payroll;
use Illuminate\Http\Request;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Services\PayrollService;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Reproducing Salary Update Issue ---\n";

// 1. Setup Data
$user = User::factory()->create();
$employee = Employee::create([
    'user_id' => $user->id,
    'employee_code' => 'REP' . rand(1000, 9999),
    'name' => 'Repro Employee',
    'phone' => '09' . rand(10000000, 99999999),
    'email' => 'repro' . rand(1000, 9999) . '@example.com',
    'hourly_rate' => 25000,
    'status' => 'Active'
]);

echo "Initial Employee Rate: " . $employee->hourly_rate . "\n";
echo "Initial DB Salary Rate: " . $employee->salary_rate . "\n";

// 2. Create Initial Payroll (Old Rate)
$payrollService = new PayrollService();
$payrollService->createPayroll($employee->id, now()->format('Y-m'));
$payroll = Payroll::where('employee_id', $employee->id)->first();
echo "Initial Payroll Rate: " . $payroll->hourly_rate . "\n";

// 3. Call Update Salary API (Simulated)
echo "\n--- Calling Update Salary API (22000) ---\n";
$employeeController = new EmployeeController();
$request = Request::create('/api/employees/' . $employee->id . '/salary', 'POST', ['hourly_rate' => 22000]);
$response = $employeeController->updateSalary($request, $employee->id);
echo "Update Status: " . $response->getData()->status . "\n";

$employee->refresh();
echo "Employee Rate After Update: " . $employee->hourly_rate . "\n";
echo "DB Salary Rate After Update: " . $employee->salary_rate . "\n";

// 4. Call Generate Payroll API (Simulated)
echo "\n--- Calling Generate Payroll API ---\n";
$payrollController = new PayrollController($payrollService);
$genRequest = Request::create('/api/payroll/generate', 'POST', [
    'employee_id' => $employee->id,
    'month' => now()->format('Y-m')
]);
$genResponse = $payrollController->generate($genRequest);
// echo "Generate Response: " . json_encode($genResponse->getData()) . "\n";

// 5. Verify Final Payroll
$payroll->refresh();
echo "Final Payroll Rate: " . $payroll->hourly_rate . "\n";

if ($payroll->hourly_rate == 22000) {
    echo "\nSUCCESS: Payroll rate updated correctly.\n";
} else {
    echo "\nFAILURE: Payroll rate is " . $payroll->hourly_rate . " (Expected 22000)\n";
}

// Cleanup
$payroll->delete();
$employee->delete();
$user->delete();
