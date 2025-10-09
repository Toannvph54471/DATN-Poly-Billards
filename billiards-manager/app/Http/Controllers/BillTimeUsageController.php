<?php

namespace App\Http\Controllers;

use App\Models\BillTimeUsage;
use App\Models\Bill;
use Illuminate\Http\Request;

class BillTimeUsageController extends Controller
{
    public function index()
    {
        $billTimeUsages = BillTimeUsage::with('bill')->latest()->get();
        return view('bill-time-usage.index', compact('billTimeUsages'));
    }

    public function create()
    {
        $bills = Bill::all();
        return view('bill-time-usage.create', compact('bills'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
            'total_minutes' => 'nullable|integer|min:0',
            'time_charge' => 'required|numeric|min:0',
        ]);

        BillTimeUsage::create($request->all());

        return redirect()->route('bill-time-usage.index')
            ->with('success', 'Bill time usage created successfully.');
    }

    public function show(BillTimeUsage $billTimeUsage)
    {
        return view('bill-time-usage.show', compact('billTimeUsage'));
    }

    public function edit(BillTimeUsage $billTimeUsage)
    {
        $bills = Bill::all();
        return view('bill-time-usage.edit', compact('billTimeUsage', 'bills'));
    }

    public function update(Request $request, BillTimeUsage $billTimeUsage)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
            'total_minutes' => 'nullable|integer|min:0',
            'time_charge' => 'required|numeric|min:0',
        ]);

        $billTimeUsage->update($request->all());

        return redirect()->route('bill-time-usage.index')
            ->with('success', 'Bill time usage updated successfully.');
    }

    public function destroy(BillTimeUsage $billTimeUsage)
    {
        $billTimeUsage->delete();

        return redirect()->route('bill-time-usage.index')
            ->with('success', 'Bill time usage deleted successfully.');
    }
}