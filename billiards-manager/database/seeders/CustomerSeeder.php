<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            [
                'name' => 'Nguyễn Văn A',
                'phone' => '0912345678',
                'email' => 'nguyenvana@email.com',
                'customer_type' => Customer::TYPE_VIP,
                'total_visits' => 15,
                'total_spent' => 3500000,
                'last_visit_at' => now()->subDays(2),
            ],
            [
                'name' => 'Trần Thị B',
                'phone' => '0923456789',
                'email' => 'tranthib@email.com',
                'customer_type' => Customer::TYPE_REGULAR,
                'total_visits' => 8,
                'total_spent' => 1200000,
                'last_visit_at' => now()->subDays(7),
            ],
            // Thêm các khách hàng mẫu khác...
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        $this->command->info('Customers seeded successfully!');
    }
}