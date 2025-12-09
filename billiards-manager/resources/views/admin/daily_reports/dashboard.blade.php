@extends('admin.layouts.app')

@section('title', 'Dashboard Thống kê')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-6">
            <div class="col-12">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                            Dashboard Thống kê
                        </h1>
                        <p class="text-gray-600">Tổng quan doanh thu và hiệu suất kinh doanh</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.daily-reports.index') }}"
                            class="btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-file-alt mr-2"></i> Báo cáo chi tiết
                        </a>
                        @if (auth()->user()->role->slug === 'admin')
                            <button onclick="generateTodayReport()"
                                class="btn bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-sync-alt mr-2"></i> Cập nhật hôm nay
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow text-white p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Doanh thu hôm nay</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ number_format($todayRevenue, 0, ',', '.') }} ₫
                        </p>
                        <p class="text-xs mt-2 opacity-80">
                            <i class="fas fa-calendar-day mr-1"></i>
                            {{ date('d/m/Y') }}
                        </p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>
                @if ($yesterdayRevenue > 0)
                    <div class="mt-4 pt-3 border-t border-white/20">
                        <div class="flex justify-between text-sm">
                            <span>So với hôm qua</span>
                            <span class="{{ $todayRevenue >= $yesterdayRevenue ? 'text-green-200' : 'text-red-200' }}">
                                @if ($yesterdayRevenue > 0)
                                    @php
                                        $change = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
                                    @endphp
                                    {{ $todayRevenue >= $yesterdayRevenue ? '+' : '' }}{{ number_format($change, 1) }}%
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow text-white p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Doanh thu tháng này</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ number_format($monthRevenue, 0, ',', '.') }} ₫
                        </p>
                        <p class="text-xs mt-2 opacity-80">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Tháng {{ date('m/Y') }}
                        </p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <i class="fas fa-chart-bar text-2xl"></i>
                    </div>
                </div>
                @if ($lastMonthRevenue > 0)
                    <div class="mt-4 pt-3 border-t border-white/20">
                        <div class="flex justify-between text-sm">
                            <span>So với tháng trước</span>
                            <span class="{{ $monthRevenue >= $lastMonthRevenue ? 'text-green-200' : 'text-red-200' }}">
                                @if ($lastMonthRevenue > 0)
                                    @php
                                        $change = (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
                                    @endphp
                                    {{ $monthRevenue >= $lastMonthRevenue ? '+' : '' }}{{ number_format($change, 1) }}%
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow text-white p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Hóa đơn tháng này</p>
                        <p class="text-2xl font-bold mt-1">{{ $monthBills }}</p>
                        <p class="text-xs mt-2 opacity-80">
                            <i class="fas fa-receipt mr-1"></i>
                            Tổng số hóa đơn
                        </p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <i class="fas fa-file-invoice text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-white/20">
                    <div class="text-sm">
                        <span>Trung bình
                            {{ $monthBills > 0 ? number_format($monthRevenue / $monthBills, 0, ',', '.') : 0 }} ₫/hóa
                            đơn</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl shadow text-white p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Hôm nay vs Hôm qua</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ number_format($todayRevenue - $yesterdayRevenue, 0, ',', '.') }} ₫
                        </p>
                        <p class="text-xs mt-2 opacity-80">
                            <i class="fas fa-exchange-alt mr-1"></i>
                            Chênh lệch
                        </p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <i class="fas fa-balance-scale text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-white/20">
                    <div class="text-sm">
                        <span>Hôm qua: {{ number_format($yesterdayRevenue, 0, ',', '.') }} ₫</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Weekly Revenue Chart -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-chart-area mr-2 text-blue-600"></i>
                        Doanh thu 7 ngày gần nhất
                    </h3>
                    <span class="text-sm text-gray-500">
                        Tổng: {{ number_format($weeklyRevenue->sum('total_revenue'), 0, ',', '.') }} ₫
                    </span>
                </div>
                <div class="h-64">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            <!-- Daily Bills Chart -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-chart-bar mr-2 text-green-600"></i>
                        Số hóa đơn 7 ngày gần nhất
                    </h3>
                    <span class="text-sm text-gray-500">
                        Tổng: {{ $weeklyRevenue->sum('total_bills') }} hóa đơn
                    </span>
                </div>
                <div class="h-64">
                    <canvas id="billsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Days and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top 5 Days -->
            <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-trophy mr-2 text-yellow-600"></i>
                    Top 5 ngày doanh thu cao nhất
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ngày
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Doanh thu
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hóa đơn
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Khách hàng
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    TB/HĐ
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($topDays as $index => $day)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $index == 0
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : ($index == 1
                                                ? 'bg-gray-100 text-gray-800'
                                                : ($index == 2
                                                    ? 'bg-orange-100 text-orange-800'
                                                    : 'bg-blue-100 text-blue-800')) }}">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="ml-3 text-sm font-medium text-gray-900">
                                                {{ date('d/m/Y', strtotime($day->report_date)) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-green-600">
                                            {{ number_format($day->total_revenue, 0, ',', '.') }} ₫
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                            {{ $day->total_bills }} hóa đơn
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $day->total_customers }}</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ number_format($day->average_bill_value, 0, ',', '.') }} ₫
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-chart-line text-3xl mb-2"></i>
                                        <p>Chưa có dữ liệu</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-bolt mr-2 text-purple-600"></i>
                    Thống kê nhanh
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-calendar-check text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Ngày có doanh thu</p>
                                <p class="text-xs text-gray-500">Trong 30 ngày qua</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-blue-600">
                            {{ \App\Models\DailyReport::where('report_date', '>=', date('Y-m-d', strtotime('-30 days')))->where('total_revenue', '>', 0)->count() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-chart-line text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Doanh thu trung bình/ngày</p>
                                <p class="text-xs text-gray-500">7 ngày gần nhất</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-green-600">
                            @php
                                $avg7Days =
                                    $weeklyRevenue->count() > 0
                                        ? $weeklyRevenue->sum('total_revenue') / $weeklyRevenue->count()
                                        : 0;
                            @endphp
                            {{ number_format($avg7Days, 0, ',', '.') }} ₫
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Khách TB/ngày</p>
                                <p class="text-xs text-gray-500">7 ngày gần nhất</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-purple-600">
                            @php
                                $avgCustomers =
                                    $weeklyRevenue->count() > 0
                                        ? $weeklyRevenue->sum('total_customers') / $weeklyRevenue->count()
                                        : 0;
                            @endphp
                            {{ round($avgCustomers, 1) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-orange-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-receipt text-orange-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">HĐ TB/ngày</p>
                                <p class="text-xs text-gray-500">7 ngày gần nhất</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-orange-600">
                            @php
                                $avgBills =
                                    $weeklyRevenue->count() > 0
                                        ? $weeklyRevenue->sum('total_bills') / $weeklyRevenue->count()
                                        : 0;
                            @endphp
                            {{ round($avgBills, 1) }}
                        </span>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-blue-800 mb-2">
                            <i class="fas fa-lightbulb mr-2"></i>
                            <strong>Mẹo:</strong> Để tăng doanh thu
                        </p>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li>• Khuyến khích khách mua combo</li>
                            <li>• Ưu đãi giờ thấp điểm</li>
                            <li>• Tích điểm cho khách hàng thân thiết</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const weeklyData = @json($weeklyRevenue);

                // Weekly Revenue Chart
                const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
                new Chart(weeklyCtx, {
                    type: 'line',
                    data: {
                        labels: weeklyData.map(item => {
                            const date = new Date(item.report_date);
                            return date.toLocaleDateString('vi-VN', {
                                weekday: 'short',
                                day: 'numeric'
                            });
                        }),
                        datasets: [{
                            label: 'Doanh thu (triệu VNĐ)',
                            data: weeklyData.map(item => Math.round(item.total_revenue / 1000000 *
                                100) / 100),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
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
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += new Intl.NumberFormat('vi-VN').format(context.raw *
                                            1000000) + ' ₫';
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value + 'M';
                                    }
                                }
                            }
                        }
                    }
                });

                // Daily Bills Chart
                const billsCtx = document.getElementById('billsChart').getContext('2d');
                new Chart(billsCtx, {
                    type: 'bar',
                    data: {
                        labels: weeklyData.map(item => {
                            const date = new Date(item.report_date);
                            return date.toLocaleDateString('vi-VN', {
                                weekday: 'short',
                                day: 'numeric'
                            });
                        }),
                        datasets: [{
                            label: 'Số hóa đơn',
                            data: weeklyData.map(item => item.total_bills),
                            backgroundColor: 'rgba(34, 197, 94, 0.5)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1
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
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });

            function generateTodayReport() {
                Swal.fire({
                    title: 'Cập nhật báo cáo hôm nay?',
                    text: 'Hệ thống sẽ tính toán lại doanh thu từ các hóa đơn đã thanh toán.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Cập nhật',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const today = new Date().toISOString().split('T')[0];

                        fetch('{{ route('admin.daily-reports.generate') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    date: today
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message || 'Báo cáo đã được cập nhật.',
                                        icon: 'success',
                                        confirmButtonColor: '#10b981'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: data.message || 'Có lỗi xảy ra khi cập nhật báo cáo.',
                                        icon: 'error',
                                        confirmButtonColor: '#ef4444'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: 'Không thể kết nối đến máy chủ.',
                                    icon: 'error',
                                    confirmButtonColor: '#ef4444'
                                });
                            });
                    }
                });
            }
        </script>
    @endpush
@endsection
