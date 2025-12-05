@extends('admin.layouts.app')

@section('title', 'Dashboard - F&B Management')

@section('content')
    <div class="space-y-6">
        <!-- Header + Bộ lọc ngày -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tổng quan hệ thống</h1>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="font-medium text-blue-600">{{ $filterLabel }}</span>
                    • Cập nhật lúc {{ now()->format('H:i, d/m/Y') }}
                </p>
            </div>

            <!-- Form lọc theo ngày -->
            <form method="GET"
                class="flex flex-wrap items-center gap-3 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <select name="filter" id="filter-type"
                    class="px-4 py-2.5 text-sm font-medium border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="toggleCustomDate()">
                    <option value="today" {{ $filterType == 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="yesterday" {{ $filterType == 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
                    <option value="week" {{ $filterType == 'week' ? 'selected' : '' }}>Tuần này</option>
                    <option value="last_week" {{ $filterType == 'last_week' ? 'selected' : '' }}>Tuần trước</option>
                    <option value="month" {{ $filterType == 'month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="last_month" {{ $filterType == 'last_month' ? 'selected' : '' }}>Tháng trước</option>
                    <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }}>Tùy chỉnh khoảng</option>
                </select>

                <div id="custom-date-range" class="flex items-center gap-2 {{ $filterType !== 'custom' ? 'hidden' : '' }}">
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-gray-500 font-medium">→</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-search"></i> Xem báo cáo
                </button>

                <a href="{{ route('admin.dashboard') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Xóa bộ lọc
                </a>

                <a href="{{ route('attendance.simulator') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Máy quét QR
                </a>
            </form>
        </div>

        <!-- 4 Cards chính -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Doanh thu -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-100 rounded-xl">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold px-3 py-1 rounded-full {{ $revenueGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $revenueGrowth >= 0 ? 'Up' : 'Down' }} {{ number_format(abs($revenueGrowth), 1) }}%
                    </span>
                </div>
                <p class="text-sm font-medium text-gray-600">Doanh thu {{ strtolower($filterLabel) }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-900 mt-2">{{ number_format($todayRevenue, 0, ',', '.') }}₫
                </p>
                <p class="text-xs text-gray-500 mt-2">So với kỳ trước</p>
            </div>

            <!-- Số đơn hàng -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <i class="fas fa-receipt text-blue-600 text-xl"></i>
                    </div>
                    <span
                        class="text-xs font-bold px-3 py-1 rounded-full {{ $billGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $billGrowth >= 0 ? 'Up' : 'Down' }} {{ number_format(abs($billGrowth), 1) }}%
                    </span>
                </div>
                <p class="text-sm font-medium text-gray-600">Số đơn hàng</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $todayBills }}</p>
                <p class="text-xs text-gray-500 mt-2">So với kỳ trước</p>
            </div>

            <!-- Bàn đang sử dụng -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-100 rounded-xl">
                        <i class="fas fa-chair text-orange-600 text-xl"></i>
                    </div>
                    <div class="text-xs font-bold px-3 py-1 bg-orange-100 text-orange-700 rounded-full">
                        {{ number_format($tableStats['occupancy_rate'], 0) }}%
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-600">Bàn đang sử dụng</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">
                    {{ $tableStats['occupied'] + $tableStats['reserved'] }}
                    <span class="text-lg text-gray-400">/ {{ $tableStats['total'] }}</span>
                </p>
                <p class="text-xs text-gray-500 mt-2">Tổng số bàn</p>
            </div>

            <!-- Khách mới -->
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-lg transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-100 rounded-xl">
                        <i class="fas fa-user-plus text-purple-600 text-xl"></i>
                    </div>
                    <div class="text-xs font-bold px-3 py-1 bg-purple-100 text-purple-700 rounded-full">
                        +{{ $newCustomersThisMonth }}
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-600">Khách hàng mới</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $newCustomersToday }}</p>
                <p class="text-xs text-gray-500 mt-2">Trong {{ strtolower($filterLabel) }}</p>
            </div>
        </div>

        <!-- Biểu đồ + Top sản phẩm -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Biểu đồ doanh thu -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Biểu đồ doanh thu</h3>
                        <p class="text-sm text-gray-500 mt-1">Từ {{ $startDateFormatted }} → {{ $endDateFormatted }}</p>
                    </div>

                    <!-- Nút chuyển đổi biểu đồ -->
                    <button id="toggleChartBtn" data-type="bar" onclick="toggleChartType()"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                        <i class="fas fa-chart-line"></i>
                        <span>Xem dạng đường</span>
                    </button>
                </div>

                <div class="h-96">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Top 5 sản phẩm bán chạy -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-5">Top sản phẩm bán chạy</h3>
                <div class="space-y-4">
                    @forelse($topProducts as $index => $product)
                        <div
                            class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl hover:shadow-md transition">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->category_name ?? 'Chưa phân loại' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">{{ $product->total_quantity }} món</p>
                                <p class="text-xs text-gray-600">{{ number_format($product->total_revenue, 0, ',', '.') }}₫
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-8">Chưa có dữ liệu bán hàng</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 3 cột cuối: Bill gần đây + Thống kê nhanh -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Bill gần đây -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Đơn hàng gần đây</h3>
                    <a href="{{ route('admin.bills.index') }}" class="text-sm text-blue-600 hover:underline">Xem tất cả
                        →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-200">
                            <tr>
                                <th class="text-left py-3 font-semibold text-gray-600">Mã đơn</th>
                                <th class="text-left py-3 font-semibold text-gray-600">Bàn</th>
                                <th class="text-left py-3 font-semibold text-gray-600">Khách</th>
                                <th class="text-right py-3 font-semibold text-gray-600">Tổng tiền</th>
                                <th class="text-center py-3 font-semibold text-gray-600">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentBills as $bill)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 font-mono font-medium">#{{ $bill['bill_number'] }}</td>
                                    <td class="py-4">{{ $bill['table_name'] }}</td>
                                    <td class="py-4 text-gray-600">{{ $bill['customer_name'] }}</td>
                                    <td class="py-4 text-right font-semibold">
                                        {{ number_format($bill['total_amount'], 0, ',', '.') }}₫</td>
                                    <td class="py-4 text-center">
                                        @if ($bill['payment_status'] == 'Paid')
                                            <span
                                                class="px-3 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Đã
                                                thanh toán</span>
                                        @else
                                            <span
                                                class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Chưa
                                                thanh toán</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-12 text-gray-400">
                                        <i class="fas fa-receipt text-4xl mb-3"></i>
                                        <p>Chưa có đơn hàng nào</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Các thống kê phụ -->
            <div class="space-y-6">
                <!-- Tình trạng bàn -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-5">Tình trạng bàn</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between"><span class="text-gray-600">Trống</span> <strong
                                class="text-green-600">{{ $tableStats['available'] }}</strong></div>
                        <div class="flex justify-between"><span class="text-gray-600">Đang dùng</span> <strong
                                class="text-blue-600">{{ $tableStats['occupied'] }}</strong></div>
                        <div class="flex justify-between"><span class="text-gray-600">Đã đặt trước</span> <strong
                                class="text-yellow-600">{{ $tableStats['reserved'] }}</strong></div>
                        <div class="flex justify-between"><span class="text-gray-600">Bảo trì</span> <strong
                                class="text-gray-500">{{ $tableStats['maintenance'] }}</strong></div>
                    </div>
                    <div class="mt-5 pt-5 border-t">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Tỷ lệ sử dụng</span>
                            <span class="font-bold">{{ number_format($tableStats['occupancy_rate'], 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all"
                                style="width: {{ $tableStats['occupancy_rate'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Cảnh báo tồn kho -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-500"></i> Cảnh báo kho
                    </h3>
                    @if ($lowStockProducts->count() > 0)
                        <div class="space-y-3">
                            @foreach ($lowStockProducts as $p)
                                <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                    <span class="font-medium text-sm">{{ $p->name }}</span>
                                    <span class="text-red-600 font-bold text-sm">{{ $p->stock_quantity }} /
                                        {{ $p->min_stock_level ?? 0 }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-green-600 py-6">
                            <i class="fas fa-check-circle text-3xl mb-2 block"></i>
                            Kho hàng ổn định
                        </p>
                    @endif
                </div>

                <!-- Ca làm việc hôm nay -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-5">Ca làm việc hôm nay</h3>
                    <div class="grid grid-cols-2 gap-6 text-center mb-6">
                        <div>
                            <p class="text-xs text-gray-500">Nhân viên đang làm</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $shiftStats['active_count'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tổng giờ công</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ number_format($shiftStats['total_hours'], 1) }}h</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Đang trực:</p>
                        @foreach ($shiftStats['working_employees'] as $emp)
                            <div class="flex justify-between text-sm">
                                <span>{{ $emp->name }}</span>
                                <span
                                    class="text-gray-500">{{ \Carbon\Carbon::parse($emp->actual_start_time)->format('H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Nhân viên xuất sắc -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-5">Nhân viên xuất sắc</h3>
                    <div class="space-y-4">
                        @forelse($activeEmployees as $i => $emp)
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($emp->name, 0, 2) }}
                                    </div>
                                    @if ($i == 0)
                                        <i class="fas fa-crown text-yellow-500 absolute -top-2 -right-2 text-lg"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold">{{ $emp->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $emp->bill_count }} đơn •
                                        {{ number_format($emp->total_revenue, 0, ',', '.') }}₫</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-6">Chưa có dữ liệu</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let revenueChart = null;

        function renderChart(type = 'bar') {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const rawData = @json($weeklyRevenue);

            const labels = rawData.map(item => item.day + '\n' + item.full_date);
            const data = rawData.map(item => item.revenue);

            // Hủy chart cũ
            if (revenueChart) {
                revenueChart.destroy();
            }

            const isBar = type === 'bar';

            revenueChart = new Chart(ctx, {
                type: isBar ? 'bar' : 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu',
                        data: data,
                        backgroundColor: isBar ? 'rgba(59, 130, 246, 0.85)' : 'rgba(59, 130, 246, 0.1)',
                        borderColor: '#3b82f6',
                        borderWidth: isBar ? 2 : 3,
                        borderRadius: isBar ? 12 : 0,
                        borderSkipped: false,
                        fill: !isBar,
                        tension: 0.4,
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: isBar ? 4 : 6,
                        pointHoverRadius: isBar ? 8 : 10,
                        barThickness: 35,
                        maxBarThickness: 50,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.9)',
                            cornerRadius: 12,
                            padding: 12,
                            displayColors: false,
                            titleFont: {
                                size: 13
                            },
                            bodyFont: {
                                size: 15,
                                weight: 'bold'
                            },
                            callbacks: {
                                label: ctx => 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(ctx.parsed.y)
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6'
                            },
                            ticks: {
                                callback: value => {
                                    if (value >= 1000000000) return (value / 1000000000).toFixed(1) + ' tỷ';
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + ' tr';
                                    if (value >= 1000) return (value / 1000) + 'k';
                                    return value + '₫';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 0,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    }
                }
            });
        }

        function toggleChartType() {
            const btn = document.getElementById('toggleChartBtn');
            const current = btn.getAttribute('data-type');

            if (current === 'bar') {
                renderChart('line');
                btn.setAttribute('data-type', 'line');
                btn.innerHTML = `<i class="fas fa-chart-bar mr-2"></i> Xem dạng cột`;
            } else {
                renderChart('bar');
                btn.setAttribute('data-type', 'bar');
                btn.innerHTML = `<i class="fas fa-chart-line mr-2"></i> Xem dạng đường`;
            }
        }

        function toggleCustomDate() {
            const value = document.getElementById('filter-type').value;
            document.getElementById('custom-date-range').classList.toggle('hidden', value !== 'custom');
        }

        // Khởi tạo khi load trang
        document.addEventListener('DOMContentLoaded', () => {
            renderChart('bar'); // Mặc định là cột (đổi thành 'line' nếu muốn mặc định đường)
            toggleCustomDate();
        });
    </script>
@endsection
