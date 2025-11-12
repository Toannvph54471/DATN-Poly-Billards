<?php

namespace App\Services;

use App\Models\Table;
use App\Models\Category;
use App\Models\TableRate;
use Carbon\Carbon;

/**
 * Service tính giá bàn theo thứ tự ưu tiên:
 * 1. table_rates (gói đặc biệt, khuyến mãi)
 * 2. categories.hourly_rate (giá mặc định)
 */
class TablePricingService
{
    /**
     * Lấy giá giờ của bàn
     */
    public function getHourlyRate($table, ?Carbon $dateTime = null, ?string $rateCode = null): float
    {
        $table = $table instanceof Table ? $table : Table::findOrFail($table);

        // Ưu tiên 1: TableRate đặc biệt (VIP, khuyến mãi, event...)
        if ($rateCode) {
            $specialRate = $this->getSpecialRate($table->category_id, $rateCode);
            if ($specialRate) {
                return (float) $specialRate->hourly_rate;
            }
        }

        // Ưu tiên 3: Category mặc định
        $category = $table->category;
        if ($category && $category->hourly_rate) {
            return (float) $category->hourly_rate;
        }

        // Fallback: Giá mặc định nếu không có gì
        return 50000.00;
    }

    /**
     * Tính tổng giá bàn theo số phút - LÀM TRÒN LÊN
     */
    public function calculateTablePrice($table, int $minutes, ?string $rateCode = null): float
    {
        $hourlyRate = $this->getHourlyRate($table, now(), $rateCode);
        $hours = $minutes / 60;
        $price = $hourlyRate * $hours;

        // Làm tròn lên đến hàng nghìn
        return ceil($price / 1000) * 1000;
    }

    /**
     * Lấy thông tin chi tiết giá bàn
     */
    public function getPricingDetails($table, int $minutes = 60, ?string $rateCode = null): array
    {
        $table = $table instanceof Table ? $table : Table::findOrFail($table);

        $hourlyRate = $this->getHourlyRate($table, now(), $rateCode);
        $totalPrice = $this->calculateTablePrice($table, $minutes, $rateCode);

        $source = $this->getPriceSource($table, $rateCode);

        return [
            'table_id' => $table->id,
            'table_name' => $table->table_name,
            'category_name' => $table->category?->name,
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
     * Lấy tất cả gói giá có sẵn cho category
     */
    public function getAvailableRates(int $categoryId): array
    {
        $category = Category::find($categoryId);

        $rates = [];

        // Giá mặc định
        $rates[] = [
            'code' => '',
            'name' => 'Giá thường',
            'hourly_rate' => $category?->hourly_rate ?? 50000,
            'hourly_rate_formatted' => number_format($category?->hourly_rate ?? 50000) . 'đ/giờ',
            'is_default' => true,
            'description' => 'Giá mặc định của loại bàn',
        ];

        // Các gói đặc biệt
        $specialRates = TableRate::where('category_id', $categoryId)
            ->where('status', 'Active')
            ->orderBy('hourly_rate')
            ->get();

        foreach ($specialRates as $rate) {
            $rates[] = [
                'code' => $rate->code,
                'name' => $rate->name,
                'hourly_rate' => (float) $rate->hourly_rate,
                'hourly_rate_formatted' => number_format($rate->hourly_rate) . 'đ/giờ',
                'is_default' => false,
                'description' => "Gói đặc biệt: " . $rate->name,
                'max_hours' => $rate->max_hours,
            ];
        }

        return $rates;
    }

    /**
     * Lấy gói giá đặc biệt theo code
     */
    private function getSpecialRate(int $categoryId, string $rateCode): ?TableRate
    {
        return TableRate::where('category_id', $categoryId)
            ->where('code', $rateCode)
            ->where('status', 'Active')
            ->first();
    }

    /**
     * Lấy gói giá active hiện tại (cho khuyến mãi tự động)
     */
    

    /**
     * Xác định nguồn giá
     */
    private function getPriceSource($table, ?string $rateCode): array
    {
        if ($rateCode) {
            $rate = $this->getSpecialRate($table->category_id, $rateCode);
            if ($rate) {
                return [
                    'type' => 'special_rate',
                    'name' => $rate->name . ' (' . $rate->code . ')',
                ];
            }
        }

        $activeRate = $this->getActiveTableRate($table->category_id);
        if ($activeRate) {
            return [
                'type' => 'active_rate',
                'name' => $activeRate->name,
            ];
        }

        if ($table->category && $table->category->hourly_rate) {
            return [
                'type' => 'category',
                'name' => 'Giá mặc định - ' . $table->category->name,
            ];
        }

        return [
            'type' => 'default',
            'name' => 'Giá hệ thống',
        ];
    }

    /**
     * Validate thời gian chơi theo gói
     */
    public function validatePlayDuration(int $categoryId, int $minutes, ?string $rateCode = null): array
    {
        if (!$rateCode) {
            return ['valid' => true];
        }

        $rate = $this->getSpecialRate($categoryId, $rateCode);

        if (!$rate) {
            return [
                'valid' => false,
                'message' => 'Không tìm thấy gói giá này',
            ];
        }

        if ($rate->max_hours) {
            $maxMinutes = $rate->max_hours * 60;
            if ($minutes > $maxMinutes) {
                return [
                    'valid' => false,
                    'message' => "Gói {$rate->name} chỉ cho phép tối đa {$rate->max_hours} giờ",
                    'max_minutes' => $maxMinutes,
                ];
            }
        }

        return ['valid' => true];
    }
}
