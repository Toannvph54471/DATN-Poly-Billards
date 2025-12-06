@extends('admin.layouts.app')

@section('title', 'POS Dashboard - Billiard Tables')

@section('styles')
    <style>
        /* Full screen layout */
        html,
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            color: #fff;
            overflow: hidden;
        }

        /* Header */
        .dashboard-header {
            background: rgba(0, 0, 0, 0.9);
            padding: 12px 25px;
            border-bottom: 1px solid #2a2a2a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .header-left h1 {
            font-size: 22px;
            margin: 0;
            color: #fff;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-left .last-update {
            font-size: 11px;
            color: #aaa;
            margin-top: 4px;
        }

        /* Real time clock */
        .real-time-clock {
            font-size: 16px;
            font-family: 'Courier New', monospace;
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 16px;
            border-radius: 8px;
            border: 1px solid #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .real-time-clock i {
            color: #00ff88;
            font-size: 14px;
        }

        /* Controls */
        .controls {
            display: flex;
            gap: 8px;
        }

        .control-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid #333;
            color: #fff;
            padding: 7px 16px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            font-size: 13px;
            font-weight: 500;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        .control-btn.save-btn {
            background: linear-gradient(135deg, #00a86b 0%, #007a4d 100%);
            border-color: #00a86b;
        }

        .control-btn.cancel-btn {
            background: linear-gradient(135deg, #ff4757 0%, #ff3838 100%);
            border-color: #ff4757;
        }

        .control-btn.edit-btn {
            background: linear-gradient(135deg, #3742fa 0%, #5352ed 100%);
            border-color: #3742fa;
        }

        .control-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Main layout */
        .main-layout {
            display: flex;
            height: calc(100vh - 60px);
        }

        /* Left panel - Statistics */
        .left-panel {
            width: 320px;
            background: rgba(0, 0, 0, 0.85);
            border-right: 1px solid #2a2a2a;
            padding: 20px;
            overflow-y: auto;
            z-index: 50;
            backdrop-filter: blur(10px);
        }

        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #333;
        }

        .section-title i {
            color: #00ff88;
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #333;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .stat-card.highlight {
            background: rgba(0, 168, 107, 0.15);
            border-color: #00a86b;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 11px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Status Breakdown */
        .status-list {
            margin-bottom: 20px;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 6px;
            margin-bottom: 6px;
            border-left: 3px solid;
        }

        .status-item.available {
            border-color: #00a86b;
        }

        .status-item.occupied {
            border-color: #ff4757;
        }

        .status-item.reserved {
            border-color: #3742fa;
        }

        .status-item.maintenance {
            border-color: #aaa;
        }

        .status-count {
            font-size: 16px;
            font-weight: 600;
        }

        .status-text {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-dot.available {
            background: #00a86b;
        }

        .status-dot.occupied {
            background: #ff4757;
        }

        .status-dot.reserved {
            background: #3742fa;
        }

        .status-dot.maintenance {
            background: #aaa;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 20px;
        }

        .action-btn-small {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid #333;
            color: #fff;
            padding: 8px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
            font-size: 12px;
        }

        .action-btn-small:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .action-btn-small i {
            font-size: 14px;
            color: #00ff88;
        }

        /* Recent Activity */
        .activity-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 8px 0;
            border-bottom: 1px solid #333;
            font-size: 12px;
            color: #aaa;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        /* Main Content Area - Tables Layout */
        .content-area {
            flex: 1;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #0c0c0c 0%, #181818 100%);
        }

        /* Grid Background */
        .grid-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.3;
        }

        /* Pool Table Styles */
        .pool-table-container {
            position: absolute;
            width: 180px;
            height: 90px;
            cursor: move;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            z-index: 2;
        }

        .pool-table-container.dragging {
            opacity: 0.8;
            z-index: 1000;
            transform: scale(1.05);
            filter: drop-shadow(0 0 15px rgba(0, 168, 107, 0.5));
        }

        /* Pool table design */
        .pool-table {
            width: 100%;
            height: 100%;
            position: relative;
            border-radius: 4px;
            overflow: hidden;
            transform-origin: center;
        }

        /* Table surface - Billiard green in dark mode */
        .table-surface {
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            background: linear-gradient(135deg, #0d3b0d 0%, #1a5c1a 100%);
            border-radius: 2px;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Table cushions (rails) */
        .table-cushion {
            position: absolute;
            background: linear-gradient(135deg, #222 0%, #333 100%);
            border: 1px solid #444;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .cushion-top {
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            border-radius: 4px 4px 0 0;
        }

        .cushion-bottom {
            bottom: 0;
            left: 0;
            right: 0;
            height: 5px;
            border-radius: 0 0 4px 4px;
        }

        .cushion-left {
            top: 0;
            left: 0;
            bottom: 0;
            width: 5px;
            border-radius: 4px 0 0 4px;
        }

        .cushion-right {
            top: 0;
            right: 0;
            bottom: 0;
            width: 5px;
            border-radius: 0 4px 4px 0;
        }

        /* Pool table pockets (6 pockets) */
        .pocket {
            position: absolute;
            width: 12px;
            height: 12px;
            background: radial-gradient(circle at center, #000 30%, #222 100%);
            border-radius: 50%;
            border: 2px solid #444;
            box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.8);
            z-index: 3;
        }

        /* Corner pockets */
        .pocket-tl {
            top: -3px;
            left: -3px;
        }

        .pocket-tr {
            top: -3px;
            right: -3px;
        }

        .pocket-bl {
            bottom: -3px;
            left: -3px;
        }

        .pocket-br {
            bottom: -3px;
            right: -3px;
        }

        /* Middle pockets */
        .pocket-mt {
            top: -3px;
            left: 50%;
            transform: translateX(-50%);
        }

        .pocket-mb {
            bottom: -3px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Table info overlay */
        .table-info {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 4;
            pointer-events: none;
            padding: 5px;
        }

        .table-number {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
            margin-bottom: 2px;
        }

        .table-name {
            font-size: 11px;
            color: #fff;
            background: rgba(0, 0, 0, 0.6);
            padding: 2px 6px;
            border-radius: 3px;
            margin-bottom: 4px;
            max-width: 90%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table-timer {
            font-size: 10px;
            font-family: 'Courier New', monospace;
            color: #ff6b6b;
            background: rgba(0, 0, 0, 0.7);
            padding: 2px 6px;
            border-radius: 3px;
            margin-top: 3px;
            font-weight: 600;
        }

        .table-status {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 8px;
            margin-top: 4px;
            background: rgba(0, 0, 0, 0.7);
        }

        /* Status colors */
        .status-available {
            color: #00ff88;
            border: 1px solid #00ff88;
        }

        .status-occupied {
            color: #ff4757;
            border: 1px solid #ff4757;
        }

        .status-reserved {
            color: #3742fa;
            border: 1px solid #3742fa;
        }

        .status-maintenance {
            color: #aaa;
            border: 1px solid #aaa;
        }

        /* Combo badge */
        .combo-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: linear-gradient(135deg, #ff9f43 0%, #ff7f00 100%);
            color: #000;
            font-size: 8px;
            font-weight: 800;
            padding: 2px 5px;
            border-radius: 3px;
            z-index: 5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Right Panel - Active Bills */
        .right-panel {
            width: 350px;
            background: rgba(0, 0, 0, 0.85);
            border-left: 1px solid #2a2a2a;
            padding: 20px;
            overflow-y: auto;
            z-index: 50;
            backdrop-filter: blur(10px);
        }

        /* Bill List */
        .bill-list {
            margin-top: 15px;
        }

        .bill-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #333;
            transition: all 0.2s ease;
        }

        .bill-item:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: #00a86b;
        }

        .bill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .bill-table {
            font-size: 14px;
            font-weight: 600;
            color: #00ff88;
        }

        .bill-amount {
            font-size: 16px;
            font-weight: 700;
            color: #fff;
        }

        .bill-time {
            font-size: 11px;
            color: #aaa;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .bill-customer {
            font-size: 12px;
            color: #ccc;
            margin-top: 5px;
        }

        .view-bill-btn {
            background: rgba(0, 168, 107, 0.2);
            border: 1px solid #00a86b;
            color: #00ff88;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
            transition: all 0.2s ease;
        }

        .view-bill-btn:hover {
            background: rgba(0, 168, 107, 0.4);
        }

        /* Edit Mode Indicator */
        .edit-mode-indicator {
            position: fixed;
            top: 70px;
            right: 370px;
            background: linear-gradient(135deg, #3742fa 0%, #5352ed 100%);
            color: #fff;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 6px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(55, 66, 250, 0.3);
        }

        .edit-mode-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.95);
            border: 1px solid #333;
            border-radius: 8px;
            padding: 12px 18px;
            color: #fff;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            border-left: 4px solid #00a86b;
        }

        .notification.error {
            border-left: 4px solid #ff4757;
        }

        .notification.info {
            border-left: 4px solid #3742fa;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: #00ff88;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 30px 20px;
            color: #666;
            font-size: 14px;
        }

        .empty-state i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #444;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .left-panel {
                width: 280px;
            }

            .right-panel {
                width: 300px;
            }

            .pool-table-container {
                width: 160px;
                height: 80px;
            }
        }

        @media (max-width: 992px) {

            .left-panel,
            .right-panel {
                display: none;
            }
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1>
                    <i class="fas fa-billiard"></i>
                    POS Dashboard - Billiard Tables
                </h1>
                <div class="last-update" id="lastUpdateTime">
                    Cập nhật lần cuối: <span id="currentTime"></span>
                </div>
            </div>

            <div class="real-time-clock">
                <i class="fas fa-clock"></i>
                <span id="liveClock">--:--:--</span>
            </div>

            <div class="controls">
                <button id="editModeBtn" class="control-btn edit-btn">
                    <i class="fas fa-edit"></i> Sắp xếp bố cục
                </button>
                <button id="saveLayoutBtn" class="control-btn save-btn" style="display: none;">
                    <i class="fas fa-save"></i> Lưu bố cục
                </button>
                <button id="cancelEditBtn" class="control-btn cancel-btn" style="display: none;">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <button onclick="refreshDashboard()" class="control-btn">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- Edit Mode Indicator -->
        <div class="edit-mode-indicator" id="editModeIndicator">
            <i class="fas fa-mouse-pointer"></i>
            Chế độ sắp xếp - Kéo thả để di chuyển bàn
        </div>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Left Panel - Statistics -->
            <div class="left-panel">
                <div class="section-title">
                    <i class="fas fa-chart-bar"></i>
                    Thống kê nhanh
                </div>

                <div class="stats-grid">
                    <div class="stat-card highlight">
                        <div class="stat-value">{{ $stats['open_bills'] }}</div>
                        <div class="stat-label">HÓA ĐƠN ĐANG MỞ</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ number_format($stats['today_revenue']) }}đ</div>
                        <div class="stat-label">DOANH THU HÔM NAY</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ $stats['occupied_tables'] }}</div>
                        <div class="stat-label">BÀN ĐANG DÙNG</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ $stats['available_tables'] }}</div>
                        <div class="stat-label">BÀN TRỐNG</div>
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-billiard"></i>
                    Trạng thái bàn
                </div>

                <div class="status-list">
                    @php
                        $allTables = $availableTables->count() + $occupiedTables->count();
                        $availableCount = $stats['available_tables'];
                        $occupiedCount = $stats['occupied_tables'];
                        $reservedCount = $stats['pending_reservations'];
                        $maintenanceCount = $allTables - ($availableCount + $occupiedCount + $reservedCount);
                        $maintenanceCount = max(0, $maintenanceCount);
                    @endphp

                    <div class="status-item available">
                        <div class="status-text">
                            <span class="status-dot available"></span>
                            Bàn trống
                        </div>
                        <div class="status-count">{{ $availableCount }}</div>
                    </div>
                    <div class="status-item occupied">
                        <div class="status-text">
                            <span class="status-dot occupied"></span>
                            Đang sử dụng
                        </div>
                        <div class="status-count">{{ $occupiedCount }}</div>
                    </div>
                    <div class="status-item reserved">
                        <div class="status-text">
                            <span class="status-dot reserved"></span>
                            Đặt trước
                        </div>
                        <div class="status-count">{{ $reservedCount }}</div>
                    </div>
                    <div class="status-item maintenance">
                        <div class="status-text">
                            <span class="status-dot maintenance"></span>
                            Bảo trì
                        </div>
                        <div class="status-count">{{ $maintenanceCount }}</div>
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-bolt"></i>
                    Hành động nhanh
                </div>

                <div class="quick-actions">
                    <button class="action-btn-small" onclick="quickNewBill()">
                        <i class="fas fa-receipt"></i>
                        Hóa đơn mới
                    </button>
                    <button class="action-btn-small" onclick="quickReservation()">
                        <i class="fas fa-calendar-plus"></i>
                        Đặt bàn
                    </button>
                    <button class="action-btn-small" onclick="quickCheckout()">
                        <i class="fas fa-cash-register"></i>
                        Thanh toán
                    </button>
                    <button class="action-btn-small" onclick="quickReport()">
                        <i class="fas fa-file-invoice"></i>
                        Báo cáo
                    </button>
                </div>

                <div class="section-title">
                    <i class="fas fa-history"></i>
                    Hoạt động gần đây
                </div>

                <div class="activity-list">
                    @if ($openBills->count() > 0)
                        @foreach ($openBills->take(5) as $bill)
                            <div class="activity-item">
                                <strong>Bàn {{ $bill->table->table_number ?? 'N/A' }}</strong>
                                mở hóa đơn lúc {{ $bill->created_at->format('H:i') }}
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-info-circle"></i>
                            <p>Chưa có hoạt động nào</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="content-area" id="contentArea">
                <div class="grid-background"></div>

                <!-- Pool Tables -->
                @foreach ($occupiedTables as $table)
                    @php
                        // Tính vị trí dựa trên ID để layout đẹp
                        $posX = ($table->id % 4) * 200 + 50;
                        $posY = intval(($table->id - 1) / 4) * 110 + 50;

                        // Tính thời gian đã sử dụng
                        $elapsedTime = '';
                        if ($table->currentBill && $table->currentBill->start_time) {
                            $startTime = $table->currentBill->start_time;
                            $elapsedSeconds = now()->diffInSeconds($startTime);
                            $hours = floor($elapsedSeconds / 3600);
                            $minutes = floor(($elapsedSeconds % 3600) / 60);
                            $seconds = $elapsedSeconds % 60;
                            $elapsedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                        }

                        // Kiểm tra có combo không
                        $hasCombo =
                            $table->currentBill &&
                            $table->currentBill->comboTimeUsages &&
                            $table->currentBill->comboTimeUsages->where('is_expired', false)->count() > 0;
                    @endphp

                    <div class="pool-table-container" data-table-id="{{ $table->id }}"
                        data-table-number="{{ $table->table_number }}"
                        style="left: {{ $posX }}px; top: {{ $posY }}px;">

                        <div class="pool-table">
                            <!-- Cushions -->
                            <div class="table-cushion cushion-top"></div>
                            <div class="table-cushion cushion-bottom"></div>
                            <div class="table-cushion cushion-left"></div>
                            <div class="table-cushion cushion-right"></div>

                            <!-- Table surface -->
                            <div class="table-surface"></div>

                            <!-- 6 pockets -->
                            <div class="pocket pocket-tl"></div>
                            <div class="pocket pocket-tr"></div>
                            <div class="pocket pocket-bl"></div>
                            <div class="pocket pocket-br"></div>
                            <div class="pocket pocket-mt"></div>
                            <div class="pocket pocket-mb"></div>

                            <!-- Table info -->
                            <div class="table-info">
                                <div class="table-number">{{ $table->table_number }}</div>
                                <div class="table-name">{{ $table->name ?? 'Bàn ' . $table->table_number }}</div>

                                @if ($elapsedTime)
                                    <div class="table-timer">{{ $elapsedTime }}</div>
                                @endif

                                <div class="table-status status-occupied">
                                    ĐANG DÙNG
                                </div>
                            </div>

                            @if ($hasCombo)
                                <div class="combo-badge">COMBO</div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @foreach ($availableTables as $table)
                    @php
                        // Tính vị trí cho bàn trống
                        $posX = ($table->id % 4) * 200 + 50;
                        $posY = intval(($table->id - 1) / 4) * 110 + 50;
                        $posY += 250; // Đẩy xuống dưới các bàn đang dùng
                    @endphp

                    <div class="pool-table-container" data-table-id="{{ $table->id }}"
                        data-table-number="{{ $table->table_number }}"
                        style="left: {{ $posX }}px; top: {{ $posY }}px;">

                        <div class="pool-table">
                            <div class="table-cushion cushion-top"></div>
                            <div class="table-cushion cushion-bottom"></div>
                            <div class="table-cushion cushion-left"></div>
                            <div class="table-cushion cushion-right"></div>

                            <div class="table-surface"></div>

                            <!-- 6 pockets -->
                            <div class="pocket pocket-tl"></div>
                            <div class="pocket pocket-tr"></div>
                            <div class="pocket pocket-bl"></div>
                            <div class="pocket pocket-br"></div>
                            <div class="pocket pocket-mt"></div>
                            <div class="pocket pocket-mb"></div>

                            <div class="table-info">
                                <div class="table-number">{{ $table->table_number }}</div>
                                <div class="table-name">{{ $table->name ?? 'Bàn ' . $table->table_number }}</div>
                                <div class="table-status status-available">
                                    TRỐNG
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Right Panel - Active Bills -->
            <div class="right-panel">
                <div class="section-title">
                    <i class="fas fa-receipt"></i>
                    Hóa đơn đang mở
                </div>

                <div class="bill-list">
                    @if ($openBills->count() > 0)
                        @foreach ($openBills as $bill)
                            <div class="bill-item">
                                <div class="bill-header">
                                    <div class="bill-table">
                                        <i class="fas fa-billiard"></i>
                                        Bàn {{ $bill->table->table_number ?? 'N/A' }}
                                    </div>
                                    <div class="bill-amount">
                                        {{ number_format($bill->total_amount ?? 0) }}đ
                                    </div>
                                </div>
                                <div class="bill-time">
                                    <i class="far fa-clock"></i>
                                    Mở: {{ $bill->created_at->format('H:i') }}
                                </div>
                                @if ($bill->customer_name)
                                    <div class="bill-customer">
                                        <i class="fas fa-user"></i>
                                        {{ $bill->customer_name }}
                                    </div>
                                @endif
                                <button class="view-bill-btn" onclick="viewBill({{ $bill->id }})">
                                    <i class="fas fa-eye"></i>
                                    Xem chi tiết
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-receipt"></i>
                            <p>Không có hóa đơn nào đang mở</p>
                        </div>
                    @endif
                </div>

                <div class="section-title" style="margin-top: 25px;">
                    <i class="fas fa-calendar-check"></i>
                    Đặt bàn hôm nay
                </div>

                <div class="bill-list">
                    @if (isset($todayReservations) && $todayReservations->count() > 0)
                        @foreach ($todayReservations as $reservation)
                            <div class="bill-item">
                                <div class="bill-header">
                                    <div class="bill-table">
                                        <i class="fas fa-billiard"></i>
                                        Bàn {{ $reservation->table->table_number ?? 'N/A' }}
                                    </div>
                                    <div class="bill-amount">
                                        {{ $reservation->reservation_time->format('H:i') }}
                                    </div>
                                </div>
                                <div class="bill-time">
                                    <i class="fas fa-user"></i>
                                    {{ $reservation->customer_name ?? 'Khách vãng lai' }}
                                </div>
                                @if ($reservation->note)
                                    <div class="bill-customer">
                                        <i class="fas fa-sticky-note"></i>
                                        {{ Str::limit($reservation->note, 30) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>Không có đặt bàn nào hôm nay</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notification -->
        <div class="notification" id="notification">
            <i class="fas fa-check-circle"></i>
            <span id="notificationMessage"></span>
        </div>

        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Global variables
        let isEditMode = false;
        let originalPositions = new Map();
        let draggedTable = null;
        let dragOffset = {
            x: 0,
            y: 0
        };

        // DOM elements
        const editModeBtn = document.getElementById('editModeBtn');
        const saveLayoutBtn = document.getElementById('saveLayoutBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const contentArea = document.getElementById('contentArea');
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notificationMessage');
        const editModeIndicator = document.getElementById('editModeIndicator');
        const loadingOverlay = document.getElementById('loadingOverlay');

        // Get pool tables
        let poolTables = document.querySelectorAll('.pool-table-container');

        // Function to update real-time clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN');
            const dateString = now.toLocaleDateString('vi-VN', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            document.getElementById('liveClock').textContent = timeString;
            document.getElementById('lastUpdateTime').innerHTML =
                `Cập nhật lần cuối: <span id="currentTime">${timeString}</span>`;
        }

        // Initialize clock
        updateClock();
        setInterval(updateClock, 1000);

        // Initialize drag and drop
        function initDragAndDrop() {
            poolTables = document.querySelectorAll('.pool-table-container');

            poolTables.forEach(table => {
                table.addEventListener('mousedown', startDrag);
                table.addEventListener('touchstart', startDragTouch, {
                    passive: false
                });

                // Prevent text selection while dragging
                table.addEventListener('selectstart', (e) => {
                    if (isEditMode) e.preventDefault();
                });

                // Click to view table details when not in edit mode
                table.addEventListener('click', (e) => {
                    if (!isEditMode && !draggedTable) {
                        const tableId = table.dataset.tableId;
                        const tableNumber = table.dataset.tableNumber;
                        viewTableDetails(tableId, tableNumber);
                    }
                });
            });

            // Add global event listeners for drag
            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);
            document.addEventListener('touchmove', dragTouch, {
                passive: false
            });
            document.addEventListener('touchend', stopDragTouch);
        }

        // Mouse drag functions
        function startDrag(e) {
            if (!isEditMode) return;

            e.preventDefault();
            e.stopPropagation();

            draggedTable = this;
            const rect = this.getBoundingClientRect();

            dragOffset.x = e.clientX - rect.left;
            dragOffset.y = e.clientY - rect.top;

            this.classList.add('dragging');
        }

        function drag(e) {
            if (!draggedTable || !isEditMode) return;

            const areaRect = contentArea.getBoundingClientRect();
            let x = e.clientX - areaRect.left - dragOffset.x;
            let y = e.clientY - areaRect.top - dragOffset.y;

            // Boundary checking
            x = Math.max(20, Math.min(x, areaRect.width - draggedTable.offsetWidth - 20));
            y = Math.max(20, Math.min(y, areaRect.height - draggedTable.offsetHeight - 20));

            draggedTable.style.left = `${x}px`;
            draggedTable.style.top = `${y}px`;
        }

        function stopDrag() {
            if (draggedTable) {
                draggedTable.classList.remove('dragging');
                draggedTable = null;
            }
        }

        // Touch drag functions
        function startDragTouch(e) {
            if (!isEditMode) return;

            e.preventDefault();
            if (e.touches.length !== 1) return;

            draggedTable = this;
            const touch = e.touches[0];
            const rect = this.getBoundingClientRect();

            dragOffset.x = touch.clientX - rect.left;
            dragOffset.y = touch.clientY - rect.top;

            this.classList.add('dragging');
        }

        function dragTouch(e) {
            if (!draggedTable || !isEditMode || e.touches.length !== 1) return;

            e.preventDefault();
            const touch = e.touches[0];
            const areaRect = contentArea.getBoundingClientRect();
            let x = touch.clientX - areaRect.left - dragOffset.x;
            let y = touch.clientY - areaRect.top - dragOffset.y;

            // Boundary checking
            x = Math.max(20, Math.min(x, areaRect.width - draggedTable.offsetWidth - 20));
            y = Math.max(20, Math.min(y, areaRect.height - draggedTable.offsetHeight - 20));

            draggedTable.style.left = `${x}px`;
            draggedTable.style.top = `${y}px`;
        }

        function stopDragTouch() {
            if (draggedTable) {
                draggedTable.classList.remove('dragging');
                draggedTable = null;
            }
        }

        // Edit mode functions
        function enterEditMode() {
            isEditMode = true;

            // Show edit mode indicator
            editModeIndicator.classList.add('show');

            // Show save/cancel buttons
            saveLayoutBtn.style.display = 'flex';
            cancelEditBtn.style.display = 'flex';
            editModeBtn.style.display = 'none';

            // Store original positions
            originalPositions.clear();
            poolTables.forEach(table => {
                const style = window.getComputedStyle(table);
                originalPositions.set(table.dataset.tableId, {
                    x: style.left,
                    y: style.top
                });
            });

            showNotification('Đang ở chế độ sắp xếp. Kéo thả các bàn để di chuyển vị trí.', 'info');
        }

        function exitEditMode() {
            isEditMode = false;

            // Hide edit mode indicator
            editModeIndicator.classList.remove('show');

            // Hide save/cancel buttons
            saveLayoutBtn.style.display = 'none';
            cancelEditBtn.style.display = 'none';
            editModeBtn.style.display = 'flex';

            // Reset positions if canceling
            poolTables.forEach(table => {
                const originalPos = originalPositions.get(table.dataset.tableId);
                if (originalPos) {
                    table.style.left = originalPos.x;
                    table.style.top = originalPos.y;
                }
            });

            showNotification('Đã thoát chế độ sắp xếp.', 'info');
        }

        // Save layout
        async function saveLayout() {
            const positions = {};

            poolTables.forEach(table => {
                const style = window.getComputedStyle(table);
                positions[table.dataset.tableId] = {
                    x: style.left,
                    y: style.top
                };
            });

            // Show loading
            showLoading(true);

            try {
                // Save to localStorage
                localStorage.setItem('billiardTableLayout', JSON.stringify(positions));

                // You can also save to server here
                // await saveToServer(positions);

                showNotification('Đã lưu bố cục thành công!', 'success');
                exitEditMode();

            } catch (error) {
                console.error('Save error:', error);
                showNotification('Có lỗi xảy ra khi lưu bố cục', 'error');
            } finally {
                showLoading(false);
            }
        }

        // Load saved layout
        function loadSavedLayout() {
            try {
                const saved = localStorage.getItem('billiardTableLayout');
                if (saved) {
                    const positions = JSON.parse(saved);
                    poolTables.forEach(table => {
                        const pos = positions[table.dataset.tableId];
                        if (pos) {
                            table.style.left = pos.x;
                            table.style.top = pos.y;
                        }
                    });
                }
            } catch (e) {
                console.error('Error loading layout:', e);
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            notificationMessage.textContent = message;

            // Remove all type classes
            notification.classList.remove('success', 'error', 'info');
            notification.classList.add(type);

            // Show notification
            notification.classList.add('show');

            // Auto hide after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Show/hide loading
        function showLoading(show) {
            if (show) {
                loadingOverlay.classList.add('show');
            } else {
                loadingOverlay.classList.remove('show');
            }
        }

        // Table actions
        function viewTableDetails(tableId, tableNumber) {
            // Redirect to table detail page
            window.location.href = `/admin/tables/${tableId}`;
        }

        function viewBill(billId) {
            // Redirect to bill detail page
            window.location.href = `/admin/bills/${billId}`;
        }

        // Quick actions
        function quickNewBill() {
            window.location.href = '/admin/bills/create';
        }

        function quickReservation() {
            window.location.href = '/admin/reservations/create';
        }

        function quickCheckout() {
            window.location.href = '/admin/bills';
        }

        function quickReport() {
            window.location.href = '/admin/reports';
        }

        // Refresh dashboard
        function refreshDashboard() {
            showLoading(true);
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize
            initDragAndDrop();
            loadSavedLayout();

            // Edit mode button
            editModeBtn.addEventListener('click', enterEditMode);

            // Save layout button
            saveLayoutBtn.addEventListener('click', saveLayout);

            // Cancel edit button
            cancelEditBtn.addEventListener('click', exitEditMode);

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && isEditMode) {
                    exitEditMode();
                }
                if ((e.ctrlKey || e.metaKey) && e.key === 's' && isEditMode) {
                    e.preventDefault();
                    saveLayout();
                }
                if (e.key === 'r' && (e.ctrlKey || e.metaKey)) {
                    e.preventDefault();
                    refreshDashboard();
                }
            });

            // Auto-refresh every 2 minutes
            setInterval(refreshDashboard, 120000);
        });
    </script>
@endsection
