@extends('admin.layouts.app')

@section('title', 'Chi tiết hóa đơn')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">

    {{-- Tiêu đề + Thẻ thống kê --}}
    <h1 class="text-2xl font-bold mb-6">Chi tiết hóa đơn</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center">
            <div class="bg-red-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-file-invoice text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Mã hóa đơn</p>
                <p class="text-xl font-semibold text-red-700">#{{ $bill->bill_number }}</p>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center">
            <div class="bg-blue-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-user text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Nhân viên</p>
                <p class="text-xl font-semibold text-blue-700">{{ $bill->staff->name ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center">
            <div class="bg-green-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-toggle-on text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Trạng thái</p>
                <span class="px-2 py-1 rounded text-sm font-semibold 
                    {{ $bill->status === 'Open' ? 'bg-green-100 text-green-700' :
                       ($bill->status === 'Paused' ? 'bg-yellow-100 text-yellow-700' :
                       'bg-red-100 text-red-700') }}">
                    {{ $bill->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- Thông tin chung --}}
    <div class="border-t border-gray-200 pt-4 pb-2 mb-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
        <div class="space-y-2">
            <p><strong>Bàn:</strong> {{ $bill->table->name ?? 'N/A' }}</p>
            <p><strong>Thời gian bắt đầu:</strong> {{ $bill->start_time ?? 'N/A' }}</p>
            <p><strong>Thời gian kết thúc:</strong> {{ $bill->end_time ?? 'Chưa kết thúc' }}</p>
        </div>
        <div class="space-y-2">
            <p><strong>Tổng tiền:</strong> 
                <span class="text-blue-700 font-semibold">{{ number_format($bill->total_amount, 0, ',', '.') }} ₫</span>
            </p>
            <p><strong>Thanh toán cuối:</strong> 
                <span class="text-green-700 font-semibold">{{ number_format($bill->final_amount, 0, ',', '.') }} ₫</span>
            </p>
            <p><strong>Ngày tạo:</strong> 
                {{ $bill->created_at ? $bill->created_at->format('d/m/Y H:i') : 'N/A' }}
            </p>
        </div>
    </div>

    {{-- Danh sách sản phẩm --}}
    <h3 class="text-xl font-semibold mb-3 text-gray-800 border-b pb-2">Chi tiết sản phẩm</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Sản phẩm / Combo</th>
                    <th class="px-4 py-2 text-center">Số lượng</th>
                    <th class="px-4 py-2 text-right">Đơn giá</th>
                    <th class="px-4 py-2 text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bill->billDetails as $detail)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="px-4 py-2">
                            @if ($detail->product)
                                {{ $detail->product->name }}
                            @elseif ($detail->combo)
                                {{ $detail->combo->name }}
                            @else
                                <em>Không xác định</em>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">{{ $detail->quantity }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($detail->unit_price, 0, ',', '.') }} ₫</td>
                        <td class="px-4 py-2 text-right font-medium">{{ number_format($detail->total_price, 0, ',', '.') }} ₫</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">Không có sản phẩm nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Nút quay lại --}}
    <div class="mt-6 flex justify-end">
        <a href="{{ route('admin.bills.index') }}" 
           class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg transition">
            <i class="fa-solid fa-arrow-left"></i>
            Quay lại danh sách
        </a>
    </div>
</div>
@endsection

