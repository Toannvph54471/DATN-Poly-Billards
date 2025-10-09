<?php

namespace App\Http\Controllers;

use App\Models\ComboItem;
use App\Models\Combo;
use App\Models\Product;
use Illuminate\Http\Request;

class ComboItemController extends Controller
{
    public function index()
    {
        $comboItems = ComboItem::with(['combo', 'product'])->latest()->get();
        return view('combo-items.index', compact('comboItems'));
    }

    public function create()
    {
        $combos = Combo::all();
        $products = Product::all();
        return view('combo-items.create', compact('combos', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'combo_id' => 'required|exists:combos,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        ComboItem::create($request->all());

        return redirect()->route('combo-items.index')
            ->with('success', 'Combo item created successfully.');
    }

    public function show(ComboItem $comboItem)
    {
        return view('combo-items.show', compact('comboItem'));
    }

    public function edit(ComboItem $comboItem)
    {
        $combos = Combo::all();
        $products = Product::all();
        return view('combo-items.edit', compact('comboItem', 'combos', 'products'));
    }

    public function update(Request $request, ComboItem $comboItem)
    {
        $request->validate([
            'combo_id' => 'required|exists:combos,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $comboItem->update($request->all());

        return redirect()->route('combo-items.index')
            ->with('success', 'Combo item updated successfully.');
    }

    public function destroy(ComboItem $comboItem)
    {
        $comboItem->delete();

        return redirect()->route('combo-items.index')
            ->with('success', 'Combo item deleted successfully.');
    }
}