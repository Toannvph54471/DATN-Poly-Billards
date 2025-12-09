@extends('admin.layouts.app')

@section('title', 'Chi tiết báo cáo ' . date('d/m/Y', strtotime($date)))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                        Báo cáo chi tiết
                    </h1>
                    <p class="text-gray-600">
                        {{ \Carbon\Carbon::parse($date)->locale('vi')->translatedFormat('l, d/m/Y') }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.daily-reports.index') }}" 
                       class="btn bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-arrow-left mr-2"></i> Quay lại
                    </a>
                    <button onclick="window.print()" 
                            class="btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-print mr-2"></i> In báo cáo
                    </button>
                    @if(auth()->user()->role->slug === 'admin')
                    <a href="{{ route('admin.daily-reports.export', ['date' => $date]) }}" 
                       class="btn bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-file-excel mr-2"></i> Xuất Excel
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow border-l-4 border-blue-500 p-5">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Tổng doanh thu</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ number_format($report->total_revenue, 0, ',', '.') }} ₫
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border-l-4 border-red-500 p-5">
            <div class="flex items-center">
                <div class="bg-red-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-tag text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Tổng giảm giá</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ number_format($report->total_discount, 0, ',', '.') }} ₫
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border-l-4 border-green-500 p-5">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-receipt text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Số hóa đơn</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $report->total_bills }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border-l-4 border-purple-500 p-5">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Số khách hàng</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $report->total_customers }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết bổ sung -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-2">Giá trị trung bình hóa đơn</p>
                <p class="text-3xl font-bold text-blue-600">
                    {{ number_format($report->average_bill_value, 0, ',', '.') }} ₫
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-2">Doanh thu thuần</p>
                <p class="text-3xl font-bold text-green-600">
                    @php
                        $netRevenue = $report->total_revenue - $report->total_discount;
                    @endphp
                    {{ number_format($netRevenue, 0, ',', '.') }} ₫
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-2">Tỉ lệ chiếm dụng</p>
                <p class="text-3xl font-bold text-orange-600">
                    @php
                        $occupancyRate = $report->total_bills > 0 ? 
                            round(($report->total_customers / $report->total_bills) * 100, 1) : 0;
                    @endphp
                    {{ $occupancyRate }}%
                </p>
                <p class="text-xs text-gray-500">Khách/Hóa đơn</p>
            </div>
        </div>
    </div>

    <!-- Danh sách hóa đơn -->
    <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-list mr-2 text-blue-600"></i>
                    Danh sách hóa đơn ({{ $bills->count() }})
                </h3>
                <span class="text-sm text-gray-500">
                    Cập nhật: {{ \Carbon\Carbon::parse($report->updated_at)->locale('vi')->diffForHumans() }}
                </span>
            </div>
        </div>

        @if($bills->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mã hóa đơn
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bàn
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Khách hàng
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thời gian
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tổng tiền
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thanh toán
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($bills as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-blue-600">
                                {{ $bill->bill_number }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $bill->table_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $bill->customer_display }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($bill->created_at)->format('H:i') }}
                                @if($bill->end_time)
                                - {{ \Carbon\Carbon::parse($bill->end_time)->format('H:i') }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-green-600">
                                {{ number_format($bill->final_amount, 0, ',', '.') }} ₫
                            </div>
                            @if($bill->discount_amount > 0)
                            <div class="text-xs text-red-500">
                                Giảm: {{ number_format($bill->discount_amount, 0, ',', '.') }} ₫
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($bill->payment_method == 'cash') bg-green-100 text-green-800
                                @elseif($bill->payment_method == 'banking') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $bill->payment_method == 'cash' ? 'Tiền mặt' : 
                                   ($bill->payment_method == 'banking' ? 'Chuyển khoản' : 'Khác') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                            Tổng cộng:
                        </td>
                        <td class="px-6 py-3 text-sm font-bold text-green-600">
                            {{ number_format($bills->sum('final_amount'), 0, ',', '.') }} ₫
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs text-gray-500">
                                {{ $bills->where('payment_method', 'cash')->count() }} tiền mặt
                                {{ $bills->where('payment_method', 'banking')->count() > 0 ? 
                                   ', ' . $bills->where('payment_method', 'banking')->count() . ' chuyển khoản' : '' }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-receipt text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Không có hóa đơn nào trong ngày này</p>
        </div>
        @endif
    </div>

    <!-- Thống kê theo giờ -->
    @php
        // Group bills by hour for chart
        $hourlyData = [];
        foreach(range(8, 23) as $hour) {
            $hourlyData[$hour] = [
                'hour' => $hour . ':00',
                'count' => 0,
                'revenue' => 0
            ];
        }
        
        foreach($bills as $bill) {
            $hour = \Carbon\Carbon::parse($bill->created_at)->hour;
            if(isset($hourlyData[$hour])) {
                $hourlyData[$hour]['count']++;
                $hourlyData[$hour]['revenue'] += $bill->final_amount;
            }
        }
    @endphp

    @if($bills->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Biểu đồ số hóa đơn theo giờ -->
        <div class="bg-white rounded-xl shadow p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                Số hóa đơn theo giờ
            </h4>
            <div class="h-64">
                <canvas id="hourlyBillsChart"></canvas>
            </div>
        </div>

        <!-- Biểu đồ doanh thu theo giờ -->
        <div class="bg-white rounded-xl shadow p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-line mr-2 text-green-600"></i>
                Doanh thu theo giờ
            </h4>
            <div class="h-64">
                <canvas id="hourlyRevenueChart"></canvas>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
@if($bills->count() > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hourlyData = @json(array_values($hourlyData));
        
        // Chart for hourly bills
        const billsCtx = document.getElementById('hourlyBillsChart').getContext('2d');
        new Chart(billsCtx, {
            type: 'bar',
            data: {
                labels: hourlyData.map(item => item.hour),
                datasets: [{
                    label: 'Số hóa đơn',
                    data: hourlyData.map(item => item.count),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Chart for hourly revenue
        const revenueCtx = document.getElementById('hourlyRevenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: hourlyData.map(item => item.hour),
                datasets: [{
                    label: 'Doanh thu (nghìn VNĐ)',
                    data: hourlyData.map(item => Math.round(item.revenue / 1000)),
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + 'K';
                            }
                        }
                    }
                },
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
                                label += new Intl.NumberFormat('vi-VN').format(context.raw * 1000) + ' ₫';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif

<!-- Print Styles -->
<style>
    @media print {
        .btn, header, footer, .no-print {
            display: none !important;
        }
        
        body {
            font-size: 12px;
        }
        
        .container-fluid {
            padding: 0;
        }
        
        .grid, .flex {
            display: block !important;
        }
        
        .shadow, .rounded-xl {
            box-shadow: none !important;
            border-radius: 0 !important;
            border: 1px solid #ddd !important;
        }
        
        .bg-white {
            background: white !important;
        }
        
        .mb-6, .mb-4 {
            margin-bottom: 10px !important;
            page-break-inside: avoid;
        }
        
        h1, h2, h3, h4 {
            page-break-after: avoid;
        }
        
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>
@endpush
@endsection