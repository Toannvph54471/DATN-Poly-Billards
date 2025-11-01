@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Combo')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa Combo</h1>
                <p class="text-gray-600 mt-1">{{ $combo->name }}</p>
            </div>
        </div>
        <a href="{{ route('admin.combos.show', $combo->id) }}"
           class="bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50 px-6 py-2.5 rounded-xl font-medium transition flex items-center">
            <i class="fas fa-eye mr-2"></i>Xem chi tiết
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-xl shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-red-800 font-bold mb-2">Có lỗi xảy ra</h4>
                <ul class="text-red-700 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

@if ($combo->is_time_combo && $combo->timeUsages()->where('is_expired', false)->exists())
    <div class="mb-6 p-5 bg-yellow-50 border-l-4 border-yellow-500 rounded-xl shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-yellow-800 font-bold mb-1">Cảnh báo</h4>
                <p class="text-yellow-700 text-sm">Combo này đang có session hoạt động. Thay đổi có thể ảnh hưởng đến session hiện tại.</p>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('admin.combos.update', $combo->id) }}" method="POST" id="combo-form">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 text-lg mr-3"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Thông tin cơ bản</h3>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tên combo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $combo->name) }}" required
                                   placeholder="VD: Combo Sinh viên vui vẻ"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                            @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="combo_code" class="block text-sm font-semibold text-gray-700 mb-2">
                                Mã combo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="combo_code" id="combo_code" value="{{ old('combo_code', $combo->combo_code) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition font-mono">
                            @error('combo_code') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Mô tả combo</label>
                        <textarea name="description" id="description" rows="3"
                                  placeholder="Mô tả ngắn về combo này..."
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">{{ old('description', $combo->description) }}</textarea>
                        @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                                Giá bán <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="price" id="price" value="{{ old('price', $combo->price) }}" required min="0" step="1000"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">đ</span>
                            </div>
                            @error('price') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="actual_value" class="block text-sm font-semibold text-gray-700 mb-2">
                                Giá trị thực
                            </label>
                            <div class="relative">
                                <input type="number" name="actual_value" id="actual_value" value="{{ old('actual_value', $combo->actual_value) }}" readonly
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">đ</span>
                            </div>
                            <p class="mt-1.5 text-xs text-gray-500">Tự động tính từ sản phẩm</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái</label>
                            <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                                <option value="active" {{ old('status', $combo->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status', $combo->status) == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                            </select>
                        </div>
                    </div>

                    <!-- Time Combo -->
                    <div class="border-t pt-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <input type="hidden" name="is_time_combo" value="0">
                            <input type="checkbox"
                                   name="is_time_combo"
                                   id="is_time_combo"
                                   value="1"
                                   {{ old('is_time_combo', $combo->is_time_combo) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_time_combo" class="text-sm font-semibold text-gray-700">
                                <i class="fas fa-clock text-purple-600 mr-1"></i> Đây là Combo bàn (theo thời gian)
                            </label>
                        </div>

                        <div id="time-combo-fields" class="{{ old('is_time_combo', $combo->is_time_combo) ? '' : 'hidden' }} bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="play_duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Thời gian chơi (phút) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="play_duration_minutes" id="play_duration_minutes"
                                           value="{{ old('play_duration_minutes', $combo->play_duration_minutes) }}" min="15" max="1440"
                                           placeholder="Tối thiểu 15 phút"
                                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 transition">
                                    @error('play_duration_minutes') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="table_category_id" class="block text-sm font-semibold text-gray-700 mb-2">Loại bàn</label>
                                    <select name="table_category_id" id="table_category_id"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 transition">
                                        <option value="">Tất cả</option>
                                        @foreach($tableCategories as $category)
                                            <option value="{{ $category->id }}" {{ old('table_category_id', $combo->table_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-boxes text-green-600 text-lg mr-3"></i>
                        <h3 class="text-lg font-semibold text-gray-900">Sản phẩm trong combo</h3>
                    </div>
                    <button type="button" id="add-product-btn"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium transition flex items-center">
                        <i class="fas fa-plus mr-2"></i>Thêm
                    </button>
                </div>
                <div id="products-container" class="divide-y divide-gray-100">
                    @foreach($combo->comboItems as $index => $item)
                        <div class="product-item p-4 hover:bg-gray-50 transition">
                            <input type="hidden" name="combo_items[{{ $index }}][id]" value="{{ $item->id }}">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-green-600"></i>
                                </div>
                                <select name="combo_items[{{ $index }}][product_id]" class="product-select flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 transition">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-price="{{ $product->price }}"
                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - {{ number_format($product->price) }}đ
                                        </option>
                                    @endforeach
                                </select>
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-600">SL:</label>
                                    <input type="number" name="combo_items[{{ $index }}][quantity]" class="quantity-input w-20 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-center" value="{{ $item->quantity }}" min="1" max="999">
                                </div>
                                <button type="button" class="remove-product-btn w-10 h-10 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Combo chỉ được chứa tối đa 1 sản phẩm dịch vụ (giờ chơi)
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calculator text-blue-600 mr-2"></i> Tóm tắt giá trị
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-sm text-gray-600">Giá trị thực tế</span>
                        <span class="text-lg font-bold text-gray-900" id="actual_value_display">
                            {{ number_format($combo->actual_value) }}đ
                        </span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-sm text-gray-600">Giá bán cho khách</span>
                        <span class="text-lg font-bold text-blue-600" id="price_display">
                            {{ number_format($combo->price) }}đ
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-semibold text-gray-700">Khách tiết kiệm</span>
                        <div class="text-right">
                            <div class="text-lg font-bold text-green-600" id="discount_display">0đ</div>
                            <div class="text-xs text-green-600" id="discount_percent_display">(0%)</div>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="mt-6 w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3.5 rounded-xl hover:from-blue-700 hover:to-blue-800 font-semibold transition shadow-lg flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i> Cập nhật combo
                </button>

                <a href="{{ route('admin.combos.show', $combo->id) }}"
                   class="mt-3 w-full bg-gray-100 text-gray-700 py-3 rounded-xl hover:bg-gray-200 font-medium transition flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i>Hủy thay đổi
                </a>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i> Gợi ý
                </h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li class="flex items-start"><i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>Đặt giá bán < giá trị thực để tạo ưu đãi</li>
                    <li class="flex items-start"><i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>Combo thời gian chỉ dành cho bàn chơi</li>
                    <li class="flex items-start"><i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>Tối đa 1 sản phẩm dịch vụ mỗi combo</li>
                </ul>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('combo-form');
    const isTimeCombo = document.getElementById('is_time_combo');
    const timeFields = document.getElementById('time-combo-fields');
    const container = document.getElementById('products-container');
    const addBtn = document.getElementById('add-product-btn');
    const priceInput = document.getElementById('price');
    const actualValueDisplay = document.getElementById('actual_value_display');
    const priceDisplay = document.getElementById('price_display');
    const discountDisplay = document.getElementById('discount_display');
    const discountPercentDisplay = document.getElementById('discount_percent_display');
    let count = container.children.length;

    // Toggle time combo fields
    isTimeCombo.addEventListener('change', function() {
        timeFields.classList.toggle('hidden', !this.checked);
        if (!this.checked) {
            document.getElementById('play_duration_minutes').value = '';
        }
    });

    // Add product
    addBtn.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'product-item p-4 hover:bg-gray-50 transition';
        div.innerHTML = `
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-green-600"></i>
                </div>
                <select name="combo_items[${count}][product_id]" class="product-select flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 transition">
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} - {{ number_format($product->price) }}đ
                        </option>
                    @endforeach
                </select>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">SL:</label>
                    <input type="number" name="combo_items[${count}][quantity]" class="quantity-input w-20 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-center" value="1" min="1" max="999">
                </div>
                <button type="button" class="remove-product-btn w-10 h-10 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(div);
        count++;

        div.querySelector('.remove-product-btn').addEventListener('click', removeHandler);
        div.querySelectorAll('select, input').forEach(el => el.addEventListener('input', calc));
        calc();
    });

    // Remove handler
    function removeHandler() {
        if (container.children.length <= 1) {
            Swal.fire('Cảnh báo', 'Combo phải có ít nhất 1 sản phẩm', 'warning');
            return;
        }
        this.closest('.product-item').remove();
        calc();
    }

    document.querySelectorAll('.remove-product-btn').forEach(btn => {
        btn.addEventListener('click', removeHandler);
    });

    // Calculate
    function calc() {
        let total = 0;
        container.querySelectorAll('.product-item').forEach(item => {
            const select = item.querySelector('.product-select');
            const qty = parseInt(item.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(select.selectedOptions[0]?.dataset.price) || 0;
            total += qty * price;
        });

        const price = parseFloat(priceInput.value) || 0;
        const discount = Math.max(0, total - price);
        const percent = total > 0 ? Math.round((discount / total) * 100) : 0;

        document.querySelector('[name="actual_value"]').value = total;
        actualValueDisplay.textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
        priceDisplay.textContent = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
        discountDisplay.textContent = new Intl.NumberFormat('vi-VN').format(discount) + 'đ';
        discountPercentDisplay.textContent = '(' + percent + '%)';

        discountDisplay.className = discount > 0 ? 'text-lg font-bold text-green-600' : 'text-lg font-bold text-gray-400';
        discountPercentDisplay.className = discount > 0 ? 'text-xs text-green-600' : 'text-xs text-gray-400';
    }

    // Events
    document.querySelectorAll('.product-select, .quantity-input, #price').forEach(el => {
        el.addEventListener('input', calc);
        el.addEventListener('change', calc);
    });

    calc();
});
</script>
@endsection