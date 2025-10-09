<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\BillTimeUsage;
use App\Models\Product;
use App\Models\Combo;
use App\Models\Table;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with(['table', 'customer', 'staff', 'details.product']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $bills = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('bills.index', compact('bills'));
    }

    public function create()
    {
        $tables = Table::where('status', 'Available')->get();
        $customers = Customer::all();
        $products = Product::where('status', 'Active')->get();
        $combos = Combo::where('status', 'Active')->with('items.product')->get();

        return view('bills.create', compact('tables', 'customers', 'products', 'combos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_id' => 'sometimes|exists:customers,id'
        ]);

        $table = Table::find($request->table_id);
        
        if (!$table->isAvailable()) {
            return redirect()->back()->with('error', 'Bàn không khả dụng!');
        }

        $bill = Bill::create([
            'bill_number' => Bill::generateBillNumber(),
            'table_id' => $request->table_id,
            'customer_id' => $request->customer_id,
            'staff_id' => $request->user()->id,
            'start_time' => now(),
            'status' => 'Open'
        ]);

        $table->markAsInUse();

        return redirect()->route('bills.show', $bill)->with('success', 'Tạo hóa đơn thành công!');
    }

    public function show(Bill $bill)
    {
        $bill->load([
            'table', 
            'customer', 
            'staff', 
            'details.product', 
            'details.combo',
            'timeUsage',
            'payments'
        ]);

        $products = Product::where('status', 'Active')->get();
        $combos = Combo::where('status', 'Active')->with('items.product')->get();

        return view('bills.show', compact('bill', 'products', 'combos'));
    }

    public function addProduct(Request $request, Bill $bill)
    {
        if (!$bill->isOpen()) {
            return redirect()->back()->with('error', 'Hóa đơn đã đóng, không thể thêm sản phẩm!');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);

        // Kiểm tra tồn kho
        if ($product->category !== 'Service' && $product->stock_quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Số lượng tồn kho không đủ!');
        }

        BillDetail::create([
            'bill_id' => $bill->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_price' => $product->price,
            'original_price' => $product->price,
            'total_price' => $product->price * $request->quantity,
            'is_combo_component' => false
        ]);

        // Trừ tồn kho nếu không phải dịch vụ
        if ($product->category !== 'Service') {
            $product->decreaseStock($request->quantity);
        }

        $bill->calculateTotal();

        return redirect()->back()->with('success', 'Thêm sản phẩm thành công!');
    }

    public function addCombo(Request $request, Bill $bill)
    {
        if (!$bill->isOpen()) {
            return redirect()->back()->with('error', 'Hóa đơn đã đóng, không thể thêm combo!');
        }

        $request->validate([
            'combo_id' => 'required|exists:combos,id'
        ]);

        $combo = Combo::with('items.product')->find($request->combo_id);

        // Kiểm tra tồn kho
        foreach ($combo->items->where('is_required', true) as $item) {
            if ($item->product->category !== 'Service' && 
                $item->product->stock_quantity < $item->quantity) {
                return redirect()->back()->with('error', "Sản phẩm {$item->product->name} không đủ tồn kho!");
            }
        }

        // Tạo bill detail cho combo
        $comboDetail = BillDetail::create([
            'bill_id' => $bill->id,
            'combo_id' => $combo->id,
            'quantity' => 1,
            'unit_price' => $combo->price,
            'original_price' => $combo->actual_value,
            'total_price' => $combo->price,
            'is_combo_component' => false
        ]);

        // Thêm các thành phần bắt buộc
        foreach ($combo->items->where('is_required', true) as $item) {
            BillDetail::create([
                'bill_id' => $bill->id,
                'product_id' => $item->product_id,
                'parent_bill_detail_id' => $comboDetail->id,
                'quantity' => $item->quantity,
                'unit_price' => 0,
                'original_price' => $item->product->price,
                'total_price' => 0,
                'is_combo_component' => true
            ]);

            // Trừ tồn kho
            if ($item->product->category !== 'Service') {
                $item->product->decreaseStock($item->quantity);
            }
        }

        $bill->calculateTotal();

        return redirect()->back()->with('success', 'Thêm combo thành công!');
    }

    public function removeItem(Bill $bill, BillDetail $billDetail)
    {
        if (!$bill->isOpen()) {
            return redirect()->back()->with('error', 'Hóa đơn đã đóng, không thể xóa sản phẩm!');
        }

        // Hoàn trả tồn kho nếu có
        if ($billDetail->product && $billDetail->product->category !== 'Service') {
            $billDetail->product->increaseStock($billDetail->quantity);
        }

        // Xóa các thành phần combo nếu có
        if ($billDetail->combo) {
            $billDetail->components()->delete();
        }

        $billDetail->delete();
        $bill->calculateTotal();

        return redirect()->back()->with('success', 'Xóa sản phẩm thành công!');
    }

    public function addTime(Request $request, Bill $bill)
    {
        if (!$bill->isOpen()) {
            return redirect()->back()->with('error', 'Hóa đơn đã đóng, không thể thêm giờ!');
        }

        $request->validate([
            'hours' => 'required|numeric|min:0.5|max:10'
        ]);

        BillTimeUsage::create([
            'bill_id' => $bill->id,
            'start_time' => now(),
            'end_time' => now()->addHours($request->hours),
            'duration_minutes' => $request->hours * 60,
            'hourly_rate' => $bill->table->hourly_rate,
            'total_price' => $request->hours * $bill->table->hourly_rate
        ]);

        $bill->calculateTotal();

        return redirect()->back()->with('success', 'Thêm giờ thành công!');
    }

    public function applyDiscount(Request $request, Bill $bill)
    {
        if (!$bill->isOpen()) {
            return redirect()->back()->with('error', 'Hóa đơn đã đóng, không thể áp dụng giảm giá!');
        }

        $request->validate([
            'discount_amount' => 'required|numeric|min:0|max:' . $bill->total_amount
        ]);

        $bill->update([
            'discount_amount' => $request->discount_amount
        ]);

        $bill->calculateTotal();

        return redirect()->back()->with('success', 'Áp dụng giảm giá thành công!');
    }

    public function close(Request $request, Bill $bill)
    {
        if (!$bill->isOpen()) {
            return redirect()->back()->with('error', 'Hóa đơn đã đóng!');
        }

        $bill->closeBill();

        return redirect()->route('bills.index')->with('success', 'Đóng hóa đơn thành công!');
    }

    public function processPayment(Request $request, Bill $bill)
    {
        if ($bill->payment_status === 'Paid') {
            return redirect()->back()->with('error', 'Hóa đơn đã được thanh toán!');
        }

        $request->validate([
            'amount' => 'required|numeric|min:' . $bill->final_amount,
            'payment_method' => 'required|in:Cash,Card,Transfer'
        ]);

        // Tạo payment
        $payment = \App\Models\Payment::create([
            'bill_id' => $bill->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'Success',
            'paid_at' => now()
        ]);

        $payment->markAsSuccess();

        return redirect()->route('bills.show', $bill)->with('success', 'Thanh toán thành công!');
    }
}