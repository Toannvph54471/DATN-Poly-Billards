<?php

namespace App\Http\Controllers;

use App\Models\BillDetail;
use App\Models\Bill;
use App\Models\Product;
use App\Models\Combo;
use Illuminate\Http\Request;

class BillDetailController extends Controller
{
    public function index()
    {
        $billDetails = BillDetail::with(['bill', 'product', 'combo'])->latest()->get();
        return view('bill-details.index', compact('billDetails'));
    }

    public function create()
    {
        $bills = Bill::all();
        $products = Product::all();
        $combos = Combo::all();
        return view('bill-details.create', compact('bills', 'products', 'combos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'product_id' => 'nullable|exists:products,id',
            'combo_id' => 'nullable|exists:combos,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        BillDetail::create($request->all());

        return redirect()->route('bill-details.index')
            ->with('success', 'Bill detail created successfully.');
    }

    public function show(BillDetail $billDetail)
    {
        return view('bill-details.show', compact('billDetail'));
    }

    public function edit(BillDetail $billDetail)
    {
        $bills = Bill::all();
        $products = Product::all();
        $combos = Combo::all();
        return view('bill-details.edit', compact('billDetail', 'bills', 'products', 'combos'));
    }

    public function update(Request $request, BillDetail $billDetail)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'product_id' => 'nullable|exists:products,id',
            'combo_id' => 'nullable|exists:combos,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $billDetail->update($request->all());

        return redirect()->route('bill-details.index')
            ->with('success', 'Bill detail updated successfully.');
    }

    public function destroy(BillDetail $billDetail)
    {
        $billDetail->delete();

        return redirect()->route('bill-details.index')
            ->with('success', 'Bill detail deleted successfully.');
    }
}