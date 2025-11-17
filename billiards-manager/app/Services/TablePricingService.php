<?php

namespace App\Services;

use App\Models\Table;
use App\Models\Category;
use App\Models\TableRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TablePricingService
{
    /**
     * Lấy giá giờ của bàn
     */
    public function getHourlyRate($table, ?Carbon $dateTime = null, ?string $rateCode = null): float
    {
        $table = $table instanceof Table ? $table : Table::find($table);
        if (!$table || !$table->category_id) {
            return 0.0;
        }

        // 1. Ưu tiên: rateCode cụ thể
        if ($rateCode) {
            $rate = $this->getSpecialRate($table->category_id, $rateCode);
            if ($rate) {
                return (float) $rate->hourly_rate;
            }
        }

        // 2. Ưu tiên: TableRate active (theo thời gian)
        $activeRate = $this->getActiveTableRate($table->category_id, $dateTime ?? now());
        if ($activeRate) {
            return (float) $activeRate->hourly_rate;
        }

        // 3. Ưu tiên: Category có hourly_rate
        if ($table->category?->hourly_rate) {
            return (float) $table->category->hourly_rate;
        }

        // 4. Fallback: giá mặc định
        return 50000.0;
    }

    /**
     * Tính giá bàn theo phút (làm tròn lên 1 phút)
     */
    public function calculateTablePrice($table, int $minutes, ?string $rateCode = null): float
    {
        $hourlyRate = $this->getHourlyRate($table, now(), $rateCode);
        if ($hourlyRate <= 0 || $minutes <= 0) {
            return 0.0;
        }

        $hours = ceil($minutes / 60.0); // Làm tròn lên giờ
        return round($hours * $hourlyRate, 2);
    }

    /**
     * Chi tiết giá
     */
    public function getPricingDetails($table, int $minutes = 60, ?string $rateCode = null): array
    {
        $table = $table instanceof Table ? $table : Table::find($table);
        if (!$table) {
            return ['error' => 'Table not found'];
        }

        $hourlyRate = $this->getHourlyRate($table, now(), $rateCode);
        $totalPrice = $this->calculateTablePrice($table, $minutes, $rateCode);
        $source = $this->getPriceSource($table, $rateCode);

        return [
            'table_id' => $table->id,
            'table_name' => $table->table_name ?? 'N/A',
            'category_name' => $table->category?->name ?? 'N/A',
            'hourly_rate' => $hourlyRate,
            'minutes' => $minutes,
            'hours' => round($minutes / 60, 2),
            'total_price' => $totalPrice,
            'price_source' => $source['type'],
            'price_source_name' => $source['name'],
            'rate_code' => $rateCode,
        ];
    }

    /**
     * Lấy danh sách gói giá
     */
    public function getAvailableRates(int $categoryId): array
    {
        $cacheKey = "table_rates_category_{$categoryId}";
        return Cache::remember($cacheKey, 3600, function () use ($categoryId) {
            $category = Category::find($categoryId);
            if (!$category) return [];

            $rates = [];

            // Giá mặc định
            $defaultRate = $category->hourly_rate ?? 50000;
            $rates[] = [
                'code' => '',
                'name' => 'Giá thường',
                'hourly_rate' => (float) $defaultRate,
                'hourly_rate_formatted' => number_format($defaultRate) . 'đ/giờ',
                'is_default' => true,
                'max_hours' => null,
            ];

            // Gói đặc biệt
            $specialRates = TableRate::where('category_id', $categoryId)
                ->where('status', 'active')
                ->orderBy('hourly_rate')
                ->get();

            foreach ($specialRates as $rate) {
                $rates[] = [
                    'code' => $rate->code,
                    'name' => $rate->name,
                    'hourly_rate' => (float) $rate->hourly_rate,
                    'hourly_rate_formatted' => number_format($rate->hourly_rate) . 'đ/giờ',
                    'is_default' => false,
                    'max_hours' => $rate->max_hours,
                    'description' => $rate->name,
                ];
            }

            return $rates;
        });
    }

    private function getSpecialRate(int $categoryId, string $rateCode): ?TableRate
    {
        return TableRate::where('category_id', $categoryId)
            ->where('code', $rateCode)
            ->where('status', 'active')
            ->first();
    }

    private function getActiveTableRate(int $categoryId, Carbon $dateTime): ?TableRate
    {
        // TODO: Thêm bảng khuyến mãi theo thời gian
        return null;
    }

    private function getPriceSource($table, ?string $rateCode): array
    {
        if ($rateCode && ($rate = $this->getSpecialRate($table->category_id, $rateCode))) {
            return ['type' => 'special', 'name' => $rate->name];
        }

        if ($table->category?->hourly_rate) {
            return ['type' => 'category', 'name' => 'Giá mặc định'];
        }

        return ['type' => 'fallback', 'name' => 'Giá hệ thống'];
    }

    public function validatePlayDuration(int $categoryId, int $minutes, ?string $rateCode = null): array
    {
        if (!$rateCode) return ['valid' => true];

        $rate = $this->getSpecialRate($categoryId, $rateCode);
        if (!$rate || !$rate->max_hours) return ['valid' => true];

        $maxMinutes = $rate->max_hours * 60;
        if ($minutes > $maxMinutes) {
            return [
                'valid' => false,
                'message' => "Chỉ được chơi tối đa {$rate->max_hours} giờ với gói này",
                'max_minutes' => $maxMinutes,
            ];
        }

        return ['valid' => true];
    }
}
