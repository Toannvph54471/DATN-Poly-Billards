@extends('admin.layouts.app')

@section('title', 'Danh sách Loại/giá bàn')

@section('content')
<div class="p-6 bg-white rounded-2xl shadow-lg">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">Danh sách Loại/giá bàn</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.table_rates.create') }}"
               class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus"></i>
                Thêm loại bàn
            </a>
            <a href="{{ route('admin.table_rates.trashed') }}"
               class="flex items-center gap-2 bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-trash-restore"></i> Đã xóa
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
        <table class="min-w-full text-center divide-y divide-gray-200">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm tracking-wider">
                <tr>
                    <th class="px-6 py-3">Mã</th>
                    <th class="px-6 py-3">Loại bàn</th>
                    <th class="px-6 py-3">Giá/giờ</th>
                    <th class="px-6 py-3">Giờ tối đa</th>
                    <th class="px-6 py-3">Trạng thái</th>
                    <th class="px-6 py-3">Hành động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($rates as $rate)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 font-medium text-gray-700">{{ $rate->code }}</td>
                    <td class="px-6 py-3 text-gray-700">{{ $rate->name }}</td>
                    <td class="px-6 py-3 text-gray-700">{{ number_format($rate->hourly_rate, 0, ',', '.') }}đ</td>
                    <td class="px-6 py-3 text-gray-700">{{ $rate->max_hours ?? '-' }}</td>
                    <td class="px-6 py-3">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $rate->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ $rate->status }}
                        </span>
                    </td>
                    <td class="px-6 py-3 flex justify-center gap-2 flex-wrap">
                        <a href="{{ route('admin.table_rates.edit', $rate->id) }}"
                           class="px-4 py-1 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 transition">
                           Sửa
                        </a>
                        <form action="{{ route('admin.table_rates.destroy', $rate->id) }}" method="POST" 
                              onsubmit="return confirm('Xóa bảng giá này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                    Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-end">
        {{ $rates->links() }}
    </div>
</div>
@endsection
