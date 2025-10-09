<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            [
                'name'       => 'Trường',
                'email'      => 'truong@example.com',
                'phone'      => '0795334989',
                'role'       => 'admin',         // hoặc 1 nếu bạn dùng int
                'password'   => Hash::make('password123'), // mã hoá mật khẩu
                'status'     => 'active',        // hoặc 1/0
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Người Dùng 1',
                'email'      => 'user1@example.com',
                'phone'      => '0987654321',
                'role'       => 'member',
                'password'   => Hash::make('user123456'),
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Người Dùng 2',
                'email'      => 'user2@example.com',
                'phone'      => '0912345678',
                'role'       => 'member',
                'password'   => Hash::make('user123456'),
                'status'     => 'inactive',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
