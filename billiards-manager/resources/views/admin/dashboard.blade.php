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
                <span class="ml-3 text-green-600 font-medium">
                    <i class="fas fa-circle text-xs animate-pulse"></i> Live
                </span>
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
                    <option value="month" {{ $filterType == 'month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="last_month" {{ $filterType == 'last_month' ? 'selected' : '' }}>Tháng trước</option>
                    <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }}>Tùy chỉnh</option>
                </select>

                <div id="custom-date-range"
                    class="flex items-center gap-2 {{ $filterType !== 'custom' ? 'hidden' : '' }}">
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                    <span class="text-gray-500">→</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                </div>

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
                <button onclick="refreshDashboard()" id="refresh-btn"
                    class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>Làm mới
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
        $stats = [
        [
        'label' => 'Đã thu',
        'value' => $revenuePaid ?? ($todayRevenue ?? 0),
        'icon' => 'money-bill-wave',
        'color' => 'green',
        'growth' => $revenuePaidGrowth ?? 0,
        ],
        ['label' => 'Dự kiến', 'value' => $revenueExpected ?? 0, 'icon' => 'clock', 'color' => 'amber'],
        [
        'label' => 'Tổng doanh thu',
        'value' => ($revenuePaid ?? 0) + ($revenueExpected ?? 0),
        'icon' => 'chart-line',
        'color' => 'purple',
        'growth' => $totalRevenueGrowth ?? 0,
        ],
        [
        'label' => 'Đã thanh toán',
        'value' => $billsPaid ?? ($todayBills ?? 0),
        'icon' => 'check-circle',
        'color' => 'emerald',
        'growth' => $billsPaidGrowth ?? 0,
        ],
        [
        'label' => 'Chờ thanh toán',
        'value' => $billsExpected ?? 0,
        'icon' => 'hourglass-half',
        'color' => 'orange',
        ],
        [
        'label' => 'Bàn đang dùng',
        'value' =>
        ($tableStats['occupied'] ?? 0) +
        ($tableStats['reserved'] ?? 0) .
        ' / ' .
        ($tableStats['total'] ?? 0),
        'icon' => 'chair',
        'color' => 'indigo',
        'rate' => $tableStats['occupancy_rate'] ?? 0,
        ],
        ];
        @endphp

        @foreach ($stats as $stat)
        <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-{{ $stat['color'] }}-50 rounded-lg">
                    <i class="fas fa-{{ $stat['icon'] }} text-{{ $stat['color'] }}-600 text-lg"></i>
                </div>
                @if (isset($stat['growth']))
                <span
                    class="text-xs font-medium px-2 py-1 rounded-full {{ $stat['growth'] >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                    <i class="fas {{ $stat['growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    {{ number_format(abs($stat['growth']), 1) }}%
                </span>
                @elseif(isset($stat['rate']))
                <span class="text-xs font-medium px-2 py-1 rounded-full bg-blue-50 text-blue-700">
                    {{ number_format($stat['rate'], 0) }}%
                </span>
                @endif
            </div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">{{ $stat['label'] }}</h3>
            <p class="text-xl font-bold text-gray-900">
                @if (in_array($stat['icon'], ['money-bill-wave', 'chart-line']))
                {{ number_format($stat['value'], 0, ',', '.') }}₫
                @else
                {{ $stat['value'] }}
                @endif
            </p>
        </div>
        @endforeach
    </div>

    <!-- Monthly Stats -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">Doanh thu tháng</h3>
                <i class="fas fa-calendar-alt text-blue-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                {{ number_format($monthlyStats['revenue'] ?? 0, 0, ',', '.') }}₫
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">Tổng đơn hàng</h3>
                <i class="fas fa-file-invoice text-green-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $monthlyStats['bills'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">Khách hàng mới</h3>
                <i class="fas fa-user-plus text-purple-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $monthlyStats['customers'] ?? 0 }}</p>
        </div>
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
                        <div class="flex bg-gray-100 rounded-lg p-1">
                            <button onclick="switchChartType('bar')" id="chart-type-bar"
                                class="chart-type-btn active px-3 py-1"><i class="fas fa-chart-bar"></i></button>
                            <button onclick="switchChartType('line')" id="chart-type-line"
                                class="chart-type-btn px-3 py-1"><i class="fas fa-chart-line"></i></button>
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
                        <p class="text-xs text-gray-500 mb-1">Tăng trưởng</p>
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
                            <p class="text-xs text-gray-500">{{ $product->category_name ?? 'Không phân loại' }}
                            </p>
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

            <!-- Cảnh báo tồn kho -->
            <div class="bg-white rounded-xl border border-red-200 p-6">
                <h3 class="text-lg font-semibold text-red-600 mb-4 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Cảnh báo tồn kho
                </h3>
                @if ($lowStockProducts->count() > 0)
                <div class="space-y-3">
                    @foreach ($lowStockProducts->take(5) as $p)
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
                @else
                <div class="text-center py-4 text-gray-400">
                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                    <p class="text-sm">Kho hàng ổn định!</p>
                </div>
                @endif
            </div>

            <!-- Nhân viên xuất sắc -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                    Nhân viên xuất sắc
                </h3>
                <div class="space-y-4">
                    @forelse ($topEmployees as $index => $emp)
                    <div
                        class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 {{ $index === 0 ? 'bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200' : '' }}">
                        <div class="flex-shrink-0 relative">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($emp->name, 0, 2)) }}
                            </div>
                            @if ($index === 0)
                            <i
                                class="fas fa-crown text-yellow-500 text-xl absolute -top-2 -right-2 drop-shadow"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $emp->name }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <span
                                    class="font-bold text-green-600">{{ number_format($emp->total_revenue / 1000) }}k</span>
                                <span class="text-gray-400 mx-1">•</span>
                                <span>{{ $emp->bill_count ?? 0 }} đơn</span>
                            </p>
                        </div>
                        @if ($index < 3)
                            <div class="text-2xl font-bold text-gray-300">#{{ $index + 1 }}
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-users text-4xl mb-3 opacity-50"></i>
                    <p class="text-sm">Chưa có dữ liệu</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>

<!-- DỮ LIỆU CHO BIỂU ĐỒ (ĐÃ SỬA HOÀN TOÀN) -->
<script type="application/json" id="chart-config">
    {
        "labels": {
            !!json_encode($chartLabels) !!
        },
        "datasets": {
            "paid": {
                !!json_encode($chartData['paid'] ?? []) !!
            },
            "expected": {
                !!json_encode($chartData['expected'] ?? []) !!
            },
            "all": {
                !!json_encode($chartData['all'] ?? []) !!
            }
        },
        "stats": {
            !!json_encode($chartStats ?? []) !!
        }
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
        let currentChartType = 'bar';

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

        function initChart(type = 'paid', chartType = 'bar') {
            if (revenueChart) revenueChart.destroy();

            const data = config.datasets[type];
            const total = data.reduce((a, b) => a + b, 0);
            const average = data.length ? total / data.length : 0;

            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, `${colors[type]}20`);
            gradient.addColorStop(1, `${colors[type]}05`);

            revenueChart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: config.labels,
                    datasets: [{
                        label: type === 'paid' ? 'Đã thu' : type === 'expected' ? 'Dự kiến' : 'Tổng',
                        data: data,
                        borderColor: colors[type],
                        backgroundColor: chartType === 'bar' ? `${colors[type]}40` : gradient,
                        borderWidth: 3,
                        borderRadius: chartType === 'bar' ? 6 : 0,
                        fill: chartType === 'line',
                        tension: 0.4,
                        pointBackgroundColor: colors[type],
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => v >= 1000000 ? (v / 1000000).toFixed(1) + 'tr' : (v /
                                    1000).toFixed(0) + 'k'
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
            initChart(t, currentChartType);
        };

        window.switchChartType = function(t) {
            currentChartType = t;
            document.querySelectorAll('.chart-type-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('chart-type-' + t).classList.add('active');
            initChart(currentDataType, t);
        };

        function refreshDashboard() {
            const btn = document.getElementById('refresh-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang tải...';
            btn.disabled = true;
            setTimeout(() => location.reload(), 800);
        }

        function toggleCustomDate() {
            document.getElementById('custom-date-range').classList.toggle('hidden', document.getElementById(
                'filter-type').value !== 'custom');
        }

        // Khởi tạo
        initChart();
        setInterval(() => {
            document.getElementById('current-time').textContent = new Date().toLocaleString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }, 60000);
    });
</script>

<style>
    .chart-data-btn.active,
    .chart-type-btn.active {
        background-color: #3b82f6;
        color: white;
    }

    .chart-data-btn,
    .chart-type-btn {
        transition: all 0.2s;
        border-radius: 6px;
    }
</style>
@endsection