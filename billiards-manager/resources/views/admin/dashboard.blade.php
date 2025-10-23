@extends('admin.layouts.app')

@section('title', 'Dashboard - F&B Management')

@section('styles')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    
    .chart-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
    }
    
    .quick-action {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .quick-action:hover {
        transform: translateY(-3px);
        border-color: #3b82f6;
    }
    
    .recent-activity {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }
    
    .status-online { background-color: #10b981; }
    .status-offline { background-color: #ef4444; }
    .status-busy { background-color: #f59e0b; }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600">Tổng quan hoạt động hệ thống</p>
    </div>
    <div class="flex items-center space-x-3">
        <div class="text-sm text-gray-500">
            <i class="far fa-calendar mr-2"></i>
            <span id="current-date">{{ now()->format('d/m/Y') }}</span>
        </div>
        <button onclick="refreshDashboard()" 
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
            <i class="fas fa-sync-alt mr-2"></i>
            Làm mới
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="dashboard-grid mb-6">
    <!-- Total Revenue -->
    <div class="stat-card p-6">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Doanh thu hôm nay</p>
                <p class="text-2xl font-bold text-gray-800">12.5M ₫</p>
                <p class="text-sm text-green-600 mt-1">
                    <i class="fas fa-arrow-up mr-1"></i>
                    +15% so với hôm qua
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="stat-card p-6">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Đơn hàng hôm nay</p>
                <p class="text-2xl font-bold text-gray-800">48</p>
                <p class="text-sm text-blue-600 mt-1">
                    <i class="fas fa-shopping-cart mr-1"></i>
                    Đang xử lý: 12
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-receipt text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Active Tables -->
    <div class="stat-card p-6">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Bàn đang hoạt động</p>
                <p class="text-2xl font-bold text-gray-800">8/12</p>
                <p class="text-sm text-orange-600 mt-1">
                    <i class="fas fa-circle mr-1"></i>
                    Tỷ lệ sử dụng: 67%
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-circle text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Inventory Alerts -->
    <div class="stat-card p-6">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Cảnh báo tồn kho</p>
                <p class="text-2xl font-bold text-gray-800">5</p>
                <p class="text-sm text-red-600 mt-1">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Cần nhập hàng
                </p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-boxes text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Revenue Chart -->
    <div class="lg:col-span-2 chart-container">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Doanh thu 7 ngày qua</h3>
            <select class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>7 ngày</option>
                <option>30 ngày</option>
                <option>3 tháng</option>
            </select>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="chart-container">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="" 
               class="quick-action bg-blue-50 p-4 rounded-lg text-center hover:shadow-md transition">
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-800">Tạo đơn hàng</p>
            </a>
            
            <a href="" 
               class="quick-action bg-green-50 p-4 rounded-lg text-center hover:shadow-md transition">
                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-clipboard-list text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-800">Nhập tồn kho</p>
            </a>
            
            <a href="{{ route('admin.products.create') }}" 
               class="quick-action bg-purple-50 p-4 rounded-lg text-center hover:shadow-md transition">
                <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-cube text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-800">Thêm sản phẩm</p>
            </a>
            
            <a href="{{ route('admin.tables.index') }}" 
               class="quick-action bg-orange-50 p-4 rounded-lg text-center hover:shadow-md transition">
                <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-circle text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-800">Quản lý bàn</p>
            </a>
        </div>
    </div>
</div>

<!-- Bottom Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="chart-container">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Đơn hàng gần đây</h3>
            <a href="" class="text-sm text-blue-600 hover:text-blue-800">
                Xem tất cả
            </a>
        </div>
        <div class="recent-activity">
            <div class="space-y-4">
                {{-- @foreach($recentOrders as $order)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Đơn #{{ $order->code }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-800">{{ number_format($order->total) }} ₫</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                            {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status == 'processing' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ $order->status_text }}
                        </span>
                    </div>
                </div>
                @endforeach --}}
            </div>
        </div>
    </div>

    <!-- Staff Activity -->
    <div class="chart-container">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Nhân viên đang hoạt động</h3>
            <a href="{{ route('admin.employees.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Quản lý
            </a>
        </div>
        <div class="space-y-4">
            {{-- @foreach($activeStaff as $staff)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr($staff->name, 0, 1) }}
                        </div>
                        <span class="status-dot {{ $staff->status_class }} absolute -bottom-1 -right-1 border-2 border-white"></span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $staff->name }}</p>
                        <p class="text-xs text-gray-500">{{ $staff->role }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Đang phục vụ</p>
                    <p class="text-sm font-medium text-gray-800">{{ $staff->active_tables }} bàn</p>
                </div>
            </div>
            @endforeach --}}
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
{{-- @if($lowStockProducts->count() > 0)
<div class="mt-6 chart-container border-l-4 border-red-500">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
            Sản phẩm sắp hết hàng
        </h3>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
            Quản lý kho
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($lowStockProducts as $product)
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <div class="flex items-center space-x-3">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                     class="w-10 h-10 rounded-lg object-cover">
                @else
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cube text-red-600"></i>
                </div>
                @endif
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">{{ $product->name }}</p>
                    <p class="text-xs text-red-600">
                        Tồn kho: {{ $product->stock_quantity }} {{ $product->unit }}
                    </p>
                </div>
            </div>
            <div class="mt-2 bg-red-200 rounded-full h-2">
                <div class="bg-red-500 h-2 rounded-full" 
                     style="width: {{ ($product->stock_quantity / $product->min_stock) * 100 }}%"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif --}}
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
                data: [8500000, 9200000, 7800000, 10500000, 12500000, 9800000, 11200000],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
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
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Refresh Dashboard
    function refreshDashboard() {
        const button = event.target;
        const originalText = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang tải...';
        button.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Đã cập nhật',
                text: 'Dữ liệu dashboard đã được làm mới',
                timer: 1500,
                showConfirmButton: false
            });
        }, 1000);
    }

    // Update current time
    function updateCurrentTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('vi-VN', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('current-time').textContent = timeString;
    }

    // Update time every second
    setInterval(updateCurrentTime, 1000);
    updateCurrentTime();

    // Real-time updates (simulated)
    setInterval(() => {
        // Simulate real-time data updates
        const randomOrders = Math.floor(Math.random() * 3);
        const randomRevenue = Math.floor(Math.random() * 500000);
        
        if (randomOrders > 0) {
            // You could add real WebSocket updates here
            console.log('New orders:', randomOrders);
        }
    }, 30000); // Check every 30 seconds
</script>
@endsection