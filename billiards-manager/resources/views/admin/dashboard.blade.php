@extends('admin.layouts.app')

@section('title', 'Dashboard - F&B Management')

@section('content')
    <div class="space-y-6" id="dashboard-container">
        <!-- Header + Filter -->
        <div
            class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Tổng quan hệ thống</h1>
                <p class="text-sm text-gray-500">
                    <span class="font-medium text-blue-600">{{ $filterLabel }}</span>
                    • Cập nhật lúc <span id="current-time">{{ now()->format('H:i, d/m/Y') }}</span>
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <!-- Filter Form -->
                <form method="GET" class="flex items-center gap-2">
                    <select name="filter" id="filter-type" onchange="toggleCustomDate()"
                        class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="today" {{ $filterType == 'today' ? 'selected' : '' }}>Hôm nay</option>
                        <option value="yesterday" {{ $filterType == 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
                        <option value="week" {{ $filterType == 'week' ? 'selected' : '' }}>Tuần này</option>
                        <option value="last_week" {{ $filterType == 'last_week' ? 'selected' : '' }}>Tuần trước</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 flex items-center">
                        <i class="fas fa-search mr-2"></i>Xem báo cáo
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </a>
                </div>
                <div class="flex items-center gap-3">
                <a href="{{ route('attendance.simulator') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center">
                    <i class="fas fa-undo mr-2"></i>Máy quét
                </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $stats = [
                    [
                        'label' => 'Doanh thu đã thu',
                        'value' => $revenuePaid,
                        'icon' => 'money-bill-wave',
                        'color' => 'green',
                        'formatted' => number_format($revenuePaid, 0, ',', '.') . '₫',
                    ],
                    [
                        'label' => 'Doanh thu dự kiến',
                        'value' => $revenueExpected,
                        'icon' => 'clock',
                        'color' => 'amber',
                        'formatted' => number_format($revenueExpected, 0, ',', '.') . '₫',
                    ],
                    [
                        'label' => 'Tổng doanh thu',
                        'value' => $totalRevenue,
                        'icon' => 'chart-line',
                        'color' => 'purple',
                        'formatted' => number_format($totalRevenue, 0, ',', '.') . '₫',
                    ],
                    [
                        'label' => 'Đơn đã thanh toán',
                        'value' => $billsPaid,
                        'icon' => 'check-circle',
                        'color' => 'emerald',
                        'formatted' => $billsPaid,
                    ],
                    [
                        'label' => 'Đơn đang mở',
                        'value' => $billsExpected,
                        'icon' => 'hourglass-half',
                        'color' => 'orange',
                        'formatted' => $billsExpected,
                    ],
                    [
                        'label' => 'Tổng đơn hàng',
                        'value' => $totalBills,
                        'icon' => 'receipt',
                        'color' => 'blue',
                        'formatted' => $totalBills,
                    ],
                    [
                        'label' => 'Bàn đang dùng',
                        'value' => $tableStats['occupied'] ?? 0,
                        'icon' => 'chair',
                        'color' => 'indigo',
                        'formatted' => $tableStats['occupied'] ?? 0,
                    ],
                    [
                        'label' => 'Bàn trống',
                        'value' => $tableStats['available'] ?? 0,
                        'icon' => 'chair',
                        'color' => 'green',
                        'formatted' => $tableStats['available'] ?? 0,
                    ],
                    [
                        'label' => 'Tỷ lệ sử dụng',
                        'value' => $tableStats['occupancy_rate'] ?? 0,
                        'icon' => 'percent',
                        'color' => 'yellow',
                        'formatted' => number_format($tableStats['occupancy_rate'] ?? 0, 1) . '%',
                    ],
                    [
                        'label' => 'Bàn bảo trì',
                        'value' => $tableStats['maintenance'] ?? 0,
                        'icon' => 'tools',
                        'color' => 'red',
                        'formatted' => $tableStats['maintenance'] ?? 0,
                    ],
                    [
                        'label' => 'Tổng số bàn',
                        'value' => $tableStats['total'] ?? 0,
                        'icon' => 'border-all',
                        'color' => 'gray',
                        'formatted' => $tableStats['total'] ?? 0,
                    ],
                    [
                        'label' => 'SP sắp hết',
                        'value' => $lowStockProducts->count(),
                        'icon' => 'exclamation-triangle',
                        'color' => 'red',
                        'formatted' => $lowStockProducts->count(),
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2 bg-{{ $stat['color'] }}-50 rounded-lg">
                            <i class="fas fa-{{ $stat['icon'] }} text-{{ $stat['color'] }}-600 text-lg"></i>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 mb-1">{{ $stat['label'] }}</h3>
                    <p class="text-xl font-bold text-gray-900">
                        {{ $stat['formatted'] }}
                    </p>
                </div>
            @endforeach
        </div>

        <!-- Chart + Sidebar -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6">
            <!-- Biểu đồ -->
            <div class="xl:col-span-8">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Biểu đồ doanh thu</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $startDateFormatted }} → {{ $endDateFormatted }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button onclick="switchChartDataType('paid')" id="chart-data-paid"
                                    class="chart-data-btn active text-xs px-3 py-1">Đã thu</button>
                                <button onclick="switchChartDataType('expected')" id="chart-data-expected"
                                    class="chart-data-btn text-xs px-3 py-1">Dự kiến</button>
                                <button onclick="switchChartDataType('all')" id="chart-data-all"
                                    class="chart-data-btn text-xs px-3 py-1">Tổng</button>
                            </div>
                        </div>
                    </div>

                    <div class="h-80">
                        <canvas id="revenueChart"></canvas>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-200 mt-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1" id="chart-total-label">Tổng</p>
                            <p class="text-lg font-bold text-gray-900" id="chart-total">0₫</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1" id="chart-average-label">Trung bình/ngày</p>
                            <p class="text-lg font-bold text-gray-900" id="chart-average">0₫</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tỷ lệ tăng trưởng</p>
                            <p class="text-lg font-bold text-green-600" id="chart-growth">0%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Right -->
            <div class="xl:col-span-4 space-y-6">
                <!-- Top sản phẩm -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top món bán chạy</h3>
                    <div class="space-y-3">
                        @foreach ($topProducts as $index => $product)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->category_name ?? 'Không phân loại' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">{{ $product->total_quantity }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ number_format($product->total_revenue / 1000, 0) }}k
                                    </p>
                                </div>
                            </div>
                        @endforeach
                        @if ($topProducts->count() == 0)
                            <p class="text-center text-gray-400 py-6">Chưa có dữ liệu</p>
                        @endif
                    </div>
                </div>

                <!-- Sản phẩm bán chạy hôm nay/tuần -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bán chạy hôm nay</h3>
                    <div class="space-y-3">
                        @forelse ($bestSellingProducts['today'] as $index => $product)
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-green-700">#{{ $index + 1 }}</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $product->name }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-green-600">{{ $product->total_quantity }}</span>
                                    <br>
                                    <span
                                        class="text-xs text-gray-500">{{ number_format($product->total_revenue, 0, ',', '.') }}₫</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-400">
                                <i class="fas fa-shopping-cart text-2xl mb-2 opacity-50"></i>
                                <p class="text-sm">Chưa có đơn hôm nay</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Cảnh báo tồn kho -->
                @if ($lowStockProducts->count() > 0)
                    <div class="bg-white rounded-xl border border-red-200 p-6">
                        <h3 class="text-lg font-semibold text-red-600 mb-4 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Cảnh báo tồn kho
                        </h3>
                        <div class="space-y-3">
                            @foreach ($lowStockProducts as $p)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $p->name }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-red-600">{{ $p->stock_quantity }} /
                                        {{ $p->min_stock_level }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Đơn hàng gần đây -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-history mr-2"></i>
                        Đơn hàng gần đây
                    </h3>
                    <div class="space-y-4">
                        @forelse ($recentBills as $bill)
                            <div class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $bill['bill_number'] }}</p>
                                        <p class="text-xs text-gray-600">{{ $bill['customer_name'] }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-blue-600">
                                        {{ number_format($bill['total_amount'], 0, ',', '.') }}₫
                                    </span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-gray-500">
                                    <span>{{ $bill['table_name'] }}</span>
                                    <span>{{ $bill['time_ago'] }}</span>
                                </div>
                                <div class="mt-2">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $bill['payment_status'] == 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $bill['payment_status'] == 'Paid' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-file-invoice text-4xl mb-3 opacity-50"></i>
                                <p class="text-sm">Chưa có đơn hàng</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DỮ LIỆU CHO BIỂU ĐỒ -->
    <script type="application/json" id="chart-config">
    {
        "labels": {!! json_encode($chartLabels) !!},
        "datasets": {
            "paid": {!! json_encode($chartData['paid'] ?? []) !!},
            "expected": {!! json_encode($chartData['expected'] ?? []) !!},
            "all": {!! json_encode($chartData['all'] ?? []) !!}
        },
        "stats": {!! json_encode($chartStats ?? []) !!}
    }
    </script>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            let revenueChart;
            let currentDataType = 'paid';

            const config = JSON.parse(document.getElementById('chart-config').textContent);
            const colors = {
                paid: '#10b981',
                expected: '#f59e0b',
                all: '#8b5cf6'
            };

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }

            function initChart(type = 'paid') {
                if (revenueChart) revenueChart.destroy();

                const data = config.datasets[type];
                const total = config.stats[type + '_total'] || 0;
                const average = config.stats[type + '_avg'] || 0;

                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, `${colors[type]}40`);
                gradient.addColorStop(1, `${colors[type]}10`);

                revenueChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: config.labels,
                        datasets: [{
                            label: type === 'paid' ? 'Đã thu' : type === 'expected' ? 'Dự kiến' :
                                'Tổng',
                            data: data,
                            borderColor: colors[type],
                            backgroundColor: gradient,
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
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
                                callbacks: {
                                    label: function(context) {
                                        return formatCurrency(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000).toFixed(1) + 'tr';
                                        } else if (value >= 1000) {
                                            return (value / 1000).toFixed(0) + 'k';
                                        }
                                        return value;
                                    }
                                },
                                grid: {
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                document.getElementById('chart-total').textContent = formatCurrency(total);
                document.getElementById('chart-average').textContent = formatCurrency(average);

                const labels = {
                    paid: 'Đã thu',
                    expected: 'Dự kiến',
                    all: 'Tổng'
                };
                document.getElementById('chart-total-label').textContent = `Tổng ${labels[type]}`;
                document.getElementById('chart-average-label').textContent = `TB/ngày ${labels[type]}`;
            }

            window.switchChartDataType = function(t) {
                currentDataType = t;
                document.querySelectorAll('.chart-data-btn').forEach(b => b.classList.remove('active'));
                document.getElementById('chart-data-' + t).classList.add('active');
                initChart(t);
            };

            function refreshDashboard() {
                const btn = document.getElementById('refresh-btn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang tải...';
                btn.disabled = true;

                fetch('/admin/dashboard/refresh', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Lỗi khi làm mới dữ liệu');
                            btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Làm mới';
                            btn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Làm mới';
                        btn.disabled = false;
                    });
            }

            // Khởi tạo
            initChart();

            // Cập nhật thời gian hiện tại
            setInterval(() => {
                const now = new Date();
                const timeStr = now.toLocaleString('vi-VN', {
                    hour: '2-digit',
                    minute: '2-digit',
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                document.getElementById('current-time').textContent = timeStr;
            }, 60000);
        });
    </script>

    <style>
        .chart-data-btn.active {
            background-color: #3b82f6;
            color: white;
        }

        .chart-data-btn {
            transition: all 0.2s;
            border-radius: 6px;
        }
    </style>
@endsection
