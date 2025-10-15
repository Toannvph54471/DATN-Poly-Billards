<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Users module
            ['name' => 'view_users', 'description' => 'Xem danh sách người dùng', 'module' => 'Users'],
            ['name' => 'create_users', 'description' => 'Tạo người dùng mới', 'module' => 'Users'],
            ['name' => 'edit_users', 'description' => 'Sửa thông tin người dùng', 'module' => 'Users'],
            ['name' => 'delete_users', 'description' => 'Xóa người dùng', 'module' => 'Users'],
            
            // Employees module
            ['name' => 'view_employees', 'description' => 'Xem danh sách nhân viên', 'module' => 'Employees'],
            ['name' => 'manage_shifts', 'description' => 'Phân ca làm việc', 'module' => 'Employees'],
            ['name' => 'confirm_attendance', 'description' => 'Xác nhận chấm công', 'module' => 'Employees'],
            ['name' => 'manage_payroll', 'description' => 'Quản lý tính lương', 'module' => 'Employees'],
            
            // Products module
            ['name' => 'view_products', 'description' => 'Xem danh sách sản phẩm', 'module' => 'Products'],
            ['name' => 'create_products', 'description' => 'Thêm sản phẩm mới', 'module' => 'Products'],
            ['name' => 'edit_products', 'description' => 'Sửa thông tin sản phẩm', 'module' => 'Products'],
            ['name' => 'manage_combos', 'description' => 'Quản lý combo', 'module' => 'Products'],
            ['name' => 'update_inventory', 'description' => 'Cập nhật tồn kho', 'module' => 'Products'],
            
            // Tables module
            ['name' => 'view_tables', 'description' => 'Xem trạng thái bàn', 'module' => 'Tables'],
            ['name' => 'manage_tables', 'description' => 'Quản lý thông tin bàn', 'module' => 'Tables'],
            ['name' => 'open_bills', 'description' => 'Mở bill mới', 'module' => 'Tables'],
            ['name' => 'close_bills', 'description' => 'Đóng bill', 'module' => 'Tables'],
            
            // Bills module
            ['name' => 'create_bills', 'description' => 'Tạo hóa đơn mới', 'module' => 'Bills'],
            ['name' => 'edit_bills', 'description' => 'Sửa hóa đơn', 'module' => 'Bills'],
            ['name' => 'add_order_items', 'description' => 'Thêm món vào hóa đơn', 'module' => 'Bills'],
            ['name' => 'apply_discount', 'description' => 'Áp dụng giảm giá', 'module' => 'Bills'],
            ['name' => 'process_payments', 'description' => 'Xử lý thanh toán', 'module' => 'Bills'],
            
            // Reservations module
            ['name' => 'view_reservations', 'description' => 'Xem danh sách đặt bàn', 'module' => 'Reservations'],
            ['name' => 'create_reservations', 'description' => 'Tạo đặt bàn mới', 'module' => 'Reservations'],
            ['name' => 'confirm_checkin', 'description' => 'Xác nhận khách đến', 'module' => 'Reservations'],
            
            // Reports module
            ['name' => 'view_daily_reports', 'description' => 'Xem báo cáo ngày', 'module' => 'Reports'],
            ['name' => 'view_revenue_reports', 'description' => 'Xem báo cáo doanh thu', 'module' => 'Reports'],
            ['name' => 'export_reports', 'description' => 'Xuất báo cáo', 'module' => 'Reports'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}