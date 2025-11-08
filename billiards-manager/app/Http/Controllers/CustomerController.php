<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
  public function index(Request $request)
{
    $query = Customer::query();
    
    // Search filter
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }
    
    // Customer type filter
    if ($request->has('customer_type') && $request->customer_type != '') {
        $query->where('customer_type', $request->customer_type);
    }
    
    // Status filter
    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    }
    
    // QUAN TRỌNG: Thêm dòng này - lấy dữ liệu customers
    $customers = $query->latest()->paginate(10);
    
    // Statistics
    $totalCustomers = Customer::count();
    $vipCount = Customer::where('customer_type', 'VIP')->count();
    $regularCount = Customer::where('customer_type', 'Regular')->count();
    $newCount = Customer::where('customer_type', 'New')->count();
    $activeCount = Customer::where('status', 'Active')->count();
    $inactiveCount = Customer::where('status', 'Inactive')->count();
    $newThisMonthCount = Customer::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count();
    
    // Tạm thời đặt trashedCount = 0
    $trashedCount = 0;
    
    return view('admin.customers.index', compact(
        'customers', // QUAN TRỌNG: phải có biến này
        'totalCustomers',
        'vipCount',
        'regularCount',
        'newCount',
        'activeCount',
        'inactiveCount',
        'newThisMonthCount',
        'trashedCount'
    ));
}

    public function create()
    {
        return view('admin.customers.create');
    }

   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|unique:customers,phone',
        'email' => 'nullable|email|unique:customers,email',
        'customer_type' => 'required|in:New,Regular,VIP',
        'status' => 'required|in:Active,Inactive', // THÊM VALIDATION
        'note' => 'nullable|string',
    ]);

    try {
        Customer::create($request->all());
        
        return redirect()->route('admin.customers.index')
            ->with('success', 'Khách hàng đã được tạo thành công.');
            
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Có lỗi xảy ra khi tạo khách hàng: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
{
    $customer = Customer::findOrFail($id);
    
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|unique:customers,phone,' . $id,
        'email' => 'nullable|email|unique:customers,email,' . $id,
        'customer_type' => 'required|in:New,Regular,VIP',
        'status' => 'required|in:Active,Inactive', // THÊM VALIDATION
        'note' => 'nullable|string',
    ]);

    try {
        $customer->update($request->all());
        
        return redirect()->route('admin.customers.index')
            ->with('success', 'Thông tin khách hàng đã được cập nhật thành công.');
            
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Có lỗi xảy ra khi cập nhật khách hàng: ' . $e->getMessage())
            ->withInput();
    }
}

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            
            return redirect()->route('admin.customers.index')
                ->with('success', 'Khách hàng đã được xóa thành công.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa khách hàng: ' . $e->getMessage());
        }
    }
}