<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - {{ $bill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        success: {
                            50: '#f0fdf4',
                            500: '#22c55e',
                            600: '#16a34a',
                        }
                    },
                    borderRadius: {
                        'xl': '12px',
                        '2xl': '16px',
                    },
                    boxShadow: {
                        'smooth': '0 4px 20px rgba(0, 0, 0, 0.08)',
                        'elegant': '0 8px 30px rgba(0, 0, 0, 0.12)',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-smooth mb-4">
                <i class="fas fa-receipt text-2xl text-primary-500"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Thanh Toán Hóa Đơn</h1>
            <p class="text-gray-600 text-lg">Hoàn tất quá trình thanh toán</p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Left Column - Bill Details -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Bill Info Card -->
                <div class="bg-white rounded-2xl shadow-smooth p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                                <i class="fas fa-file-invoice-dollar text-primary-500 mr-3"></i>
                                Thông Tin Hóa Đơn
                            </h2>
                            <p class="text-gray-500 mt-1">Mã: <span
                                    class="font-semibold">{{ $bill->bill_number }}</span></p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Tổng thanh toán</div>
                            <div class="text-3xl font-bold text-success-600">
                                {{ number_format(ceil($bill->final_amount / 1000) * 1000) }} ₫
                            </div>
                        </div>
                    </div>

                    <!-- Table Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="text-sm text-gray-500 mb-1">Bàn</div>
                            <div class="font-semibold text-gray-800">{{ $bill->table->table_name }}</div>
                            <div class="text-xs text-gray-400">
                                @if ($bill->table->tableRate)
                                    {{ $bill->table->tableRate->name }}
                                @else
                                    Chưa phân loại
                                @endif
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="text-sm text-gray-500 mb-1">Thời gian bắt đầu</div>
                            <div class="font-semibold text-gray-800">{{ $bill->start_time->format('H:i') }}</div>
                            <div class="text-xs text-gray-400">{{ $bill->start_time->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <!-- Bill Items -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-list-ul text-primary-500 mr-2"></i>
                            Chi Tiết Hóa Đơn
                        </h3>

                        <div class="space-y-3">
                            <!-- Time Usage -->
                            @php
                                $roundedTimeCost = ceil($timeCost / 1000) * 1000;
                            @endphp
                            @if ($roundedTimeCost > 0)
                                <div
                                    class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-clock text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">Giờ chơi</div>
                                            <div class="text-sm text-gray-600">
                                                @php
                                                    $totalMinutes = $timeDetails['total_minutes'] ?? 0;
                                                    $hourlyRate = $timeDetails['hourly_rate'] ?? 0;
                                                @endphp
                                                {{ $totalMinutes }} phút
                                                @
                                                {{ number_format(ceil($hourlyRate / 1000) * 1000) }}₫/giờ
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-800">{{ number_format($roundedTimeCost) }} ₫
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Combos -->
                            @foreach ($bill->billDetails->where('combo_id', '!=', null)->where('is_combo_component', false) as $comboDetail)
                                @php
                                    $roundedComboPrice = ceil($comboDetail->unit_price / 1000) * 1000;
                                    $roundedComboTotal = ceil($comboDetail->total_price / 1000) * 1000;
                                @endphp
                                <div
                                    class="flex items-center justify-between p-4 bg-purple-50 rounded-xl border border-purple-100">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-gift text-purple-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">
                                                {{ $comboDetail->combo->name ?? 'Combo' }}
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                @if ($comboDetail->combo)
                                                    @php
                                                        $components = $bill->billDetails->where(
                                                            'parent_bill_detail_id',
                                                            $comboDetail->id,
                                                        );
                                                    @endphp
                                                    @if ($components->count() > 0)
                                                        @foreach ($components as $component)
                                                            {{ $component->quantity }}x
                                                            {{ $component->product->name ?? 'Sản phẩm' }}
                                                            @if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{ $comboDetail->combo->description ?? 'Combo' }}
                                                    @endif
                                                @else
                                                    Combo đã bị xóa
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">{{ $comboDetail->quantity }} x
                                            {{ number_format($roundedComboPrice) }} ₫</div>
                                        <div class="font-bold text-gray-800">{{ number_format($roundedComboTotal) }} ₫
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Individual Products -->
                            @foreach ($bill->billDetails->whereNull('combo_id')->where('is_combo_component', false) as $item)
                                @if ($item->product)
                                    @php
                                        $roundedUnitPrice = ceil($item->unit_price / 1000) * 1000;
                                        $roundedItemTotal = ceil($item->total_price / 1000) * 1000;
                                    @endphp
                                    <div
                                        class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                                <i class="fas fa-utensils text-green-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800">
                                                    {{ $item->product->name }}
                                                </div>
                                                <div class="text-sm text-gray-600">Đơn giá:
                                                    {{ number_format($roundedUnitPrice) }} ₫</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-500">{{ $item->quantity }} x
                                                {{ number_format($roundedUnitPrice) }} ₫</div>
                                            <div class="font-bold text-gray-800">{{ number_format($roundedItemTotal) }}
                                                ₫
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Extra Charges -->
                            @foreach ($bill->billDetails->whereNull('product_id')->whereNull('combo_id')->where('is_combo_component', false) as $extra)
                                @php
                                    $roundedExtraPrice = ceil($extra->unit_price / 1000) * 1000;
                                    $roundedExtraTotal = ceil($extra->total_price / 1000) * 1000;
                                @endphp
                                <div
                                    class="flex items-center justify-between p-4 bg-orange-50 rounded-xl border border-orange-100">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-plus-circle text-orange-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-orange-700">
                                                {{ $extra->note ?? 'Phí phát sinh' }}</div>
                                            <div class="text-sm text-orange-600">Phí phát sinh</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-orange-600">{{ $extra->quantity }} x
                                            {{ number_format($roundedExtraPrice) }} ₫</div>
                                        <div class="font-bold text-orange-700">{{ number_format($roundedExtraTotal) }}
                                            ₫</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                @if ($bill->user)
                    <div class="bg-white rounded-2xl shadow-smooth p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user text-primary-500 mr-2"></i>
                            Thông Tin Khách Hàng
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-primary-600 mb-1">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="text-sm text-gray-500">Tên khách</div>
                                <div class="font-semibold text-gray-800 truncate">{{ $bill->user->name }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-primary-600 mb-1">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="text-sm text-gray-500">Điện thoại</div>
                                <div class="font-semibold text-gray-800">{{ $bill->user->phone }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-primary-600 mb-1">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div class="text-sm text-gray-500">Loại khách</div>
                                <div class="font-semibold">
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                        {{ $bill->user->customer_type ?? 'Khách mới' }}
                                    </span>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-primary-600 mb-1">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="text-sm text-gray-500">Số lần đến</div>
                                <div class="font-semibold text-gray-800">{{ $bill->user->total_visits ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Payment -->
            <div class="xl:col-span-1">
                <div class="bg-white rounded-2xl shadow-elegant p-6 sticky top-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-credit-card text-primary-500 mr-3"></i>
                        Phương Thức Thanh Toán
                    </h2>

                    @php
                        $finalAmount = ceil($bill->final_amount / 1000) * 1000;
                    @endphp

                    <form action="{{ route('admin.bills.process-payment', $bill->id) }}" method="POST" id="paymentForm">
                        @csrf

                        <!-- Payment Methods -->
                        <div class="space-y-3 mb-6">
                            <div class="payment-method-card" data-method="cash">
                                <input type="radio" name="payment_method" value="cash" id="cash"
                                    class="hidden" checked>
                                <div
                                    class="flex items-center p-4 border-2 border-primary-500 bg-primary-50 rounded-xl cursor-pointer transition-all">
                                    <div
                                        class="w-6 h-6 rounded-full border-2 border-primary-500 flex items-center justify-center mr-4">
                                        <div class="w-3 h-3 rounded-full bg-primary-500 payment-check"></div>
                                    </div>
                                    <div class="flex items-center flex-1">
                                        <div
                                            class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800">Tiền mặt</div>
                                            <div class="text-sm text-gray-500">Thanh toán bằng tiền mặt</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-method-card" data-method="bank">
                                <input type="radio" name="payment_method" value="bank" id="bank"
                                    class="hidden">
                                <div
                                    class="flex items-center p-4 border-2 border-gray-200 bg-white rounded-xl cursor-pointer hover:border-primary-300 transition-all">
                                    <div
                                        class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4">
                                        <div class="w-3 h-3 rounded-full bg-white payment-check"></div>
                                    </div>
                                    <div class="flex items-center flex-1">
                                        <div
                                            class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-university text-blue-600 text-xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800">Chuyển khoản</div>
                                            <div class="text-sm text-gray-500">Chuyển khoản ngân hàng</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-method-card" data-method="card">
                                <input type="radio" name="payment_method" value="card" id="card"
                                    class="hidden">
                                <div
                                    class="flex items-center p-4 border-2 border-gray-200 bg-white rounded-xl cursor-pointer hover:border-primary-300 transition-all">
                                    <div
                                        class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4">
                                        <div class="w-3 h-3 rounded-full bg-white payment-check"></div>
                                    </div>
                                    <div class="flex items-center flex-1">
                                        <div
                                            class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-credit-card text-purple-600 text-xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800">Thẻ</div>
                                            <div class="text-sm text-gray-500">Thẻ ATM/Visa/Mastercard</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="space-y-4 mb-6">
                            <!-- Amount -->
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Số tiền thanh
                                    toán</label>
                                <input type="number" name="amount" value="{{ $finalAmount }}"
                                    class="w-full bg-white border-0 text-2xl font-bold text-success-600 text-right focus:ring-0"
                                    readonly>
                            </div>

                            <!-- Cash Received -->
                            <div id="cashAmountSection" class="transition-all duration-300">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Khách đưa</label>
                                <input type="number" id="cash_received" name="cash_received"
                                    value="{{ $finalAmount }}"
                                    class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-lg text-right focus:border-primary-500 focus:ring-0"
                                    oninput="calculateChange()" min="{{ $finalAmount }}" step="1000">
                            </div>

                            <!-- Change Amount -->
                            <div id="changeAmountSection" class="transition-all duration-300">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tiền thối lại</label>
                                <input type="number" id="change_amount" name="change_amount" value="0"
                                    class="w-full border-2 border-success-200 bg-success-50 rounded-xl px-4 py-3 text-lg font-bold text-success-600 text-right focus:ring-0"
                                    readonly>
                            </div>

                            <!-- Note -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú</label>
                                <textarea name="note" rows="3"
                                    class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-primary-500 focus:ring-0 resize-none"
                                    placeholder="Nhập ghi chú cho hóa đơn..."></textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-success-500 to-success-600 hover:from-success-600 hover:to-success-700 text-white py-4 px-6 rounded-xl transition-all transform hover:scale-[1.02] font-semibold text-lg shadow-lg">
                                <i class="fas fa-check-circle mr-2"></i>
                                Xác Nhận Thanh Toán
                            </button>

                            <a href="{{ route('admin.tables.detail', $bill->table_id) }}"
                                class="w-full bg-gray-500 hover:bg-gray-600 text-white py-4 px-6 rounded-xl transition-all text-center block font-semibold text-lg">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Quay Lại
                            </a>
                        </div>

                        <!-- Thêm vào phần header hoặc sau khi thanh toán thành công -->
                        <div class="flex gap-2 mt-4">
                            <a href="{{ route('admin.bills.print', $bill->id) }}" target="_blank"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                🖨️ In hóa đơn
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        const totalAmount = {{ $finalAmount }};

        // Payment method selection
        document.querySelectorAll('.payment-method-card').forEach(method => {
            method.addEventListener('click', function() {
                // Remove all selections
                document.querySelectorAll('.payment-method-card').forEach(m => {
                    const card = m.querySelector('div');
                    const check = m.querySelector('.payment-check');
                    card.classList.remove('border-primary-500', 'bg-primary-50');
                    card.classList.add('border-gray-200', 'bg-white');
                    check.classList.remove('bg-primary-500');
                    check.classList.add('bg-white');
                    m.querySelector('input[type="radio"]').checked = false;
                });

                // Select this method
                const card = this.querySelector('div');
                const check = this.querySelector('.payment-check');
                card.classList.remove('border-gray-200', 'bg-white');
                card.classList.add('border-primary-500', 'bg-primary-50');
                check.classList.remove('bg-white');
                check.classList.add('bg-primary-500');
                this.querySelector('input[type="radio"]').checked = true;

                // Show/hide cash sections
                const selectedMethod = this.dataset.method;
                const cashSection = document.getElementById('cashAmountSection');
                const changeSection = document.getElementById('changeAmountSection');

                if (selectedMethod === 'cash') {
                    cashSection.style.display = 'block';
                    changeSection.style.display = 'block';
                    calculateChange();
                } else {
                    cashSection.style.display = 'none';
                    changeSection.style.display = 'none';
                }
            });
        });

        // Calculate change
        function calculateChange() {
            const cashReceived = parseFloat(document.getElementById('cash_received').value) || 0;
            const changeAmount = Math.max(0, cashReceived - totalAmount);
            const roundedChange = Math.ceil(changeAmount / 1000) * 1000;

            const changeInput = document.getElementById('change_amount');
            changeInput.value = roundedChange;

            // Update styling based on change amount
            if (cashReceived < totalAmount) {
                changeInput.classList.remove('border-success-200', 'bg-success-50', 'text-success-600');
                changeInput.classList.add('border-red-200', 'bg-red-50', 'text-red-600');
            } else {
                changeInput.classList.remove('border-red-200', 'bg-red-50', 'text-red-600');
                changeInput.classList.add('border-success-200', 'bg-success-50', 'text-success-600');
            }
        }

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const cashReceived = parseFloat(document.getElementById('cash_received').value) || 0;

            if (paymentMethod === 'cash' && cashReceived < totalAmount) {
                e.preventDefault();
                alert('Số tiền khách đưa không đủ! Vui lòng nhập số tiền lớn hơn hoặc bằng ' + totalAmount
                    .toLocaleString('vi-VN') + ' ₫');
                return false;
            }

            if (!confirm('Xác nhận thanh toán hóa đơn ' + totalAmount.toLocaleString('vi-VN') + ' ₫?')) {
                e.preventDefault();
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';
            submitBtn.disabled = true;
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.payment-method-card[data-method="cash"]').click();
            document.getElementById('cash_received').value = totalAmount;
            calculateChange();
        });
    </script>
</body>

</html>
