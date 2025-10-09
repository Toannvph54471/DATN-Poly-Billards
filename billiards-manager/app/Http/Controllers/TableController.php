<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Bill;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::with('currentBill.customer')->get();
        return view('tables.index', compact('tables'));
    }

    public function create()
    {
        return view('tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|unique:tables',
            'type' => 'required|in:VIP,Regular',
            'hourly_rate' => 'required|numeric|min:0'
        ]);

        Table::create($request->all());

        return redirect()->route('tables.index')->with('success', 'Tạo bàn thành công!');
    }

    public function show(Table $table)
    {
        $table->load('currentBill.customer', 'bills.customer');
        return view('tables.show', compact('table'));
    }

    public function edit(Table $table)
    {
        return view('tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|unique:tables,table_number,' . $table->id,
            'type' => 'required|in:VIP,Regular',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:Available,InUse,Reserved,Maintenance'
        ]);

        $table->update($request->all());

        return redirect()->route('tables.index')->with('success', 'Cập nhật bàn thành công!');
    }

    public function destroy(Table $table)
    {
        if ($table->status === 'InUse') {
            return redirect()->back()->with('error', 'Không thể xóa bàn đang được sử dụng!');
        }

        $table->delete();

        return redirect()->route('tables.index')->with('success', 'Xóa bàn thành công!');
    }

    public function openTable(Request $request, Table $table)
    {
        if (!$table->isAvailable()) {
            return redirect()->back()->with('error', 'Bàn không khả dụng!');
        }

        $bill = Bill::create([
            'bill_number' => Bill::generateBillNumber(),
            'table_id' => $table->id,
            'staff_id' => $request->user()->id,
            'start_time' => now(),
            'status' => 'Open'
        ]);

        $table->markAsInUse();

        return redirect()->route('bills.show', $bill)->with('success', 'Mở bàn thành công!');
    }

    public function closeTable(Table $table)
    {
        if (!$table->isInUse()) {
            return redirect()->back()->with('error', 'Bàn không đang được sử dụng!');
        }

        $bill = $table->currentBill;
        if ($bill) {
            $bill->closeBill();
        }

        return redirect()->route('tables.index')->with('success', 'Đóng bàn thành công!');
    }
}