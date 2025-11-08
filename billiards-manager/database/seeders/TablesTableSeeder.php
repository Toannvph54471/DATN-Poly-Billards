<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TablesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tables')->truncate();
        $catStd = Category::where('name', 'Bàn Tiêu Chuẩn')->first();
        $catVip = Category::where('name', 'Bàn VIP')->first();

        Table::create(['category_id' => $catStd->id, 'table_number' => 'A01', 'table_name' => 'Bàn A01', 'type' => 'Regular', 'status' => 'available']);
        Table::create(['category_id' => $catStd->id, 'table_number' => 'A02', 'table_name' => 'Bàn A02', 'type' => 'Regular', 'status' => 'available']);
        Table::create(['category_id' => $catStd->id, 'table_number' => 'A03', 'table_name' => 'Bàn A03', 'type' => 'Regular', 'status' => 'occupied']);
        Table::create(['category_id' => $catVip->id, 'table_number' => 'V01', 'table_name' => 'Bàn VIP 01', 'type' => 'VIP', 'status' => 'available']);
        Table::create(['category_id' => $catVip->id, 'table_number' => 'V02', 'table_name' => 'Bàn VIP 02', 'type' => 'VIP', 'status' => 'maintenance']);
    }
}
