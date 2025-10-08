<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Chạy seeder
     */
    public function run(): void
    {
        // Fake 10 user ngẫu nhiên
        User::factory()->count(10)->create();

        // Tạo thêm 1 user admin thật
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '0123456789',
            'role' => 'Admin',
            'password' => bcrypt('admin123'),
        ]);
    }
}
