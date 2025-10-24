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
       
    }

   
    public function show(string $id)
    {
        
    }

    
   public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // ✅ Xác thực dữ liệu đầu vào
    $validated = $request->validate([
        'product_code'   => 'required|string|max:255|unique:products,product_code,' . $id,
        'name'           => 'required|string|max:255',
        'category'       => 'required|string',
        'product_type'   => 'nullable|string',
        'price'          => 'required|numeric|min:0',
        'cost_price'     => 'nullable|numeric|min:0',
        'stock_quantity' => 'nullable|integer|min:0',
        'min_stock'      => 'nullable|integer|min:0',
        'unit'           => 'required|string|max:50',
        'is_available'   => 'required|in:0,1',
        'description'    => 'nullable|string',
       
    ]);

    

    // ✅ Cập nhật sản phẩm
    $product->update($validated);

    // ✅ Quay lại danh sách sản phẩm
    return redirect()
        ->route('admin.products.index')
        ->with('success', 'Sản phẩm đã được cập nhật thành công.');
}

    
    public function destroy(string $id)
    {
              $product = Product::findOrFail($id);
    $product->delete(); // xóa mềm (chỉ set deleted_at)

    return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa tạm thời!');
    }
    public function trashed()
{
    $products = Product::onlyTrashed()->paginate(10);
    return view('admin.products.trashed', compact('products'));
}
   public function restore($id)
{
    // Tìm sản phẩm đã bị xóa mềm (có deleted_at)
    $product = Product::withTrashed()->find($id);

    if (!$product) {
        return redirect()->back()->with('error', 'Sản phẩm không tồn tại hoặc chưa bị xóa.');
    }

    // Khôi phục sản phẩm
    $product->restore();

    return redirect()->route('admin.products.trashed')->with('success', 'Sản phẩm đã được khôi phục thành công!');
}
public function forceDelete($id)

{
    $product = Product::onlyTrashed()->findOrFail($id);
    $product->forceDelete();
    // Xóa các bản ghi con liên quan trước
    \DB::table('combo_items')->where('product_id', $product->id)->delete();

    return redirect()->route('admin.products.deleted')
                     ->with('success', 'Đã xóa vĩnh viễn sản phẩm thành công!');
}

    }

