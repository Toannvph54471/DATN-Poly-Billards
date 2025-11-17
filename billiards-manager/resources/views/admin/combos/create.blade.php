@extends('admin.layouts.app')

@section('title', 'Tạo Combo Mới')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg flex items-center justify-center">
                    <i class="fas fa-plus-circle text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tạo Combo Mới</h1>
                    <p class="text-gray-600 mt-1">Điền thông tin để tạo combo ưu đãi cho khách hàng</p>
                </div>
            </div>
            <a href="{{ route('admin.combos.index') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-xl p-5 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-red-800 font-bold text-lg mb-2">Có lỗi xảy ra</h4>
                    <ul class="text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start">
                                <i class="fas fa-circle text-red-400 text-xs mr-2 mt-1"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Progress Indicator -->
    <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-center">
            <div class="flex items-center">
                <!-- Step 1 -->
                <div class="flex items-center" id="step-indicator-1">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg">1</div>
                        <span class="mt-2 text-sm font-semibold text-blue-600">Thông tin</span>
                    </div>
                </div>
                <div class="w-20 h-1 bg-gray-300 mx-2"></div>
                
                <!-- Step 2 -->
                <div class="flex items-center" id="step-indicator-2">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold text-lg">2</div>
                        <span class="mt-2 text-sm font-medium text-gray-500">Loại combo</span>
                    </div>
                </div>
                <div class="w-20 h-1 bg-gray-300 mx-2"></div>
                
                <!-- Step 3 -->
                <div class="flex items-center" id="step-indicator-3">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold text-lg">3</div>
                        <span class="mt-2 text-sm font-medium text-gray-500">Sản phẩm</span>
                    </div>
                </div>
                <div class="w-20 h-1 bg-gray-300 mx-2"></div>
                
                <!-- Step 4 -->
                <div class="flex items-center" id="step-indicator-4">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold text-lg">4</div>
                        <span class="mt-2 text-sm font-medium text-gray-500">Xác nhận</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.combos.store') }}" method="POST" id="combo-form">
        @csrf

        <!-- Step 1: Basic Info -->
        <div id="step-1" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    Bước 1: Thông tin cơ bản
                </h2>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Tên combo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               placeholder="VD: Combo Sinh viên vui vẻ"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mã combo</label>
                        <input type="text" name="combo_code" id="combo_code" value="{{ old('combo_code') }}"
                               placeholder="Tự động tạo nếu để trống"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả combo</label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="Mô tả ngắn gọn về combo..."
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="nextStep(2)"
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all duration-200">
                    Tiếp theo <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Combo Type -->
        <div id="step-2" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    Bước 2: Loại combo
                </h2>
            </div>

            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-4">Chọn loại combo</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="is_time_combo" value="0" checked onchange="toggleTimeComboOptions()" class="peer sr-only">
                            <div class="p-6 border-2 border-gray-300 rounded-xl group-hover:border-green-400 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:ring-2 peer-checked:ring-green-200 transition-all duration-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                        <i class="fas fa-shopping-basket text-green-600 text-xl"></i>
                                    </div>
                                    <div class="hidden peer-checked:block">
                                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Combo thường</h3>
                                <p class="text-sm text-gray-600">Chỉ bao gồm sản phẩm tiêu dùng</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="is_time_combo" value="1" onchange="toggleTimeComboOptions()" class="peer sr-only">
                            <div class="p-6 border-2 border-gray-300 rounded-xl group-hover:border-purple-400 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:ring-2 peer-checked:ring-purple-200 transition-all duration-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                        <i class="fas fa-clock text-purple-600 text-xl"></i>
                                    </div>
                                    <div class="hidden peer-checked:block">
                                        <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Combo bàn</h3>
                                <p class="text-sm text-gray-600">Bao gồm thời gian chơi bàn</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Time Combo Options -->
                <div id="time-combo-options" class="hidden bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-300 rounded-xl p-6 space-y-6">
                    <h3 class="text-lg font-bold text-purple-900 flex items-center">
                        <i class="fas fa-cog text-purple-600 mr-2"></i>
                        Cấu hình thời gian chơi
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Bảng giá bàn <span class="text-red-500">*</span>
                            </label>
                            <select name="table_rate_id" id="table_rate_id"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white transition"
                                    onchange="calculateTotal()">
                                <option value="">-- Chọn bảng giá --</option>
                                @foreach($tableRates as $rate)
                                    <option value="{{ $rate->id }}" data-hourly-rate="{{ $rate->hourly_rate }}">
                                        {{ $rate->name }} - {{ number_format($rate->hourly_rate) }}đ/giờ
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Thời gian chơi (phút) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="play_duration_minutes" id="play_duration_minutes" min="15" max="1440" step="15" value="120"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                   oninput="calculateTotal()">
                            <p class="text-xs text-gray-500 mt-1">Tối thiểu 15 phút, bội số của 15</p>
                        </div>
                    </div>

                    <!-- Quick Duration Buttons -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Chọn nhanh:</label>
                        <div class="grid grid-cols-4 gap-3">
                            <button type="button" onclick="setDuration(60)" 
                                    class="px-4 py-3 border-2 border-purple-300 bg-white hover:bg-purple-500 hover:text-white hover:border-purple-500 text-gray-700 rounded-lg font-semibold transition-all duration-200 active:scale-95">
                                1 giờ
                            </button>
                            <button type="button" onclick="setDuration(90)"
                                    class="px-4 py-3 border-2 border-purple-300 bg-white hover:bg-purple-500 hover:text-white hover:border-purple-500 text-gray-700 rounded-lg font-semibold transition-all duration-200 active:scale-95">
                                1.5 giờ
                            </button>
                            <button type="button" onclick="setDuration(120)"
                                    class="px-4 py-3 border-2 border-purple-300 bg-purple-500 text-white border-purple-500 rounded-lg font-semibold shadow-md">
                                2 giờ
                            </button>
                            <button type="button" onclick="setDuration(180)"
                                    class="px-4 py-3 border-2 border-purple-300 bg-white hover:bg-purple-500 hover:text-white hover:border-purple-500 text-gray-700 rounded-lg font-semibold transition-all duration-200 active:scale-95">
                                3 giờ
                            </button>
                        </div>
                    </div>

                    <!-- Table Price Preview -->
                    <div class="bg-white rounded-xl border-2 border-purple-300 p-5 shadow-sm">
                        <div class="text-center">
                            <p class="text-sm text-purple-700 font-semibold mb-2">Giá bàn tính được</p>
                            <p id="table-price-preview" class="text-3xl font-bold text-purple-600">0đ</p>
                            <p class="text-xs text-gray-500 mt-2">Được làm tròn lên hàng nghìn</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between">
                <button type="button" onclick="prevStep(1)" class="px-8 py-3 bg-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </button>
                <button type="button" onclick="validateAndNext(3)" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all duration-200">
                    Tiếp theo <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Products -->
        <div id="step-3" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 hidden">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                        <i class="fas fa-shopping-basket text-white"></i>
                    </div>
                    Bước 3: Thêm sản phẩm
                </h2>
                <button type="button" id="add-product-btn" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition-colors duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Thêm sản phẩm
                </button>
            </div>

            <div id="products-container" class="divide-y divide-gray-200">
                <!-- Products will be added here dynamically -->
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    Phải có ít nhất 1 sản phẩm trong combo
                </p>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between">
                <button type="button" onclick="prevStep(2)" class="px-8 py-3 bg-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </button>
                <button type="button" onclick="validateAndNext(4)" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all duration-200">
                    Tiếp theo <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 4: Summary & Price -->
        <div id="step-4" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    Bước 4: Xác nhận & Giá bán
                </h2>
            </div>

            <div class="p-6 space-y-6">
                <!-- Summary -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200">
                    <h3 class="font-bold text-lg mb-4 text-gray-900">
                        <i class="fas fa-list-check text-blue-600 mr-2"></i>Tóm tắt combo
                    </h3>
                    <div id="combo-summary" class="space-y-3 text-base"></div>
                </div>

                <!-- Pricing -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Giá bán cuối cùng <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="price" id="price" required min="0" step="1000"
                                   class="w-full px-4 py-4 pr-12 border-2 border-blue-500 rounded-lg text-2xl font-bold text-blue-600 focus:ring-2 focus:ring-blue-500"
                                   value="0" oninput="validatePrice()">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-600 text-xl font-bold">đ</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                            Gợi ý: <span id="suggested-price" class="font-semibold text-green-600">0đ</span> (giảm ~20%)
                        </p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border-2 border-green-300">
                        <p class="text-sm font-semibold text-green-800 mb-2">
                            <i class="fas fa-gift mr-1"></i>Khách tiết kiệm
                        </p>
                        <p class="text-3xl font-bold text-green-600 mb-1" id="summary-discount">0đ</p>
                        <p class="text-lg font-semibold text-green-700" id="summary-percent"></p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between">
                <button type="button" onclick="prevStep(3)" class="px-8 py-3 bg-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </button>
                <button type="submit" class="px-10 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-lg hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-200">
                    <i class="fas fa-check mr-2"></i>Tạo Combo
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentStep = 1;
let productIndex = 0;

function setDuration(minutes) {
    document.getElementById('play_duration_minutes').value = minutes;
    
    // Update button styles
    document.querySelectorAll('[onclick^="setDuration"]').forEach(btn => {
        btn.className = 'px-4 py-3 border-2 border-purple-300 bg-white hover:bg-purple-500 hover:text-white hover:border-purple-500 text-gray-700 rounded-lg font-semibold transition-all duration-200 active:scale-95';
    });
    event.target.className = 'px-4 py-3 border-2 border-purple-300 bg-purple-500 text-white border-purple-500 rounded-lg font-semibold shadow-md';
    
    calculateTotal();
}

function nextStep(step) {
    document.getElementById(`step-${currentStep}`).classList.add('hidden');
    document.getElementById(`step-${step}`).classList.remove('hidden');
    
    // Update indicators
    document.querySelectorAll('[id^="step-indicator-"]').forEach((el, index) => {
        const stepNum = index + 1;
        const circle = el.querySelector('div > div');
        const text = el.querySelector('span');
        
        if (stepNum < step) {
            circle.className = 'w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg';
            circle.innerHTML = '<i class="fas fa-check"></i>';
            text.className = 'mt-2 text-sm font-semibold text-green-600';
        } else if (stepNum === step) {
            circle.className = 'w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg';
            circle.textContent = stepNum;
            text.className = 'mt-2 text-sm font-semibold text-blue-600';
        } else {
            circle.className = 'w-12 h-12 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold text-lg';
            circle.textContent = stepNum;
            text.className = 'mt-2 text-sm font-medium text-gray-500';
        }
    });
    
    currentStep = step;
    if (step === 4) calculateTotal();
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) { 
    nextStep(step); 
}

function validateAndNext(step) {
    // Validate step 2 (combo type)
    if (currentStep === 2) {
        const isTimeCombo = document.querySelector('input[name="is_time_combo"]:checked').value === '1';
        if (isTimeCombo) {
            const tableRateId = document.getElementById('table_rate_id').value;
            const playDuration = document.getElementById('play_duration_minutes').value;
            
            if (!tableRateId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Thiếu thông tin',
                    text: 'Vui lòng chọn bảng giá bàn',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            
            if (!playDuration || playDuration < 15) {
                Swal.fire({
                    icon: 'error',
                    title: 'Thời gian không hợp lệ',
                    text: 'Thời gian chơi tối thiểu là 15 phút',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            
            if (playDuration % 15 !== 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Thời gian không hợp lệ',
                    text: 'Thời gian chơi phải là bội số của 15 phút',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
        }
    }
    
    // Validate step 3 (products)
    if (currentStep === 3) {
        const products = document.querySelectorAll('.product-row');
        if (products.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Chưa có sản phẩm',
                text: 'Vui lòng thêm ít nhất 1 sản phẩm vào combo',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }
        
        // Check if all products have been selected
        let hasEmpty = false;
        products.forEach(row => {
            const select = row.querySelector('.product-select');
            if (!select.value) {
                hasEmpty = true;
            }
        });
        
        if (hasEmpty) {
            Swal.fire({
                icon: 'error',
                title: 'Chưa chọn sản phẩm',
                text: 'Vui lòng chọn sản phẩm cho tất cả các dòng',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }
    }
    
    nextStep(step);
}

function toggleTimeComboOptions() {
    const isTime = document.querySelector('input[name="is_time_combo"]:checked').value === '1';
    const options = document.getElementById('time-combo-options');
    
    if (isTime) {
        options.classList.remove('hidden');
        document.getElementById('table_rate_id').setAttribute('required', 'required');
        document.getElementById('play_duration_minutes').setAttribute('required', 'required');
    } else {
        options.classList.add('hidden');
        document.getElementById('table_rate_id').removeAttribute('required');
        document.getElementById('play_duration_minutes').removeAttribute('required');
    }
    
    calculateTotal();
}

function addProduct() {
    const container = document.getElementById('products-container');
    const div = document.createElement('div');
    div.className = 'product-row p-5 hover:bg-gray-50 transition-colors duration-150';
    div.innerHTML = `
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center shadow-sm">
                <i class="fas fa-box text-green-600"></i>
            </div>
            <select name="combo_items[${productIndex}][product_id]" class="product-select flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" required onchange="calculateTotal()">
                <option value="">-- Chọn sản phẩm --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }} - {{ number_format($p->price) }}đ</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-lg border-2 border-gray-300">
                <label class="text-sm text-gray-700 whitespace-nowrap font-semibold">SL:</label>
                <input type="number" name="combo_items[${productIndex}][quantity]" class="quantity-input w-24 px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center font-semibold transition" value="1" min="1" max="999" required oninput="calculateTotal()">
            </div>
            <button type="button" class="remove-product w-11 h-11 flex items-center justify-center text-red-600 hover:bg-red-100 rounded-lg transition-colors duration-200 flex-shrink-0">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    productIndex++;
    
    div.querySelector('.remove-product').onclick = () => {
        if (container.children.length <= 1) {
            return Swal.fire({
                icon: 'warning',
                title: 'Cảnh báo',
                text: 'Phải có ít nhất 1 sản phẩm',
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-lg font-semibold'
                }
            });
        }
        div.remove();
        calculateTotal();
    };
}

function calculateTotal() {
    let productsTotal = 0;
    document.querySelectorAll('.product-row').forEach(row => {
        const select = row.querySelector('.product-select');
        const price = parseFloat(select.selectedOptions[0]?.dataset.price) || 0;
        const qty = parseInt(row.querySelector('.quantity-input').value) || 0;
        productsTotal += price * qty;
    });

    let tablePrice = 0;
    const isTime = document.querySelector('input[name="is_time_combo"]:checked')?.value === '1';
    if (isTime && document.getElementById('table_rate_id')) {
        const rate = document.getElementById('table_rate_id');
        const opt = rate.options[rate.selectedIndex];
        const hourly = parseFloat(opt?.dataset.hourlyRate) || 0;
        const mins = parseFloat(document.getElementById('play_duration_minutes').value) || 0;
        tablePrice = Math.ceil((hourly * mins / 60) / 1000) * 1000;
        document.getElementById('table-price-preview').textContent = new Intl.NumberFormat('vi-VN').format(tablePrice) + 'đ';
    }

    const actualValue = Math.ceil((productsTotal + tablePrice) / 1000) * 1000;
    const suggested = Math.floor(actualValue * 0.8 / 1000) * 1000;

    document.getElementById('suggested-price').textContent = new Intl.NumberFormat('vi-VN').format(suggested) + 'đ';
    
    // Only auto-fill if price field is empty or greater than actual value
    const priceInput = document.getElementById('price');
    if (!priceInput.value || parseInt(priceInput.value) > actualValue || parseInt(priceInput.value) === 0) {
        priceInput.value = suggested;
    }

    // Summary
    let summary = `<div class="text-2xl font-bold mb-4 text-gray-900">${document.getElementById('name').value || 'Combo mới'}</div>`;
    summary += `<div class="flex justify-between py-2 border-b border-gray-200"><strong class="text-gray-700">Tổng sản phẩm:</strong> <span class="font-semibold">${new Intl.NumberFormat('vi-VN').format(productsTotal)}đ</span></div>`;
    if (isTime) summary += `<div class="flex justify-between py-2 border-b border-gray-200"><strong class="text-gray-700">Giá bàn:</strong> <span class="font-semibold">${new Intl.NumberFormat('vi-VN').format(tablePrice)}đ</span></div>`;
    summary += `<div class="flex justify-between py-3 border-t-2 border-blue-300 text-xl font-bold"><strong>Giá trị thực:</strong> <span class="text-blue-600">${new Intl.NumberFormat('vi-VN').format(actualValue)}đ</span></div>`;

    document.getElementById('combo-summary').innerHTML = summary;

    updateDiscountDisplay();
}

function validatePrice() {
    const priceInput = document.getElementById('price');
    const price = parseFloat(priceInput.value) || 0;
    
    // Get actual value from summary
    const summaryText = document.getElementById('combo-summary').textContent;
    const match = summaryText.match(/Giá trị thực:\s*([\d,.]+)đ/);
    const actualValue = match ? parseFloat(match[1].replace(/[,.]/g, '')) : 0;
    
    if (price > actualValue && actualValue > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Giá không hợp lệ',
            text: 'Giá bán không được lớn hơn giá trị thực',
            confirmButtonColor: '#3b82f6'
        });
        priceInput.value = Math.floor(actualValue * 0.8 / 1000) * 1000;
    }
    
    updateDiscountDisplay();
}

function updateDiscountDisplay() {
    const summaryText = document.getElementById('combo-summary')?.textContent || '';
    const match = summaryText.match(/Giá trị thực:\s*([\d,.]+)đ/);
    const actualValue = match ? parseFloat(match[1].replace(/[,.]/g, '')) : 0;
    
    const price = parseFloat(document.getElementById('price').value) || 0;
    const discount = Math.max(0, actualValue - price);
    const percent = actualValue > 0 ? Math.round((discount / actualValue) * 100) : 0;
    
    document.getElementById('summary-discount').textContent = new Intl.NumberFormat('vi-VN').format(discount) + 'đ';
    document.getElementById('summary-percent').textContent = percent > 0 ? `(${percent}% OFF)` : '';
}

document.getElementById('add-product-btn').onclick = addProduct;
document.getElementById('price').oninput = validatePrice;

// Form submission validation
document.getElementById('combo-form').onsubmit = function(e) {
    const name = document.getElementById('name').value.trim();
    if (!name) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập tên combo',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }
    
    const products = document.querySelectorAll('.product-row');
    if (products.length === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Chưa có sản phẩm',
            text: 'Vui lòng thêm ít nhất 1 sản phẩm',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }
    
    const price = parseFloat(document.getElementById('price').value) || 0;
    if (price <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Giá không hợp lệ',
            text: 'Vui lòng nhập giá bán hợp lệ',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }
    
    return true;
};

// Initialize
addProduct();
</script>
@endsection