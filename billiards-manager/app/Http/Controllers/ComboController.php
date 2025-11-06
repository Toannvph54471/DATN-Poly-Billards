<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ComboController extends Controller
{
    public function index(Request $request)
    {
        $query = Combo::with(['comboItems.product', 'tableCategory']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('combo_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $isTimeCombo = $request->input('type') === 'time';
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
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $tableCategories = Category::where('type', 'table')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.combos.create', compact('products', 'tableCategories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCombo($request);

        // Kiểm tra giới hạn combo thời gian
        if ($validated['is_time_combo']) {
            $existingTimeCombo = Combo::where('is_time_combo', true)->first();
            if ($existingTimeCombo) {
                return back()->withErrors([
                    'is_time_combo' => 'Hệ thống chỉ cho phép tồn tại 1 combo thời gian. Combo hiện tại: ' . $existingTimeCombo->name
                ])->withInput();
            }
        }

        try {
            return DB::transaction(function () use ($validated, $request) {
                // Tính giá trị thực
                $actualValue = $this->calculateActualValue($request->combo_items);

                // Tạo combo
                $combo = Combo::create([
                    'combo_code' => $validated['combo_code'],
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'price' => $validated['price'],
                    'actual_value' => $actualValue,
                    'status' => $validated['status'] ?? 'active',
                    'is_time_combo' => $validated['is_time_combo'] ?? false,
                    'play_duration_minutes' => $validated['play_duration_minutes'] ?? null,
                    'table_category_id' => $validated['table_category_id'] ?? null,
                ]);

                // Thêm sản phẩm vào combo
                $this->syncComboItems($combo, $request->combo_items);

                return redirect()
                    ->route('admin.combos.show', $combo->id)
                    ->with('success', 'Tạo combo thành công!');
            });
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
            'tableCategory',
            'timeUsages' => function ($query) {
                $query->where('is_expired', false)
                    ->whereNull('end_time')
                    ->latest();
            }
        ])->findOrFail($id);

        $activeSession = $combo->is_time_combo
            ? $combo->getCurrentTimeUsage()
            : null;

        return view('admin.combos.show', compact('combo', 'activeSession'));
    }

    public function edit($id)
    {
        $combo = Combo::with('comboItems.product')->findOrFail($id);

        $products = Product::where('status', 'active')->orderBy('name')->get();
        $tableCategories = Category::where('type', 'table')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.combos.edit', compact('combo', 'products', 'tableCategories'));
    }

    public function update(Request $request, $id)
    {
        $combo = Combo::findOrFail($id);
        $validated = $this->validateCombo($request, $id);

        // Kiểm tra combo thời gian
        if ($validated['is_time_combo']) {
            $existingTimeCombo = Combo::where('is_time_combo', true)
                ->where('id', '!=', $id)
                ->first();

            if ($existingTimeCombo) {
                return back()->withErrors([
                    'is_time_combo' => 'Hệ thống chỉ cho phép 1 combo thời gian. Combo hiện tại: ' . $existingTimeCombo->name
                ])->withInput();
            }
        }

        // Cảnh báo nếu có session đang chạy
        if ($combo->is_time_combo && $combo->timeUsages()->where('is_expired', false)->exists()) {
            session()->flash('warning', 'Combo này đang có session hoạt động. Thay đổi có thể ảnh hưởng đến session hiện tại.');
        }

        try {
            return DB::transaction(function () use ($combo, $validated, $request) {
                $actualValue = $this->calculateActualValue($request->combo_items);

                $combo->update([
                    'combo_code' => $validated['combo_code'],
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'price' => $validated['price'],
                    'actual_value' => $actualValue,
                    'status' => $validated['status'] ?? 'active',
                    'is_time_combo' => $validated['is_time_combo'] ?? false,
                    'play_duration_minutes' => $validated['play_duration_minutes'] ?? null,
                    'table_category_id' => $validated['table_category_id'] ?? null,
                ]);

                $this->syncComboItems($combo, $request->combo_items);

                return redirect()
                    ->route('admin.combos.show', $combo->id)
                    ->with('success', 'Cập nhật combo thành công!');
            });
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $combo = Combo::findOrFail($id);

        // Kiểm tra xem combo có đang được sử dụng không
        if ($combo->timeUsages()->where('is_expired', false)->exists()) {
            return back()->withErrors([
                'error' => 'Không thể xóa combo đang có session hoạt động'
            ]);
        }

        $combo->delete();

        return redirect()
            ->route('admin.combos.index')
            ->with('success', 'Xóa combo thành công (soft delete)');
    }

    public function trash()
    {
        $combos = Combo::onlyTrashed()
            ->with(['comboItems.product', 'tableCategory'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('admin.combos.trash', compact('combos'));
    }

    public function restore($id)
    {
        $combo = Combo::onlyTrashed()->findOrFail($id);
        $combo->restore();

        return redirect()
            ->route('admin.combos.trash')
            ->with('success', 'Khôi phục combo thành công!');
    }

    public function forceDelete($id)
    {
        $combo = Combo::onlyTrashed()->findOrFail($id);
        $combo->forceDelete();

        return redirect()
            ->route('admin.combos.trash')
            ->with('success', 'Xóa vĩnh viễn combo thành công!');
    }

    // ============ HELPER METHODS ============

    private function validateCombo(Request $request, $id = null)
    {
        $comboCodeRule = $id
            ? "unique:combos,combo_code,{$id},id,deleted_at,NULL"
            : 'unique:combos,combo_code,NULL,id,deleted_at,NULL';

        $rules = [
            'name' => 'required|string|max:255',
            'combo_code' => "required|string|max:50|{$comboCodeRule}",
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'is_time_combo' => 'nullable|boolean',
            'combo_items' => 'required|array|min:1',
            'combo_items.*.product_id' => 'required|exists:products,id',
            'combo_items.*.quantity' => 'required|integer|min:1|max:999',
        ];

        // Nếu là combo thời gian
        if ($request->boolean('is_time_combo')) {
            $rules['play_duration_minutes'] = 'required|integer|min:15|max:1440';
            $rules['table_category_id'] = 'nullable|exists:categories,id';
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Vui lòng nhập tên combo',
            'combo_code.required' => 'Vui lòng nhập mã combo',
            'combo_code.unique' => 'Mã combo đã tồn tại',
            'price.required' => 'Vui lòng nhập giá bán',
            'price.min' => 'Giá bán phải lớn hơn hoặc bằng 0',
            'combo_items.required' => 'Vui lòng thêm ít nhất 1 sản phẩm',
            'combo_items.min' => 'Combo phải có ít nhất 1 sản phẩm',
            'play_duration_minutes.required' => 'Vui lòng nhập thời gian chơi',
            'play_duration_minutes.min' => 'Thời gian chơi tối thiểu 15 phút',
        ]);

        // Kiểm tra số lượng sản phẩm dịch vụ (giờ chơi)
        $serviceCount = 0;
        foreach ($request->combo_items as $item) {
            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product && $product->product_type === 'Service') {
                    $serviceCount++;
                }
            }
        }

        if ($serviceCount > 1) {
            throw ValidationException::withMessages([
                'combo_items' => 'Combo chỉ được phép có tối đa 1 sản phẩm dịch vụ (giờ chơi)'
            ]);
        }

        // Đảm bảo giá bán <= giá trị thực
        $actualValue = $this->calculateActualValue($request->combo_items);
        if ($validated['price'] > $actualValue) {
            throw ValidationException::withMessages([
                'price' => 'Giá bán không được lớn hơn giá trị thực (' . number_format($actualValue) . 'đ)'
            ]);
        }

        return $validated;
    }

    private function calculateActualValue(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            if (!empty($item['product_id']) && !empty($item['quantity'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $total += $product->price * $item['quantity'];
                }
            }
        }

        return $total;
    }

    private function syncComboItems(Combo $combo, array $items): void
    {
        $existingIds = [];

        foreach ($items as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                continue;
            }

            $product = Product::find($item['product_id']);
            if (!$product) {
                continue;
            }

            $itemData = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
            ];

            // Cập nhật hoặc tạo mới
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

        // Xóa các item không còn tồn tại
        $combo->comboItems()->whereNotIn('id', $existingIds)->delete();
    }
}
