<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('type')) {
            $query->where('customer_type', $request->type);
        }

        $customers = $query->orderBy('total_spent', 'desc')->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'sometimes|email|unique:customers,email',
            'note' => 'sometimes|string'
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Tạo khách hàng thành công!');
    }

    public function show(Customer $customer)
    {
        $customer->load(['bills.table', 'reservations.table']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'sometimes|email|unique:customers,email,' . $customer->id,
            'customer_type' => 'required|in:Regular,VIP,New',
            'note' => 'sometimes|string'
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Cập nhật khách hàng thành công!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Xóa khách hàng thành công!');
    }
}