<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $categories = Category::where(function ($query) use ($search, $status) {
            if ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            }
            if ($status) {
                $query->where('status', $status);
            }
        })->orderByDesc('id')->get();

        $totalCategories = Category::all()->count();
        $categoryActives = Category::where('status', 'active')->count();
        $categoriesInactive = Category::where('status', 'inactive')->count();

        $data =  [
            'categories' => $categories,
            'totalCategories' => $totalCategories,
            'categoryActives' => $categoryActives,
            'categoriesInactive' => $categoriesInactive,
        ];

        return view('admin.categories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:1|max:999999999999999',
            'status' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $category = Category::create($input);

        if (!$category) {
            return back()->withErrors([
                'error' => 'Lỗi xảy ra khi tạo thể loại.'
            ])
                ->withInput($request->all());
        }
        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:1|max:999999999999999',
            'status' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update($input);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công.');
    }
}
