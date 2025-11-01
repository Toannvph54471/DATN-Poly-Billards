<?php

// app/Services/ComboService.php

namespace App\Services;

use App\Models\Bill;
use App\Models\Combo;
use App\Models\ComboTimeUsage;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

class ComboService
{
    /**
     * Áp dụng combo cho bàn
     * Kiểm tra: combo bàn phải match với loại bàn
     */
    public function applyComboToTable(Combo $combo, Table $table): bool
    {
        // Nếu không phải combo bàn, có thể áp dụng
        if (!$combo->isTimeCombo()) {
            return true;
        }

        // Nếu là combo bàn, phải match loại bàn
        return $combo->table_category_id === $table->category_id;
    }

    /**
     * Lấy bàn trống theo loại
     */
    public function getAvailableTablesByCategory($categoryId)
    {
        return Table::where('category_id', $categoryId)
            ->where('status', 'available')
            ->get();
    }

    /**
     * Tạo session sử dụng combo bàn
     */
    public function startComboSession(Combo $combo, Bill $bill, Table $table): ComboTimeUsage
    {
        if (!$combo->isTimeCombo()) {
            throw new \Exception('Combo này không phải combo bàn');
        }

        if (!$combo->isActive()) {
            throw new \Exception('Combo không hoạt động');
        }

        if (!$this->applyComboToTable($combo, $table)) {
            throw new \Exception('Combo không phù hợp với loại bàn này');
        }

        if (!$table->isAvailable()) {
            throw new \Exception('Bàn này không khả dụng');
        }

        return DB::transaction(function () use ($combo, $bill, $table) {
            $session = ComboTimeUsage::create([
                'combo_id' => $combo->id,
                'bill_id' => $bill->id,
                'table_id' => $table->id,
                'total_minutes' => $combo->play_duration_minutes,
                'remaining_minutes' => $combo->play_duration_minutes,
                'is_expired' => false,
            ]);

            $session->startSession();

            // Update bàn thành occupied
            $table->update(['status' => 'occupied']);

            return $session;
        });
    }

    /**
     * Kết thúc session
     */
    public function endComboSession(ComboTimeUsage $session, float $overTimePricePerMin = 0): array
    {
        return DB::transaction(function () use ($session, $overTimePricePerMin) {
            $session->endSession();

            $extraCharge = 0;
            if ($overTimePricePerMin > 0) {
                $extraCharge = $session->calculateExtraCharge($overTimePricePerMin);
            }

            // Update bàn thành available
            $session->table->update(['status' => 'available']);

            return [
                'success' => true,
                'session' => $session,
                'extra_charge' => $extraCharge,
            ];
        });
    }

    /**
     * Kiểm tra bàn trống theo loại
     */
    public function hasAvailableTable(Combo $combo): bool
    {
        if (!$combo->isTimeCombo()) {
            return true;
        }

        return Table::where('category_id', $combo->table_category_id)
            ->where('status', 'available')
            ->exists();
    }

    /**
     * Lấy thông tin chi tiết combo
     */
    public function getComboDetails(Combo $combo): array
    {
        $serviceProduct = $combo->getServiceProduct();

        return [
            'id' => $combo->id,
            'code' => $combo->combo_code,
            'name' => $combo->name,
            'price' => (float) $combo->price,
            'actual_value' => (float) $combo->actual_value,
            'discount' => $combo->getDiscountAmount(),
            'discount_percent' => $combo->getDiscountPercent(),
            'is_time_combo' => $combo->isTimeCombo(),
            'play_duration_minutes' => $combo->play_duration_minutes,
            'table_type' => $combo->tableCategory?->name ?? null,
            'available_tables_count' => $combo->getAvailableTables()?->count() ?? 0,
            'service_product' => $serviceProduct ? [
                'id' => $serviceProduct->product->id,
                'name' => $serviceProduct->product->name,
                'price' => (float) $serviceProduct->unit_price,
            ] : null,
            'items' => $combo->comboItems()->with('product')->get()->map(fn($item) => [
                'product_name' => $item->product->name,
                'product_type' => $item->product->product_type,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => $item->getSubtotal(),
            ]),
        ];
    }

    /**
     * Báo cáo sử dụng combo theo ngày
     */
    public function getDailyReport(\DateTime $date = null): array
    {
        $date = $date ?? now();
        $start = $date->clone()->startOfDay();
        $end = $date->clone()->endOfDay();

        $sessions = ComboTimeUsage::whereBetween('start_time', [$start, $end])
            ->with(['combo', 'table.category'])
            ->get();

        $report = [
            'date' => $date->format('Y-m-d'),
            'total_sessions' => $sessions->count(),
            'total_minutes_used' => 0,
            'total_extra_charge' => 0,
            'by_table_type' => [],
            'by_combo' => [],
        ];

        foreach ($sessions as $session) {
            $elapsed = $session->start_time?->diffInMinutes($session->end_time ?? now()) ?? 0;
            $report['total_minutes_used'] += $elapsed;
            $report['total_extra_charge'] += $session->extra_charge ?? 0;

            // By table type
            $tableType = $session->table?->category?->name ?? 'Unknown';
            if (!isset($report['by_table_type'][$tableType])) {
                $report['by_table_type'][$tableType] = [
                    'count' => 0,
                    'minutes' => 0,
                    'extra_charge' => 0,
                ];
            }
            $report['by_table_type'][$tableType]['count']++;
            $report['by_table_type'][$tableType]['minutes'] += $elapsed;
            $report['by_table_type'][$tableType]['extra_charge'] += $session->extra_charge ?? 0;

            // By combo
            $comboName = $session->combo->name;
            if (!isset($report['by_combo'][$comboName])) {
                $report['by_combo'][$comboName] = [
                    'count' => 0,
                    'minutes' => 0,
                    'extra_charge' => 0,
                ];
            }
            $report['by_combo'][$comboName]['count']++;
            $report['by_combo'][$comboName]['minutes'] += $elapsed;
            $report['by_combo'][$comboName]['extra_charge'] += $session->extra_charge ?? 0;
        }

        return $report;
    }

    /**
     * Validate combo trước khi tạo
     */
    public function validateComboData($data): array
    {
        $errors = [];

        // Check only 1 service product
        $serviceCount = 0;
        foreach ($data['combo_items'] ?? [] as $item) {
            if ($product = \App\Models\Product::find($item['product_id'] ?? null)) {
                if ($product->isService()) {
                    $serviceCount++;
                }
            }
        }

        if ($serviceCount > 1) {
            $errors[] = 'Combo chỉ được phép có 1 sản phẩm dịch vụ';
        }

        // Check if time combo needs table type
        if (($data['is_time_combo'] ?? false) && empty($data['table_category_id'])) {
            $errors[] = 'Combo bàn phải chọn loại bàn';
        }

        return $errors;
    }
}
