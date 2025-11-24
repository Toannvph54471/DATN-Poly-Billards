@extends('admin.layouts.app')

@section('title', 'Chi tiết hóa đơn - ' . $bill->bill_number)

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Chi tiết hóa đơn: {{ $bill->bill_number }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.bills.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
            </a>
            @if($bill->status === 'Open')
            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <i class="fa-solid fa-stop mr-1"></i> Đóng hóa đơn
            </button>
            @endif
        </div>
    </div>

    {{-- Thông tin cơ bản --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold text-lg mb-3">Thông tin hóa đơn</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Mã hóa đơn:</span>
                    <span class="font-medium">{{ $bill->bill_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Bàn:</span>
                    <span class="font-medium">{{ $bill->table->table_name }} ({{ $bill->table->table_number }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Nhân viên:</span>
                    <span class="font-medium">{{ $bill->staff->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Khách hàng:</span>
                    <span class="font-medium">{{ $bill->user->name ?? 'Khách vãng lai' }}</span>
                </div>
            </div>
        </div>

        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold text-lg mb-3">Trạng thái & Thanh toán</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="px-2 py-1 rounded text-sm
                        {{ $bill->status === 'Open' ? 'bg-green-100 text-green-700' :
                           ($bill->status === 'quick' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-gray-100 text-gray-700') }}">
                        {{ $bill->status }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Thanh toán:</span>
                    <span class="px-2 py-1 rounded text-sm
                        {{ $bill->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-green-100 text-green-700' }}">
                        {{ $bill->payment_status }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phương thức:</span>
                    <span class="font-medium">{{ $bill->payment_method ?? 'Chưa chọn' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Thời gian bắt đầu:</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($bill->start_time)->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chi tiết sử dụng thời gian --}}
    @if($bill->billTimeUsages->count() > 0)
    <div class="border border-gray-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-lg mb-3">Chi tiết sử dụng thời gian</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Bắt đầu</th>
                        <th class="px-4 py-2 text-left">Kết thúc</th>
                        <th class="px-4 py-2 text-left">Thời gian (phút)</th>
                        <th class="px-4 py-2 text-left">Giá/giờ</th>
                        <th class="px-4 py-2 text-left">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->billTimeUsages as $timeUsage)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($timeUsage->start_time)->format('H:i d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $timeUsage->end_time ? \Carbon\Carbon::parse($timeUsage->end_time)->format('H:i d/m/Y') : 'Đang sử dụng' }}</td>
                        <td class="px-4 py-2">{{ $timeUsage->duration_minutes ?? 'Đang tính' }}</td>
                        <td class="px-4 py-2">{{ number_format($timeUsage->hourly_rate) }} ₫/h</td>
                        <td class="px-4 py-2 font-medium">{{ number_format($timeUsage->total_price) }} ₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Chi tiết sản phẩm/combo --}}
    @if($bill->billDetails->count() > 0)
    <div class="border border-gray-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-lg mb-3">Chi tiết đặt hàng</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Sản phẩm/Combo</th>
                        <th class="px-4 py-2 text-left">Số lượng</th>
                        <th class="px-4 py-2 text-left">Đơn giá</th>
                        <th class="px-4 py-2 text-left">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->billDetails->where('is_combo_component', false) as $detail)
                    <tr class="border-t">
                        <td class="px-4 py-2">
                            @if($detail->product)
                                {{ $detail->product->name }}
                                <span class="text-sm text-gray-500">({{ $detail->product->product_code }})</span>
                            @elseif($detail->combo)
                                {{ $detail->combo->name }}
                                <span class="text-sm text-gray-500">(Combo)</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $detail->quantity }}</td>
                        <td class="px-4 py-2">{{ number_format($detail->unit_price) }} ₫</td>
                        <td class="px-4 py-2 font-medium">{{ number_format($detail->total_price) }} ₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Tổng kết --}}
    <div class="border border-gray-200 rounded-lg p-4">
        <h3 class="font-semibold text-lg mb-3">Tổng kết thanh toán</h3>
        <div class="max-w-md ml-auto space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Tổng tiền:</span>
                <span class="font-medium">{{ number_format($bill->total_amount) }} ₫</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Giảm giá:</span>
                <span class="font-medium text-red-600">-{{ number_format($bill->discount_amount) }} ₫</span>
            </div>
            <div class="flex justify-between border-t pt-2">
                <span class="text-gray-600 font-semibold">Thành tiền:</span>
                <span class="font-bold text-lg text-blue-600">{{ number_format($bill->final_amount) }} ₫</span>
            </div>
        </div>
    </div>
</div>
@endsection