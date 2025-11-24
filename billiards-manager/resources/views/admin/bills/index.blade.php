@extends('admin.layouts.app')

@section('title', 'Quản lý hóa đơn')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Quản lý hóa đơn</h1>

    {{-- Thống kê tổng quan --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center">
            <div class="bg-blue-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-file-invoice text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Tổng hóa đơn</p>
                <p class="text-xl font-semibold">{{ $bills->total() }}</p>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center">
            <div class="bg-green-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-play text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Đang mở</p>
                <p class="text-xl font-semibold">{{ $bills->where('status', 'Open')->count() }}</p>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center">
            <div class="bg-yellow-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-pause text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Quick Service</p>
                <p class="text-xl font-semibold">{{ $bills->where('status', 'quick')->count() }}</p>
            </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center">
            <div class="bg-red-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-stop text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Đã đóng</p>
                <p class="text-xl font-semibold">{{ $bills->where('status', 'Closed')->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <div class="flex flex-col md:flex-row items-center gap-3 mb-6">
        <input type="text" placeholder="Tìm kiếm mã hóa đơn, bàn..."
               class="w-full md:w-1/3 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
        
        <select class="border border-gray-300 rounded-lg px-3 py-2">
            <option value="">Tất cả trạng thái</option>
            <option value="Open">Đang mở</option>
            <option value="Closed">Đã đóng</option>
            <option value="quick">Quick Service</option>
        </select>

        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fa-solid fa-filter mr-1"></i> Lọc
        </button>
    </div>

    {{-- Bảng hóa đơn --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Mã hóa đơn</th>
                    <th class="px-4 py-2 text-left">Bàn</th>
                    <th class="px-4 py-2 text-left">Nhân viên</th>
                    <th class="px-4 py-2 text-left">Tổng tiền</th>
                    <th class="px-4 py-2 text-left">Trạng thái</th>
                    <th class="px-4 py-2 text-left">Thanh toán</th>
                    <th class="px-4 py-2 text-left">Ngày tạo</th>
                    <th class="px-4 py-2 text-left">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bills as $bill)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium">{{ $bill->bill_number }}</td>
                        <td class="px-4 py-2">
                            <span class="font-medium">{{ $bill->table->table_name ?? 'N/A' }}</span>
                            <br>
                            <span class="text-sm text-gray-500">{{ $bill->table->table_number ?? '' }}</span>
                        </td>
                        <td class="px-4 py-2">{{ $bill->staff->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2 font-medium">
                            {{ number_format($bill->final_amount) }} ₫
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-sm
                                {{ $bill->status === 'Open' ? 'bg-green-100 text-green-700' :
                                   ($bill->status === 'quick' ? 'bg-yellow-100 text-yellow-700' :
                                   'bg-gray-100 text-gray-700') }}">
                                @if($bill->status === 'Open')
                                    Đang mở
                                @elseif($bill->status === 'quick') 
                                    Quick
                                @else
                                    Đã đóng
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-sm
                                {{ $bill->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-700' :
                                   'bg-green-100 text-green-700' }}">
                                {{ $bill->payment_status === 'Pending' ? 'Chờ thanh toán' : 'Đã thanh toán' }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            {{ $bill->created_at ? $bill->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.bills.show', $bill->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors"
                                   title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if($bill->status === 'Open')
                                <a href="#" 
                                   class="text-green-600 hover:text-green-800 transition-colors"
                                   title="Tiếp tục">
                                    <i class="fa-solid fa-play"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <i class="fa-solid fa-file-invoice text-3xl mb-2 text-gray-300"></i>
                            <p>Không có hóa đơn nào.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="mt-6">
        {{ $bills->links() }}
    </div>
</div>
@endsection