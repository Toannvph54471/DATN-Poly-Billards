@extends('admin.layouts.app')

@section('title', 'Quản lý hóa đơn')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Quản lý hóa đơn</h1>

    {{-- Thống kê tổng quan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center">
            <div class="bg-blue-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-file-invoice text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Tổng hóa đơn</p>
                <p class="text-xl font-semibold">{{ $bill->total() }}</p>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center">
            <div class="bg-green-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-check text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Hóa đơn đang mở</p>
                <p class="text-xl font-semibold">{{ $bill->where('status', 'Open')->count() }}</p>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center">
            <div class="bg-yellow-500 text-white p-3 rounded-lg mr-3">
                <i class="fa-solid fa-pause text-xl"></i>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Hóa đơn tạm dừng</p>
                <p class="text-xl font-semibold">{{ $bill->where('status', 'Paused')->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <div class="flex flex-col md:flex-row items-center gap-3 mb-6">
        <input type="text" placeholder="Tìm kiếm mã hóa đơn, nhân viên..."
               class="w-full md:w-1/3 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
        
        <select class="border border-gray-300 rounded-lg px-3 py-2">
            <option value="">Tất cả trạng thái</option>
            <option value="Open">Open</option>
            <option value="Paused">Paused</option>
            <option value="Closed">Closed</option>
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
                    <th class="px-4 py-2 text-left">Trạng thái</th>
                    <th class="px-4 py-2 text-left">Ngày tạo</th>
                    <th class="px-4 py-2 text-left">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bill as $item)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $item->bill_number }}</td>
                        <td class="px-4 py-2">{{ $item->table->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $item->staff->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-sm
                                {{ $item->status === 'Open' ? 'bg-green-100 text-green-700' :
                                   ($item->status === 'Paused' ? 'bg-yellow-100 text-yellow-700' :
                                   'bg-red-100 text-red-700') }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.bills.show', $item->id) }}" 
                               class="text-blue-600 hover:underline">
                                <i class="fa-solid fa-eye mr-1"></i> Xem chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                            Không có hóa đơn nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="mt-6">
        {{ $bill->links() }}
    </div>
</div>
@endsection
