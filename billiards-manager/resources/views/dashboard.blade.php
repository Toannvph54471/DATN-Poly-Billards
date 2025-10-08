@extends('admin.layouts.app')

@section('title', 'Tổng quan - F&B Management')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Tổng quan</h1>
        <p class="text-gray-600">Thống kê và báo cáo tổng quan cửa hàng</p>
    </div>
    <div class="flex space-x-3">
        <button class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-50 transition flex items-center">
            <i class="fas fa-calendar-alt mr-2"></i>
            Hôm nay
            <i class="fas fa-chevron-down ml-2"></i>
        </button>
        <button class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
            <i class="fas fa-download mr-2"></i>
            Xuất báo cáo
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Doanh thu -->
    <div class="stat-card p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-500 text-sm">Doanh thu hôm nay</p>
                <p class="text-2xl font-bold text-gray-800">12.850.000₫</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
            <span class="text-green-500 font-medium">+12.5%</span>
            <span class="text-gray-500 ml-1">so với hôm qua</span>
        </div>
    </div>

    <!-- Đơn hàng -->
    <div class="stat-card p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-500 text-sm">Tổng đơn hàng</p>
                <p class="text-2xl font-bold text-gray-800">48</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
            <span class="text-green-500 font-medium">+8.2%</span>
            <span class="text-gray-500 ml-1">so với hôm qua</span>
        </div>
    </div>

    <!-- Khách hàng -->
    <div class="stat-card p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-500 text-sm">Khách hàng mới</p>
                <p class="text-2xl font-bold text-gray-800">15</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
            <span class="text-green-500 font-medium">+5.3%</span>
            <span class="text-gray-500 ml-1">so với hôm qua</span>
        </div>
    </div>

    <!-- Tồn kho -->
    <div class="stat-card p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-gray-500 text-sm">Sản phẩm tồn</p>
                <p class="text-2xl font-bold text-gray-800">23</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-boxes text-orange-600 text-xl"></i>
            </div>
        </div>
        <div class="flex items-center text-sm">
            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
            <span class="text-red-500 font-medium">-3.1%</span>
            <span class="text-gray-500 ml-1">so với hôm qua</span>
        </div>
    </div>
</div>

<!-- Charts & Tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Doanh thu chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold">Doanh thu 7 ngày qua</h3>
            <button class="text-blue-600 text-sm font-medium">Xem tất cả</button>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Top sản phẩm -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold">Sản phẩm bán chạy</h3>
            <button class="text-blue-600 text-sm font-medium">Xem tất cả</button>
        </div>
        <div class="space-y-4">
            <!-- Product 1 -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-coffee text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium">Cà phê đen</p>
                        <p class="text-sm text-gray-500">Đã bán: 45</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold">2.150.000₫</p>
                    <p class="text-sm text-green-500">+15%</p>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-mug-hot text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium">Trà sữa</p>
                        <p class="text-sm text-gray-500">Đã bán: 38</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold">1.850.000₫</p>
                    <p class="text-sm text-green-500">+12%</p>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cookie text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-medium">Bánh ngọt</p>
                        <p class="text-sm text-gray-500">Đã bán: 28</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold">1.250.000₫</p>
                    <p class="text-sm text-green-500">+8%</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Đơn hàng gần đây</h3>
        <button class="text-blue-600 text-sm font-medium">Xem tất cả</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">MÃ ĐƠN</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">KHÁCH HÀNG</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">THỜI GIAN</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">TỔNG TIỀN</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">TRẠNG THÁI</th>
                </tr>
            </thead>
            <tbody>
                <!-- Order 1 -->
                <tr class="table-row border-b border-gray-100">
                    <td class="py-3 px-4">
                        <span class="font-medium text-blue-600">#DH001</span>
                    </td>
                    <td class="py-3 px-4">
                        <div>
                            <p class="font-medium">Nguyễn Văn A</p>
                            <p class="text-sm text-gray-500">0123456789</p>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <p class="text-sm">14:30 - 25/12/2024</p>
                    </td>
                    <td class="py-3 px-4">
                        <p class="font-semibold">350.000₫</p>
                    </td>
                    <td class="py-3 px-4">
                        <span class="badge-success px-2 py-1 rounded-full text-xs font-medium">Hoàn thành</span>
                    </td>
                </tr>

                <!-- Order 2 -->
                <tr class="table-row border-b border-gray-100">
                    <td class="py-3 px-4">
                        <span class="font-medium text-blue-600">#DH002</span>
                    </td>
                    <td class="py-3 px-4">
                        <div>
                            <p class="font-medium">Trần Thị B</p>
                            <p class="text-sm text-gray-500">0987654321</p>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <p class="text-sm">13:15 - 25/12/2024</p>
                    </td>
                    <td class="py-3 px-4">
                        <p class="font-semibold">520.000₫</p>
                    </td>
                    <td class="py-3 px-4">
                        <span class="badge-warning px-2 py-1 rounded-full text-xs font-medium">Đang xử lý</span>
                    </td>
                </tr>

                <!-- Order 3 -->
                <tr class="table-row border-b border-gray-100">
                    <td class="py-3 px-4">
                        <span class="font-medium text-blue-600">#DH003</span>
                    </td>
                    <td class="py-3 px-4">
                        <div>
                            <p class="font-medium">Lê Văn C</p>
                            <p class="text-sm text-gray-500">0369852147</p>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <p class="text-sm">12:45 - 25/12/2024</p>
                    </td>
                    <td class="py-3 px-4">
                        <p class="font-semibold">280.000₫</p>
                    </td>
                    <td class="py-3 px-4">
                        <span class="badge-danger px-2 py-1 rounded-full text-xs font-medium">Đã hủy</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: [8500000, 9200000, 10500000, 11200000, 9850000, 12850000, 11500000],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
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
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000).toFixed(0) + 'tr';
                        }
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
</script>
@endsection