<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Tạm thời return đơn giản để test
        try {
              $customers = Customer::paginate(10);
            
            return view('admin.customers.index', [
                'customers' => $customers,
                'totalCustomers' => $customers->count(),
                'vipCount' => $customers->where('customer_type', 'VIP')->count(),
                'regularCount' => $customers->where('customer_type', 'Regular')->count(),
                'newCount' => $customers->where('customer_type', 'New')->count(),
                'newThisMonthCount' => 0
            ]);
        } catch (\Exception $e) {
            // Nếu vẫn lỗi, return view với dữ liệu rỗng
            return view('admin.customers.index', [
                'customers' => [],
                'totalCustomers' => 0,
                'vipCount' => 0,
                'regularCount' => 0,
                'newCount' => 0,
                'newThisMonthCount' => 0
            ]);
        }
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