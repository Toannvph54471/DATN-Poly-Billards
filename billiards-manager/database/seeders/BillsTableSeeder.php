<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bill;
use App\Models\User;
use App\Models\Table;
use App\Models\Role;

use Illuminate\Support\Facades\DB;

class BillsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bills')->truncate();
        
        $customer = User::where('phone', '0912345678')->first();
        $employeeRole = Role::where('slug', 'employee')->first();
        $staff = $employeeRole ? User::where('role_id', $employeeRole->id)->first() : null;
        $table = Table::where('status', 'occupied')->first();

        if ($customer && $staff && $table) {
            Bill::create([
                'bill_number' => 'BILL2025001',
                'table_id' => $table->id,
                'staff_id' => $staff->id,
                'start_time' => now()->subHour(),
                'status' => 'Open',
            ]);
        } else {
            \Log::warning('Không đủ dữ liệu để tạo Bill trong seeder', [
                'staff' => $staff?->id,
                'table' => $table?->id,
            ]);
        }
    }
}