<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Dữ liệu Cốt lõi & Phân quyền
        $this->call([
            RolesTableSeeder::class,           // (1) 
            PermissionsTableSeeder::class,     // (2) đồng bộ với config
            UsersTableSeeder::class,           // (3) Tạo Admin, Manager, Staff, Customer 
            RolePermissionsTableSeeder::class, // (4) đồng bộ với config
        ]);
        // 2. Dữ liệu Nhân sự (HR)
        $this->call([
            ShiftsTableSeeder::class,      // (5)
            EmployeesTableSeeder::class,   // (6) 
        ]);
        // 3. Dữ liệu Sản phẩm & Bàn (Pricing)
        $this->call([
            CategoriesTableSeeder::class,    // (7)
            TableRatesTableSeeder::class,    // (8) cần tạo thêm

            TablesTableSeeder::class,        // (10) cần tạo thêm
            ProductsTableSeeder::class,      // (11)
            CombosTableSeeder::class,        // (12) cần tạo thêm
            ComboItemsTableSeeder::class,    // (13) cần tạo thêm
        ]);
        // 4. Dữ liệu Khuyến mãi
        $this->call([
            PromotionsTableSeeder::class,       // (14) cần tạo thêm
            PromotionProductsTableSeeder::class, // (15) cần tạo thêm
            PromotionComboTableSeeder::class,    // (16) cần tạo thêm
        ]);
        // 5. Dữ liệu Nghiệp vụ MẪU (Tùy chọn)
        $this->call([
            ReservationSeeder::class, // (17) cần tạo lại
            BillsTableSeeder::class,     // (18) 
            BillDetailsTableSeeder::class,         // (19) **MỚI**
            BillTimeUsageTableSeeder::class,       // (20) **MỚI**
            PaymentSeeder::class,          // (21) **MỚI**
            PromotionApplicationsTableSeeder::class, // (22) **MỚI**
            ComboTimeUsagesTableSeeder::class,     // (24) **MỚI**
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}
