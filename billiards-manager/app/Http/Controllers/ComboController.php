<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function index()
    {
        $combos = Combo::with('items.product')->get();
        return view('combos.index', compact('combos'));
    }

    public function create()
    {
        $products = Product::where('status', 'Active')->get();
        return view('combos.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'required|numeric|min:0',
            'actual_value' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'items' => 'required|array|min:1'
        ]);

        $combo = Combo::create($request->except('items'));

        foreach ($request->items as $item) {
            ComboItem::create([
                'combo_id' => $combo->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'is_required' => $item['is_required'] ?? true,
                'choice_group' => $item['choice_group'] ?? null,
                'max_choices' => $item['max_choices'] ?? null
            ]);
        }

        return redirect()->route('combos.index')->with('success', 'Tạo combo thành công!');
    }

    public function show(Combo $combo)
    {
        $combo->load('items.product');
        return view('combos.show', compact('combo'));
    }

    public function edit(Combo $combo)
    {
        $combo->load('items.product');
        $products = Product::where('status', 'Active')->get();
        return view('combos.edit', compact('combo', 'products'));
    }

    public function update(Request $request, Combo $combo)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'required|numeric|min:0',
            'actual_value' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive'
        ]);

        $combo->update($request->all());

        return redirect()->route('combos.index')->with('success', 'Cập nhật combo thành công!');
    }

    public function destroy(Combo $combo)
    {
        $combo->delete();
        return redirect()->route('combos.index')->with('success', 'Xóa combo thành công!');
    }
}