<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Table;
use Illuminate\Database\Seeder;

class TablesTableSeeder extends Seeder
{
    public function run(): void
    {
        $standardCat = Category::where('name', 'Khu vực Standard')->first();
        $vipCat = Category::where('name', 'Khu vực VIP')->first();
        $compCat = Category::where('name', 'Khu vực Thi đấu')->first();

        $tables = [
            [
                'table_number' => 'T01',
                'table_name' => 'Bàn 1',
                'category_id' => $standardCat->id,
                'capacity' => 4,
                'type' => 'Standard',
                'status' => 'available',
                'hourly_rate' => 50000,
            ],
            [
                'table_number' => 'T02',
                'table_name' => 'Bàn 2',
                'category_id' => $standardCat->id,
                'capacity' => 4,
                'type' => 'Standard',
                'status' => 'available',
                'hourly_rate' => 50000,
            ],
            [
                'table_number' => 'VIP01',
                'table_name' => 'Bàn VIP 1',
                'category_id' => $vipCat->id,
                'capacity' => 6,
                'type' => 'VIP',
                'status' => 'available',
                'hourly_rate' => 80000,
            ],
            [
                'table_number' => 'COMP01',
                'table_name' => 'Bàn Thi Đấu',
                'category_id' => $compCat->id,
                'capacity' => 8,
                'type' => 'Competition',
                'status' => 'available',
                'hourly_rate' => 100000,
            ]
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
