@extends('admin.layouts.app')

@section('title', 'Tạo Combo Mới')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tạo Combo Mới</h1>
                <p class="text-gray-600 mt-1">Điền thông tin để tạo combo ưu đãi cho khách hàng</p>
            </div>
            <a href="{{ route('admin.combos.index') }}" 
               class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-xl">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-4"></i>
                <div>
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

    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-center">
            <div class="flex items-center space-x-4">
                <div class="flex items-center" id="step-indicator-1">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold">1</div>
                    <span class="ml-3 text-sm font-medium text-gray-900">Thông tin</span>
                </div>
                <div class="w-16 h-0.5 bg-gray-300"></div>
                <div class="flex items-center" id="step-indicator-2">
                    <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold">2</div>
                    <span class="ml-3 text-sm font-medium text-gray-500">Loại combo</span>
                </div>
                <div class="w-16 h-0.5 bg-gray-300"></div>
                <div class="flex items-center" id="step-indicator-3">
                    <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold">3</div>
                    <span class="ml-3 text-sm font-medium text-gray-500">Sản phẩm</span>
                </div>
                <div class="w-16 h-0.5 bg-gray-300"></div>
                <div class="flex items-center" id="step-indicator-4">
                    <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold">4</div>
                    <span class="ml-3 text-sm font-medium text-gray-500">Xác nhận</span>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.combos.store') }}" method="POST" id="combo-form">
        @csrf

        <!-- Step 1: Basic Info -->
        <div id="step-1" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                Bước 1: Thông tin cơ bản
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tên combo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           placeholder="VD: Combo Sinh viên vui vẻ"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mã combo</label>
                    <input type="text" name="combo_code" id="combo_code" value="{{ old('combo_code') }}"
                           placeholder="Tự động tạo nếu để trống"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Mô tả combo</label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Mô tả ngắn gọn về combo..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" onclick="nextStep(2)"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                    Tiếp theo <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Combo Type -->
        <div id="step-2" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-layer-group text-blue-600"></i>
                </div>
                Bước 2: Chọn loại combo
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="combo-type-card border-2 border-green-500 bg-green-50 rounded-xl p-6 cursor-pointer transition"
                     onclick="selectComboType('regular')">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-shopping-basket text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Combo Thường</h3>
                            <p class="text-sm text-gray-600 mt-2">Chỉ bao gồm sản phẩm tiêu dùng (nước, đồ ăn)</p>
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check text-green-600 mr-2"></i>Đơn giản, nhanh chóng
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check text-green-600 mr-2"></i>Áp dụng mọi loại bàn
                                </div>
                            </div>
                        </div>
                        <input type="radio" name="is_time_combo" value="0" class="mt-2" checked>
                    </div>
                </div>

                <div class="combo-type-card border-2 border-gray-200 rounded-xl p-6 cursor-pointer hover:border-purple-500 transition"
                     onclick="selectComboType('time')">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-clock text-purple-600 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Combo Bàn</h3>
                            <p class="text-sm text-gray-600 mt-2">Bao gồm giờ chơi bàn + sản phẩm tiêu dùng</p>
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check text-purple-600 mr-2"></i>Gói trọn gói hấp dẫn
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check text-purple-600 mr-2"></i>Tiết kiệm chi phí
                                </div>
                            </div>
                        </div>
                        <input type="radio" name="is_time_combo" value="1" class="mt-2">
                    </div>
                </div>
            </div>

            <div id="time-settings" class="hidden bg-purple-50 border-2 border-purple-200 rounded-xl p-6">
                <h4 class="font-semibold text-gray-900 mb-4">
                    <i class="fas fa-cog text-purple-600 mr-2"></i>Cài đặt combo bàn
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Loại bàn <span class="text-red-500">*</span>
                        </label>
                        <select name="table_category_id" id="table_category_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="">-- Chọn loại bàn --</option>
                            @foreach($tableCategories as $category)
                                <option value="{{ $category->id }}" 
                                        data-hourly-rate="{{ $category->hourly_rate }}"
                                        {{ old('table_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} - {{ number_format($category->hourly_rate) }}đ/giờ
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Thời gian chơi <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <button type="button" onclick="setDuration(60)" 
                                    class="duration-btn px-3 py-2 border-2 border-purple-500 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium">
                                1 giờ
                            </button>
                            <button type="button" onclick="setDuration(90)"
                                    class="duration-btn px-3 py-2 border-2 border-gray-300 rounded-lg hover:border-purple-500 text-sm font-medium">
                                1.5 giờ
                            </button>
                            <button type="button" onclick="setDuration(120)"
                                    class="duration-btn px-3 py-2 border-2 border-gray-300 rounded-lg hover:border-purple-500 text-sm font-medium">
                                2 giờ
                            </button>
                        </div>
                        <input type="number" name="play_duration_minutes" id="play_duration_minutes"
                               value="{{ old('play_duration_minutes', 60) }}" min="15" step="15"
                               placeholder="Hoặc nhập số phút"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <div id="table-price-preview" class="mt-4 p-4 bg-white rounded-lg border-2 border-purple-300">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-600 block mb-1">
                                <i class="fas fa-tag text-purple-600 mr-1"></i>Giá giờ:
                            </span>
                            <div class="text-2xl font-bold text-purple-600" id="hourly-rate-display">0đ/giờ</div>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 block mb-1">
                                <i class="fas fa-calculator text-purple-600 mr-1"></i>Tổng giá bàn:
                            </span>
                            <div class="text-2xl font-bold text-purple-600" id="table-price-value">0đ</div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-purple-200">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle text-purple-500"></i> 
                            Giá bàn được làm tròn lên hàng nghìn
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="prevStep(1)"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </button>
                <button type="button" onclick="nextStep(3)"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                    Tiếp theo <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Products -->
        <div id="step-3" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                    Bước 3: Thêm sản phẩm tiêu dùng
                </h2>
                <button type="button" onclick="addProduct()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition">
                    <i class="fas fa-plus mr-2"></i>Thêm sản phẩm
                </button>
            </div>

            <div id="products-list" class="space-y-3"></div>

            <div id="no-products" class="bg-gray-50 rounded-lg p-8 text-center">
                <i class="fas fa-box-open text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500">Chưa có sản phẩm. Nhấn "Thêm sản phẩm" để bắt đầu</p>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="prevStep(2)"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </button>
                <button type="button" onclick="nextStep(4)"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                    Tiếp theo <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 4: Confirm -->
        <div id="step-4" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-blue-600"></i>
                </div>
                Bước 4: Xác nhận và định giá
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Tóm tắt combo</h3>
                    <div id="combo-summary" class="bg-gray-50 rounded-lg p-4 space-y-3"></div>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Định giá combo</h3>
                    <div class="bg-blue-50 rounded-lg p-4 space-y-4">
                        <div class="flex justify-between pb-3 border-b">
                            <span class="text-sm text-gray-600">Giá sản phẩm</span>
                            <span class="font-semibold" id="summary-products">0đ</span>
                        </div>
                        <div id="summary-table-row" class="hidden flex justify-between pb-3 border-b">
                            <span class="text-sm text-gray-600">Giá bàn</span>
                            <span class="font-semibold text-purple-600" id="summary-table">0đ</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b-2">
                            <span class="font-semibold">Giá trị thực tế</span>
                            <span class="text-xl font-bold" id="summary-actual">0đ</span>
                        </div>

                        <div class="pt-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Giá bán cho khách <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="price" id="price" min="0" step="1000" required
                                       value="{{ old('price', 0) }}"
                                       class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg font-bold">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">đ</span>
                            </div>
                        </div>

                        <div class="bg-green-100 rounded-lg p-4 border-2 border-green-300">
                            <div class="flex justify-between">
                                <span class="font-semibold">Khách tiết kiệm</span>
                                <div class="text-right">
                                    <div class="text-xl font-bold text-green-600" id="summary-discount">0đ</div>
                                    <div class="text-sm text-green-600" id="summary-percent">(0%)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái</label>
                        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                            <option value="active" selected>Hoạt động</option>
                            <option value="inactive">Tạm dừng</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="button" onclick="prevStep(3)"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </button>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-semibold shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>Tạo combo
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentStep = 1;
let productCount = 0;

// Navigation
function nextStep(step) {
    if (!validateStep(currentStep)) return;
    
    document.getElementById(`step-${currentStep}`).classList.add('hidden');
    currentStep = step;
    document.getElementById(`step-${step}`).classList.remove('hidden');
    updateProgress();
    
    if (step === 4) generateSummary();
}

function prevStep(step) {
    document.getElementById(`step-${currentStep}`).classList.add('hidden');
    currentStep = step;
    document.getElementById(`step-${step}`).classList.remove('hidden');
    updateProgress();
}

function updateProgress() {
    for (let i = 1; i <= 4; i++) {
        const indicator = document.getElementById(`step-indicator-${i}`);
        const circle = indicator.querySelector('div');
        const text = indicator.querySelector('span');
        
        if (i <= currentStep) {
            circle.className = 'w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold';
            text.className = 'ml-3 text-sm font-medium text-gray-900';
        } else {
            circle.className = 'w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold';
            text.className = 'ml-3 text-sm font-medium text-gray-500';
        }
    }
}

function validateStep(step) {
    if (step === 1) {
        const name = document.getElementById('name').value.trim();
        if (!name) {
            Swal.fire('Thiếu thông tin', 'Vui lòng nhập tên combo', 'error');
            return false;
        }
    } else if (step === 2) {
        const isTime = document.querySelector('input[name="is_time_combo"]:checked').value === '1';
        if (isTime) {
            const category = document.getElementById('table_category_id').value;
            const duration = document.getElementById('play_duration_minutes').value;
            if (!category) {
                Swal.fire('Thiếu thông tin', 'Vui lòng chọn loại bàn', 'error');
                return false;
            }
            if (!duration || duration < 15) {
                Swal.fire('Thiếu thông tin', 'Vui lòng nhập thời gian chơi (tối thiểu 15 phút)', 'error');
                return false;
            }
        }
    } else if (step === 3) {
        const products = document.querySelectorAll('#products-list .product-row');
        if (products.length === 0) {
            Swal.fire('Thiếu sản phẩm', 'Vui lòng thêm ít nhất 1 sản phẩm', 'error');
            return false;
        }
    }
    return true;
}

// Combo Type Selection
function selectComboType(type) {
    document.querySelectorAll('.combo-type-card').forEach(card => {
        card.classList.remove('border-green-500', 'bg-green-50', 'border-purple-500', 'bg-purple-50');
        card.classList.add('border-gray-200');
    });
    
    event.currentTarget.classList.remove('border-gray-200');
    if (type === 'regular') {
        event.currentTarget.classList.add('border-green-500', 'bg-green-50');
        event.currentTarget.querySelector('input').checked = true;
        document.getElementById('time-settings').classList.add('hidden');
    } else {
        event.currentTarget.classList.add('border-purple-500', 'bg-purple-50');
        event.currentTarget.querySelector('input').checked = true;
        document.getElementById('time-settings').classList.remove('hidden');
        calculateTablePrice();
    }
}

// Duration Selection
function setDuration(minutes) {
    document.getElementById('play_duration_minutes').value = minutes;
    calculateTablePrice();
    
    document.querySelectorAll('.duration-btn').forEach(btn => {
        btn.classList.remove('border-purple-500', 'bg-purple-50', 'text-purple-700');
        btn.classList.add('border-gray-300');
    });
    event.currentTarget.classList.remove('border-gray-300');
    event.currentTarget.classList.add('border-purple-500', 'bg-purple-50', 'text-purple-700');
}

function calculateTablePrice() {
    const categorySelect = document.getElementById('table_category_id');
    const option = categorySelect.options[categorySelect.selectedIndex];
    const hourlyRate = parseFloat(option.dataset.hourlyRate) || 0;
    const duration = parseFloat(document.getElementById('play_duration_minutes').value) || 0;
    
    // Tính giá theo phút
    const priceRaw = (hourlyRate * duration) / 60;
    // Làm tròn lên hàng nghìn
    const price = Math.ceil(priceRaw / 1000) * 1000;
    
    document.getElementById('hourly-rate-display').textContent = 
        new Intl.NumberFormat('vi-VN').format(hourlyRate) + 'đ/giờ';
    document.getElementById('table-price-value').textContent = 
        new Intl.NumberFormat('vi-VN').format(price) + 'đ';
}

// Event listeners for category and duration changes
document.getElementById('table_category_id').addEventListener('change', calculateTablePrice);
document.getElementById('play_duration_minutes').addEventListener('input', calculateTablePrice);

// Product Management
function addProduct() {
    const id = productCount++;
    const html = `
        <div class="product-row flex items-center gap-3 p-4 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-blue-300 transition" data-id="${id}">
            <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-box text-green-600"></i>
            </div>
            <select name="combo_items[${id}][product_id]" required onchange="calculateTotal()"
                    class="product-select flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="">-- Chọn sản phẩm --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                        {{ $product->name }} - {{ number_format($product->price) }}đ
                    </option>
                @endforeach
            </select>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600 whitespace-nowrap">SL:</label>
                <input type="number" name="combo_items[${id}][quantity]" value="1" min="1" max="999" required
                       onchange="calculateTotal()"
                       class="quantity-input w-20 px-3 py-2 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-green-500">
            </div>
            <button type="button" onclick="removeProduct(${id})"
                    class="w-10 h-10 text-red-600 hover:bg-red-50 rounded-lg transition flex-shrink-0">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    document.getElementById('products-list').insertAdjacentHTML('beforeend', html);
    document.getElementById('no-products').classList.add('hidden');
}

function removeProduct(id) {
    if (document.querySelectorAll('.product-row').length <= 1) {
        Swal.fire('Không thể xóa', 'Combo phải có ít nhất 1 sản phẩm', 'warning');
        return;
    }
    document.querySelector(`[data-id="${id}"]`).remove();
    if (document.querySelectorAll('.product-row').length === 0) {
        document.getElementById('no-products').classList.remove('hidden');
    }
    calculateTotal();
}

// Summary
function generateSummary() {
    const name = document.getElementById('name').value;
    const code = document.getElementById('combo_code').value || 'Tự động tạo';
    const isTime = document.querySelector('input[name="is_time_combo"]:checked').value === '1';
    
    let summary = `
        <div class="flex justify-between py-2 border-b">
            <strong class="text-gray-700">Tên:</strong> 
            <span class="text-gray-900">${name}</span>
        </div>
        <div class="flex justify-between py-2 border-b">
            <strong class="text-gray-700">Mã:</strong> 
            <code class="bg-gray-200 px-2 py-1 rounded">${code}</code>
        </div>
        <div class="flex justify-between py-2 border-b">
            <strong class="text-gray-700">Loại:</strong> 
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${isTime ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'}">
                <i class="fas fa-${isTime ? 'clock' : 'shopping-basket'} mr-1"></i>
                ${isTime ? 'Combo Bàn' : 'Combo Thường'}
            </span>
        </div>
    `;
    
    if (isTime) {
        const catSelect = document.getElementById('table_category_id');
        const dur = document.getElementById('play_duration_minutes').value;
        
        summary += `
            <div class="flex justify-between py-2 border-b">
                <strong class="text-gray-700">Loại bàn:</strong> 
                <span class="text-gray-900">${catSelect.options[catSelect.selectedIndex].text}</span>
            </div>
            <div class="flex justify-between py-2 border-b">
                <strong class="text-gray-700">Thời gian:</strong> 
                <span class="text-gray-900">${dur} phút (${(dur/60).toFixed(1)} giờ)</span>
            </div>
        `;
    }
    
    const productCount = document.querySelectorAll('.product-row').length;
    summary += `
        <div class="flex justify-between py-2 pt-3 border-t-2">
            <strong class="text-gray-700">Số sản phẩm:</strong> 
            <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                ${productCount} sản phẩm
            </span>
        </div>
    `;
    
    document.getElementById('combo-summary').innerHTML = summary;
    calculateTotal();
}

function calculateTotal() {
    let productsTotal = 0;
    
    document.querySelectorAll('.product-row').forEach(row => {
        const select = row.querySelector('.product-select');
        const qty = parseInt(row.querySelector('.quantity-input').value) || 0;
        const option = select.options[select.selectedIndex];
        const price = parseFloat(option.dataset.price) || 0;
        productsTotal += price * qty;
    });
    
    let tablePrice = 0;
    const isTime = document.querySelector('input[name="is_time_combo"]:checked').value === '1';
    
    if (isTime) {
        const categorySelect = document.getElementById('table_category_id');
        const option = categorySelect.options[categorySelect.selectedIndex];
        const hourlyRate = parseFloat(option.dataset.hourlyRate) || 0;
        const duration = parseFloat(document.getElementById('play_duration_minutes').value) || 0;
        
        // Tính giá và làm tròn lên
        const priceRaw = (hourlyRate * duration) / 60;
        tablePrice = Math.ceil(priceRaw / 1000) * 1000;
    }
    
    // Tổng giá trị và làm tròn
    const actualValueRaw = productsTotal + tablePrice;
    const actualValue = Math.ceil(actualValueRaw / 1000) * 1000;
    
    document.getElementById('summary-products').textContent = 
        new Intl.NumberFormat('vi-VN').format(productsTotal) + 'đ';
    document.getElementById('summary-table').textContent = 
        new Intl.NumberFormat('vi-VN').format(tablePrice) + 'đ';
    document.getElementById('summary-actual').textContent = 
        new Intl.NumberFormat('vi-VN').format(actualValue) + 'đ';
    
    document.getElementById('summary-table-row').classList.toggle('hidden', !isTime);
    
    // Set suggested price (20% discount, làm tròn)
    const suggestedPrice = Math.floor((actualValue * 0.8) / 1000) * 1000;
    document.getElementById('price').value = suggestedPrice;
    calculateDiscount();
}

function calculateDiscount() {
    const actualText = document.getElementById('summary-actual').textContent.replace(/[^\d]/g, '');
    const actual = parseFloat(actualText) || 0;
    const sale = parseFloat(document.getElementById('price').value) || 0;
    const discount = Math.max(0, actual - sale);
    const percent = actual > 0 ? Math.round((discount / actual) * 100) : 0;
    
    document.getElementById('summary-discount').textContent = 
        new Intl.NumberFormat('vi-VN').format(discount) + 'đ';
    document.getElementById('summary-percent').textContent = `(${percent}%)`;
}

// Event Listeners
document.getElementById('price').addEventListener('input', calculateDiscount);

// Add first product on load
window.addEventListener('DOMContentLoaded', function() {
    addProduct();
});

// Form validation before submit
document.getElementById('combo-form').addEventListener('submit', function(e) {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const actualText = document.getElementById('summary-actual').textContent.replace(/[^\d]/g, '');
    const actual = parseFloat(actualText) || 0;
    
    if (price > actual) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Giá không hợp lệ',
            text: `Giá bán (${new Intl.NumberFormat('vi-VN').format(price)}đ) không được lớn hơn giá trị thực tế (${new Intl.NumberFormat('vi-VN').format(actual)}đ)`,
            confirmButtonColor: '#3b82f6'
        });
    }
});
</script>
@endsection