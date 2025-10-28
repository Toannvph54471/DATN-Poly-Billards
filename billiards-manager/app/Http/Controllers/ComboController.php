<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComboController extends Controller
{
    public function index(Request $request)
    {
        $query = Combo::query();

        // Lọc theo mã combo
        if ($request->filled('code')) {
            $query->where('combo_code', 'like', '%' . $request->input('code') . '%');
        }

        // Lọc theo tên combo
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Thống kê
        $totalCombos = Combo::count();
        $activeCombos = Combo::where('status', 'Active')->count();
        $inactiveCombos = Combo::where('status', 'Inactive')->count();

        // Lấy danh sách combo với phân trang, giữ các tham số truy vấn
        $combos = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.combos.index', compact('combos', 'totalCombos', 'activeCombos', 'inactiveCombos'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.combos.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'combo_code' => 'required|string|max:50|unique:combos,combo_code',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'actual_value' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'combo_items.*.product_id' => 'nullable|exists:products,id',
            'combo_items.*.quantity' => 'nullable|integer|min:1',
        ], [
            'name.required' => 'Vui lòng nhập tên combo.',
            'combo_code.required' => 'Vui lòng nhập mã combo.',
            'combo_code.unique' => 'Mã combo này đã tồn tại.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là số.',
            'actual_value.required' => 'Vui lòng nhập giá trị thực.',
            'actual_value.numeric' => 'Giá trị thực phải là số.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'combo_items.*.product_id.exists' => 'Sản phẩm không hợp lệ.',
            'combo_items.*.quantity.min' => 'Số lượng tối thiểu là 1.',
        ]);

        // Nếu validate thành công thì lưu
        $combo = Combo::create([
            'name' => $validated['name'],
            'combo_code' => $validated['combo_code'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'actual_value' => $validated['actual_value'],
            'status' => $validated['status'],
        ]);

        // Lưu combo_items
        if ($request->has('combo_items')) {
            foreach ($request->combo_items as $item) {
                if (!empty($item['product_id'])) {
                    $combo->comboItems()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'] ?? 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.combos.index')->with('success', 'Tạo combo thành công!');
    }


    public function show($id)
    {
        // Lấy combo kèm theo các sản phẩm của nó
        $combo = Combo::with(['comboItems.product'])->findOrFail($id);

        $actualValue = 0;
        foreach ($combo->comboItems as $item) {
            if ($item->product) {
                $actualValue += $item->product->price * $item->quantity;
            }
        }
        return view('admin.combos.show', compact('combo', 'actualValue'));
    }

    public function edit($id)
    {
        $combo = Combo::with('comboItems.product')->findOrFail($id);
        $products = Product::all();

        return view('admin.combos.edit', compact('combo', 'products'));
    }

    public function update(Request $request, $id)
    {
        $combo = Combo::with('comboItems')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'combo_code' => 'required|string|max:50|unique:combos,combo_code,' . $combo->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'actual_value' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'combo_items' => 'required|array|min:1',
            'combo_items.*.product_id' => 'required|exists:products,id',
            'combo_items.*.quantity' => 'required|integer|min:1',
            'combo_items.*.id' => 'nullable|exists:combo_items,id', // Thêm validation cho id
        ], [
            'name.required' => 'Vui lòng nhập tên combo.',
            'combo_code.required' => 'Vui lòng nhập mã combo.',
            'combo_code.unique' => 'Mã combo này đã tồn tại.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là số.',
            'actual_value.required' => 'Vui lòng nhập giá trị thực.',
            'actual_value.numeric' => 'Giá trị thực phải là số.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'combo_items.required' => 'Combo phải có ít nhất 1 sản phẩm.',
            'combo_items.min' => 'Combo phải có ít nhất 1 sản phẩm.',
            'combo_items.*.product_id.required' => 'Vui lòng chọn sản phẩm.',
            'combo_items.*.product_id.exists' => 'Sản phẩm không hợp lệ.',
            'combo_items.*.quantity.required' => 'Vui lòng nhập số lượng.',
            'combo_items.*.quantity.min' => 'Số lượng tối thiểu là 1.',
            'combo_items.*.id.exists' => 'ID sản phẩm combo không hợp lệ.',
        ]);

        // ✅ Cập nhật combo chính
        $combo->update([
            'name' => $validated['name'],
            'combo_code' => $validated['combo_code'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'actual_value' => $validated['actual_value'],
            'status' => $validated['status'],
        ]);

        // ✅ Xử lý combo items thông minh
        $existingItemIds = [];

        foreach ($request->combo_items as $index => $item) {
            if (!empty($item['product_id'])) {
                // Nếu có ID (item cũ) thì update, không thì tạo mới
                if (!empty($item['id'])) {
                    $comboItem = $combo->comboItems()->where('id', $item['id'])->first();
                    if ($comboItem) {
                        $comboItem->update([
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'] ?? 1,
                            'sort_order' => $index, // Thêm sort_order nếu cần
                        ]);
                        $existingItemIds[] = $comboItem->id;
                    }
                } else {
                    // Tạo mới
                    $newItem = $combo->comboItems()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'] ?? 1,
                        'sort_order' => $index, // Thêm sort_order nếu cần
                    ]);
                    $existingItemIds[] = $newItem->id;
                }
            }
        }

        // ✅ Xóa các item không còn tồn tại
        $combo->comboItems()->whereNotIn('id', $existingItemIds)->delete();

        return redirect()->route('admin.combos.index')->with('success', 'Cập nhật combo thành công!');
    }

    public function destroy($id)
    {
        $combo = Combo::findOrFail($id);
        $combo->delete();

        return redirect()->route('admin.combos.index', request()->query())->with('success', 'Combo đã được xóa (soft delete).');
    }
    public function trash()
{
    $combos = Combo::onlyTrashed()->latest()->paginate(10);

    return view('admin.combos.trash', compact('combos'));
}

/**
 * Khôi phục combo đã bị xóa mềm
 */
public function restore($id)
{
    $combo = Combo::onlyTrashed()->findOrFail($id);
    $combo->restore();

    return redirect()->route('admin.combos.trash')->with('success', 'Đã khôi phục combo thành công!');
}

/**
 * Xóa vĩnh viễn combo
 */
public function forceDelete($id)
{
    $combo = Combo::onlyTrashed()->findOrFail($id);
    $combo->forceDelete();

    return redirect()->route('admin.combos.trash')->with('success', 'Đã xóa vĩnh viễn combo!');
}
}
