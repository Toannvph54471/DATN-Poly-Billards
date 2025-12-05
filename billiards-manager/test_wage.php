<?php

use App\Models\Employee;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Hourly Rate Update...\n";

// Create a dummy user and employee
$user = User::factory()->create();
$employee = Employee::create([
    'user_id' => $user->id,
    'employee_code' => 'TEST' . rand(1000, 9999),
    'name' => 'Test Employee',
    'phone' => '09' . rand(10000000, 99999999),
    'email' => 'test' . rand(1000, 9999) . '@example.com',
    'hourly_rate' => 25000, // Initial rate
    'status' => 'Active'
]);

echo "Initial Rate: " . $employee->hourly_rate . "\n";
echo "DB Salary Rate: " . $employee->salary_rate . "\n";

// Update rate
$employee->update(['hourly_rate' => 22000]);

// Refresh from DB
$employee->refresh();

echo "Updated Rate: " . $employee->hourly_rate . "\n";
echo "DB Salary Rate: " . $employee->salary_rate . "\n";

if ($employee->hourly_rate == 22000 && $employee->salary_rate == 22000) {
    echo "SUCCESS: Hourly rate updated correctly.\n";
} else {
    echo "FAILURE: Hourly rate did not update.\n";
}

// Clean up
$employee->delete();
$user->delete();
