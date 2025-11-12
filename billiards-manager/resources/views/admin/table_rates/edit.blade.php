@extends('admin.layouts.app')

@section('title', 'Sửa bảng giá bàn')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Sửa bảng giá bàn</h1>
        <a href="{{ route('admin.table_rates.index') }}" class="text-sm text-blue-600 hover:underline">← Quay lại danh sách</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.table_rates.update', $rate->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')
        <!-- Mã giá -->
        <div>
            <label class="block font-medium text-gray-700 mb-1">Mã bảng giá</label>
            <input type="text" name="code" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                   value="{{ old('code', $rate->code) }}" required>
            @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tên giá -->
        <div>
            <label class="block font-medium text-gray-700 mb-1">Tên bảng giá</label>
            <input type="text" name="name" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                   value="{{ old('name', $rate->name) }}" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Giá/giờ và Giờ tối đa -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-gray-700 mb-1">Giá / Giờ (VNĐ)</label>
                <input type="number" name="hourly_rate" step="1000" min="0"
                       value="{{ old('hourly_rate', $rate->hourly_rate) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" required>
                @error('hourly_rate')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block font-medium text-gray-700 mb-1">Giờ tối đa</label>
                <input type="number" name="max_hours" min="1"
                       value="{{ old('max_hours', $rate->max_hours) }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" required>
                @error('max_hours')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Trạng thái -->
        <div>
            <label class="block font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                <option value="Active" {{ $rate->status == 'Active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="Inactive" {{ $rate->status == 'Inactive' ? 'selected' : '' }}>Ngừng</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nút hành động -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.table_rates.index') }}"
               class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                Hủy
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection
