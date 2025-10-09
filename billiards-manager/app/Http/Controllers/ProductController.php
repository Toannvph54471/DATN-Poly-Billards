<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $products = $query->orderBy('name')->paginate(20);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Drink,Food,Service',
            'product_type' => 'required|in:Single,Combo',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'unit' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Tạo sản phẩm thành công!');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Drink,Food,Service',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'unit' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:import,export,adjustment'
        ]);

        switch ($request->type) {
            case 'import':
                $product->increaseStock($request->quantity);
                break;
            case 'export':
                $product->decreaseStock($request->quantity);
                break;
            case 'adjustment':
                $product->update(['stock_quantity' => $request->quantity]);
                break;
        }

        return redirect()->back()->with('success', 'Cập nhật tồn kho thành công!');
    }

    public function lowStock()
    {
        $products = Product::lowStock()->get();
        return view('products.low-stock', compact('products'));
    }
}