<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeShift;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminCheckoutTest extends TestCase
{
    // use RefreshDatabase; // Be careful with RefreshDatabase on existing DBs, maybe just transaction?
    // Using transaction trait usually safer if we simply rollback, but standard Laravel uses RefreshDatabase.
    // Given the user context, I will try to clean up manually or use transaction if possible.
    // For now, I'll rely on manually creating and deleting data to avoid wiping their DB if they haven't configured testing env correctly.
    // Actually, safer to NOT use RefreshDatabase unless sure. I will use a suffix for data.

    public function test_admin_can_checkout_employee()
    {
        // 1. Setup Data
        $admin = User::factory()->create(['role' => 'admin']); // Assuming factory exists or create manually
        // If factory fails, we might need a fallback, but let's try standard way first.
        
        // Let's create an Employee and Attendance manually to be safe.
        $employee = Employee::create([
            'name' => 'Test Employee ' . time(),
            'code' => 'TE' . time(),
            'email' => 'test' . time() . '@example.com',
            'phone' => '0123456789',
            'hourly_rate' => 50000 
        ]);

        $checkInTime = Carbon::now()->subHours(4);
        
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'check_in' => $checkInTime,
            'status' => 'Late', // Test if status is preserved
            'late_minutes' => 10,
            'approval_status' => 'pending'
        ]);

        // 2. Act
        $response = $this->actingAs($admin)
            ->postJson("/admin/attendance/{$attendance->id}/admin-checkout", [
                'reason' => 'Admin Test Checkout'
            ]);

        // 3. Assert
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $attendance->refresh();
        
        // Verify Check out time set
        $this->assertNotNull($attendance->check_out);
        
        // Verify Total Minutes (approx 240 mins)
        $this->assertEqualsWithDelta(240, $attendance->total_minutes, 1); // allow 1 min diff
        
        // Verify Status PRESERVED (Was 'Late', should stay 'Late')
        $this->assertEquals('Late', $attendance->status);
        
        // Verify Admin info
        $this->assertEquals($admin->id, $attendance->admin_checkout_by);
        $this->assertEquals('Admin Test Checkout', $attendance->admin_checkout_reason);

        // Cleanup
        $attendance->delete();
        $employee->delete();
        $admin->delete();
    }

    public function test_admin_cannot_double_checkout()
    {
        // 1. Setup
        $admin = User::first(); // Use existing admin if possible to avoid factory issues
        if (!$admin) $admin = User::factory()->create(['role' => 'admin']);

        $employee = Employee::create([
            'name' => 'Test Employee 2',
            'code' => 'TE2',
            'hourly_rate' => 50000
        ]);

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'check_in' => Carbon::now()->subHours(2),
            'check_out' => Carbon::now()->subHour(), // Already checked out
            'total_minutes' => 60,
            'status' => 'Present'
        ]);

        // 2. Act
        $response = $this->actingAs($admin)
            ->postJson("/admin/attendance/{$attendance->id}/admin-checkout", [
                'reason' => 'Again'
            ]);

        // 3. Assert error
        $response->assertJson(['status' => 'error']);
        
        // Cleanup
        $attendance->delete();
        $employee->delete();
    }
}
