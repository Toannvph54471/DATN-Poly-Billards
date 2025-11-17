<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\TableRate;
use App\Models\Table;
use App\Services\TablePricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ComboController extends Controller
{
    protected $pricingService;

    public function __construct(TablePricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function index(Request $request)
    {
        $query = Combo::with(['comboItems.product', 'tableRate'])
            ->withCount('comboItems');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('combo_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $isTimeCombo = $request->type === 'time';
            $query->where('is_time_combo', $isTimeCombo);
        }

        $combos = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Combo::count(),
            'active' => Combo::where('status', 'active')->count(),
            'inactive' => Combo::where('status', 'inactive')->count(),
            'time_combos' => Combo::where('is_time_combo', true)->count(),
        ];

        return view('admin.combos.index', compact('combos', 'stats'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')
            ->where('product_type', 'Consumption')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'product_code']);

        // Lấy tất cả TableRate với thông tin đầy đủ
        $tableRates = TableRate::where('status', 'Active')
            ->orderBy('name')
            ->get();

        return view('admin.combos.create', compact('products', 'tableRates'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateComboRequest($request);

        try {
            return DB::transaction(function () use ($validated, $request) {
                // Tính actual value SỬ DỤNG SERVICE
                $actualValue = $this->calculateActualValueViaService(
                    $validated['combo_items'] ?? [],
                    $validated['is_time_combo'] ?? false,
                    $validated['table_rate_id'] ?? null,
                    $validated['play_duration_minutes'] ?? null
                );

                if ($validated['price'] > $actualValue) {
                    throw ValidationException::withMessages([
                        'price' => "Giá bán ({$validated['price']}đ) không được lớn hơn giá trị thực ({$actualValue}đ)"
                    ]);
                }

                $combo = Combo::create([
                    'combo_code' => $validated['combo_code'] ?: $this->generateComboCode(),
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'price' => $validated['price'],
                    'actual_value' => $actualValue,
                    'status' => $validated['status'] ?? 'active',
                    'is_time_combo' => $validated['is_time_combo'] ?? false,
                    'play_duration_minutes' => $validated['play_duration_minutes'] ?? null,
                    'table_rate_id' => $validated['table_rate_id'] ?? null,
                ]);

                if (!empty($validated['combo_items'])) {
                    $this->attachComboItems($combo, $validated['combo_items']);
                }

                return redirect()
                    ->route('admin.combos.show', $combo->id)
                    ->with('success', 'Tạo combo thành công!');
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $combo = Combo::with([
            'comboItems.product.category',
            'tableRate',
            'timeUsages' => function ($query) {
                $query->where('is_expired', false)
                    ->whereNull('end_time')
                    ->latest();
            }
        ])->findOrFail($id);

        $activeSession = $combo->is_time_combo ? $combo->getCurrentTimeUsage() : null;

        return view('admin.combos.show', compact('combo', 'activeSession'));
    }

    public function edit($id)
    {
        $combo = Combo::with('comboItems.product', 'tableRate')->findOrFail($id);

        $products = Product::where('status', 'active')
            ->where('product_type', 'Consumption')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'product_code']);

        $tableRates = TableRate::where('status', 'Active')
            ->orderBy('name')
            ->get();

        return view('admin.combos.edit', compact('combo', 'products', 'tableRates'));
    }

    public function update(Request $request, $id)
    {
        $combo = Combo::findOrFail($id);
        $validated = $this->validateComboRequest($request, $id);

        if ($combo->is_time_combo && $combo->hasActiveSession()) {
            session()->flash('warning', 'Combo này đang có session hoạt động. Thay đổi có thể ảnh hưởng đến session hiện tại.');
        }

        try {
            return DB::transaction(function () use ($combo, $validated, $request) {
                $actualValue = $this->calculateActualValueViaService(
                    $validated['combo_items'] ?? [],
                    $validated['is_time_combo'] ?? false,
                    $validated['table_rate_id'] ?? null,
                    $validated['play_duration_minutes'] ?? null
                );

                if ($validated['price'] > $actualValue) {
                    throw ValidationException::withMessages([
                        'price' => "Giá bán không được lớn hơn giá trị thực (" . number_format($actualValue) . "đ)"
                    ]);
                }

                $combo->update([
                    'combo_code' => $validated['combo_code'],
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'price' => $validated['price'],
                    'actual_value' => $actualValue,
                    'status' => $validated['status'] ?? 'active',
                    'is_time_combo' => $validated['is_time_combo'] ?? false,
                    'play_duration_minutes' => $validated['play_duration_minutes'] ?? null,
                    'table_rate_id' => $validated['table_rate_id'] ?? null,
                ]);

                if (!empty($validated['combo_items'])) {
                    $this->syncComboItems($combo, $validated['combo_items']);
                }

                return redirect()
                    ->route('admin.combos.show', $combo->id)
                    ->with('success', 'Cập nhật combo thành công!');
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $combo = Combo::findOrFail($id);

        if ($combo->hasActiveSession()) {
            return back()->withErrors([
                'error' => 'Không thể xóa combo đang có session hoạt động'
            ]);
        }

        $combo->delete();

        return redirect()
            ->route('admin.combos.index')
            ->with('success', 'Xóa combo thành công');
    }

    // ============ API ENDPOINTS ============

    /**
     * API: Tính giá bàn real-time  
     */
    public function calculateTablePriceAPI(Request $request)
    {
        try {
            $tableRateId = $request->input('table_rate_id');
            $minutes = $request->input('minutes', 60);

            if (!$tableRateId) {
                return response()->json(['error' => 'Table Rate ID required'], 400);
            }

            $tableRate = TableRate::find($tableRateId);
            if (!$tableRate) {
                return response()->json(['error' => 'Table rate not found'], 404);
            }

            $hourlyRate = $tableRate->hourly_rate;
            $hours = $minutes / 60;
            $tablePrice = ceil($hourlyRate * $hours); // Làm tròn lên

            return response()->json([
                'success' => true,
                'rate_name' => $tableRate->name,
                'rate_code' => $tableRate->code,
                'hourly_rate' => $hourlyRate,
                'hourly_rate_formatted' => number_format($hourlyRate) . 'đ',
                'minutes' => $minutes,
                'hours' => round($hours, 2),
                'table_price' => $tablePrice,
                'table_price_formatted' => number_format($tablePrice) . 'đ',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API: Tính giá preview combo
     */
    public function previewComboPrice(Request $request)
    {
        try {
            $items = $request->input('combo_items', []);
            $isTimeCombo = $request->boolean('is_time_combo');
            $tableRateId = $request->input('table_rate_id');
            $playDurationMinutes = $request->input('play_duration_minutes');

            $actualValue = $this->calculateActualValueViaService(
                $items,
                $isTimeCombo,
                $tableRateId,
                $playDurationMinutes
            );

            return response()->json([
                'success' => true,
                'actual_value' => $actualValue,
                'formatted' => number_format($actualValue) . 'đ',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // ============ HELPER METHODS ============

    private function validateComboRequest(Request $request, $id = null): array
    {
        $comboCodeRule = $id
            ? "unique:combos,combo_code,{$id},id,deleted_at,NULL"
            : 'unique:combos,combo_code,NULL,id,deleted_at,NULL';

        $rules = [
            'name' => 'required|string|max:255',
            'combo_code' => "nullable|string|max:50|{$comboCodeRule}",
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'is_time_combo' => 'nullable|boolean',
            'combo_items' => 'required|array|min:1',
            'combo_items.*.product_id' => 'required|exists:products,id',
            'combo_items.*.quantity' => 'required|integer|min:1|max:999',
        ];

        // Chỉ validate time combo fields nếu is_time_combo = true
        if ($request->boolean('is_time_combo')) {
            $rules['play_duration_minutes'] = 'required|integer|min:15|max:1440';
            $rules['table_rate_id'] = 'required|exists:table_rates,id';
        }

        $messages = [
            'name.required' => 'Vui lòng nhập tên combo',
            'combo_code.unique' => 'Mã combo đã tồn tại',
            'price.required' => 'Vui lòng nhập giá bán',
            'price.min' => 'Giá bán phải lớn hơn hoặc bằng 0',
            'combo_items.required' => 'Vui lòng thêm ít nhất 1 sản phẩm',
            'combo_items.min' => 'Combo phải có ít nhất 1 sản phẩm',
            'play_duration_minutes.required' => 'Vui lòng nhập thời gian chơi',
            'play_duration_minutes.min' => 'Thời gian chơi tối thiểu 15 phút',
            'table_rate_id.required' => 'Vui lòng chọn loại bàn',
        ];

        $validated = $request->validate($rules, $messages);

        // Validate products are consumption type only
        foreach ($validated['combo_items'] as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->product_type !== 'Consumption') {
                throw ValidationException::withMessages([
                    'combo_items' => 'Combo chỉ được chứa sản phẩm tiêu dùng!'
                ]);
            }
        }

        return $validated;
    }

    /**
     * Tính actual value SỬ DỤNG TableRate - LÀM TRÒN LÊN
     */
    private function calculateActualValueViaService(
        array $items,
        bool $isTimeCombo,
        ?int $tableRateId,
        ?int $playDurationMinutes
    ): float {
        $total = 0;

        // Tính giá sản phẩm
        foreach ($items as $item) {
            if (!empty($item['product_id']) && !empty($item['quantity'])) {
                $product = Product::find($item['product_id']);
                if ($product && $product->product_type === 'Consumption') {
                    $total += $product->price * $item['quantity'];
                }
            }
        }

        // Tính giá bàn từ TableRate
        if ($isTimeCombo && $tableRateId && $playDurationMinutes) {
            $tableRate = TableRate::find($tableRateId);
            if ($tableRate) {
                $hourlyRate = $tableRate->hourly_rate;
                $hours = $playDurationMinutes / 60;
                $tablePrice = ceil($hourlyRate * $hours); // Làm tròn lên
                $total += $tablePrice;
            }
        }

        // Làm tròn tổng giá lên hàng nghìn
        return ceil($total / 1000) * 1000;
    }

    private function attachComboItems(Combo $combo, array $items): void
    {
        foreach ($items as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }

            $product = Product::find($item['product_id']);
            if (!$product || $product->product_type !== 'Consumption') {
                continue;
            }

            $combo->comboItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
            ]);
        }
    }

    private function syncComboItems(Combo $combo, array $items): void
    {
        $existingIds = [];

        foreach ($items as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }

            $product = Product::find($item['product_id']);
            if (!$product || $product->product_type !== 'Consumption') {
                continue;
            }

            $itemData = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
            ];

            if (!empty($item['id'])) {
                $comboItem = $combo->comboItems()->find($item['id']);
                if ($comboItem) {
                    $comboItem->update($itemData);
                    $existingIds[] = $comboItem->id;
                }
            } else {
                $newItem = $combo->comboItems()->create($itemData);
                $existingIds[] = $newItem->id;
            }
        }

        $combo->comboItems()->whereNotIn('id', $existingIds)->delete();
    }

    private function generateComboCode(): string
    {
        do {
            $code = 'COMBO' . strtoupper(substr(uniqid(), -6));
        } while (Combo::where('combo_code', $code)->exists());

        return $code;
    }
}
