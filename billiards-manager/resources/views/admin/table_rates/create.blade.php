@extends('admin.layouts.app')

@section('title', 'Thêm bảng giá mới')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Thêm bảng giá mới</h2>

    <form action="{{ route('admin.table_rates.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="block font-medium text-gray-600 mb-1">Mã bảng giá</label>
            <input type="text" name="code" value="{{ old('code') }}" class="w-full border rounded-lg px-3 py-2" required>
            @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-3">
            <label class="block font-medium text-gray-600 mb-1">Loại bàn</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-medium text-gray-600 mb-1">Giá/giờ (VNĐ)</label>
                <input type="number" name="hourly_rate" min="0" value="{{ old('hourly_rate') }}" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            <div>
                <label class="block font-medium text-gray-600 mb-1">Giờ tối đa</label>
                <input type="number" name="max_hours" min="1" value="{{ old('max_hours', 1) }}" class="w-full border rounded-lg px-3 py-2" required>
            </div>
        </div>

        <div class="mt-3">
            <label class="block font-medium text-gray-600 mb-1">Trạng thái</label>
            <select name="status" class="w-full border rounded-lg px-3 py-2">
                <option value="Active">Hoạt động</option>
                <option value="Inactive">Ngừng</option>
            </select>
        </div>

        <div class="mt-5 flex justify-end gap-3">
            <a href="{{ route('admin.table_rates.index') }}" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Quay lại</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Lưu</button>
        </div>
    </form>
</div>
@endsection
