@extends('layouts.customer')

@section('title', 'Đặt bàn - Poly Billiards')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4">
        <!-- Progress Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between max-w-3xl mx-auto">
                <div class="flex items-center">
                    <div id="step-icon-1" class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">1</div>
                    <span class="ml-2 text-sm font-medium text-gray-700">Thời gian</span>
                </div>
                <div class="w-16 h-1 bg-gray-300" id="line-1"></div>
                <div class="flex items-center">
                    <div id="step-icon-2" class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">2</div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Chọn bàn</span>
                </div>
                <div class="w-16 h-1 bg-gray-300" id="line-2"></div>
                <div class="flex items-center">
                    <div id="step-icon-3" class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">3</div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Xác nhận</span>
                </div>
            </div>
        </div>

        <!-- Step 1: Time Selection -->
        <div id="step-1" class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Chọn thời gian</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày *</label>
                    <input type="date" id="reservation_date" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giờ bắt đầu *</label>
                    <input type="time" id="reservation_time" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thời lượng *</label>
                    <select id="duration" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="60">1 giờ</option>
                        <option value="120" selected>2 giờ</option>
                        <option value="180">3 giờ</option>
                        <option value="240">4 giờ</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số người *</label>
                    <input type="number" id="guest_count" value="2" min="1" max="20" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <button onclick="checkAvailability()" 
                    class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
                Tìm bàn trống
            </button>
        </div>

        <!-- Step 2: Table Selection -->
        <div id="step-2" class="hidden bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Chọn bàn</h2>
                <button onclick="goToStep(1)" class="text-blue-600 hover:text-blue-700 font-medium">← Quay lại</button>
            </div>
            
            <!-- Loading -->
            <div id="loading-tables" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
            </div>
            
            <!-- Tables Grid -->
            <div id="tables-grid" class="hidden">
                <div id="tables-container" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Tables will be inserted here -->
                </div>
                
                <button onclick="goToStep(3)" id="btn-next" disabled
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Tiếp tục
                </button>
            </div>
        </div>

        <!-- Step 3: Confirmation -->
        <div id="step-3" class="hidden bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Xác nhận đặt bàn</h2>
                <button onclick="goToStep(2)" class="text-blue-600 hover:text-blue-700 font-medium">← Quay lại</button>
            </div>
            
            <!-- Booking Summary -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Chi tiết đặt bàn</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bàn:</span>
                        <span class="font-medium" id="summary-table">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Thời gian:</span>
                        <span class="font-medium" id="summary-time">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Thời lượng:</span>
                        <span class="font-medium" id="summary-duration">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Giá/giờ:</span>
                        <span class="font-medium" id="summary-rate">-</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <span class="text-gray-800 font-semibold">Tổng tiền:</span>
                        <span class="text-blue-600 font-bold text-lg" id="summary-price">0đ</span>
                    </div>
                </div>
            </div>
            
            <!-- Customer Info -->
            <form id="customer-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên *</label>
                    <input type="text" id="customer_name" value="{{ auth()->user()->name ?? '' }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại *</label>
                    <input type="tel" id="customer_phone" value="{{ auth()->user()->phone ?? '' }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="customer_email" value="{{ auth()->user()->email ?? '' }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                    <textarea id="note" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Yêu cầu đặc biệt..."></textarea>
                </div>
                
                <!-- Payment Type -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Hình thức thanh toán</label>
                    <div class="space-y-3">
                        <label class="flex items-start cursor-pointer">
                            <input type="radio" name="payment_type" value="online" class="mt-1 mr-3">
                            <div>
                                <div class="font-medium">Thanh toán online</div>
                                <div class="text-sm text-gray-600">Xác nhận ngay, đảm bảo có bàn</div>
                            </div>
                        </label>
                        <label class="flex items-start cursor-pointer">
                            <input type="radio" name="payment_type" value="onsite" checked class="mt-1 mr-3">
                            <div>
                                <div class="font-medium">Thanh toán tại quán</div>
                                <div class="text-sm text-gray-600">Thanh toán khi đến hoặc sau khi chơi</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition">
                    Xác nhận đặt bàn
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-8 max-w-md w-full text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">Đặt bàn thành công!</h3>
        <p class="text-gray-600 mb-1">Mã đặt bàn:</p>
        <p class="text-3xl font-bold text-blue-600 mb-6" id="reservation-code"></p>
        <div class="space-y-2">
            <button onclick="redirectToPayment()" id="btn-payment" class="hidden w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
                Thanh toán ngay
            </button>
            <button onclick="window.location.href='{{ route('reservations.index') }}'" 
                    class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-lg transition">
                Xem đặt bàn của tôi
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentStep = 1;
let selectedTableData = null;
let reservationData = {};
let createdReservationId = null;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('reservation_date').value = today;
    document.getElementById('reservation_date').min = today;
    
    const nextHour = new Date();
    nextHour.setHours(nextHour.getHours() + 1, 0, 0);
    document.getElementById('reservation_time').value = nextHour.toTimeString().slice(0, 5);
});

function goToStep(step) {
    document.getElementById('step-1').classList.add('hidden');
    document.getElementById('step-2').classList.add('hidden');
    document.getElementById('step-3').classList.add('hidden');
    document.getElementById(`step-${step}`).classList.remove('hidden');
    updateProgressBar(step);
    currentStep = step;
}

function updateProgressBar(step) {
    for (let i = 1; i <= 3; i++) {
        const icon = document.getElementById(`step-icon-${i}`);
        const line = document.getElementById(`line-${i}`);
        
        if (i < step) {
            icon.className = 'w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-bold';
            icon.innerHTML = '✓';
            if (line) line.className = 'w-16 h-1 bg-green-600';
        } else if (i === step) {
            icon.className = 'w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold';
            icon.innerHTML = i;
            if (line) line.className = 'w-16 h-1 bg-gray-300';
        } else {
            icon.className = 'w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold';
            icon.innerHTML = i;
        }
    }
}

async function checkAvailability() {
    const date = document.getElementById('reservation_date').value;
    const time = document.getElementById('reservation_time').value;
    const duration = parseInt(document.getElementById('duration').value);
    const guestCount = parseInt(document.getElementById('guest_count').value);

    if (!date || !time) {
        alert('Vui lòng chọn ngày và giờ!');
        return;
    }

    reservationData = {
        reservation_time: `${date} ${time}`,
        duration: duration,
        guest_count: guestCount
    };

    goToStep(2);
    document.getElementById('loading-tables').classList.remove('hidden');
    document.getElementById('tables-grid').classList.add('hidden');

    try {
        const response = await fetch('{{ route("api.tables.available") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ date, time, duration, guest_count: guestCount })
        });

        const data = await response.json();
        
        document.getElementById('loading-tables').classList.add('hidden');
        document.getElementById('tables-grid').classList.remove('hidden');
        
        displayTables(data.tables || data.data || []);
    } catch (error) {
        document.getElementById('loading-tables').classList.add('hidden');
        alert('Lỗi: ' + error.message);
    }
}

function displayTables(tables) {
    const container = document.getElementById('tables-container');
    
    if (!tables || tables.length === 0) {
        container.innerHTML = `
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500 mb-4">Không có bàn trống</p>
                <button onclick="goToStep(1)" class="text-blue-600 hover:text-blue-700 font-medium">
                    Chọn thời gian khác
                </button>
            </div>
        `;
        return;
    }

    container.innerHTML = tables.map(t => {
        // ✅ Xử lý an toàn
        const tableName = t.table_name || t.name || 'Bàn #' + t.id;
        const tableNumber = t.table_number || tableName.substring(0, 3);
        const hourlyRate = Number(t.hourly_rate || 0);
        const totalPrice = Number(t.total_price || 0);
        
        return `
            <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition table-card" 
                 onclick='selectTable(${JSON.stringify(t).replace(/'/g, "&apos;")})' 
                 data-id="${t.id}">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                        ${tableNumber}
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Trống</span>
                </div>
                <div class="font-semibold text-gray-800 mb-2">${tableName}</div>
                <div class="text-sm text-gray-600 space-y-1">
                    <div>👥 ${t.capacity || 0} người</div>
                    <div>💰 ${hourlyRate.toLocaleString()}đ/giờ</div>
                    <div class="font-semibold text-blue-600 pt-2 border-t mt-2">
                        Tổng: ${totalPrice.toLocaleString()}đ
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function selectTable(tableData) {
    document.querySelectorAll('.table-card').forEach(el => {
        el.classList.remove('border-blue-500', 'bg-blue-50');
        el.classList.add('border-gray-200');
    });
    
    const card = document.querySelector(`[data-id="${tableData.id}"]`);
    card.classList.remove('border-gray-200');
    card.classList.add('border-blue-500', 'bg-blue-50');
    
    selectedTableData = tableData;
    document.getElementById('btn-next').disabled = false;
    
    updateSummary();
}

function updateSummary() {
    if (!selectedTableData) return;
    
    const date = document.getElementById('reservation_date').value;
    const time = document.getElementById('reservation_time').value;
    const duration = document.getElementById('duration').value;
    
    document.getElementById('summary-table').textContent = selectedTableData.table_name || selectedTableData.name;
    document.getElementById('summary-time').textContent = `${date} ${time}`;
    document.getElementById('summary-duration').textContent = `${duration} phút`;
    document.getElementById('summary-rate').textContent = Number(selectedTableData.hourly_rate || 0).toLocaleString() + 'đ/giờ';
    document.getElementById('summary-price').textContent = Number(selectedTableData.total_price || 0).toLocaleString() + 'đ';
}

document.getElementById('customer-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!selectedTableData) {
        alert('Vui lòng chọn bàn!');
        return;
    }

    const paymentType = document.querySelector('input[name="payment_type"]:checked').value;

    const payload = {
        table_id: selectedTableData.id,
        reservation_time: reservationData.reservation_time,
        duration: reservationData.duration,
        guest_count: reservationData.guest_count,
        customer_name: document.getElementById('customer_name').value,
        customer_phone: document.getElementById('customer_phone').value,
        customer_email: document.getElementById('customer_email').value || null,
        note: document.getElementById('note').value || null,
        payment_type: paymentType
    };

    try {
        const response = await fetch('{{ route("reservations.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        
        if (data.success) {
    createdReservationId = data.reservation?.id || data.data?.id;
    document.getElementById('reservation-code').textContent = data.reservation_code || data.reservation?.reservation_code || data.data?.reservation_code;
    
    // ✅ SỬA PHẦN NÀY
    if (paymentType === 'online' && data.redirect) {
        // Redirect ngay đến trang thanh toán
        window.location.href = data.redirect;
        return;
    }
    
    // Hiển thị modal cho onsite payment
    document.getElementById('success-modal').classList.remove('hidden');
} else {
    alert(data.message || 'Có lỗi xảy ra!');
}
    } catch (error) {
        alert('Lỗi: ' + error.message);
    }
});

function redirectToPayment() {
    if (createdReservationId) {
        window.location.href = `/reservation/${createdReservationId}/payment`;
    }
}
</script>

<style>
.table-card {
    transition: all 0.2s ease;
}
.table-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
@endsection