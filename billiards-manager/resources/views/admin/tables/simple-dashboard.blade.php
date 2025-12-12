<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Billiard POS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== RESET & BASE STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            color: #fff;
            overflow: hidden;
            height: 100vh;
            -webkit-tap-highlight-color: transparent;
        }

        /* ===== DASHBOARD CONTAINER ===== */
        .dashboard-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background: #0a0a0a;
            overflow: hidden;
            position: relative;
        }

        /* ===== HEADER ===== */
        .dashboard-header {
            background: rgba(15, 15, 15, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            z-index: 100;
            position: relative;
            min-height: 70px;
        }

        /* Nút quay lại */
        .header-back {
            margin-right: 15px;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateX(-2px);
        }

        .back-btn i {
            font-size: 12px;
        }

        .header-left {
            flex: 1;
        }

        .header-left h1 {
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.3px;
        }

        .header-left h1 i {
            color: #fff;
            font-size: 20px;
        }

        .last-update {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 3px;
            font-weight: 400;
        }

        /* ===== REAL-TIME CLOCK ===== */
        .real-time-clock {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 6px 12px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            min-width: 110px;
            margin: 0 15px;
        }

        .real-time-clock:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .real-time-clock i {
            color: #fff;
            font-size: 12px;
        }

        /* ===== CONTROLS ===== */
        .controls {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }

        .control-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        .control-btn:active {
            transform: translateY(0);
        }

        .control-btn.edit-btn {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .control-btn.save-btn {
            background: rgba(0, 168, 107, 0.2);
            border-color: rgba(0, 168, 107, 0.3);
        }

        .control-btn.cancel-btn {
            background: rgba(255, 71, 87, 0.2);
            border-color: rgba(255, 71, 87, 0.3);
        }

        .control-btn i {
            font-size: 12px;
        }

        /* ===== MAIN LAYOUT ===== */
        .main-layout {
            display: flex;
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        /* ===== LEFT PANEL - STATISTICS ===== */
        .left-panel {
            width: 280px;
            background: rgba(15, 15, 15, 0.98);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px;
            overflow-y: auto;
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            gap: 20px;
            transition: transform 0.3s ease;
            z-index: 50;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-title i {
            color: #fff;
            font-size: 12px;
        }

        /* ===== STATISTICS CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 15px 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .stat-card.highlight {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.15);
            animation: pulse 2s infinite;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }

        .stat-label {
            font-size: 9px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        /* ===== STATUS LIST ===== */
        .status-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .status-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 6px;
            padding: 10px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .status-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .status-text {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-dot.available {
            background: #00ff88;
            box-shadow: 0 0 8px rgba(0, 255, 136, 0.5);
        }

        .status-dot.occupied {
            background: #ff4757;
            box-shadow: 0 0 8px rgba(255, 71, 87, 0.5);
        }

        .status-dot.quick {
            background: #ff9f43;
            box-shadow: 0 0 8px rgba(255, 159, 67, 0.5);
        }

        .status-dot.maintenance {
            background: #aaa;
            box-shadow: 0 0 8px rgba(170, 170, 170, 0.5);
        }

        .status-count {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        /* ===== QUICK ACTIONS ===== */
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 12px;
            color: #fff;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            text-align: center;
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .action-btn i {
            font-size: 16px;
            color: #fff;
        }

        /* ===== ACTIVITY LIST ===== */
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 150px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.4;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item strong {
            color: #fff;
            font-weight: 600;
        }

        /* ===== MAIN CONTENT AREA ===== */
        .content-area {
            flex: 1;
            position: relative;
            overflow: hidden;
            background: #0a0a0a;
            touch-action: none;
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
            background-size: 60px 60px;
            opacity: 0.15;
            pointer-events: none;
            animation: gridMove 30s linear infinite;
        }

        @keyframes gridMove {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 60px 60px;
            }
        }

        /* ===== TABLES CONTAINER ===== */
        .tables-container {
            position: relative;
            height: 100%;
            padding: 20px;
            overflow: auto;
            min-height: 500px;
            width: 100%;
            height: 100%;
        }

        /* ===== TABLES GRID SYSTEM ===== */
        .tables-grid {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 500px;
        }

        /* ===== POOL TABLE STYLES ===== */
        .pool-table-container {
            position: absolute;
            width: 200px;
            height: 120px;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
            z-index: 10;
            touch-action: none;
        }

        .pool-table-container.edit-mode {
            cursor: move;
        }

        .pool-table-container.dragging {
            z-index: 1000;
            filter: drop-shadow(0 15px 40px rgba(255, 255, 255, 0.2));
            transform: scale(1.08);
            transition: transform 0.1s ease;
            pointer-events: none;
        }

        /* Combo warning effects */
        .pool-table-container.unprocessed {
            animation: emergencyFlash 0.8s ease-in-out infinite;
        }

        @keyframes emergencyFlash {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.7),
                    inset 0 0 0 0 rgba(255, 107, 107, 0.3);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(255, 107, 107, 0.7),
                    inset 0 0 20px 5px rgba(255, 107, 107, 0.3);
            }
        }

        .pool-table {
            width: 100%;
            height: 100%;
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            background: #000;
            border: 2px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
            transition: all 0.2s ease;
        }

        .pool-table-container:hover .pool-table {
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.8);
        }

        .pool-table-container.dragging .pool-table {
            border-color: rgba(255, 255, 255, 0.6);
            box-shadow: 0 20px 50px rgba(255, 255, 255, 0.3);
        }

        /* Table surface */
        .table-surface {
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border-radius: 6px;
            box-shadow: inset 0 0 25px rgba(0, 0, 0, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* Table cushions */
        .table-cushion {
            position: absolute;
            background: linear-gradient(135deg, #3a3a3a 0%, #4a4a4a 100%);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: inset 0 0 12px rgba(0, 0, 0, 0.9);
        }

        .cushion-top {
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            border-radius: 10px 10px 0 0;
        }

        .cushion-bottom {
            bottom: 0;
            left: 0;
            right: 0;
            height: 5px;
            border-radius: 0 0 10px 10px;
        }

        .cushion-left {
            top: 0;
            left: 0;
            bottom: 0;
            width: 5px;
            border-radius: 10px 0 0 10px;
        }

        .cushion-right {
            top: 0;
            right: 0;
            bottom: 0;
            width: 5px;
            border-radius: 0 10px 10px 0;
        }

        /* Pool table pockets - LARGER */
        .pocket {
            position: absolute;
            width: 16px;
            height: 16px;
            background: radial-gradient(circle at center, #000 40%, #333 100%);
            border-radius: 50%;
            border: 2px solid #555;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 1);
            z-index: 2;
        }

        .pocket-tl {
            top: -5px;
            left: -5px;
        }

        .pocket-tr {
            top: -5px;
            right: -5px;
        }

        .pocket-bl {
            bottom: -5px;
            left: -5px;
        }

        .pocket-br {
            bottom: -5px;
            right: -5px;
        }

        .pocket-mt {
            top: -5px;
            left: 50%;
            transform: translateX(-50%);
        }

        .pocket-mb {
            bottom: -5px;
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
            z-index: 3;
            pointer-events: none;
            padding: 8px;
        }

        .table-number {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.9);
            margin-bottom: 3px;
            letter-spacing: -0.5px;
        }

        .table-name {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.95);
            background: rgba(0, 0, 0, 0.8);
            padding: 4px 10px;
            border-radius: 4px;
            margin-bottom: 5px;
            max-width: 95%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table-timer {
            font-size: 12px;
            font-family: 'Courier New', monospace;
            color: #fff;
            background: rgba(0, 0, 0, 0.85);
            padding: 3px 10px;
            border-radius: 4px;
            margin-top: 4px;
            font-weight: 600;
            letter-spacing: 1px;
            min-width: 120px;
            text-align: center;
        }

        .table-timer.unprocessed {
            background: rgba(255, 107, 107, 0.9);
            color: white;
            animation: blink 0.8s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .table-status {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 12px;
            border-radius: 20px;
            margin-top: 6px;
            background: rgba(0, 0, 0, 0.9);
            border: 2px solid;
        }

        .status-available {
            color: #00ff88;
            border-color: #00ff88;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.4);
        }

        .status-occupied {
            color: #ff4757;
            border-color: #ff4757;
            box-shadow: 0 0 15px rgba(255, 71, 87, 0.4);
        }

        /* Thêm vào file CSS của bạn */
        .status-paused {
            background-color: #ffc107 !important;
            /* Màu vàng */
            color: #333 !important;
            border-color: #ff9800 !important;
        }

        .status-quick {
            color: #ff9f43;
            border-color: #ff9f43;
            box-shadow: 0 0 15px rgba(255, 159, 67, 0.4);
        }

        .status-maintenance {
            color: #aaa;
            border-color: #aaa;
            box-shadow: 0 0 15px rgba(170, 170, 170, 0.4);
        }

        /* Combo badge */
        .combo-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 159, 67, 0.95);
            color: #000;
            font-size: 10px;
            font-weight: 900;
            padding: 4px 8px;
            border-radius: 5px;
            z-index: 4;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(255, 159, 67, 0.5);
        }

        .combo-badge.unprocessed {
            background: rgba(255, 107, 107, 0.95);
            animation: badgeFlash 0.8s infinite;
        }

        @keyframes badgeFlash {

            0%,
            100% {
                transform: scale(1);
                background: rgba(255, 107, 107, 0.95);
                box-shadow: 0 5px 15px rgba(255, 107, 107, 0.5);
            }

            50% {
                transform: scale(1.1);
                background: rgba(255, 255, 255, 0.95);
                color: #ff4757;
                box-shadow: 0 8px 20px rgba(255, 107, 107, 0.7);
            }
        }

        /* ===== RIGHT PANEL - ACTIVE BILLS ===== */
        .right-panel {
            width: 300px;
            background: rgba(15, 15, 15, 0.98);
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px;
            overflow-y: auto;
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            gap: 20px;
            transition: transform 0.3s ease;
            z-index: 50;
        }

        /* Bill List */
        .bill-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bill-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .bill-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 3px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
        }

        .bill-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateX(3px);
        }

        .bill-item:hover::before {
            background: rgba(255, 255, 255, 0.3);
        }

        .bill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .bill-table {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .bill-table i {
            color: #fff;
            font-size: 13px;
        }

        .bill-amount {
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.3px;
        }

        .bill-time {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 6px;
        }

        .bill-time i {
            font-size: 10px;
        }

        .bill-customer {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
        }

        .bill-customer i {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
        }

        .bill-actions {
            display: flex;
            gap: 6px;
            margin-top: 10px;
        }

        .bill-btn {
            flex: 1;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .bill-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        .bill-btn.detail {
            background: rgba(0, 168, 107, 0.15);
            border-color: rgba(0, 168, 107, 0.3);
        }

        .bill-btn.checkout {
            background: rgba(255, 71, 87, 0.15);
            border-color: rgba(255, 71, 87, 0.3);
        }

        /* ===== STATUS SUMMARY ===== */
        .status-summary {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 15px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .summary-value {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        /* ===== EDIT MODE INDICATOR ===== */
        .edit-mode-indicator {
            position: fixed;
            top: 75px;
            right: 320px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 6px;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .edit-mode-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== NOTIFICATION ===== */
        .notification {
            position: fixed;
            top: 15px;
            right: 15px;
            background: rgba(15, 15, 15, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 12px 18px;
            color: #fff;
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(400px);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
            max-width: 300px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification i {
            font-size: 16px;
            flex-shrink: 0;
        }

        .notification.success i {
            color: #00ff88;
        }

        .notification.error i {
            color: #ff4757;
        }

        .notification.info i {
            color: #fff;
        }

        #notificationMessage {
            font-size: 13px;
            line-height: 1.4;
        }

        /* ===== LOADING OVERLAY ===== */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 10, 10, 0.98);
            backdrop-filter: blur(20px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 20000;
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
            border-top-color: #fff;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        .loading-text {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 30px 15px;
            color: rgba(255, 255, 255, 0.5);
        }

        .empty-state i {
            font-size: 36px;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.3);
        }

        .empty-state p {
            font-size: 13px;
            font-weight: 500;
        }

        /* ===== MOBILE MENU TOGGLE ===== */
        .mobile-menu-toggle {
            display: none;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            padding: 8px;
            color: #fff;
            cursor: pointer;
            z-index: 101;
        }

        .panel-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            z-index: 49;
        }

        /* ===== SCROLLBAR STYLING ===== */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 2px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 2px;
            transition: all 0.2s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {

            .left-panel,
            .right-panel {
                width: 260px;
            }

            .pool-table-container {
                width: 180px;
                height: 110px;
            }

            .edit-mode-indicator {
                right: 280px;
            }
        }

        @media (max-width: 992px) {
            .dashboard-header {
                flex-wrap: wrap;
                gap: 8px;
            }

            .header-back {
                order: 1;
                margin-right: 5px;
            }

            .mobile-menu-toggle.left-toggle {
                order: 2;
            }

            .header-left {
                order: 3;
                width: 100%;
                text-align: center;
                margin: 5px 0;
            }

            .real-time-clock {
                order: 4;
                margin: 0 auto;
            }

            .mobile-menu-toggle.right-toggle {
                order: 5;
            }

            .controls {
                order: 6;
                width: 100%;
                justify-content: center;
                margin-top: 5px;
            }

            .left-panel,
            .right-panel {
                position: fixed;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                z-index: 50;
                width: 85%;
                max-width: 300px;
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5);
            }

            .left-panel.active {
                transform: translateX(0);
                left: 0;
            }

            .right-panel {
                left: auto;
                right: 0;
                transform: translateX(100%);
            }

            .right-panel.active {
                transform: translateX(0);
                right: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .panel-overlay.active {
                display: block;
            }

            .edit-mode-indicator {
                right: 20px;
                top: 140px;
            }

            .back-btn span {
                display: none;
            }

            .pool-table-container {
                width: 160px;
                height: 100px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                padding: 10px 15px;
                min-height: 80px;
            }

            .header-left h1 {
                font-size: 16px;
            }

            .real-time-clock {
                font-size: 11px;
                padding: 5px 8px;
                min-width: 90px;
            }

            .control-btn {
                padding: 5px 8px;
                font-size: 11px;
                max-width: 100px;
            }

            .control-btn i {
                font-size: 11px;
            }

            .pool-table-container {
                width: 140px;
                height: 90px;
            }

            .table-number {
                font-size: 24px;
            }

            .tables-container {
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .pool-table-container {
                width: 120px;
                height: 80px;
            }

            .table-number {
                font-size: 20px;
            }

            .table-status {
                font-size: 9px;
                padding: 4px 8px;
            }
        }

        @media (max-width: 480px) {
            .pool-table-container {
                width: 100px;
                height: 70px;
            }

            .table-number {
                font-size: 18px;
            }

            .table-name {
                font-size: 10px;
                padding: 3px 6px;
            }

            .table-status {
                font-size: 8px;
                padding: 3px 6px;
            }

            .combo-badge {
                font-size: 8px;
                padding: 3px 5px;
            }
        }

        /* ===== FLOATING ANIMATIONS ===== */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }

        /* ===== GRID SNAP GUIDE ===== */
        .snap-guide {
            position: absolute;
            background: rgba(0, 255, 136, 0.1);
            border: 1px dashed rgba(0, 255, 136, 0.3);
            pointer-events: none;
            z-index: 999;
            display: none;
        }

        .snap-guide.show {
            display: block;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            @php
                $userRole = Auth::user()->role->slug ?? '';
                $isAdminOrManager = in_array($userRole, ['admin', 'manager']);
                $isStaff = in_array($userRole, ['admin', 'manager', 'employee']);

                // Lấy tên route hiện tại
                $currentRoute = request()->route()->getName();

                // Hàm kiểm tra route có active không
                function isRouteActive($routePattern, $currentRoute)
                {
                    // Nếu là route chính xác
                    if ($routePattern === $currentRoute) {
                        return true;
                    }

                    // Nếu có wildcard * (cho các route con)
                    if (strpos($routePattern, '*') !== false) {
                        $pattern = str_replace('*', '.*', $routePattern);
                        $pattern = '/^' . str_replace('.', '\.', $pattern) . '$/';
                        return preg_match($pattern, $currentRoute);
                    }

                    // Kiểm tra route bắt đầu bằng
                    if (strpos($currentRoute, $routePattern . '.') === 0) {
                        return true;
                    }

                    return false;
                }
            @endphp

            <div class="header-back">
                @if ($userRole === 'employee')
                    {{-- Employee chuyển về simple-dashboard --}}
                    <button onclick="window.location.href='{{ route('admin.pos.dashboard') }}'"
                        class="back-btn">
                        <i class="fas fa-chevron-left"></i>
                        <span>Dashboard</span>
                    </button>
                @else
                    {{-- Admin + Manager chuyển về admin.dashboard --}}
                    <button onclick="window.location.href='{{ route('admin.dashboard') }}'" class="back-btn">
                        <i class="fas fa-chevron-left"></i>
                        <span>Dashboard</span>
                    </button>
                @endif
            </div>


            <button class="mobile-menu-toggle left-toggle" id="leftPanelToggle">
                <i class="fas fa-chart-bar"></i>
            </button>

            <div class="header-left">
                <h1>
                    <i class="fas fa-billiard floating"></i>
                    Billiard POS Dashboard
                </h1>
                <div class="last-update" id="lastUpdateTime">
                    Cập nhật: <span id="currentTime"></span>
                </div>
            </div>

            <div class="real-time-clock">
                <i class="fas fa-clock"></i>
                <span id="liveClock">--:--:--</span>
            </div>

            <button class="mobile-menu-toggle right-toggle" id="rightPanelToggle">
                <i class="fas fa-receipt"></i>
            </button>

            <div class="controls">
                <button id="editModeBtn" class="control-btn edit-btn">
                    <i class="fas fa-edit"></i> Sắp xếp
                </button>
                <button id="saveLayoutBtn" class="control-btn save-btn" style="display: none;">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <button id="cancelEditBtn" class="control-btn cancel-btn" style="display: none;">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <button id="resetLayoutBtn" class="control-btn" style="display: none;">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <button onclick="refreshDashboard()" class="control-btn">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- Panel Overlay for Mobile -->
        <div class="panel-overlay" id="panelOverlay"></div>

        <!-- Edit Mode Indicator -->
        <div class="edit-mode-indicator" id="editModeIndicator">
            <i class="fas fa-mouse-pointer"></i>
            Chế độ sắp xếp - Kéo thả để di chuyển bàn
        </div>

        <!-- Snap Guide -->
        <div class="snap-guide" id="snapGuide"></div>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Left Panel - Statistics -->
            <div class="left-panel" id="leftPanel">
                <!-- Statistics -->
                <div>
                    <div class="section-title">
                        <i class="fas fa-chart-bar"></i>
                        Thống kê hôm nay
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card highlight">
                            <div class="stat-value">{{ $stats['open_bills'] ?? 0 }}</div>
                            <div class="stat-label">HÓA ĐƠN MỞ</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="todayRevenue">{{ number_format($todayRevenue ?? 0) }}đ</div>
                            <div class="stat-label">DOANH THU</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">{{ $stats['total_occupied'] ?? 0 }}</div>
                            <div class="stat-label">BÀN ĐANG DÙNG</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">{{ $stats['available'] ?? 0 }}</div>
                            <div class="stat-label">BÀN TRỐNG</div>
                        </div>
                    </div>
                </div>

                <!-- Status Breakdown -->
                <div>
                    <div class="section-title">
                        <i class="fas fa-billiard"></i>
                        Trạng thái bàn
                    </div>
                    <div class="status-list">
                        <div class="status-item available">
                            <div class="status-text">
                                <span class="status-dot available"></span>
                                Trống
                            </div>
                            <div class="status-count">{{ $stats['available'] ?? 0 }}</div>
                        </div>
                        <div class="status-item occupied">
                            <div class="status-text">
                                <span class="status-dot occupied"></span>
                                Đang dùng
                            </div>
                            <div class="status-count">{{ $stats['occupied'] ?? 0 }}</div>
                        </div>
                        <div class="status-item quick">
                            <div class="status-text">
                                <span class="status-dot quick"></span>
                                Nhanh
                            </div>
                            <div class="status-count">{{ $stats['quick'] ?? 0 }}</div>
                        </div>
                        <div class="status-item maintenance">
                            <div class="status-text">
                                <span class="status-dot maintenance"></span>
                                Bảo trì
                            </div>
                            <div class="status-count">{{ $stats['maintenance'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <div class="section-title">
                        <i class="fas fa-bolt"></i>
                        Hành động nhanh
                    </div>
                    <div class="quick-actions">
                        <button class="action-btn" onclick="quickNewBill()">
                            <i class="fas fa-receipt"></i>
                            Hóa đơn mới
                        </button>
                        <button class="action-btn" onclick="quickTableManagement()">
                            <i class="fas fa-billiard"></i>
                            Quản lý bàn
                        </button>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div>
                    <div class="section-title">
                        <i class="fas fa-history"></i>
                        Hoạt động gần đây
                    </div>
                    <div class="activity-list">
                        @if (isset($openBills) && $openBills->count() > 0)
                            @foreach ($openBills->take(5) as $bill)
                                <div class="activity-item">
                                    <strong>Bàn {{ $bill->table->table_number ?? 'N/A' }}</strong>
                                    mở hóa đơn lúc {{ $bill->created_at->format('H:i') }}
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-info-circle"></i>
                                <p>Chưa có hoạt động</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="content-area" id="contentArea">
                <div class="grid-background"></div>
                <div class="tables-container" id="tablesContainer">
                    @if (isset($error))
                        <div
                            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: rgba(255, 255, 255, 0.5);">
                            <i class="fas fa-exclamation-triangle" style="font-size: 36px; margin-bottom: 10px;"></i>
                            <p>{{ $error }}</p>
                        </div>
                    @elseif(isset($tables) && $tables->count() > 0)
                        <div class="tables-grid" id="tablesGrid">
                            @php
                                $todayRevenue = $todayRevenue ?? 0;
                            @endphp

                            @foreach ($tables as $table)
                                @php
                                    // XỬ LÝ HIỂN THỊ THỜI GIAN COMBO
                                    $elapsedTime = $table['elapsed_time'] ?? '';
                                    $hasCombo = $table['has_combo'] ?? false;
                                    $comboRemaining = $table['combo_remaining'] ?? null;
                                    $isUnprocessed = $table['is_unprocessed'] ?? false;

                                    // LẤY TRẠNG THÁI BÀN
                                    $tableStatus = $table['status'] ?? 'available';

                                    // ĐẢM BẢO THỜI GIAN KHÔNG BAO GIỜ ÂM
                                    $safeRemaining = $comboRemaining !== null ? max(0, $comboRemaining) : 0;

                                    // FORMAT THỜI GIAN COMBO: XhYp (nếu có giờ) hoặc Xp (nếu chỉ có phút)
                                    $comboTimeDisplay = '';
                                    if ($hasCombo && $safeRemaining > 0) {
                                        $hours = floor($safeRemaining / 60);
                                        $minutes = $safeRemaining % 60;

                                        if ($hours > 0 && $minutes > 0) {
                                            $comboTimeDisplay = $hours . 'h' . $minutes . 'p';
                                        } elseif ($hours > 0) {
                                            $comboTimeDisplay = $hours . 'h';
                                        } else {
                                            $comboTimeDisplay = $minutes . 'p';
                                        }
                                    }

                                    // XÁC ĐỊNH THỜI GIAN HIỂN THỊ (CHỈ HIỂN THỊ KHI KHÔNG PHẢI TRẠNG THÁI PAUSED)
                                    $displayTime = '';
                                    $timerClass = '';
                                    $badgeClass = '';
                                    $containerClass = '';

                                    // CHỈ XỬ LÝ HIỂN THỊ THỜI GIAN NẾU KHÔNG PHẢI TRẠNG THÁI PAUSED
                                    if ($tableStatus !== 'paused') {
                                        if ($hasCombo) {
                                            if ($isUnprocessed) {
                                                // Bàn combo đã hết nhưng chưa xử lý
                                                $displayTime = 'HẾT COMBO!';
                                                $timerClass = 'unprocessed';
                                                $badgeClass = 'unprocessed';
                                                $containerClass = 'unprocessed';
                                            } elseif ($safeRemaining <= 0) {
                                                // Combo đã hết thời gian và đã xử lý
                                                $displayTime = 'COMBO ĐÃ HẾT';
                                                $timerClass = '';
                                                $badgeClass = '';
                                            } else {
                                                // Combo còn thời gian - hiển thị định dạng XhYp
                                                $displayTime = $comboTimeDisplay;
                                                $timerClass = '';
                                                $badgeClass = '';
                                            }
                                        } else {
                                            // Không có combo, hiển thị thời gian sử dụng giờ thường
                                            $displayTime = $elapsedTime;
                                        }
                                    }

                                    // Xác định class status bàn
                                    $statusConfig = [
                                        'available' => ['class' => 'status-available', 'text' => 'TRỐNG'],
                                        'paused' => ['class' => 'status-paused', 'text' => 'Tạm Dừng'],
                                        'occupied' => ['class' => 'status-occupied', 'text' => 'ĐANG DÙNG'],
                                        'quick' => ['class' => 'status-quick', 'text' => 'NHANH'],
                                        'maintenance' => ['class' => 'status-maintenance', 'text' => 'BẢO TRÌ'],
                                    ];

                                    $status = $statusConfig[$tableStatus] ?? $statusConfig['available'];

                                    // Tính vị trí hiển thị
                                    $posX = $table['position_x'] ?? ($loop->index % 5) * 220 + 50;
                                    $posY = $table['position_y'] ?? floor($loop->index / 5) * 140 + 50;
                                    $zIndex = $table['z_index'] ?? $loop->index + 1;
                                @endphp

                                <div class="pool-table-container {{ $containerClass }}"
                                    data-table-id="{{ $table['id'] }}"
                                    data-table-number="{{ $table['table_number'] }}"
                                    data-combo-remaining="{{ $safeRemaining }}"
                                    data-is-unprocessed="{{ $isUnprocessed ? 'true' : 'false' }}"
                                    data-status="{{ $tableStatus }}" id="table-{{ $table['id'] }}"
                                    style="left: {{ $posX }}px; top: {{ $posY }}px; z-index: {{ $zIndex }};">

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
                                            <div class="table-number">{{ $table['table_number'] }}</div>
                                            <div class="table-name">{{ $table['table_name'] }}</div>

                                            @if ($displayTime && $tableStatus !== 'paused')
                                                <div class="table-timer {{ $timerClass }}">
                                                    {{ $displayTime }}
                                                </div>
                                            @endif

                                            <div class="table-status {{ $status['class'] }}">
                                                {{ $status['text'] }}
                                            </div>
                                        </div>

                                        <!-- CHỈ HIỂN THỊ BADGE COMBO KHI KHÔNG PHẢI TRẠNG THÁI PAUSED -->
                                        @if (($hasCombo || $isUnprocessed) && $tableStatus !== 'paused')
                                            <div class="combo-badge {{ $badgeClass }}">
                                                @if ($isUnprocessed)
                                                    <i class="fas fa-exclamation-triangle"></i> CHƯA XỬ LÝ
                                                @else
                                                    COMBO
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: rgba(255, 255, 255, 0.5);">
                            <i class="fas fa-billiard" style="font-size: 36px; margin-bottom: 10px;"></i>
                            <p>Chưa có bàn nào được thiết lập</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Panel - Active Bills -->
            <div class="right-panel" id="rightPanel">
                <!-- Active Bills -->
                <div>
                    <div class="section-title">
                        <i class="fas fa-receipt"></i>
                        Hóa đơn đang mở
                    </div>
                    <div class="bill-list">
                        @if (isset($openBills) && $openBills->count() > 0)
                            @foreach ($openBills as $bill)
                                <div class="bill-item" onclick="viewBillDetail({{ $bill->id }})">
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
                                        @if ($bill->start_time)
                                            • {{ Carbon\Carbon::parse($bill->start_time)->diffForHumans() }}
                                        @endif
                                    </div>
                                    @if ($bill->customer_name)
                                        <div class="bill-customer">
                                            <i class="fas fa-user"></i>
                                            {{ $bill->customer_name }}
                                        </div>
                                    @endif
                                    <div class="bill-actions">
                                        <button class="bill-btn detail"
                                            onclick="event.stopPropagation(); viewBillDetail({{ $bill->id }})">
                                            <i class="fas fa-eye"></i>
                                            Chi tiết
                                        </button>
                                        <button class="bill-btn checkout"
                                            onclick="event.stopPropagation(); checkoutBill({{ $bill->id }})">
                                            <i class="fas fa-cash-register"></i>
                                            Thanh toán
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-receipt"></i>
                                <p>Không có hóa đơn đang mở</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Summary -->
                <div>
                    <div class="section-title">
                        <i class="fas fa-chart-pie"></i>
                        Tổng quan
                    </div>
                    <div class="status-summary">
                        <div class="summary-item">
                            <div class="summary-label">Tổng số bàn:</div>
                            <div class="summary-value">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Tỷ lệ sử dụng:</div>
                            <div class="summary-value">{{ $stats['occupancy_rate'] ?? 0 }}%</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Doanh thu hôm nay:</div>
                            <div class="summary-value" id="todayRevenueSummary">
                                {{ number_format($todayRevenue ?? 0) }}đ</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Hóa đơn đang mở:</div>
                            <div class="summary-value">{{ $stats['open_bills'] ?? 0 }}</div>
                        </div>
                    </div>
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
            <div class="loading-text" id="loadingText">Đang xử lý...</div>
        </div>
    </div>

    <script>
        // ===== GLOBAL VARIABLES =====
        let isEditMode = false;
        let originalPositions = new Map();
        let draggedTable = null;
        let dragOffset = {
            x: 0,
            y: 0
        };
        let maxZIndex = 1000;
        let isMobile = window.innerWidth <= 992;
        let snapGrid = 20; // Grid size for snapping
        let velocity = {
            x: 0,
            y: 0
        };
        let lastPos = {
            x: 0,
            y: 0
        };
        let lastTime = 0;
        let todayRevenue = {{ $todayRevenue ?? 0 }};

        // ===== DOM ELEMENTS =====
        const editModeBtn = document.getElementById('editModeBtn');
        const saveLayoutBtn = document.getElementById('saveLayoutBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const resetLayoutBtn = document.getElementById('resetLayoutBtn');
        const tablesContainer = document.getElementById('tablesContainer');
        const tablesGrid = document.getElementById('tablesGrid');
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notificationMessage');
        const editModeIndicator = document.getElementById('editModeIndicator');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const loadingText = document.getElementById('loadingText');
        const leftPanel = document.getElementById('leftPanel');
        const rightPanel = document.getElementById('rightPanel');
        const leftPanelToggle = document.getElementById('leftPanelToggle');
        const rightPanelToggle = document.getElementById('rightPanelToggle');
        const panelOverlay = document.getElementById('panelOverlay');
        const snapGuide = document.getElementById('snapGuide');
        const todayRevenueElement = document.getElementById('todayRevenue');
        const todayRevenueSummary = document.getElementById('todayRevenueSummary');

        // ===== REVENUE FUNCTIONS =====
        function updateRevenueDisplay() {
            if (todayRevenueElement) {
                todayRevenueElement.textContent = formatCurrency(todayRevenue);
            }
            if (todayRevenueSummary) {
                todayRevenueSummary.textContent = formatCurrency(todayRevenue);
            }
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
        }

        // ===== REAL-TIME CLOCK =====
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN');

            if (document.getElementById('liveClock')) {
                document.getElementById('liveClock').textContent = timeString;
            }
            if (document.getElementById('lastUpdateTime')) {
                document.getElementById('lastUpdateTime').innerHTML =
                    `Cập nhật: <span id="currentTime">${timeString}</span>`;
            }
        }

        // ===== MOBILE PANEL TOGGLE =====
        function initMobilePanels() {
            if (!isMobile) return;

            leftPanelToggle.addEventListener('click', () => {
                leftPanel.classList.toggle('active');
                panelOverlay.classList.toggle('active');
                rightPanel.classList.remove('active');
            });

            rightPanelToggle.addEventListener('click', () => {
                rightPanel.classList.toggle('active');
                panelOverlay.classList.toggle('active');
                leftPanel.classList.remove('active');
            });

            panelOverlay.addEventListener('click', () => {
                leftPanel.classList.remove('active');
                rightPanel.classList.remove('active');
                panelOverlay.classList.remove('active');
            });
        }

        // ===== DRAG AND DROP FUNCTIONS =====
        function initDragAndDrop() {
            const poolTables = document.querySelectorAll('.pool-table-container');

            poolTables.forEach(table => {
                table.addEventListener('mousedown', startDrag);
                table.addEventListener('touchstart', startDragTouch, {
                    passive: false
                });

                // Double click to view details in edit mode
                table.addEventListener('dblclick', (e) => {
                    if (isEditMode) {
                        const tableId = table.dataset.tableId;
                        viewTableDetail(tableId);
                    }
                });

                // Prevent text selection while dragging
                table.addEventListener('selectstart', (e) => {
                    if (isEditMode) e.preventDefault();
                });

                // Click to view table details when not in edit mode
                table.addEventListener('click', (e) => {
                    if (!isEditMode && !draggedTable) {
                        const tableId = table.dataset.tableId;
                        viewTableDetail(tableId);
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

            // Handle window resize
            window.addEventListener('resize', handleResize);
        }

        function handleResize() {
            isMobile = window.innerWidth <= 992;
            if (!isMobile) {
                leftPanel.classList.remove('active');
                rightPanel.classList.remove('active');
                panelOverlay.classList.remove('active');
            }
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

            // Tăng z-index khi kéo
            maxZIndex++;
            this.style.zIndex = maxZIndex;
            this.classList.add('dragging');
            this.classList.add('edit-mode');

            // Store initial position and time for velocity calculation
            lastPos = {
                x: rect.left,
                y: rect.top
            };
            lastTime = Date.now();

            // Show snap guide
            showSnapGuide(rect);
        }

        function drag(e) {
            if (!draggedTable || !isEditMode) return;

            const areaRect = tablesContainer.getBoundingClientRect();
            let x = e.clientX - areaRect.left - dragOffset.x;
            let y = e.clientY - areaRect.top - dragOffset.y;

            // Calculate velocity for smooth movement
            const currentTime = Date.now();
            const deltaTime = currentTime - lastTime;
            if (deltaTime > 0) {
                velocity.x = (x - lastPos.x) / deltaTime;
                velocity.y = (y - lastPos.y) / deltaTime;
                lastPos = {
                    x,
                    y
                };
                lastTime = currentTime;
            }

            // Apply grid snapping
            x = Math.round(x / snapGrid) * snapGrid;
            y = Math.round(y / snapGrid) * snapGrid;

            // Boundary checking với padding
            x = Math.max(20, Math.min(x, areaRect.width - draggedTable.offsetWidth - 20));
            y = Math.max(20, Math.min(y, areaRect.height - draggedTable.offsetHeight - 20));

            draggedTable.style.left = `${x}px`;
            draggedTable.style.top = `${y}px`;

            // Update snap guide
            const rect = draggedTable.getBoundingClientRect();
            showSnapGuide(rect);
        }

        function stopDrag() {
            if (draggedTable) {
                // Apply momentum
                if (Math.abs(velocity.x) > 0.1 || Math.abs(velocity.y) > 0.1) {
                    applyMomentum();
                }

                draggedTable.classList.remove('dragging');
                draggedTable.classList.remove('edit-mode');
                draggedTable = null;

                // Hide snap guide
                hideSnapGuide();

                // Reset velocity
                velocity = {
                    x: 0,
                    y: 0
                };
            }
        }

        function applyMomentum() {
            if (!draggedTable) return;

            const areaRect = tablesContainer.getBoundingClientRect();
            let x = parseInt(draggedTable.style.left);
            let y = parseInt(draggedTable.style.top);

            // Apply velocity with damping
            x += velocity.x * 50;
            y += velocity.y * 50;

            // Boundary checking
            x = Math.max(20, Math.min(x, areaRect.width - draggedTable.offsetWidth - 20));
            y = Math.max(20, Math.min(y, areaRect.height - draggedTable.offsetHeight - 20));

            // Snap to grid
            x = Math.round(x / snapGrid) * snapGrid;
            y = Math.round(y / snapGrid) * snapGrid;

            // Smooth animation
            draggedTable.style.transition = 'left 0.2s ease-out, top 0.2s ease-out';
            draggedTable.style.left = `${x}px`;
            draggedTable.style.top = `${y}px`;

            // Remove transition after animation
            setTimeout(() => {
                if (draggedTable) {
                    draggedTable.style.transition = '';
                }
            }, 200);
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
            this.classList.add('edit-mode');

            lastPos = {
                x: rect.left,
                y: rect.top
            };
            lastTime = Date.now();

            showSnapGuide(rect);
        }

        function dragTouch(e) {
            if (!draggedTable || !isEditMode || e.touches.length !== 1) return;

            e.preventDefault();
            const touch = e.touches[0];
            const areaRect = tablesContainer.getBoundingClientRect();
            let x = touch.clientX - areaRect.left - dragOffset.x;
            let y = touch.clientY - areaRect.top - dragOffset.y;

            // Calculate velocity
            const currentTime = Date.now();
            const deltaTime = currentTime - lastTime;
            if (deltaTime > 0) {
                velocity.x = (x - lastPos.x) / deltaTime;
                velocity.y = (y - lastPos.y) / deltaTime;
                lastPos = {
                    x,
                    y
                };
                lastTime = currentTime;
            }

            // Grid snapping
            x = Math.round(x / snapGrid) * snapGrid;
            y = Math.round(y / snapGrid) * snapGrid;

            // Boundary checking
            x = Math.max(20, Math.min(x, areaRect.width - draggedTable.offsetWidth - 20));
            y = Math.max(20, Math.min(y, areaRect.height - draggedTable.offsetHeight - 20));

            draggedTable.style.left = `${x}px`;
            draggedTable.style.top = `${y}px`;

            const rect = draggedTable.getBoundingClientRect();
            showSnapGuide(rect);
        }

        function stopDragTouch() {
            if (draggedTable) {
                // Apply momentum for touch
                if (Math.abs(velocity.x) > 0.05 || Math.abs(velocity.y) > 0.05) {
                    applyMomentum();
                }

                draggedTable.classList.remove('dragging');
                draggedTable.classList.remove('edit-mode');
                draggedTable = null;

                hideSnapGuide();
                velocity = {
                    x: 0,
                    y: 0
                };
            }
        }

        // Snap guide functions
        function showSnapGuide(rect) {
            const containerRect = tablesContainer.getBoundingClientRect();
            const x = Math.round((rect.left - containerRect.left) / snapGrid) * snapGrid;
            const y = Math.round((rect.top - containerRect.top) / snapGrid) * snapGrid;

            snapGuide.style.left = `${x}px`;
            snapGuide.style.top = `${y}px`;
            snapGuide.style.width = `${draggedTable.offsetWidth}px`;
            snapGuide.style.height = `${draggedTable.offsetHeight}px`;
            snapGuide.classList.add('show');
        }

        function hideSnapGuide() {
            snapGuide.classList.remove('show');
        }

        // ===== EDIT MODE FUNCTIONS =====
        function enterEditMode() {
            isEditMode = true;

            // Show edit mode indicator
            editModeIndicator.classList.add('show');

            // Show save/cancel/reset buttons
            saveLayoutBtn.style.display = 'flex';
            cancelEditBtn.style.display = 'flex';
            resetLayoutBtn.style.display = 'flex';
            editModeBtn.style.display = 'none';

            // Add edit-mode class to all tables
            document.querySelectorAll('.pool-table-container').forEach(table => {
                table.classList.add('edit-mode');
            });

            // Store original positions (lưu cả vị trí hiện tại)
            originalPositions.clear();
            document.querySelectorAll('.pool-table-container').forEach(table => {
                const currentX = parseInt(table.style.left) || 0;
                const currentY = parseInt(table.style.top) || 0;
                const currentZ = parseInt(table.style.zIndex) || 0;

                originalPositions.set(table.dataset.tableId, {
                    x: currentX,
                    y: currentY,
                    z: currentZ,
                    element: table
                });
            });

            showNotification('Đang ở chế độ sắp xếp. Kéo thả các bàn để di chuyển vị trí.', 'info');
        }

        function exitEditMode() {
            isEditMode = false;

            // Hide edit mode indicator
            editModeIndicator.classList.remove('show');

            // Hide save/cancel/reset buttons
            saveLayoutBtn.style.display = 'none';
            cancelEditBtn.style.display = 'none';
            resetLayoutBtn.style.display = 'none';
            editModeBtn.style.display = 'flex';

            // Remove edit-mode class from all tables
            document.querySelectorAll('.pool-table-container').forEach(table => {
                table.classList.remove('edit-mode');
            });

            showNotification('Đã thoát chế độ sắp xếp.', 'info');
        }

        // ===== LAYOUT MANAGEMENT =====
        async function saveLayout() {
            const positions = {};

            // Lấy vị trí hiện tại của các bàn
            document.querySelectorAll('.pool-table-container').forEach(table => {
                const tableId = table.dataset.tableId;
                const x = parseInt(table.style.left) || 0;
                const y = parseInt(table.style.top) || 0;
                const z = parseInt(table.style.zIndex) || 0;

                positions[tableId] = {
                    x: x,
                    y: y,
                    z: z
                };
            });

            // Show loading
            showLoading(true, 'Đang lưu vị trí bàn...');

            try {
                const response = await fetch('{{ route('admin.tables.save-layout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        positions: positions
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(data.message || 'Đã lưu vị trí bàn thành công!', 'success');
                    exitEditMode();
                } else {
                    throw new Error(data.message || 'Lỗi khi lưu vị trí bàn');
                }

            } catch (error) {
                console.error('Save error:', error);
                showNotification(error.message || 'Có lỗi xảy ra khi lưu vị trí bàn', 'error');
            } finally {
                showLoading(false);
            }
        }

        async function resetLayout() {
            if (!confirm('Bạn có chắc chắn muốn reset vị trí tất cả bàn về mặc định?')) {
                return;
            }

            showLoading(true, 'Đang reset vị trí bàn...');

            try {
                const response = await fetch('{{ route('admin.tables.reset-layout') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showNotification(data.message || 'Đã reset vị trí bàn!', 'success');
                    refreshDashboard();
                } else {
                    throw new Error(data.message || 'Lỗi khi reset vị trí bàn');
                }

            } catch (error) {
                console.error('Reset error:', error);
                showNotification(error.message || 'Có lỗi xảy ra khi reset vị trí bàn', 'error');
            } finally {
                showLoading(false);
            }
        }

        function undoChanges() {
            if (!isEditMode) return;

            // Khôi phục vị trí gốc
            originalPositions.forEach((position, tableId) => {
                const table = document.querySelector(`[data-table-id="${tableId}"]`);
                if (table) {
                    table.style.left = `${position.x}px`;
                    table.style.top = `${position.y}px`;
                    table.style.zIndex = `${position.z}`;
                }
            });

            showNotification('Đã hoàn tác thay đổi.', 'info');
        }

        // ===== COMBO AUTO UPDATE =====
        function initComboAutoUpdate() {
            // Cập nhật thời gian combo mỗi phút
            setInterval(() => {
                document.querySelectorAll('.pool-table-container').forEach(table => {
                    const remaining = parseInt(table.dataset.comboRemaining) || 0;
                    const isUnprocessed = table.dataset.isUnprocessed === 'true';

                    if (remaining > 0 && !isUnprocessed) {
                        // Giảm thời gian còn lại
                        const newRemaining = remaining - 1;
                        table.dataset.comboRemaining = newRemaining;

                        // Cập nhật hiển thị
                        const timerElement = table.querySelector('.table-timer');
                        if (timerElement) {
                            if (newRemaining <= 0) {
                                // Combo hết, đánh dấu chưa xử lý
                                table.dataset.isUnprocessed = 'true';
                                timerElement.textContent = 'HẾT COMBO!';
                                timerElement.classList.add('unprocessed');
                                table.classList.add('unprocessed');

                                // Cập nhật badge
                                const badge = table.querySelector('.combo-badge');
                                if (badge) {
                                    badge.innerHTML =
                                        '<i class="fas fa-exclamation-triangle"></i> CHƯA XỬ LÝ';
                                    badge.classList.add('unprocessed');
                                }

                                // Gửi thông báo đến server (nếu cần)
                                markComboAsUnprocessed(table.dataset.tableId);
                            } else {
                                // Cập nhật thời gian còn lại
                                const hours = Math.floor(newRemaining / 60);
                                const minutes = newRemaining % 60;

                                if (hours > 0 && minutes > 0) {
                                    timerElement.textContent = `${hours}h${minutes}p`;
                                } else if (hours > 0) {
                                    timerElement.textContent = `${hours}h`;
                                } else {
                                    timerElement.textContent = `${minutes}p`;
                                }
                            }
                        }
                    }
                });
            }, 60000); // Cập nhật mỗi phút
        }

        async function markComboAsUnprocessed(tableId) {
            try {
                await fetch(`/admin/tables/${tableId}/mark-combo-unprocessed`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            } catch (error) {
                console.error('Error marking combo as unprocessed:', error);
            }
        }

        // ===== UI FUNCTIONS =====
        function showNotification(message, type = 'success') {
            if (!notification || !notificationMessage) return;

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

        function showLoading(show, text = 'Đang xử lý...') {
            if (!loadingOverlay || !loadingText) return;

            if (show) {
                loadingText.textContent = text;
                loadingOverlay.classList.add('show');
            } else {
                loadingOverlay.classList.remove('show');
            }
        }

        // ===== ACTION FUNCTIONS =====
        function viewTableDetail(tableId) {
            window.location.href = `/admin/tables/${tableId}/detail`;
        }

        function viewBillDetail(billId) {
            window.location.href = `/admin/bills/${billId}`;
        }

        function checkoutBill(billId) {
            window.location.href = `/admin/bills/${billId}/checkout`;
        }

        function quickNewBill() {
            window.location.href = '/admin/bills/index';
        }

        function quickTableManagement() {
            window.location.href = '/admin/tables';
        }

        function refreshDashboard() {
            showLoading(true, 'Đang tải lại dữ liệu...');
            window.location.reload();
        }

        // ===== EVENT LISTENERS =====
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize
            updateClock();
            setInterval(updateClock, 1000);

            // Update revenue display
            updateRevenueDisplay();

            initMobilePanels();
            initDragAndDrop();
            initComboAutoUpdate();

            // Edit mode button
            editModeBtn.addEventListener('click', enterEditMode);

            // Save layout button
            saveLayoutBtn.addEventListener('click', saveLayout);

            // Cancel edit button
            cancelEditBtn.addEventListener('click', exitEditMode);

            // Reset layout button
            resetLayoutBtn.addEventListener('click', resetLayout);

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
                if (e.key === 'z' && (e.ctrlKey || e.metaKey) && isEditMode) {
                    e.preventDefault();
                    undoChanges();
                }
            });

            // Auto-refresh every 2 minutes
            setInterval(refreshDashboard, 120000);

            // Prevent pull-to-refresh on mobile
            document.addEventListener('touchmove', function(e) {
                if (isEditMode && draggedTable) {
                    e.preventDefault();
                }
            }, {
                passive: false
            });
        });
    </script>
</body>

</html>
