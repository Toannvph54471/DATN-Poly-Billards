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
            $query->where(function($q) use ($search) {
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
}
