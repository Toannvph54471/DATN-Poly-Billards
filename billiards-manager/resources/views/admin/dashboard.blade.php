@extends('admin.layouts.app')

@section('title', 'Dashboard - F&B Management')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Tổng quan hệ thống</h1>
            <p class="text-sm text-gray-500">Cập nhật lúc {{ date('H:i, d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-download mr-2"></i>Xuất báo cáo
            </button>
            <a href="{{ route('admin.tables.simple-dashboard') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Tổng Quan Bàn
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Doanh thu hôm nay -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-green-50 rounded-lg">
                    <i class="fas fa-money-bill-wave text-green-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full {{ $revenueGrowth >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                    <i class="fas {{ $revenueGrowth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs"></i>
                    {{ number_format(abs($revenueGrowth), 1) }}%
                </span>
            </div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Doanh thu hôm nay</h3>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($todayRevenue, 0, ',', '.') }}₫</p>
            <p class="text-xs text-gray-500 mt-2">So với hôm qua</p>
        </div>

        <!-- Số bill hôm nay -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-receipt text-blue-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full {{ $billGrowth >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                    <i class="fas {{ $billGrowth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs"></i>
                    {{ number_format(abs($billGrowth), 1) }}%
                </span>
            </div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Số đơn hàng</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $todayBills }}</p>
            <p class="text-xs text-gray-500 mt-2">So với hôm qua</p>
        </div>

        <!-- Bàn đang hoạt động -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-orange-50 rounded-lg">
                    <i class="fas fa-chair text-orange-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full bg-orange-50 text-orange-700">
                    {{ number_format($tableStats['occupancy_rate'], 0) }}%
                </span>
            </div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Bàn đang sử dụng</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $tableStats['occupied'] + $tableStats['reserved'] }}<span class="text-base text-gray-400 font-normal">/{{ $tableStats['total'] }}</span></p>
            <p class="text-xs text-gray-500 mt-2">Tỷ lệ sử dụng</p>
        </div>

        <!-- Khách hàng mới -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-purple-50 rounded-lg">
                    <i class="fas fa-users text-purple-600 text-lg"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full bg-purple-50 text-purple-700">
                    +{{ $newCustomersThisMonth }}
                </span>
            </div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Khách hàng mới</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $newCustomersToday }}</p>
            <p class="text-xs text-gray-500 mt-2">Hôm nay</p>
        </div>
    </div>

    <!-- Charts and Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Doanh thu theo tuần -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Doanh thu 7 ngày</h3>
                    <p class="text-sm text-gray-500 mt-1">Biểu đồ doanh thu theo thời gian</p>
                </div>
                <select id="chart-period" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="weekly">7 ngày</option>
                    <option value="monthly">30 ngày</option>
                    <option value="yearly">12 tháng</option>
                </select>
            </div>
            
            <div class="h-72 mb-6">
                <canvas id="revenueChart"></canvas>
            </div>
            
            <!-- Thống kê tổng quan -->
            <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-200">
                <div>
                    <p class="text-xs text-gray-500 mb-1" id="chart-total-label">Tổng tuần</p>
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

        <!-- Sản phẩm bán chạy -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sản phẩm bán chạy</h3>
            <div class="space-y-3">
                @foreach($topProducts as $index => $product)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ $product->total_quantity }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($product->total_revenue/1000, 0) }}k</p>
                    </div>
                </div>
                @endforeach
                @if($topProducts->count() == 0)
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-chart-bar text-3xl mb-2"></i>
                    <p class="text-sm">Chưa có dữ liệu</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Bills and Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Bill gần đây -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Đơn hàng gần đây</h3>
                <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                    Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Mã đơn</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Bàn</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Tổng tiền</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentBills as $bill)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <span class="font-mono text-sm font-medium text-gray-900">{{ $bill['bill_number'] }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-sm text-gray-900">{{ $bill['table_name'] }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-sm font-medium text-gray-900">{{ number_format($bill['total_amount'], 0, ',', '.') }}₫</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($bill['payment_status'] == 'Paid')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                    Đã thanh toán
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700">
                                    Chờ thanh toán
                                </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-sm text-gray-500">{{ $bill['time_ago'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                        @if($recentBills->count() == 0)
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                <i class="fas fa-receipt text-3xl mb-2"></i>
                                <p class="text-sm">Chưa có đơn hàng</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Thống kê nhanh -->
        <div class="space-y-6">
            <!-- Tình trạng bàn -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tình trạng bàn</h3>
                <div class="space-y-3">
                    @php
                        $statusItems = [
                            ['color' => 'green', 'label' => 'Trống', 'value' => $tableStats['available']],
                            ['color' => 'blue', 'label' => 'Đang dùng', 'value' => $tableStats['occupied']],
                            ['color' => 'yellow', 'label' => 'Đã đặt', 'value' => $tableStats['reserved']],
                            ['color' => 'gray', 'label' => 'Bảo trì', 'value' => $tableStats['maintenance']]
                        ];
                    @endphp
                    
                    @foreach($statusItems as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-{{ $item['color'] }}-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">{{ $item['label'] }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $item['value'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Tỷ lệ sử dụng</span>
                        <span class="font-semibold text-gray-900">{{ number_format($tableStats['occupancy_rate'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                             style="width: {{ $tableStats['occupancy_rate'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Nhân viên tích cực -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Nhân viên xuất sắc</h3>
                <div class="space-y-3">
                    @foreach($activeEmployees as $index => $employee)
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr($employee->name, 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $employee->name }}</p>
                            <p class="text-xs text-gray-500">{{ number_format($employee->total_revenue/1000, 0) }}k • {{ $employee->bill_count }} đơn</p>
                        </div>
                        @if($index == 0)
                        <i class="fas fa-crown text-yellow-500"></i>
                        @endif
                    </div>
                    @endforeach
                    @if($activeEmployees->count() == 0)
                    <div class="text-center py-4 text-gray-400">
                        <i class="fas fa-users text-2xl mb-2"></i>
                        <p class="text-sm">Chưa có dữ liệu</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Đặt bàn hôm nay -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Đặt bàn hôm nay</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Tổng đặt bàn</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $reservationStats['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Đã xác nhận</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $reservationStats['confirmed'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Đã hoàn thành</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $reservationStats['completed'] }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Hoàn thành</span>
                        <span class="font-semibold text-gray-900">{{ number_format($reservationStats['completion_rate'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full transition-all duration-500" 
                             style="width: {{ $reservationStats['completion_rate'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    let revenueChart;

    const weeklyData = @json($weeklyRevenue);

    function initChart(data) {
        if (revenueChart) {
            revenueChart.destroy();
        }

        const gradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.1)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

        revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: data.revenues,
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                        backgroundColor: '#ffffff',
                        titleColor: '#111827',
                        bodyColor: '#111827',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return formatCurrency(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: {
                            display: false
                        },
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000).toFixed(0) + 'tr';
                            },
                            font: {
                                size: 11
                            },
                            color: '#6b7280'
                        }
                    },
                    x: {
                        border: {
                            display: false
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#6b7280'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        updateStatistics(data);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    function updateStatistics(data) {
        const total = data.revenues.reduce((sum, revenue) => sum + revenue, 0);
        const average = total / data.revenues.length;
        const growth = 15.3;

        document.getElementById('chart-total').textContent = formatCurrency(total);
        document.getElementById('chart-average').textContent = formatCurrency(average);
        document.getElementById('chart-growth').textContent = '+' + growth + '%';
    }

    const chartData = {
        labels: weeklyData.map(item => item.day),
        revenues: weeklyData.map(item => item.revenue)
    };
    
    initChart(chartData);

    document.getElementById('chart-period').addEventListener('change', function() {
        loadChartData(this.value);
    });

    function loadChartData(period) {
        fetch(`/admin/dashboard/chart-data?type=${period}`)
            .then(response => response.json())
            .then(data => initChart(data))
            .catch(error => {
                console.error('Error:', error);
                initChart(chartData);
            });
    }
});
</script>
@endsection