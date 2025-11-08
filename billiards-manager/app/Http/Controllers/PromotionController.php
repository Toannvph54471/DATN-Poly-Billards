<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    // Hiển thị danh sách khuyến mãi
    public function index(Request $request)
    {
        // Lấy danh sách khuyến mại với phân trang
        $query = Promotion::query();
        $now = now();

        // Tìm kiếm theo mã hoặc tên
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('promotion_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Lọc theo loại giảm giá
        if ($request->has('discount_type') && $request->discount_type != '') {
            $query->where('discount_type', $request->discount_type);
        }
        // Lọc theo trạng thái (0, 1, 'ongoing', 'upcoming')
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'ongoing') {
                // Đang diễn ra: status = 1 và trong thời gian hiệu lực
                $query->where('status', 1)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            } elseif ($request->status === 'upcoming') {
                // Sắp diễn ra: status = 1 và chưa đến ngày bắt đầu
                $query->where('status', 1)
                    ->where('start_date', '>', $now);
            } else {
                // Lọc theo status 0 hoặc 1
                $query->where('status', $request->status);
            }
        }

        // Sắp xếp theo ngày tạo mới nhất
        $promotions = $query->orderBy('created_at', 'desc')->paginate(10);

        // Trong hàm index() của PromotionController
        $totalPromotions = Promotion::count();
        $activePromotions = Promotion::where('status', 1)->count();
        $inactivePromotions = Promotion::where('status', 0)->count();

        // Tính số khuyến mại đang diễn ra (status = 1 và trong thời gian hiệu lực)

        $ongoingPromotions = Promotion::where('status', 1)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();
        return view('admin.promotions.index', compact(
            'promotions',
            'totalPromotions',
            'activePromotions',
            'inactivePromotions',
            'ongoingPromotions'
        ));
    }
    // Form tạo mới
    public function create()
    {
        return view('admin.promotions.create');
    }

    // Lưu khuyến mãi mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'promotion_code'   => 'required|string|max:50|unique:promotions,promotion_code',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'discount_type'    => 'required|in:percent,fixed',
            'discount_value'   => [
                'required',
                'numeric',
                'min:0', // không âm
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Giá trị giảm theo phần trăm không được vượt quá 100%.');
                    }
                },
            ],
            'min_total_amount' => 'nullable|numeric|min:0', // không âm
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'status'           => 'required|in:active,inactive',
        ]);

        Promotion::create($validated);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Đã thêm chương trình khuyến mãi thành công!');
    }
    public function show($id)
    {
        $promotion = Promotion::findOrFail($id);
        return view('admin.promotions.show', compact('promotion'));
    }
    // Form tạo mới
    public function edit($id)
    {
        $promotion = Promotion::findOrFail($id);
        // dd($promotion);
        return view('admin.promotions.edit', compact('promotion'));
    }

    // Lưu khuyến mãi mới
    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $validated = $request->validate([
            'promotion_code'   => 'required|string|max:50|unique:promotions,promotion_code,' . $promotion->id,
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'discount_type'    => 'required|in:percent,fixed',
            'discount_value'   => [
                'required',
                'numeric',
                'min:0', // không âm
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Giá trị giảm theo phần trăm không được vượt quá 100%.');
                    }
                },
            ],
            'min_total_amount' => 'nullable|numeric|min:0', // không âm
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'status'           => 'required|in:active,inactive',
        ]);

        // Cập nhật dữ liệu
        $promotion->update($validated);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Đã cập nhật chương trình khuyến mại thành công!');
    }

    public function destroy($id)
    {
        $product = Promotion::findOrFail($id);
        if (!$product) {
            return redirect()->route('admin.promotions.index')->with('error', 'Chương trình khuyến mại không tìm thấy!');
        }
        $product->delete();

        return redirect()->route('admin.promotions.index')->with('success', 'Chương trình khuyến mại đã được xóa tạm thời!');
    }

    public function trashed()
    {
        $promotions = Promotion::onlyTrashed()->paginate(10);
        return view('admin.promotions.trashed', compact('promotions'));
    }

    public function restore($id)
    {
        $promotion = Promotion::withTrashed()->findOrFail($id);
        if (!$promotion) {
            return redirect()->route('admin.promotions.index')->with('error', 'Chương trình khuyến mại không tìm thấy!');
        }
        $promotion->restore();

        return redirect()->route('admin.promotions.index')->with('success', 'Chương trình khuyến mại đã được khôi phục thành công!');
    }

    public function forceDelete($id)
    {
        $promotion = Promotion::withTrashed()->findOrFail($id);
        if (!$promotion) {
            return redirect()->route('admin.promotions.index')->with('error', 'Chương trình khuyến mại không tìm thấy!');
        }
        $promotion->forceDelete();

        return redirect()->route('admin.promotions.index')->with('success', 'Chương trình khuyến mại đã được xoa tạm thời!');
    }
}
