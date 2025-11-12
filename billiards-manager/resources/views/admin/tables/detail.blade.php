{{-- resources/views/tables/detail.blade.php --}}
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chi Tiết Bàn - {{ $table->table_name }}</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card {
            @apply bg-white rounded-lg shadow px-6 py-5;
        }

        .btn-neutral {
            @apply bg-gray-900 text-white rounded-md px-4 py-2 hover:bg-gray-800 transition;
        }

        .btn-outline {
            @apply border border-gray-300 text-gray-900 rounded-md px-3 py-2 hover:bg-gray-50 transition;
        }

        table th,
        table td {
            @apply px-3 py-2 text-sm;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="max-w-7xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.tables.index') }}" class="btn-outline inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
            <h1 class="text-2xl font-semibold">Chi tiết bàn — <span class="text-lg">{{ $table->table_name }}</span></h1>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            {{-- Left Sidebar --}}
            <div class="xl:col-span-1 space-y-6">
                {{-- Table Info Card --}}
                <div class="card">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-bold">{{ $table->table_name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">Số: {{ $table->table_number }}</p>
                        </div>
                        <div class="text-right">
                            <span
                                class="text-xs font-medium px-2 py-1 rounded-md {{ $table->status === 'available' ? 'bg-gray-100 text-gray-800' : 'bg-gray-800 text-white' }}">
                                {{ $table->status === 'available' ? 'Trống' : 'Đang sử dụng' }}
                            </span>
                        </div>
                    </div>

                    @if ($table->currentBill)
                        <div class="mt-4 pt-4 border-t">
                            <div class="text-sm text-gray-600">Tổng hiện tại</div>
                            <div id="totalAmountDisplay" class="text-2xl font-bold text-green-600 mt-1">
                                {{ number_format(round($table->currentBill->final_amount)) }} ₫
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="card">
                    <h3 class="text-lg font-semibold mb-4">🚀 Thao Tác Nhanh</h3>
                    <div class="space-y-3">
                        @if ($table->currentBill)
                            <a href="{{ route('bills.payment-page', $table->currentBill->id) }}"
                                class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-md transition font-semibold text-center block">
                                <i class="fas fa-credit-card mr-2"></i>
                                Thanh Toán
                            </a>

                            <button onclick="updateBillTotal()" class="w-full btn-outline py-3 text-center">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Cập Nhật Tổng Tiền
                            </button>

                            @if ($timeInfo['mode'] === 'combo' && $timeInfo['is_near_end'])
                                <form action="{{ route('bills.extend-combo', $table->currentBill->id) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="extra_minutes" value="30">
                                    <button type="submit"
                                        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-md transition font-semibold">
                                        <i class="fas fa-clock mr-2"></i>
                                        Gia Hạn 30 Phút
                                    </button>
                                </form>
                            @endif

                            @if ($timeInfo['mode'] === 'combo')
                                <form action="{{ route('bills.switch-regular', $table->currentBill->id) }}"
                                    method="POST" onsubmit="return confirm('Chuyển sang tính giờ thường?')">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-md transition font-semibold">
                                        <i class="fas fa-exchange-alt mr-2"></i>
                                        Chuyển Giờ Thường
                                    </button>
                                </form>
                            @endif
                        @else
                            <form action="{{ route('bills.create') }}" method="POST">
                                @csrf
                                <input type="hidden" name="table_id" value="{{ $table->id }}">
                                <input type="hidden" name="guest_count" value="1">
                                <button type="submit"
                                    class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-md transition font-semibold">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tạo Hóa Đơn Mới
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Customer Info --}}
                @if ($table->currentBill && $table->currentBill->customer)
                    <div class="card">
                        <h3 class="text-lg font-semibold mb-4">👤 Khách Hàng</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500">Tên khách hàng</div>
                                <div class="font-semibold">{{ $table->currentBill->customer->name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Số điện thoại</div>
                                <div class="font-semibold">{{ $table->currentBill->customer->phone }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Loại khách</div>
                                <div>
                                    <span
                                        class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                        {{ $table->currentBill->customer->customer_type }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Số lần đến</div>
                                <div class="font-semibold">{{ $table->currentBill->customer->total_visits }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Main Content --}}
            <div class="xl:col-span-3 space-y-6">
                {{-- Time Tracking --}}
                <div class="card">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-2xl font-bold flex items-center">
                                <i class="fas fa-clock text-blue-500 mr-3"></i>
                                Theo Dõi Thời Gian
                            </h2>
                            <p class="text-gray-600 mt-1">Cập nhật thời gian thực từ server</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div id="modeBadge"
                                class="px-3 py-2 rounded-md text-sm font-semibold {{ $timeInfo['mode'] === 'regular' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $timeInfo['mode'] === 'regular' ? '🕒 Giờ Thường' : '🎁 Combo Time' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Current Time --}}
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-xs text-gray-500 mb-2">Thời Gian Hiện Tại</div>
                            <div id="currentTime" class="text-2xl font-mono font-bold text-gray-800">--:--:--</div>
                        </div>

                        {{-- Elapsed Time --}}
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-xs text-blue-600 mb-2">Đã Sử Dụng</div>
                            <div id="elapsedTimeDisplay" class="text-2xl font-mono font-bold text-blue-700">00:00:00
                            </div>
                        </div>

                        {{-- Remaining Time --}}
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-xs text-green-600 mb-2">Thời Gian Còn Lại</div>
                            <div id="remainingTimeDisplay" class="text-2xl font-mono font-bold text-green-700">--:--
                            </div>
                        </div>

                        {{-- Current Cost --}}
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <div class="text-xs text-orange-600 mb-2">Chi Phí Hiện Tại</div>
                            <div id="currentCostDisplay" class="text-2xl font-bold text-orange-700">
                                {{ number_format(round($timeInfo['current_cost'])) }} ₫
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    @if ($timeInfo['mode'] === 'combo')
                        <div class="mt-6">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Tiến độ sử dụng combo</span>
                                <span id="progressText">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div id="progressBar"
                                    class="bg-blue-500 h-3 rounded-full transition-all duration-1000"
                                    style="width: 0%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Add Products & Combos --}}
                @if ($table->currentBill)
                    <div class="card">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-plus-circle text-green-500 mr-3"></i>
                            Thêm Sản Phẩm & Combo
                        </h2>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Add Combo --}}
                            <div class="border-2 border-dashed border-purple-200 rounded-xl p-5 bg-purple-50">
                                <h3 class="text-lg font-semibold mb-4 flex items-center text-purple-800">
                                    <i class="fas fa-gift mr-2"></i>
                                    Thêm Combo
                                </h3>
                                <form action="{{ route('bills.add-combo', $table->currentBill->id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="space-y-3">
                                        <select name="combo_id"
                                            class="w-full border border-purple-300 rounded-lg px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                                            required>
                                            <option value="">Chọn combo...</option>
                                            @foreach ($combos as $combo)
                                                <option value="{{ $combo->id }}">{{ $combo->name }} -
                                                    {{ number_format($combo->price) }}₫</option>
                                            @endforeach
                                        </select>
                                        <div class="flex gap-3">
                                            <input type="number" name="quantity" value="1" min="1"
                                                class="flex-1 border border-purple-300 rounded-lg px-4 py-3 focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                                                required>
                                            <button type="submit"
                                                class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                                                <i class="fas fa-plus mr-2"></i>
                                                Thêm
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- Add Product --}}
                            <div class="border-2 border-dashed border-green-200 rounded-xl p-5 bg-green-50">
                                <h3 class="text-lg font-semibold mb-4 flex items-center text-green-800">
                                    <i class="fas fa-utensils mr-2"></i>
                                    Thêm Sản Phẩm
                                </h3>
                                <form action="{{ route('bills.add-product', $table->currentBill->id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="space-y-3">
                                        <select name="product_id"
                                            class="w-full border border-green-300 rounded-lg px-4 py-3 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                            required>
                                            <option value="">Chọn sản phẩm...</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} -
                                                    {{ number_format($product->price) }}₫</option>
                                            @endforeach
                                        </select>
                                        <div class="flex gap-3">
                                            <input type="number" name="quantity" value="1" min="1"
                                                class="flex-1 border border-green-300 rounded-lg px-4 py-3 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                                required>
                                            <button type="submit"
                                                class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                                                <i class="fas fa-plus mr-2"></i>
                                                Thêm
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Bill Details --}}
                <div class="card">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-receipt text-gray-700 mr-3"></i>
                            Chi Tiết Hóa Đơn
                        </h2>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Tổng hóa đơn</div>
                            <div id="finalAmountDisplay" class="text-3xl font-bold text-green-600">
                                {{ number_format(round($table->currentBill->final_amount ?? 0)) }} ₫
                            </div>
                        </div>
                    </div>

                    @if ($table->currentBill && $table->currentBill->billDetails->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="text-left py-3 px-4 font-semibold">Sản phẩm/Dịch vụ</th>
                                        <th class="text-center py-3 px-4 font-semibold">SL</th>
                                        <th class="text-right py-3 px-4 font-semibold">Đơn giá</th>
                                        <th class="text-right py-3 px-4 font-semibold">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($table->currentBill->billDetails as $item)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4">
                                                <div class="flex items-center">
                                                    @if ($item->product_id && $item->product)
                                                        <i class="fas fa-utensils text-green-500 mr-3"></i>
                                                        <div>
                                                            <div class="font-medium">{{ $item->product->name }}</div>
                                                            @if ($item->is_combo_component)
                                                                <div class="text-xs text-gray-500">Thành phần combo
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @elseif($item->combo_id && $item->combo)
                                                        <i class="fas fa-gift text-purple-500 mr-3"></i>
                                                        <div>
                                                            <div class="font-medium">{{ $item->combo->name }}</div>
                                                            <div class="text-xs text-gray-500">Combo</div>
                                                        </div>
                                                    @else
                                                        <i class="fas fa-plus-circle text-orange-500 mr-3"></i>
                                                        <div class="font-medium">{{ $item->note ?? 'Dịch vụ khác' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center py-3 px-4">
                                                <span class="bg-gray-100 px-2 py-1 rounded-md text-sm">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                            <td class="text-right py-3 px-4 font-medium">
                                                {{ number_format(round($item->unit_price)) }} ₫
                                            </td>
                                            <td class="text-right py-3 px-4 font-bold text-green-600">
                                                {{ number_format(round($item->total_price)) }} ₫
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 border-t pt-4">
                            <div class="flex justify-between items-center">
                                <div class="text-lg font-semibold">Tổng cộng:</div>
                                <div id="billTotalAmount" class="text-3xl font-bold text-green-600">
                                    {{ number_format(round($table->currentBill->final_amount)) }} ₫
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-receipt text-5xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">Chưa có sản phẩm nào trong hóa đơn</p>
                            <p class="text-gray-400 text-sm mt-2">Thêm sản phẩm hoặc combo để bắt đầu</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // Server data
        const isRunning = {{ $timeInfo['is_running'] ? 'true' : 'false' }};
        const currentMode = '{{ $timeInfo['mode'] }}';
        const hourlyRate = Number({{ round($timeInfo['hourly_rate']) ?? 0 }});
        const totalComboMinutes = Number({{ $timeInfo['total_minutes'] ?? 0 }});
        const elapsedMinutesFromServer = Number({{ $timeInfo['elapsed_minutes'] ?? 0 }});

        let startTimeMs = null;
        @if ($timeInfo['is_running'] && $table->currentBill)
            startTimeMs = new Date('{{ $table->currentBill->start_time->format('Y-m-d\TH:i:s.v\Z') }}').getTime();
        @endif

        const totalComboSeconds = totalComboMinutes * 60;
        let rafId = null;

        // Format functions
        function pad(n) {
            return n.toString().padStart(2, '0');
        }

        function formatHMS(totalSeconds) {
            const hrs = Math.floor(totalSeconds / 3600);
            const mins = Math.floor((totalSeconds % 3600) / 60);
            const secs = Math.floor(totalSeconds % 60);
            return `${pad(hrs)}:${pad(mins)}:${pad(secs)}`;
        }

        function formatHM(totalSeconds) {
            const hrs = Math.floor(totalSeconds / 3600);
            const mins = Math.floor((totalSeconds % 3600) / 60);
            return `${pad(hrs)}:${pad(mins)}`;
        }

        function formatCurrency(amount) {
            const rounded = Math.round(amount);
            return new Intl.NumberFormat('vi-VN').format(rounded) + ' ₫';
        }

        function calculateCurrentCost(elapsedSeconds) {
            if (currentMode === 'regular') {
                return (hourlyRate / 3600) * elapsedSeconds;
            } else if (currentMode === 'combo') {
                const extraSeconds = Math.max(0, elapsedSeconds - totalComboSeconds);
                return (hourlyRate / 3600) * extraSeconds;
            }
            return 0;
        }

        // Update UI
        function render(elapsedSeconds) {
            // Current time
            const now = new Date();
            document.getElementById('currentTime').textContent =
                now.toLocaleTimeString('vi-VN', {
                    hour12: false,
                    timeZone: 'Asia/Ho_Chi_Minh'
                });

            // Elapsed time
            document.getElementById('elapsedTimeDisplay').textContent = formatHMS(elapsedSeconds);

            // Remaining time and progress
            if (currentMode === 'combo') {
                const remainingSeconds = totalComboSeconds - elapsedSeconds;
                document.getElementById('remainingTimeDisplay').textContent = formatHM(Math.max(0, remainingSeconds));

                const percent = totalComboSeconds > 0 ? Math.min(100, (elapsedSeconds / totalComboSeconds) * 100) : 0;
                document.getElementById('progressBar').style.width = percent + '%';
                document.getElementById('progressText').textContent = Math.round(percent) + '% đã sử dụng';
            }

            // Current cost
            const currentCost = calculateCurrentCost(elapsedSeconds);
            document.getElementById('currentCostDisplay').textContent = formatCurrency(currentCost);
        }

        // Timer loop
        function loop() {
            if (!startTimeMs) return;
            const elapsedSeconds = Math.floor((Date.now() - startTimeMs) / 1000);
            render(elapsedSeconds);
            rafId = requestAnimationFrame(loop);
        }

        function startTimer() {
            if (!startTimeMs || rafId) return;
            rafId = requestAnimationFrame(loop);
        }

        function stopTimer() {
            if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
        }

        // Update bill total
        function updateBillTotal() {
            @if ($table->currentBill)
                fetch('{{ route('bills.update-total', $table->currentBill->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        const final = data.final_amount;
                        ['totalAmountDisplay', 'finalAmountDisplay', 'billTotalAmount'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.textContent = formatCurrency(final);
                        });
                    }
                }).catch(console.error);
            @endif
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            const initialElapsedSeconds = Math.floor(elapsedMinutesFromServer * 60);
            render(initialElapsedSeconds);

            if (isRunning && startTimeMs) {
                startTimer();
            }

            setInterval(updateBillTotal, 30000);
        });

        window.addEventListener('beforeunload', stopTimer);
    </script>
</body>

</html>
