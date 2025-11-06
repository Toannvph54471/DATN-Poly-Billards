<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PromotionClientController extends Controller
{
    public function index()
    {
        $promotions = Promotion::all(); // Lấy tất cả khuyến mãi
        return view('client.promotions', compact('promotions'));
    }

    public function show($id)
    {
        $promotion = Promotion::findOrFail($id);
        return view('client.promotion_detail', compact('promotion'));
    }
}
