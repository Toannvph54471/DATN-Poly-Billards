<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
      // Hiển thị danh sách khuyến mãi
    public function index()
    {
        $promotions = Promotion::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.promotions.index', compact('promotions'));
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
            'promotion_code' => 'required|string|max:50|unique:promotions,promotion_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_total_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        Promotion::create($validated);

        return redirect()->route('admin.promotions.index')->with('success', 'Tạo khuyến mãi thành công!');
    }
}
