<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Định nghĩa dữ liệu mẫu cho bảng users
     */
    public function definition(): array
    {
        return [
            // 'name' => $this->faker->name(),
            // 'email' => $this->faker->unique()->safeEmail(),
            // 'phone' => $this->faker->optional()->phoneNumber(),
            // 'role' => $this->faker->randomElement(['Admin', 'Manager', 'Staff']),
            // 'password' => bcrypt('password'), // Mật khẩu mặc định
            // 'created_at' => now(),
            // 'updated_at' => now(),
        ];
    }
}
