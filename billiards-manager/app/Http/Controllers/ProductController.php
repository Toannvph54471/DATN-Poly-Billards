<?php

namespace App\Http\Controllers;

use App\Models\Product;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $request)
    {

        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            });
        }


        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }


        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }


        if ($request->filled('is_available')) {
            if ($request->is_available == '1') {
                $query->where('status', 'Active');
            } elseif ($request->is_available == '0') {
                $query->where('status', 'Inactive');
            }
        }

        $totalProducts = Product::count();
        $availableProducts = Product::where('status', 'Active')->count();
        $unavailableProducts = Product::where('status', 'Inactive')->count();
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count();

        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        $products->appends($request->all());

        $categories = Product::select('category')->distinct()->pluck('category');
        $types = Product::select('product_type')->distinct()->pluck('product_type');

        return view('admin.products.index', compact(
            'products',
            'totalProducts',
            'availableProducts',
            'unavailableProducts',
            'lowStockProducts',
            'categories',
            'types'
        ));
    }


    public function create()
    {
        return view('admin.products.created');
    }


    public function edit(string $id)
    {
        $product = Product::find($id);
        return view('admin.products.edit', compact('product'));
    }
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'category' => 'required|string|max:100',
            'type' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'is_available' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240', // 10MB
            'description' => 'nullable|string|max:2000',
        ], [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'product_code.required' => 'Mã sản phẩm là bắt buộc.',
            'product_code.unique' => 'Mã sản phẩm đã tồn tại.',
            'price.required' => 'Giá bán là bắt buộc.',
            'unit.required' => 'Vui lòng chọn đơn vị tính.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.max' => 'Ảnh không được vượt quá 10MB.',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'product_code' => $validated['product_code'],
            'category' => $validated['category'],
            'type' => $validated['type'] ?? null,
            'price' => $validated['price'],
            'cost_price' => $validated['cost_price'] ?? 0,
            'unit' => $validated['unit'],
            'stock_quantity' => $validated['stock_quantity'] ?? 0,
            'min_stock' => $validated['min_stock'] ?? 0,
            'is_available' => $validated['is_available'] ?? 1,
            'description' => $validated['description'] ?? null,
        ]);
        // var_dump($product);die;

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Sản phẩm "' . $product->name . '" đã được thêm thành công!');
    }


    public function show(string $id) {}


    public function update(Request $request, string $id) {}


    public function destroy(string $id) {}
}
