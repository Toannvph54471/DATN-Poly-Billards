@extends('admin.layouts.app')

@section('title', 'Bảng giá bàn đã xóa')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Bảng giá bàn đã xóa</h1>
    <a href="{{ route('admin.table_rates.index') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
        <i class="fas fa-arrow-left mr-1"></i> Quay lại
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-3 px-6">#</th>
                <th class="py-3 px-6">Mã</th>
                <th class="py-3 px-6">Loại bàn</th>
                <th class="py-3 px-6">Giá/giờ</th>
                <th class="py-3 px-6">Giờ tối đa</th>
                <th class="py-3 px-6">Trạng thái</th>
                <th class="py-3 px-6">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rates as $rate)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-4 px-6">{{ $loop->iteration }}</td>
                    <td class="py-4 px-6 font-medium text-gray-800">{{ $rate->code }}</td>
                    <td class="py-4 px-6 text-gray-700">{{ $rate->name }}</td>
                    <td class="py-4 px-6 text-gray-700">{{ number_format($rate->hourly_rate, 0, ',', '.') }}đ</td>
                    <td class="py-4 px-6 text-gray-700">{{ $rate->max_hours ?? '-' }}</td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded text-sm {{ $rate->status == 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                            {{ $rate->status }}
                        </span>
                    </td>
                    <td class="py-4 px-6 flex flex-col md:flex-row justify-center gap-2">
                        <!-- Khôi phục -->
                        <form action="{{ route('admin.table_rates.restore', $rate->id) }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục bảng giá này không?');">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900 transition">
                                <i class="fas fa-undo"></i> Khôi phục
                            </button>
                        </form>
                        <!-- Xóa vĩnh viễn -->
                        <form action="{{ route('admin.table_rates.forceDelete', $rate->id) }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn bảng giá này không? Hành động này không thể hoàn tác.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 transition">
                                <i class="fas fa-trash-alt"></i> Xóa vĩnh viễn
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-4 px-6 text-center text-gray-500">Không có bảng giá nào bị xóa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
