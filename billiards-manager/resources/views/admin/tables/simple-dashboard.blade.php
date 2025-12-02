@extends('admin.layouts.app')

@section('title', 'Simple Dashboard')

@section('styles')
<style>
    .table-card {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        height: 140px;
        display: flex;
        align-items: center;
        transform-style: preserve-3d;
        perspective: 1000px;
    }
    
    .table-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    }
    
    .table-card:before {
        content: '';
        position: absolute;
        top: 12px;
        right: 12px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid;
        z-index: 2;
    }
    
    .status-available:before {
        background-color: #10b981;
        border-color: #059669;
        animation: pulse-green 2s infinite;
    }
    
    .status-occupied:before {
        background-color: #ef4444;
        border-color: #dc2626;
        animation: pulse-red 2s infinite;
    }
    
    .status-quick:before {
        background-color: #f59e0b;
        border-color: #d97706;
        animation: pulse-yellow 2s infinite;
    }
    
    .combo-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
        z-index: 10;
        transform: translateZ(20px);
        animation: float 3s ease-in-out infinite;
    }
    
    .timer-display {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        font-size: 18px;
        letter-spacing: 1px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 6px 12px;
        border-radius: 8px;
        margin-top: 6px;
        display: inline-block;
        border: 1px solid #e2e8f0;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
    }
    
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
    }
    
    @media (max-width: 1400px) {
        .grid-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    @media (max-width: 1200px) {
        .grid-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 900px) {
        .grid-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            padding: 16px;
        }
    }
    
    @media (max-width: 600px) {
        .grid-container {
            grid-template-columns: 1fr;
        }
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        color: white;
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }
    
    .refresh-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.4);
    }
    
    .refresh-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(102, 126, 234, 0.5);
    }
    
    .refresh-btn:active {
        transform: translateY(0);
    }
    
    .table-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0 24px;
    }
    
    .table-left {
        text-align: left;
        flex: 1;
    }
    
    .table-right {
        text-align: right;
        margin-left: 16px;
    }
    
    .status-badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Hiệu ứng cho bàn đang sử dụng (đen chữ trắng) */
    .table-occupied {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        color: white !important;
        border: 2px solid #4b5563;
    }
    
    .table-occupied .timer-display {
        background: rgba(255, 255, 255, 0.1);
        color: #60a5fa;
        border-color: #4b5563;
    }
    
    .table-occupied .text-gray-600,
    .table-occupied .text-gray-800,
    .table-occupied .text-gray-500 {
        color: #e5e7eb !important;
    }
    
    .table-available {
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        border: 2px solid #d1d5db;
    }
    
    .table-quick {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #fbbf24;
    }
    
    /* Hiệu ứng glow */
    .glow {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: radial-gradient(circle at center, transparent 0%, transparent 60%, rgba(255,255,255,0.1) 100%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .table-card:hover .glow {
        opacity: 1;
    }
    
    /* Animations */
    @keyframes pulse-green {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    @keyframes pulse-red {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    @keyframes pulse-yellow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0) translateZ(20px); }
        50% { transform: translateY(-5px) translateZ(20px); }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    /* Wave effect for empty tables */
    .wave-effect {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 40px;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
        animation: wave 3s linear infinite;
        opacity: 0.5;
    }
    
    @keyframes wave {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    /* Shine effect */
    .shine {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            to right,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.1) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        transform: rotate(30deg);
        transition: all 0.6s ease;
    }
    
    .table-card:hover .shine {
        left: 100%;
    }
    
    /* Loading skeleton */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto p-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Simple Dashboard</h1>
            <p class="text-gray-600">Click vào bàn để xem chi tiết và quản lý</p>
        </div>
        <div class="flex items-center gap-3">
            @if($stats)
            <div class="hidden md:flex items-center gap-4 text-sm text-gray-600 bg-white p-3 rounded-lg shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <span>Trống: <span class="font-semibold">{{ $stats['available'] }}</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <span>Đang dùng: <span class="font-semibold">{{ $stats['occupied'] + $stats['quick'] }}</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <span>Tỷ lệ: <span class="font-semibold">{{ $stats['occupancy_rate'] }}%</span></span>
                </div>
            </div>
            @endif
            <button onclick="refreshDashboard()" class="refresh-btn flex items-center gap-2">
                <i class="fas fa-sync-alt"></i>
                <span>Làm mới</span>
            </button>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    @if($stats)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium opacity-90">Tổng số bàn</div>
                    <div class="text-3xl font-bold mt-2">{{ $stats['total'] }}</div>
                </div>
                <i class="fas fa-chair text-2xl opacity-80"></i>
            </div>
            <div class="mt-4 pt-3 border-t border-white/20 text-xs opacity-80">
                <i class="fas fa-info-circle mr-1"></i> Tổng số bàn trong hệ thống
            </div>
        </div>
        
        <div class="stats-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium opacity-90">Bàn trống</div>
                    <div class="text-3xl font-bold mt-2">{{ $stats['available'] }}</div>
                </div>
                <i class="fas fa-check-circle text-2xl opacity-80"></i>
            </div>
            <div class="mt-4 pt-3 border-t border-white/20 text-xs opacity-80">
                <i class="fas fa-info-circle mr-1"></i> Bàn sẵn sàng phục vụ
            </div>
        </div>
        
        <div class="stats-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium opacity-90">Bàn đang dùng</div>
                    <div class="text-3xl font-bold mt-2">{{ $stats['occupied'] + $stats['quick'] }}</div>
                </div>
                <i class="fas fa-clock text-2xl opacity-80"></i>
            </div>
            <div class="mt-4 pt-3 border-t border-white/20 text-xs opacity-80">
                <i class="fas fa-info-circle mr-1"></i> Bàn đang được sử dụng
            </div>
        </div>
        
        <div class="stats-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium opacity-90">Tỷ lệ sử dụng</div>
                    <div class="text-3xl font-bold mt-2">{{ $stats['occupancy_rate'] }}%</div>
                </div>
                <i class="fas fa-chart-line text-2xl opacity-80"></i>
            </div>
            <div class="mt-4 pt-3 border-t border-white/20 text-xs opacity-80">
                <i class="fas fa-info-circle mr-1"></i> Hiệu suất sử dụng bàn
            </div>
        </div>
    </div>
    @endif
    
    <!-- Error Message -->
    @if(isset($error))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <div>
                <p class="font-medium">{{ $error }}</p>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Tables Grid -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Danh sách bàn</h2>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <i class="fas fa-info-circle"></i>
                <span>Tổng: {{ count($tables) }} bàn</span>
            </div>
        </div>
        
        <div class="grid-container" id="tablesGrid">
            @forelse($tables as $table)
                @php
                    $hasCombo = $table['has_combo'] ?? false;
                    $elapsedTime = $table['elapsed_time'] ?? null;
                    
                    // Xác định class dựa trên trạng thái
                    $tableClasses = [
                        'available' => 'table-available',
                        'occupied' => 'table-occupied',
                        'quick' => 'table-quick',
                        'maintenance' => 'bg-gradient-to-br from-gray-100 to-gray-200 border-gray-300',
                        'reserved' => 'bg-gradient-to-br from-blue-50 to-blue-100 border-blue-300'
                    ];
                    
                    $cardClass = $tableClasses[$table['status']] ?? 'bg-gradient-to-br from-gray-50 to-gray-100 border-gray-200';
                @endphp
                
                <div class="table-card {{ $cardClass }} status-{{ $table['status'] }}"
                     onclick="goToTableDetail('{{ $table['id'] }}')"
                     data-status="{{ $table['status'] }}"
                     data-table-id="{{ $table['id'] }}">
                    
                    <!-- Wave effect for empty tables -->
                    @if($table['status'] === 'available')
                        <div class="wave-effect"></div>
                    @endif
                    
                    <!-- Shine effect -->
                    <div class="shine"></div>
                    
                    <!-- Glow effect -->
                    <div class="glow"></div>
                    
                    <!-- Combo Badge -->
                    @if($hasCombo)
                        <div class="combo-badge">
                            <i class="fas fa-gift mr-1"></i> COMBO
                        </div>
                    @endif
                    
                    <div class="table-content">
                        <div class="table-left">
                            <!-- Table Number/Name -->
                            <div class="mb-3">
                                <div class="text-3xl font-bold text-gray-800 mb-1">
                                    {{ $table['table_number'] }}
                                </div>
                                <div class="text-sm text-gray-600 font-medium">
                                    {{ $table['table_name'] }}
                                </div>
                            </div>
                            
                            <!-- Capacity -->
                            <div class="flex items-center text-sm">
                                <i class="fas fa-users text-gray-400 mr-2"></i>
                                <span class="text-gray-500">{{ $table['capacity'] }} người</span>
                                @if($table['hourly_rate'])
                                    <span class="mx-2 text-gray-300">•</span>
                                    <span class="text-amber-600 font-semibold">{{ number_format($table['hourly_rate']) }}đ/giờ</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="table-right">
                            <!-- Timer Display (if occupied) -->
                            @if($table['status'] !== 'available' && $elapsedTime)
                                <div class="mb-3">
                                    <div class="text-xs text-gray-500 mb-1 flex items-center">
                                        <i class="fas fa-clock mr-1"></i> Thời gian
                                    </div>
                                    <div class="timer-display text-gray-800">
                                        {{ $elapsedTime }}
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div>
                                @php
                                    $statusText = [
                                        'available' => 'TRỐNG',
                                        'occupied' => 'ĐANG DÙNG',
                                        'quick' => 'QUICK',
                                        'maintenance' => 'BẢO TRÌ',
                                        'reserved' => 'ĐÃ ĐẶT'
                                    ];
                                    
                                    $badgeClasses = [
                                        'available' => 'bg-green-100 text-green-800 border border-green-200',
                                        'occupied' => 'bg-gray-800 text-white border border-gray-900',
                                        'quick' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                        'maintenance' => 'bg-gray-200 text-gray-800 border border-gray-300',
                                        'reserved' => 'bg-blue-100 text-blue-800 border border-blue-200'
                                    ];
                                @endphp
                                
                                <div class="status-badge {{ $badgeClasses[$table['status']] }}">
                                    <i class="fas fa-circle text-xs mr-1 opacity-70"></i>
                                    {{ $statusText[$table['status']] ?? $table['status'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 text-xl mb-4">
                        <i class="fas fa-table text-4xl mb-4"></i>
                        <p>Không có dữ liệu bàn</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Legend -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-key mr-2"></i> Chú thích
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-4 h-4 rounded-full bg-green-500 mr-3 animate-pulse"></div>
                <div>
                    <div class="font-medium text-gray-700">Bàn trống</div>
                    <div class="text-sm text-gray-500">Sẵn sàng phục vụ</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-4 h-4 rounded-full bg-red-500 mr-3 animate-pulse"></div>
                <div>
                    <div class="font-medium text-gray-700">Bàn đang sử dụng</div>
                    <div class="text-sm text-gray-500">Đen chữ trắng</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-4 h-4 rounded-full bg-yellow-500 mr-3 animate-pulse"></div>
                <div>
                    <div class="font-medium text-gray-700">Quick bill</div>
                    <div class="text-sm text-gray-500">Thanh toán nhanh</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="px-3 py-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs rounded-full mr-3">
                    COMBO
                </div>
                <div>
                    <div class="font-medium text-gray-700">Bàn có combo</div>
                    <div class="text-sm text-gray-500">Đang dùng gói combo</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function refreshDashboard() {
        // Hiệu ứng loading
        const refreshBtn = document.querySelector('.refresh-btn');
        const originalHtml = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
        refreshBtn.disabled = true;
        
        // Reload trang sau 1 giây
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
    
    function goToTableDetail(tableId) {
        // Hiệu ứng click
        const card = document.querySelector(`[data-table-id="${tableId}"]`);
        if (card) {
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.style.transform = '';
            }, 150);
        }
        
        // Chuyển đến trang detail
        setTimeout(() => {
            window.location.href = `/admin/tables/${tableId}/detail`;
        }, 200);
    }
    
    // Auto-refresh every 60 seconds
    setTimeout(function() {
        refreshDashboard();
    }, 60000);
    
    // Animation on load
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.table-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px) scale(0.95)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0) scale(1)';
            }, index * 50);
        });
        
        // Hiệu ứng hover 3D
        cards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateY = (x - centerX) / 20;
                const rotateX = (centerY - y) / 20;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px) scale(1.02)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)';
            });
        });
        
        // Hiệu ứng đếm số cho stats cards
        const statsNumbers = document.querySelectorAll('.stats-card .text-3xl');
        statsNumbers.forEach(stat => {
            const target = parseInt(stat.textContent);
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                stat.textContent = Math.round(current);
            }, 20);
        });
        
        // Hiệu ứng real-time update cho timer
        setInterval(() => {
            const occupiedCards = document.querySelectorAll('.table-occupied .timer-display');
            occupiedCards.forEach(timer => {
                const timeParts = timer.textContent.split(':');
                let hours = parseInt(timeParts[0]);
                let minutes = parseInt(timeParts[1]);
                
                minutes++;
                if (minutes >= 60) {
                    minutes = 0;
                    hours++;
                }
                
                timer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            });
        }, 60000); // Cập nhật mỗi phút
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // R - Refresh
        if (e.key === 'r' || e.key === 'R') {
            refreshDashboard();
        }
        // ESC - Close modal (nếu có)
        if (e.key === 'Escape') {
            // Logic để đóng modal nếu cần
        }
    });
</script>
@endsection