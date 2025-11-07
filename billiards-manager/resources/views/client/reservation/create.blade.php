@extends('layouts.customer')

@section('title', 'Đặt bàn - Poly Billiards')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .table-option {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .table-option:hover {
        background-color: #f3f4f6;
        transform: translateY(-2px);
    }
    
    .table-option.selected {
        background-color: #dbeafe;
        border-color: #3b82f6;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .step-content {
        transition: all 0.3s ease;
    }

    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
</style>
@endsection


@section('content')
@if (!auth()->check())
    <script>
        alert('Vui lòng đăng nhập để đặt bàn!');
        window.location.href = '{{ route("login") }}';
    </script>
@else
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <!-- Card Header -->
            <div class="bg-elegant-navy px-6 py-4 border-b-4 border-elegant-gold">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-elegant-gold rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-plus text-elegant-navy"></i>
                    </div>
                    <h2 class="text-2xl font-display font-bold text-white">Đặt bàn</h2>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6">
                <!-- Stepper -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        @foreach([1 => 'Chọn thời gian', 2 => 'Chọn bàn', 3 => 'Thông tin', 4 => 'Xác nhận'] as $step => $label)
                        <div class="flex items-center">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold
                                    @if($step == 1) bg-elegant-gold @else bg-gray-300 @endif
                                    step @if($step == 1) active @endif" id="step{{ $step }}">
                                    {{ $step }}
                                </div>
                                <span class="text-xs mt-2 text-gray-600 text-center">{{ $label }}</span>
                            </div>
                            @if($step < 4)
                            <div class="w-16 h-1 mx-2 bg-gray-300"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Step 1: Time Selection -->
                <div id="step1-content" class="step-content">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Chọn thời gian đặt bàn</h3>
                    
                    <form id="timeForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Date -->
                            <div>
                                <label for="reservation_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ngày đặt bàn *
                                </label>
                                <input type="date" id="reservation_date" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                       min="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Time -->
                            <div>
                                <label for="reservation_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Giờ bắt đầu *
                                </label>
                                <input type="time" id="reservation_time" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                       min="08:00" max="23:00" required>
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Thời lượng *</label>
                                <select id="duration" class="w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                                    <option value="60">1 giờ</option>
                                    <option value="120" selected>2 giờ</option>
                                    <option value="180">3 giờ</option>
                                    <option value="240">4 giờ</option>
                                    <option value="300">5 giờ</option>
                                </select>
                            </div>

                            <!-- Guest Count -->
                            <div>
                                <label for="guest_count" class="block text-sm font-medium text-gray-700 mb-2">
                                    Số người *
                                </label>
                                <input type="number" id="guest_count" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                       min="1" max="10" value="2" required>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="button" onclick="checkAvailability()" 
                                    class="w-full md:w-auto bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-8 py-4 rounded-lg transition duration-200 transform hover:scale-105 flex items-center justify-center">
                                <i class="fas fa-search mr-3"></i>
                                Kiểm tra bàn trống
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: Table Selection -->
                <div id="step2-content" class="step-content hidden">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Chọn bàn phù hợp</h3>
                    
                    <div id="tableResults" class="border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 min-h-[200px] flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-clock text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">Vui lòng chọn thời gian để xem bàn trống</p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" onclick="previousStep(2)" 
                                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Quay lại
                        </button>
                        <button type="button" onclick="nextStep(2)" id="nextStep2" disabled
                                class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                            Tiếp theo
                            <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Customer Information -->
                <div id="step3-content" class="step-content hidden">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Thông tin khách hàng</h3>
                    
                    <form id="confirmForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Họ tên *
                                </label>
                                <input type="text" id="customer_name" value="{{ auth()->check() ? auth()->user()->name : '' }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                       required>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Số điện thoại *
                                </label>
                                <input type="tel" id="customer_phone" value="{{ auth()->check() ? auth()->user()->phone : '' }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                       required>
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email" id="customer_email" value="{{ auth()->check() ? auth()->user()->email : '' }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200">
                            </div>

                            <!-- Note -->
                            <div class="md:col-span-2">
                                <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ghi chú thêm
                                </label>
                                <textarea id="note" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                                          placeholder="Yêu cầu đặc biệt, dịp kỷ niệm..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="previousStep(3)" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Quay lại
                            </button>
                            <button type="submit" 
                                    class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center">
                                <i class="fas fa-check mr-2"></i>
                                Xác nhận đặt bàn
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 4: Confirmation -->
                <div id="step4-content" class="step-content hidden">
                    <div class="text-center py-8">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-check-circle text-green-600 text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Đặt bàn thành công!</h3>
                        <p class="text-lg text-gray-600 mb-2">Mã đặt bàn của bạn:</p>
                        <p class="text-2xl font-bold text-elegant-gold mb-6" id="reservationCode"></p>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8 max-w-md mx-auto">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                                <p class="text-blue-800 text-sm">Chúng tôi sẽ gửi xác nhận qua SMS/Email. Vui lòng đến đúng giờ!</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('home') }}" 
                               class="bg-elegant-gold hover:bg-yellow-500 text-elegant-navy font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                                <i class="fas fa-home mr-2"></i>
                                Về trang chủ
                            </a>
                            <a href="{{ route('reservations.track') }}" 
                               class="border border-elegant-gold text-elegant-gold hover:bg-elegant-gold hover:text-elegant-navy font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>
                                Tra cứu đặt bàn
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let currentStep = 1;
let selectedTable = localStorage.getItem('selectedTable') || null;
let reservationData = JSON.parse(localStorage.getItem('reservationData')) || { date: null, time: null, duration: null };

async function safeFetch(url, options = {}) {
    const opts = {
        credentials: 'same-origin', // gửi cookie/session Laravel
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            ...(options.headers || {})
        },
        ...options
    };

    const res = await fetch(url, opts);
    let json = null;
    try {
        json = await res.json();
    } catch (e) {
        throw new Error(`Server trả lỗi không phải JSON (status ${res.status})`);
    }
    if (!res.ok) {
        const message = json.message || json.error || `Server error ${res.status}`;
        const errors = json.errors || null;
        const err = new Error(message);
        err.status = res.status;
        err.errors = errors;
        throw err;
    }
    return json;
}

// TỰ ĐỘNG CHUYỂN BƯỚC NẾU CÓ DỮ LIỆU (BƯỚC 2)
document.addEventListener('DOMContentLoaded', function () {
    if (selectedTable && reservationData.date && reservationData.time && reservationData.duration) {
        showStep(3); // Tự động sang bước 3 khi reload
    }
});

function updateStepper(step) {
    document.querySelectorAll('.step').forEach(s => {
        s.classList.remove('bg-elegant-gold'); s.classList.add('bg-gray-300');
    });
    for (let i = 1; i <= step; i++) {
        document.getElementById(`step${i}`).classList.remove('bg-gray-300');
        document.getElementById(`step${i}`).classList.add('bg-elegant-gold');
    }
}

function showStep(step) {
    document.querySelectorAll('.step-content').forEach(c => c.classList.add('hidden'));
    document.getElementById(`step${step}-content`).classList.remove('hidden');
    updateStepper(step);
    currentStep = step;
}

function nextStep(step) { showStep(step + 1); }
function previousStep(step) { showStep(step - 1); }

async function checkAvailability() {
    const date = document.getElementById('reservation_date').value;
    const time = document.getElementById('reservation_time').value;
    const duration = parseInt(document.getElementById('duration').value);

    if (!date || !time || !duration) {
        alert('Vui lòng nhập đầy đủ thông tin!');
        return;
    }

    const container = document.getElementById('tableResults');
    container.innerHTML = '<p class="text-center">Đang kiểm tra...</p>';

    try {
        const payload = { date, time, duration };
        const data = await safeFetch('{{ route("api.tables.available") }}', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        // Hỗ trợ nhiều format backend: data.tables || data.data
        const tables = data.tables || data.data || [];
        displayTables(tables);

        // Lưu reservationData theo 1 chuẩn duy nhất
        const reservationTime = `${date} ${time}`; // "YYYY-MM-DD HH:mm"
        reservationData = { reservation_time: reservationTime, duration: duration };
        localStorage.setItem('reservationData', JSON.stringify(reservationData));

    } catch (err) {
        console.error('checkAvailability error:', err);
        container.innerHTML = `<p class="text-center text-red-600">Lỗi: ${err.message}</p>`;
        if (err.errors) console.log(err.errors);
    }
}

function displayTables(tables) {
    const container = document.getElementById('tableResults');
    if (!tables || tables.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500">Không có bàn trống</p>';
        return;
    }

let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    tables.forEach(t => {
        html += `
            <div class="p-4 border rounded-lg cursor-pointer hover:bg-gray-50 table-option" 
                 onclick="selectTable(${t.id})" data-id="${t.id}">
                <h6 class="font-bold">${t.table_name}</h6>
                <p>Sức chứa: ${t.capacity} người</p>
                <p>Giá: ${t.hourly_rate}đ/giờ</p>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
    showStep(2); // CHÍNH XÁC: chuyển sang bước 2
}

function selectTable(id) {
    document.querySelectorAll('[data-id]').forEach(el => {
        el.classList.remove('border-blue-500', 'bg-blue-50');
    });
    const el = document.querySelector(`[data-id="${id}"]`);
    el.classList.add('border-blue-500', 'bg-blue-50');
    selectedTable = id;
    localStorage.setItem('selectedTable', id);
    document.getElementById('nextStep2').disabled = false;
}

// Handle form submission
document.getElementById('confirmForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!selectedTable || !reservationData || !reservationData.reservation_time) {
        alert('Vui lòng chọn bàn và thời gian!');
        return;
    }

    const payload = {
        table_id: selectedTable,
        reservation_time: reservationData.reservation_time, // "YYYY-MM-DD HH:mm"
        duration: reservationData.duration,
        guest_count: parseInt(document.getElementById('guest_count').value) || 1,
        customer_name: document.getElementById('customer_name').value,
        customer_phone: document.getElementById('customer_phone').value,
        customer_email: document.getElementById('customer_email').value || null,
        note: document.getElementById('note').value || null,
    };

    try {
        const res = await safeFetch('{{ route("reservation.store") }}', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        // success
        document.getElementById('reservationCode').textContent = res.reservation_code || res.data?.reservation_code || '';
        // clear storage
        localStorage.removeItem('selectedTable');
        localStorage.removeItem('reservationData');
        selectedTable = null; reservationData = {};
        showStep(4);

    } catch (err) {
        console.error('Reservation store error:', err);
        if (err.errors) {
            const errs = Object.values(err.errors).flat();
            alert('Lỗi:\n• ' + errs.join('\n• '));
        } else {
            alert(err.message || 'Đã có lỗi xảy ra. Vui lòng thử lại.');
        }
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('reservation_date').value = today;
    document.getElementById('reservation_date').min = today;

    const nextHour = new Date();
    nextHour.setHours(nextHour.getHours() + 1);
    nextHour.setMinutes(0);
    document.getElementById('reservation_time').value = nextHour.toTimeString().slice(0, 5);

    $('#duration').select2({
        minimumResultsForSearch: -1
    });
});
</script>
@endsection