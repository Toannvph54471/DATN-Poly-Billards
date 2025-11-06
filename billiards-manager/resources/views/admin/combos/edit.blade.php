@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Combo')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md flex items-center justify-center">
                <i class="fas fa-edit text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa Combo</h1>
                <p class="text-gray-600 text-sm mt-0.5">{{ $combo->name }}</p>
            </div>
        </div>
        <a href="{{ route('admin.combos.show', $combo->id) }}"
           class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2 rounded-lg font-medium transition flex items-center">
            <i class="fas fa-eye mr-2"></i>Xem chi tiết
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-500 text-lg mr-3"></i>
            <div>
                <h4 class="text-red-800 font-semibold mb-1">Có lỗi xảy ra</h4>
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
    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-lg mr-3"></i>
            <div>
                <h4 class="text-yellow-800 font-semibold mb-0.5">Cảnh báo</h4>
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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-5 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Thông tin cơ bản
                    </h3>
                </div>
                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Tên combo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $combo->name) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>

                        <div>
                            <label for="combo_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Mã combo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="combo_code" id="combo_code" value="{{ old('combo_code', $combo->combo_code) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono transition">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả combo</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">{{ old('description', $combo->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                Giá bán <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="price" id="price" value="{{ old('price', $combo->price) }}" required min="0" step="1000"
                                       class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">đ</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                            <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="active" {{ old('status', $combo->status) == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="inactive" {{ old('status', $combo->status) == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Combo Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-5 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-clock text-purple-600 mr-2"></i>
                        Cài đặt Combo Bàn
                    </h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center space-x-3 mb-5">
                        <input type="hidden" name="is_time_combo" value="0">
                        <input type="checkbox"
                               name="is_time_combo"
                               id="is_time_combo"
                               value="1"
                               {{ old('is_time_combo', $combo->is_time_combo) ? 'checked' : '' }}
                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="is_time_combo" class="text-sm font-medium text-gray-700 cursor-pointer">
                            <i class="fas fa-clock text-purple-600 mr-1"></i>Đây là Combo Bàn (theo thời gian)
                        </label>
                    </div>

                    <div id="time-combo-fields" class="{{ old('is_time_combo', $combo->is_time_combo) ? '' : 'hidden' }} bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-lg p-5 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="table_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Loại bàn <span class="text-red-500">*</span>
                                </label>
                                <select name="table_category_id" id="table_category_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white transition">
                                    <option value="">-- Chọn loại bàn --</option>
                                    @foreach($tableCategories as $category)
                                        <option value="{{ $category->id }}"
                                                data-hourly-rate="{{ $category->hourly_rate }}"
                                                {{ old('table_category_id', $combo->table_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} - {{ number_format($category->hourly_rate) }}đ/giờ
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="play_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Thời gian chơi <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="play_duration_minutes" id="play_duration_minutes"
                                           value="{{ old('play_duration_minutes', $combo->play_duration_minutes) }}" 
                                           min="15" max="1440" step="15"
                                           class="w-full px-4 py-2.5 pr-16 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white transition">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">phút</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Time Buttons -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn nhanh:</label>
                            <div class="grid grid-cols-4 gap-2">
                                <button type="button" onclick="setDuration(60)" 
                                        class="quick-duration-btn px-3 py-2 border-2 border-purple-300 bg-white hover:bg-purple-50 text-gray-700 rounded-lg text-sm font-medium transition">
                                    1 giờ
                                </button>
                                <button type="button" onclick="setDuration(90)"
                                        class="quick-duration-btn px-3 py-2 border-2 border-purple-300 bg-white hover:bg-purple-50 text-gray-700 rounded-lg text-sm font-medium transition">
                                    1.5 giờ
                                </button>
                                <button type="button" onclick="setDuration(120)"
                                        class="quick-duration-btn px-3 py-2 border-2 border-purple-300 bg-white hover:bg-purple-50 text-gray-700 rounded-lg text-sm font-medium transition">
                                    2 giờ
                                </button>
                                <button type="button" onclick="setDuration(180)"
                                        class="quick-duration-btn px-3 py-2 border-2 border-purple-300 bg-white hover:bg-purple-50 text-gray-700 rounded-lg text-sm font-medium transition">
                                    3 giờ
                                </button>
                            </div>
                        </div>

                        <!-- Table Price Preview -->
                        <div id="table-price-info" class="bg-white rounded-lg border-2 border-purple-300 p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-calculator text-purple-600 mr-2"></i>
                                Thông tin giá bàn
                            </h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <span class="text-xs text-gray-600 block mb-1">Giá giờ</span>
                                    <div class="text-lg font-bold text-purple-600" id="hourly-rate-display">0đ/giờ</div>
                                </div>
                                <div class="text-center">
                                    <span class="text-xs text-gray-600 block mb-1">Thời gian</span>
                                    <div class="text-lg font-bold text-purple-600" id="duration-display">0 giờ</div>
                                </div>
                                <div class="text-center">
                                    <span class="text-xs text-gray-600 block mb-1">Tổng giá bàn</span>
                                    <div class="text-lg font-bold text-purple-600" id="table-price-display">0đ</div>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-purple-200">
                                <p class="text-xs text-gray-500 text-center">
                                    <i class="fas fa-info-circle text-purple-500 mr-1"></i>
                                    Giá được làm tròn lên hàng nghìn
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-shopping-basket text-green-600 mr-2"></i>
                        Sản phẩm tiêu dùng
                    </h3>
                    <button type="button" id="add-product-btn"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium transition flex items-center text-sm shadow-sm">
                        <i class="fas fa-plus mr-2"></i>Thêm sản phẩm
                    </button>
                </div>
                <div id="products-container" class="divide-y divide-gray-100">
                    @foreach($combo->comboItems as $index => $item)
                        <div class="product-item p-4 hover:bg-gray-50 transition" data-index="{{ $index }}">
                            <input type="hidden" name="combo_items[{{ $index }}][id]" value="{{ $item->id }}">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center shadow-sm">
                                    <i class="fas fa-box text-green-600"></i>
                                </div>
                                <select name="combo_items[{{ $index }}][product_id]" class="product-select flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required>
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-price="{{ $product->price }}"
                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - {{ number_format($product->price) }}đ
                                        </option>
                                    @endforeach
                                </select>
                                <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg">
                                    <label class="text-sm text-gray-600 whitespace-nowrap font-medium">SL:</label>
                                    <input type="number" name="combo_items[{{ $index }}][quantity]" class="quantity-input w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-center transition" value="{{ $item->quantity }}" min="1" max="999" required>
                                </div>
                                <button type="button" class="remove-product-btn w-10 h-10 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition flex-shrink-0">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="bg-gray-50 px-5 py-2.5 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Chỉ được chọn sản phẩm tiêu dùng
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Price Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-5 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calculator text-blue-600 mr-2"></i> 
                        Tóm tắt giá trị
                    </h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-shopping-basket text-green-600 mr-2"></i>
                                Giá trị sản phẩm
                            </span>
                            <span class="text-lg font-bold text-green-700" id="products_total_display">0đ</span>
                        </div>
                    </div>
                    
                    <div id="table-price-row" class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200 {{ $combo->is_time_combo ? '' : 'hidden' }}">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-clock text-purple-600 mr-2"></i>
                                Giá bàn
                            </span>
                            <span class="text-lg font-bold text-purple-700" id="table_price_display">0đ</span>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border-2 border-gray-300">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Giá trị thực tế</span>
                            <span class="text-xl font-bold text-gray-900" id="actual_value_display">0đ</span>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border-2 border-blue-300">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Giá bán cho khách</span>
                            <span class="text-xl font-bold text-blue-600" id="price_display">0đ</span>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-lg p-4 border-2 border-green-300">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <i class="fas fa-tag text-green-600 mr-2"></i>
                                <span class="text-sm font-semibold text-gray-700">Khách tiết kiệm</span>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-green-600" id="discount_display">0đ</div>
                                <div class="text-xs text-green-600 font-medium" id="discount_percent_display">(0%)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 pt-0 space-y-3">
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 font-semibold transition shadow-md flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i> Cập nhật combo
                    </button>

                    <a href="{{ route('admin.combos.show', $combo->id) }}"
                       class="w-full bg-gray-100 text-gray-700 py-2.5 rounded-lg hover:bg-gray-200 font-medium transition flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Hủy thay đổi
                    </a>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-4 border border-blue-200">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center text-sm">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i> Gợi ý
                </h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-600 mr-2 mt-0.5 text-xs"></i>
                        Đặt giá bán thấp hơn giá trị thực
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-600 mr-2 mt-0.5 text-xs"></i>
                        Sản phẩm chỉ là tiêu dùng
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-600 mr-2 mt-0.5 text-xs"></i>
                        Thời gian tối thiểu 15 phút
                    </li>
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
    const isTimeCombo = document.getElementById('is_time_combo');
    const timeFields = document.getElementById('time-combo-fields');
    const tableCategorySelect = document.getElementById('table_category_id');
    const playDurationInput = document.getElementById('play_duration_minutes');
    const container = document.getElementById('products-container');
    const addBtn = document.getElementById('add-product-btn');
    const priceInput = document.getElementById('price');
    
    let count = {{ $combo->comboItems->count() }};

    // Toggle time combo fields
    isTimeCombo.addEventListener('change', function() {
        timeFields.classList.toggle('hidden', !this.checked);
        document.getElementById('table-price-row').classList.toggle('hidden', !this.checked);
        calculateTablePrice();
        calc();
    });

    // Category change
    tableCategorySelect.addEventListener('change', calculateTablePrice);
    playDurationInput.addEventListener('input', function() {
        calculateTablePrice();
        updateDurationDisplay();
    });

    // Quick duration buttons
    window.setDuration = function(minutes) {
        playDurationInput.value = minutes;
        calculateTablePrice();
        updateDurationDisplay();
        
        // Highlight active button
        document.querySelectorAll('.quick-duration-btn').forEach(btn => {
            btn.classList.remove('bg-purple-500', 'text-white', 'border-purple-500');
            btn.classList.add('bg-white', 'text-gray-700', 'border-purple-300');
        });
        event.target.classList.remove('bg-white', 'text-gray-700', 'border-purple-300');
        event.target.classList.add('bg-purple-500', 'text-white', 'border-purple-500');
    };

    function updateDurationDisplay() {
        const minutes = parseInt(playDurationInput.value) || 0;
        const hours = minutes / 60;
        let display = '';
        
        if (hours >= 1) {
            const fullHours = Math.floor(hours);
            const remainMinutes = minutes % 60;
            if (remainMinutes > 0) {
                display = `${fullHours}h ${remainMinutes}p`;
            } else {
                display = `${fullHours} giờ`;
            }
        } else {
            display = `${minutes} phút`;
        }
        
        document.getElementById('duration-display').textContent = display;
    }

    function calculateTablePrice() {
        const selectedOption = tableCategorySelect.options[tableCategorySelect.selectedIndex];
        const hourlyRate = parseFloat(selectedOption.dataset.hourlyRate) || 0;
        const minutes = parseInt(playDurationInput.value) || 0;
        
        const tablePriceRaw = (hourlyRate * minutes) / 60;
        const tablePrice = Math.ceil(tablePriceRaw / 1000) * 1000;

        document.getElementById('hourly-rate-display').textContent = 
            new Intl.NumberFormat('vi-VN').format(hourlyRate) + 'đ/giờ';
        document.getElementById('table-price-display').textContent = 
            new Intl.NumberFormat('vi-VN').format(tablePrice) + 'đ';
        
        updateDurationDisplay();
        calc();
    }

    // Add product
    addBtn.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'product-item p-4 hover:bg-gray-50 transition border-b border-gray-100';
        div.innerHTML = `
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center shadow-sm">
                    <i class="fas fa-box text-green-600"></i>
                </div>
                <select name="combo_items[${count}][product_id]" class="product-select flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required>
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} - {{ number_format($product->price) }}đ
                        </option>
                    @endforeach
                </select>
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg">
                    <label class="text-sm text-gray-600 whitespace-nowrap font-medium">SL:</label>
                    <input type="number" name="combo_items[${count}][quantity]" class="quantity-input w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-center transition" value="1" min="1" max="999" required>
                </div>
                <button type="button" class="remove-product-btn w-10 h-10 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition flex-shrink-0">
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

    function removeHandler() {
        if (container.children.length <= 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Không thể xóa',
                text: 'Combo phải có ít nhất 1 sản phẩm',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }
        this.closest('.product-item').remove();
        calc();
    }

    document.querySelectorAll('.remove-product-btn').forEach(btn => {
        btn.addEventListener('click', removeHandler);
    });

    function calc() {
        let productsTotal = 0;
        container.querySelectorAll('.product-item').forEach(item => {
            const select = item.querySelector('.product-select');
            const qty = parseInt(item.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(select.selectedOptions[0]?.dataset.price) || 0;
            productsTotal += qty * price;
        });

        let tablePrice = 0;
        if (isTimeCombo.checked) {
            const selectedOption = tableCategorySelect.options[tableCategorySelect.selectedIndex];
            const hourlyRate = parseFloat(selectedOption.dataset.hourlyRate) || 0;
            const minutes = parseInt(playDurationInput.value) || 0;
            
            const tablePriceRaw = (hourlyRate * minutes) / 60;
            tablePrice = Math.ceil(tablePriceRaw / 1000) * 1000;
        }

        const actualValueRaw = productsTotal + tablePrice;
        const actualValue = Math.ceil(actualValueRaw / 1000) * 1000;
        
        const salePrice = parseFloat(priceInput.value) || 0;
        const discount = Math.max(0, actualValue - salePrice);
        const percent = actualValue > 0 ? Math.round((discount / actualValue) * 100) : 0;

        document.getElementById('products_total_display').textContent = 
            new Intl.NumberFormat('vi-VN').format(productsTotal) + 'đ';
        document.getElementById('table_price_display').textContent = 
            new Intl.NumberFormat('vi-VN').format(tablePrice) + 'đ';
        document.getElementById('actual_value_display').textContent = 
            new Intl.NumberFormat('vi-VN').format(actualValue) + 'đ';
        document.getElementById('price_display').textContent = 
            new Intl.NumberFormat('vi-VN').format(salePrice) + 'đ';
        document.getElementById('discount_display').textContent = 
            new Intl.NumberFormat('vi-VN').format(discount) + 'đ';
        document.getElementById('discount_percent_display').textContent = 
            '(' + percent + '%)';
    }

    document.querySelectorAll('.product-select, .quantity-input, #price').forEach(el => {
        el.addEventListener('input', calc);
        el.addEventListener('change', calc);
    });

    // Initialize
    if (tableCategorySelect.value) {
        calculateTablePrice();
    } else {
        calc();
    }
    
    updateDurationDisplay();
});
</script>
@endsection