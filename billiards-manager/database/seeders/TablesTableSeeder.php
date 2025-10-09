<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TablesTableSeeder extends Seeder
{
    public function run()
    {
        $tables = [
            [
                'table_number' => 'T01',
                'table_name' => 'Bàn 1',
                'type' => Table::TYPE_STANDARD,
                'status' => Table::STATUS_AVAILABLE,
                'hourly_rate' => 50000,
            ],
            [
                'table_number' => 'T02',
                'table_name' => 'Bàn 2', 
                'type' => Table::TYPE_STANDARD,
                'status' => Table::STATUS_AVAILABLE,
                'hourly_rate' => 50000,
                
            ],
            [
                'table_number' => 'VIP01',
                'table_name' => 'Bàn VIP 1',
                'type' => Table::TYPE_VIP,
                'status' => Table::STATUS_AVAILABLE,
                'hourly_rate' => 80000,
            
            ],
            [
                'table_number' => 'COMP01',
                'table_name' => 'Bàn Thi Đấu',
                'type' => Table::TYPE_COMPETITION,
                'status' => Table::STATUS_AVAILABLE,
                'hourly_rate' => 100000,
                
            ]
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}