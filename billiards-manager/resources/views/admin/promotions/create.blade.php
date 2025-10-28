@extends('admin.layouts.app')

@section('title', 'Thêm khuyến mãi')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="bg-white shadow-md rounded-xl p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-gift text-blue-600"></i> Thêm chương trình khuyến mãi
            </h1>
            <a href="{{ route('admin.promotions.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
               <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <form action="{{ route('admin.promotions.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Mã & Tên khuyến mãi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-barcode mr-1 text-blue-500"></i> Mã khuyến mãi
                    </label>
                    <input type="text" name="promotion_code"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="VD: SALE2025" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-tag mr-1 text-blue-500"></i> Tên chương trình
                    </label>
                    <input type="text" name="name"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="VD: Giảm giá hè rực rỡ" required>
                </div>
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-align-left mr-1 text-blue-500"></i> Mô tả
                </label>
                <textarea name="description" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Mô tả chi tiết chương trình..."></textarea>
            </div>

            {{-- Loại, Giá trị, Điều kiện --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-percent mr-1 text-blue-500"></i> Loại giảm
                    </label>
                    <select name="discount_type"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                        <option value="percent">Phần trăm (%)</option>
                        <option value="fixed">Số tiền cố định</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-dollar-sign mr-1 text-blue-500"></i> Giá trị giảm
                    </label>
                    <input type="number" name="discount_value" step="0.01"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="VD: 10 hoặc 50000" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-receipt mr-1 text-blue-500"></i> Tổng bill tối thiểu (₫)
                    </label>
                    <input type="number" name="min_total_amount" value="100000"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Nhập tổng bill tối thiểu">
                </div>
            </div>

            {{-- Thời gian --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-calendar-day mr-1 text-blue-500"></i> Bắt đầu
                    </label>
                    <input type="datetime-local" name="start_date"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-calendar-check mr-1 text-blue-500"></i> Kết thúc
                    </label>
                    <input type="datetime-local" name="end_date"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                </div>
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-toggle-on mr-1 text-blue-500"></i> Trạng thái
                </label>
                <select name="status"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="active" selected>Hoạt động</option>
                    <option value="inactive">Ngưng</option>
                </select>
            </div>

            {{-- Nút hành động --}}
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 mt-6">
                <a href="{{ route('admin.promotions.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-xmark"></i> Hủy
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Lưu khuyến mãi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
