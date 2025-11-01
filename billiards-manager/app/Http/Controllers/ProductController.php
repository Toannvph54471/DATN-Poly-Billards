<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->filled('is_available')) {
            $status = $request->is_available == '1' ? 'Active' : 'Inactive';
            $query->where('status', $status);
        }

        $totalProducts = Product::count();
        $availableProducts = Product::where('status', 'Active')->count();
        $unavailableProducts = Product::where('status', 'Inactive')->count();
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count();

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $products->appends($request->all());

        $categories = Category::where('type', 'product')->pluck('name', 'id');
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
        $categories = Category::where('type', 'product')->pluck('name', 'id');
        return view('admin.products.created', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code'   => 'required|string|max:255|unique:products',
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'product_type'   => 'required|in:Service,Consumption',
            'price'          => 'required|numeric|min:0',
            'cost_price'     => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'unit'           => 'required|string|max:50',
            'status'         => 'required|in:Active,Inactive',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'Active')
            ->limit(3)
            ->get();

        return view('admin.products.show', compact('product', 'relatedProducts'));
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('type', 'product')->pluck('name', 'id');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_code'   => 'required|string|max:255|unique:products,product_code,' . $id,
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'product_type'   => 'required|in:Service,Consumption',
            'price'          => 'required|numeric|min:0',
            'cost_price'     => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'unit'           => 'required|string|max:50',
            'status'         => 'required|in:Active,Inactive',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Soft delete

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được xóa tạm thời!');
    }

    public function trashed()
    {
        $products = Product::onlyTrashed()->paginate(10);
        return view('admin.products.trashed', compact('products'));
    }

    public function restore(string $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.products.trashed')
            ->with('success', 'Sản phẩm đã được khôi phục thành công!');
    }

    public function forceDelete(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        // Xóa ảnh nếu có
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Xóa liên kết combo_items
        \DB::table('combo_items')->where('product_id', $product->id)->delete();

        $product->forceDelete();

        return redirect()->route('admin.products.trashed')
            ->with('success', 'Sản phẩm đã được xóa vĩnh viễn!');
    }
}
