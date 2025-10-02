<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            $this->call([
        RoleSeeder::class,
        PermissionSeeder::class,
        RolePermissionSeeder::class,
        UserSeeder::class,
        EmployeeSeeder::class,
        CustomerSeeder::class,
        TableSeeder::class,
        ShiftSeeder::class,
        AttendanceSeeder::class,
        PayrollSeeder::class,
        ProductSeeder::class,
        ComboSeeder::class,
        ComboItemSeeder::class,
        PromotionSeeder::class,
        ReservationSeeder::class,
        ReservationLogSeeder::class,
        BillSeeder::class,
        BillDetailSeeder::class,
        BillTimeBlockSeeder::class,
        InventorySeeder::class,
        ReportSeeder::class,
    ]);
    }
}
