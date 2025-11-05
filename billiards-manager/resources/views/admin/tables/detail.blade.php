<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bàn {{ $table->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">

    <div class="container mx-auto p-6 max-w-6xl">

        <!-- Tiêu đề bàn -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Bàn: {{ $table->name }}</h1>
            <p class="text-gray-600">Khu vực: {{ $table->area ?? 'Chưa xác định' }}</p>
        </div>

        <!-- Trạng thái hiện tại -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            @if ($currentUsage)
                <!-- Đang sử dụng -->
                <div
                    class="bg-white rounded-lg shadow p-6 border {{ $currentUsage->bill->status === 'Paused' ? 'border-yellow-400' : 'border-green-400' }}">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        @if ($currentUsage->bill->status === 'Open')
                            <i class="fas fa-play-circle text-green-600"></i>
                            <span class="text-green-800">ĐANG SỬ DỤNG</span>
                        @elseif($currentUsage->bill->status === 'Paused')
                            <i class="fas fa-pause-circle text-yellow-600"></i>
                            <span class="text-yellow-800">TẠM DỪNG</span>
                        @endif
                    </h2>
                    <p class="mt-2"><strong>Khách:</strong> {{ $currentUsage->bill->customer->name ?? 'Khách lẻ' }}
                    </p>
                    <p><strong>Nhân viên:</strong> {{ $currentUsage->bill->staff->name ?? 'Chưa có' }}</p>
                    <p><strong>Bắt đầu:</strong> {{ $currentUsage->start_time->format('H:i d/m/Y') }}</p>
                    <!-- Thay đoạn <p><strong>Thời gian:</strong> -->
                    <p><strong>Thời gian:</strong>
                        <span class="live-time font-mono text-lg text-blue-700"
                            data-start="{{ $currentUsage->start_time->timestamp }}"
                            data-paused-duration="{{ $currentUsage->bill->paused_duration ?? 0 }}"
                            data-paused-at="{{ $currentUsage->bill->status === 'Paused' ? $currentUsage->bill->paused_at?->timestamp : 0 }}"
                            data-rate="{{ $currentUsage->hourly_rate ?? 0 }}"
                            data-product-total="{{ $currentUsage->bill->total_amount - ($currentUsage->total_price ?? 0) }}">
                            00:00:00
                        </span>
                    </p>

                    <!-- Form thêm sản phẩm -->
                    <form action="{{ route('bills.add-product', $currentUsage->bill) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="space-y-2 max-h-64 overflow-y-auto border rounded p-2 bg-gray-50">
                            @foreach ($products as $p)
                                <div class="flex items-center gap-3 py-1 product-row" data-id="{{ $p->id }}">
                                    <input type="checkbox" class="product-checkbox" data-id="{{ $p->id }}"
                                        onchange="toggleQuantity(this)">
                                    <div class="flex-1 flex justify-between">
                                        <span class="text-sm font-medium">{{ $p->name }}</span>
                                        <span class="text-xs text-gray-600">{{ number_format($p->price) }}đ</span>
                                    </div>
                                    <input type="number" name="products[{{ $p->id }}]"
                                        class="quantity-input w-16 border rounded px-2 py-1 text-sm hidden"
                                        min="1" value="1">
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" onclick="selectAll()"
                                class="text-xs text-blue-600 hover:underline">Chọn tất cả</button>
                            <button type="button" onclick="clearAll()" class="text-xs text-red-600 hover:underline">Bỏ
                                chọn</button>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                                Thêm vào order
                            </button>
                        </div>
                    </form>

                    <!-- Nút điều khiển bàn -->
                    <div class="mt-6 space-y-3">
                        @if ($currentUsage->bill->status === 'Open')
                            <button onclick="pauseBill({{ $currentUsage->bill->id }})"
                                class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-white py-3 rounded-lg font-semibold 
                                       hover:from-yellow-600 hover:to-yellow-700 flex items-center justify-center gap-2 shadow-md transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                TẠM DỪNG BÀN
                            </button>
                        @endif

                        @if ($currentUsage->bill->status === 'Paused')
                            <button onclick="resumeBill({{ $currentUsage->bill->id }})"
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 rounded-lg font-semibold 
                                       hover:from-green-600 hover:to-green-700 flex items-center justify-center gap-2 shadow-md transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                TIẾP TỤC BÀN
                            </button>
                        @endif

                        <button onclick="confirmCloseBill({{ $currentUsage->bill->id }})"
                            class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-3 rounded-lg font-semibold 
                                   hover:from-red-700 hover:to-red-800 flex items-center justify-center gap-2 shadow-md transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            TÍNH TIỀN & KẾT THÚC
                        </button>
                    </div>
                </div>

                <!-- Danh sách sản phẩm + Tiền bàn + Tổng tiền -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">
                        Đã gọi ({{ $currentUsage->bill->billDetails->count() }} món)
                    </h3>

                    <div class="text-sm space-y-1 max-h-64 overflow-y-auto">
                        @forelse($currentUsage->bill->billDetails as $detail)
                            <div class="flex justify-between py-1 border-b">
                                <span>
                                    {{ $detail->quantity }}x
                                    {{ $detail->product->name ?? ($detail->combo->name ?? 'N/A') }}
                                </span>
                                <span class="font-medium">
                                    {{ number_format($detail->unit_price * $detail->quantity) }}đ
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic">Chưa gọi món</p>
                        @endforelse
                    </div>

                    <!-- TIỀN BÀN + TỔNG TIỀN (LIVE) -->
                    <div class="mt-4 space-y-2 border-t pt-3">
                        <div class="flex justify-between text-sm">
                            <span>Tiền sản phẩm:</span>
                            <span class="font-medium">
                                {{ number_format($currentUsage->bill->total_amount - ($currentUsage->total_price ?? 0)) }}đ
                            </span>
                        </div>

                        <div class="flex justify-between text-sm font-medium">
                            <span class="text-blue-700">Tiền bàn (đang chạy):</span>
                            <span id="live-table-price" class="text-blue-700 font-bold">0đ</span>
                        </div>

                        <div class="flex justify-between text-lg font-bold text-green-700 border-t pt-2">
                            <span>TỔNG CỘNG:</span>
                            <span id="live-total-amount">
                                {{ number_format($currentUsage->bill->total_amount) }}đ
                            </span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Bàn trống -->
                <div class="col-span-2 bg-gray-50 border border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-chair text-6xl text-gray-400 mb-4"></i>
                    <h2 class="text-xl font-semibold text-gray-700">BÀN ĐANG TRỐNG</h2>
                    <p class="text-gray-600 mt-2">Chưa có khách sử dụng</p>

                    <form action="{{ route('tables.open', $table) }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit"
                            class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-medium text-lg transition-all">
                            <i class="fas fa-play mr-2"></i> MỞ BÀN
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Thống kê tổng -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Thống kê bàn</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-blue-50 p-4 rounded">
                    <p class="text-2xl font-bold text-blue-700">{{ $usageHistory->count() }}</p>
                    <p class="text-sm text-gray-600">Phiên đã dùng</p>
                </div>
                <div class="bg-green-50 p-4 rounded">
                    <p class="text-2xl font-bold text-green-700">{{ $totalMinutes }}</p>
                    <p class="text-sm text-gray-600">Tổng phút</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded">
                    <p class="text-2xl font-bold text-yellow-700">
                        {{ number_format($totalRevenue) }}đ
                    </p>
                    <p class="text-sm text-gray-600">Doanh thu</p>
                </div>
                <div class="bg-purple-50 p-4 rounded">
                    <p class="text-2xl font-bold text-purple-700">
                        {{ $totalRevenue > 0 ? round($totalMinutes / $usageHistory->count(), 1) : 0 }}
                    </p>
                    <p class="text-sm text-gray-600">Phút trung bình</p>
                </div>
            </div>
        </div>

        <!-- Lịch sử sử dụng -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Lịch sử sử dụng ({{ $usageHistory->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Thời gian</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Khách</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                NV</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Phút</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Tiền</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($usageHistory as $usage)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm">
                                    {{ $usage->start_time->format('d/m H:i') }} -
                                    {{ $usage->end_time?->format('H:i') ?? '--' }}
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $usage->bill->customer->name ?? 'Khách lẻ' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $usage->bill->staff->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center font-medium text-sm">
                                    {{ $usage->duration_minutes ?? '?' }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-green-600 text-sm">
                                    {{ number_format($usage->bill->total_amount ?? 0) }}đ
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    Chưa có lịch sử sử dụng
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript - Tính tiền bàn live -->
    <script>
        function updateLiveTimeAndPrice() {
            const el = document.querySelector('.live-time');
            if (!el) return;

            const start = parseInt(el.dataset.start) * 1000;
            const paused = parseInt(el.dataset.paused) || 0;
            const rate = parseFloat(el.dataset.rate) || 0;
            const productTotal = parseFloat(el.dataset.productTotal) || 0;
            const now = new Date().getTime();

            // Tính giây đã trôi
            let diffSeconds = Math.floor((now - start) / 1000);
            let totalMinutes = Math.floor(diffSeconds / 60) - paused;
            totalMinutes = Math.max(0, totalMinutes);

            // Cập nhật thời gian
            const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
            const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
            const seconds = String(diffSeconds % 60).padStart(2, '0');
            el.textContent = `${hours}:${minutes}:${seconds}`;

            // Tính tiền bàn
            if (rate > 0) {
                const tablePrice = Math.round((totalMinutes / 60) * rate);
                const grandTotal = productTotal + tablePrice;

                document.getElementById('live-table-price').textContent = `${tablePrice.toLocaleString()}đ`;
                document.getElementById('live-total-amount').textContent = `${grandTotal.toLocaleString()}đ`;
            }
        }

        // Cập nhật mỗi giây
        setInterval(updateLiveTimeAndPrice, 1000);
        updateLiveTimeAndPrice();

        // === CÁC HÀM CŨ ===
        function toggleQuantity(checkbox) {
            const qtyInput = checkbox.closest('.product-row').querySelector('.quantity-input');
            qtyInput.classList.toggle('hidden', !checkbox.checked);
            if (!checkbox.checked) qtyInput.value = 1;
        }

        function selectAll() {
            document.querySelectorAll('.product-checkbox').forEach(cb => {
                cb.checked = true;
                toggleQuantity(cb);
            });
        }

        function clearAll() {
            document.querySelectorAll('.product-checkbox').forEach(cb => {
                cb.checked = false;
                toggleQuantity(cb);
            });
        }

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                this.querySelectorAll('.quantity-input.hidden').forEach(input => {
                    input.removeAttribute('name');
                });
            });
        });

        function callBillAction(id, action, successMsg) {
            const btn = event.target.closest('button');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML =
                `<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang xử lý...`;

            fetch(`/admin/bills/${id}/${action}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    showToast(data.success ? successMsg : (data.message || 'Lỗi'), data.success ? 'success' : 'error');
                    if (data.success) setTimeout(() => location.reload(), 1000);
                })
                .catch(() => showToast('Lỗi kết nối', 'error'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = original;
                });
        }

        function pauseBill(id) {
            callBillAction(id, 'pause', 'Đã tạm dừng bàn');
        }

        function resumeBill(id) {
            callBillAction(id, 'resume', 'Đã tiếp tục bàn');
        }

        function confirmCloseBill(id) {
            if (confirm('Xác nhận tính tiền và kết thúc bàn?\nHóa đơn sẽ được chốt vĩnh viễn.')) {
                callBillAction(id, 'close', 'Thanh toán thành công!');
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-6 right-6 px-6 py-3 rounded-lg text-white font-medium shadow-xl z-50 
                               transform transition-all duration-300 translate-y-16 opacity-0
                               ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-y-16', 'opacity-0'), 100);
            setTimeout(() => {
                toast.classList.add('translate-y-16', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function updateLiveTimeAndPrice() {
            const el = document.querySelector('.live-time');
            if (!el) return;

            const start = parseInt(el.dataset.start) * 1000;
            const pausedDuration = parseInt(el.dataset.pausedDuration) || 0;
            const pausedAt = parseInt(el.dataset.pausedAt) || 0;
            const rate = parseFloat(el.dataset.rate) || 0;
            const productTotal = parseFloat(el.dataset.productTotal) || 0;
            const now = new Date().getTime();

            let totalMinutes = 0;
            let displayTime = '00:00:00';

            @if ($currentUsage->bill->status === 'Open')
                // Đang chạy: tính từ start_time - paused_duration
                let diffSeconds = Math.floor((now - start) / 1000);
                totalMinutes = Math.floor(diffSeconds / 60) - pausedDuration;
                totalMinutes = Math.max(0, totalMinutes);

                const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                const seconds = String(diffSeconds % 60).padStart(2, '0');
                displayTime = `${hours}:${minutes}:${seconds}`;
            @elseif ($currentUsage->bill->status === 'Paused' && $currentUsage->bill->paused_at)
                // Đang tạm dừng: tính từ start_time đến paused_at - paused_duration
                let diffSeconds = Math.floor((pausedAt * 1000 - start) / 1000);
                totalMinutes = Math.floor(diffSeconds / 60) - pausedDuration;
                totalMinutes = Math.max(0, totalMinutes);

                const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                const seconds = String(diffSeconds % 60).padStart(2, '0');
                displayTime = `${hours}:${minutes}:${seconds}`;
            @endif

            // Cập nhật UI
            el.textContent = displayTime;

            // Tính tiền bàn (chỉ tăng khi Open)
            if (rate > 0 && '{{ $currentUsage->bill->status }}' === 'Open') {
                const tablePrice = Math.round((totalMinutes / 60) * rate);
                const grandTotal = productTotal + tablePrice;

                document.getElementById('live-table-price').textContent = `${tablePrice.toLocaleString()}đ`;
                document.getElementById('live-total-amount').textContent = `${grandTotal.toLocaleString()}đ`;
            }
        }
    </script>



</body>

</html>
