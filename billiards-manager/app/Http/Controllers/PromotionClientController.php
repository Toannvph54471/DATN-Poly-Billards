<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionClientController extends Controller
{
    public function index()
    {
        $promotions = Promotion::all(); // Lấy tất cả khuyến mãi
        return view('client.promotion.index', compact('promotions'));
    }

    public function show($id)
    {
        $promotion = Promotion::findOrFail($id);
        return view('client.promotion.show', compact('promotion'));
    }
}
