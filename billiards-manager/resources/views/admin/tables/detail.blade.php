<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Bàn - {{ $table->table_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS styles giữ nguyên từ code cũ */
        :root {
            --primary: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --purple: #8b5cf6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            height: 100vh;
            overflow: hidden;
        }

        .app-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Header Styles */
        .header {
            background: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .table-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .back-btn {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: #475569;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .back-btn:hover {
            background: #e2e8f0;
            transform: translateX(-2px);
        }

        .table-details h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        .table-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.25rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .table-status {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .status-available {
            background: #dcfce7;
            color: #166534;
        }

        .status-occupied {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-maintenance {
            background: #fef3c7;
            color: #92400e;
        }

        .status-paused {
            background: #dbeafe;
            color: #1e40af;
        }

        .hourly-rate {
            font-size: 0.875rem;
            color: #475569;
        }

        /* Main Content Styles */
        .main-content {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .left-panel {
            width: 35%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 1.5rem;
            gap: 1.5rem;
        }

        .center-panel {
            width: 40%;
            background: white;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .right-panel {
            width: 25%;
            background: white;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 8px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Time Tracking */
        .time-tracking {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .time-box {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.2s;
        }

        .time-box:hover {
            transform: translateY(-2px);
        }

        .time-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .time-value {
            font-size: 1.25rem;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        .time-elapsed {
            color: #3b82f6;
        }

        .time-remaining {
            color: #10b981;
        }

        .time-cost {
            color: #f59e0b;
        }

        /* Progress Bar */
        .progress-container {
            margin-top: 1rem;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #f1f5f9;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        /* Products List */
        .products-list {
            flex: 1;
            overflow: auto;
        }

        .products-list table {
            min-width: 100%;
        }

        .products-list th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            z-index: 10;
        }

        .quantity-btn {
            transition: all 0.2s;
        }

        .quantity-btn:hover:not(:disabled) {
            background: #3b82f6 !important;
            color: white;
            border-color: #3b82f6;
        }

        .add-btn:disabled {
            background: #cbd5e1 !important;
            cursor: not-allowed;
            transform: none;
        }

        .add-btn:disabled:hover {
            background: #cbd5e1 !important;
            transform: none;
        }

        /* Products & Combos Section */
        .products-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .products-tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1rem;
        }

        .tab {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-weight: 500;
            color: #64748b;
            transition: all 0.2s;
        }

        .tab:hover {
            color: #3b82f6;
        }

        .tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }

        .search-box {
            width: 100%;
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            transition: border-color 0.2s;
        }

        .search-box:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .products-container {
            flex: 1;
            overflow: auto;
        }

        /* Bill Details */
        .bill-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .bill-container {
            flex: 1;
            overflow: auto;
        }

        .bill-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bill-table th {
            text-align: left;
            padding: 0.75rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            position: sticky;
            top: 0;
        }

        .bill-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .total-amount {
            text-align: right;
            font-size: 1.5rem;
            font-weight: bold;
            color: #10b981;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        /* Right Panel Content */
        .right-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: auto;
            padding: 1.5rem;
        }

        .info-section {
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        .info-value {
            font-weight: 600;
            text-align: right;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .action-btn {
            width: 100%;
            padding: 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .action-btn-primary {
            background: #3b82f6;
            color: white;
        }

        .action-btn-primary:hover {
            background: #2563eb;
        }

        .action-btn-success {
            background: #10b981;
            color: white;
        }

        .action-btn-success:hover {
            background: #059669;
        }

        .action-btn-warning {
            background: #f59e0b;
            color: white;
        }

        .action-btn-warning:hover {
            background: #d97706;
        }

        .action-btn-danger {
            background: #ef4444;
            color: white;
        }

        .action-btn-danger:hover {
            background: #dc2626;
        }

        .action-btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .action-btn-secondary:hover {
            background: #e2e8f0;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow: auto;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .modal-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Custom Toast Notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            padding: 12px 20px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 350px;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateX(100%);
            opacity: 0;
        }

        .toast-success {
            background-color: #10b981;
        }

        .toast-error {
            background-color: #ef4444;
        }

        .toast-warning {
            background-color: #f59e0b;
        }

        .toast-info {
            background-color: #3b82f6;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Fade In Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Slide In Animation */
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Pulse Animation */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        /* Bounce Animation */
        .bounce {
            animation: bounce 0.5s;
        }

        @keyframes bounce {

            0%,
            20%,
            60%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            80% {
                transform: translateY(-5px);
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Combo Status Banner */
        .combo-status-banner {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .combo-status-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .combo-status-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .combo-status-text {
            flex: 1;
        }

        .combo-status-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .combo-status-description {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        /* Warning Banner */
        .warning-banner {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .warning-banner-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #92400e;
        }

        .warning-banner-content i {
            font-size: 1.25rem;
        }

        .warning-banner-text {
            flex: 1;
        }

        .warning-banner-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .warning-banner-description {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        /* Critical Warning Banner */
        .critical-warning-banner {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }

            100% {
                opacity: 1;
            }
        }

        .critical-warning-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .critical-warning-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .critical-warning-text {
            flex: 1;
        }

        .critical-warning-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
        }

        .critical-warning-description {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        /* Combo Ended Info */
        .combo-ended-info {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .combo-ended-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #92400e;
        }

        .combo-ended-content i {
            font-size: 1.25rem;
        }

        .combo-ended-text {
            flex: 1;
        }

        .combo-ended-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .combo-ended-description {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        /* Delete Confirmation Modal Styles */
        .delete-confirm-icon {
            animation: pulse 1.5s infinite;
        }

        .delete-product-btn {
            transition: all 0.3s ease;
        }

        .delete-product-btn:hover {
            transform: scale(1.1);
            background: #fef2f2 !important;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Mobile Panel Tabs */
        .mobile-panel-tabs {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e2e8f0;
            z-index: 1000;
        }

        .mobile-tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.75rem;
            color: #64748b;
            transition: all 0.2s;
        }

        .mobile-tab.active {
            color: #3b82f6;
            background: #eff6ff;
        }

        .mobile-tab i {
            display: block;
            font-size: 1.25rem;
            margin-bottom: 4px;
        }

        /* Mobile Styles */
        @media (max-width: 1024px) {
            .main-content {
                flex-direction: column;
            }

            .left-panel,
            .center-panel,
            .right-panel {
                width: 100%;
                height: auto;
                border: none;
            }

            .panel {
                display: none;
            }

            .panel.active {
                display: flex;
            }

            .mobile-panel-tabs {
                display: flex;
            }

            .header {
                padding: 1rem;
            }

            .table-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .table-status {
                align-items: flex-start;
                width: 100%;
            }

            .time-tracking {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
            }

            .time-box {
                padding: 0.75rem 0.5rem;
            }

            .time-value {
                font-size: 1rem;
            }

            .card {
                padding: 1rem;
            }

            .right-content {
                padding: 1rem;
            }

            .products-tabs {
                flex-wrap: wrap;
            }

            .tab {
                flex: 1;
                min-width: 120px;
                text-align: center;
                padding: 0.75rem 0.5rem;
            }

            .action-buttons {
                gap: 0.5rem;
            }

            .action-btn {
                padding: 0.6rem;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .table-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .back-btn {
                align-self: flex-start;
            }

            .table-details h1 {
                font-size: 1.25rem;
            }

            .table-meta {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .time-tracking {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .time-box {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem;
            }

            .time-label {
                margin-bottom: 0;
                font-size: 0.8rem;
            }

            .time-value {
                font-size: 1rem;
            }

            .combo-status-content {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .combo-actions {
                width: 100%;
                justify-content: space-between;
            }

            .combo-actions .action-btn {
                flex: 1;
                margin: 0 0.25rem;
            }

            .products-list table {
                font-size: 0.875rem;
            }

            .bill-table {
                font-size: 0.875rem;
            }

            .bill-table th,
            .bill-table td {
                padding: 0.5rem;
            }

            .total-amount {
                font-size: 1.25rem;
            }

            .transfer-item {
                padding: 0.75rem;
            }

            .transfer-item .grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .info-item {
                padding: 0.5rem 0;
                font-size: 0.875rem;
            }

            .modal-content {
                padding: 1.5rem;
                margin: 1rem;
            }

            .mobile-tab {
                padding: 10px 8px;
                font-size: 0.7rem;
            }

            .mobile-tab i {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 0.75rem;
            }

            .table-details h1 {
                font-size: 1.1rem;
            }

            .status-badge {
                font-size: 0.7rem;
                padding: 0.3rem 0.6rem;
            }

            .hourly-rate {
                font-size: 0.8rem;
            }

            .card {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }

            .section-title {
                font-size: 1rem;
            }

            .time-box {
                padding: 0.6rem;
            }

            .time-label {
                font-size: 0.75rem;
            }

            .time-value {
                font-size: 0.9rem;
            }

            .products-tabs .tab {
                padding: 0.6rem 0.4rem;
                font-size: 0.8rem;
            }

            .search-box {
                padding: 0.6rem;
                font-size: 0.875rem;
            }

            .quantity-input {
                width: 40px;
                padding: 0.25rem;
            }

            .quantity-btn {
                width: 28px;
                height: 28px;
            }

            .add-btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }

            .right-content {
                padding: 0.75rem;
            }

            .action-btn {
                padding: 0.5rem;
                font-size: 0.8rem;
            }

            .mobile-tab {
                padding: 8px 6px;
                font-size: 0.65rem;
            }

            .mobile-tab i {
                font-size: 1rem;
                margin-bottom: 2px;
            }

            .empty-state {
                padding: 1.5rem;
            }

            .empty-state i {
                font-size: 2rem;
            }
        }

        /* Utility classes for mobile */
        .mobile-only {
            display: none;
        }

        .desktop-only {
            display: block;
        }

        @media (max-width: 1024px) {
            .mobile-only {
                display: block;
            }

            .desktop-only {
                display: none;
            }
        }

        /* Table responsive */
        .table-responsive {
            overflow-x: auto;
        }

        .table-responsive table {
            min-width: 600px;
        }

        @media (max-width: 768px) {
            .table-responsive table {
                min-width: 500px;
            }
        }

        @media (max-width: 480px) {
            .table-responsive table {
                min-width: 400px;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Toast Container -->
        <div class="toast-container" id="toastContainer"></div>

        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
        </div>

        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars text-lg"></i>
        </button>

        @php
            $userRole = Auth::user()->role->slug ?? '';
            $isAdminOrManager = in_array($userRole, ['admin', 'manager']);
            $isStaff = in_array($userRole, ['admin', 'manager', 'employee']);
        @endphp

        <!-- Header -->
        <div class="header">
            <div class="table-info">
                <div class="table-title">
                    @if (in_array($userRole, ['admin', 'manager']))
                        <a href="{{ route('admin.tables.index') }}" class="back-btn">
                            <i class="fas fa-arrow-left"></i> <span class="desktop-only">Quay lại</span>
                        </a>
                    @elseif($userRole === 'employee')
                        <a href="{{ route('admin.tables.simple-dashboard') }}" class="back-btn">
                            <i class="fas fa-arrow-left"></i> <span class="desktop-only">Quay lại</span>
                        </a>
                    @endif
                    <div class="table-details">
                        <h1>{{ $table->table_name }}</h1>
                        <div class="table-meta">
                            <span>Số: {{ $table->table_number }}</span>
                            <span class="desktop-only">•</span>
                            <span>{{ $table->tableRate->name ?? 'Chưa phân loại' }}</span>
                        </div>
                    </div>
                </div>
                <div class="table-status">
                    @php
                        $statusClass = match ($table->status) {
                            'available' => 'status-available',
                            'occupied' => 'status-occupied',
                            'maintenance' => 'status-maintenance',
                            'paused' => 'status-paused',
                            default => 'status-available',
                        };

                        $statusText = match ($table->status) {
                            'available' => '🟢 TRỐNG',
                            'occupied' => '🔴 ĐANG SỬ DỤNG',
                            'maintenance' => '🟡 BẢO TRÌ',
                            'paused' => '🔵 TẠM DỪNG',
                            default => '🟢 TRỐNG',
                        };
                    @endphp
                    <div class="status-badge {{ $statusClass }}">
                        {{ $statusText }}
                    </div>
                    <div class="hourly-rate">
                        Giá giờ: <strong>{{ number_format($table->getHourlyRate()) }} ₫/h</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Panel - Products & Combos -->
            <div class="left-panel panel active" id="productsPanel">
                <!-- Time Tracking -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="section-title">
                            <i class="fas fa-clock text-blue-500"></i>
                            THEO DÕI THỜI GIAN
                        </h2>
                        <div>
                            @php
                                $modeText = match ($timeInfo['mode'] ?? 'none') {
                                    'regular' => '🕒 GIỜ THƯỜNG',
                                    'combo' => '🎁 COMBO TIME',
                                    'quick' => '⚡ BÀN LẺ',
                                    'combo_ended' => '⏹️ COMBO ĐÃ HẾT',
                                    default => '⏸️ KHÔNG HOẠT ĐỘNG',
                                };
                            @endphp
                            <span class="text-sm font-medium text-gray-600">{{ $modeText }}</span>
                        </div>
                    </div>

                    <div class="time-tracking">
                        <div class="time-box">
                            <div class="time-label">ĐÃ SỬ DỤNG</div>
                            <div id="elapsedTimeDisplay" class="time-value time-elapsed">
                                @if (isset($timeInfo['elapsed_minutes']))
                                    @php
                                        $elapsedMinutes = $timeInfo['elapsed_minutes'];
                                        $hours = floor($elapsedMinutes / 60);
                                        $minutes = $elapsedMinutes % 60;
                                    @endphp
                                    {{ sprintf('%02d:%02d', $hours, $minutes) }}
                                @else
                                    00:00
                                @endif
                            </div>
                        </div>

                        <div class="time-box">
                            <div class="time-label">THỜI GIAN CÒN LẠI</div>
                            <div id="remainingTimeDisplay" class="time-value time-remaining">
                                @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo' && isset($timeInfo['remaining_minutes']))
                                    @php
                                        $remainingMinutes = $timeInfo['remaining_minutes'];
                                        $hours = floor($remainingMinutes / 60);
                                        $minutes = $remainingMinutes % 60;
                                    @endphp
                                    {{ sprintf('%02d:%02d', $hours, $minutes) }}
                                @elseif (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo_ended')
                                    <span class="text-red-500">00:00</span>
                                @else
                                    --:--
                                @endif
                            </div>
                        </div>

                        <div class="time-box">
                            <div class="time-label">CHI PHÍ HIỆN TẠI</div>
                            <div id="currentCostDisplay" class="time-value time-cost">
                                {{ number_format(round($timeInfo['current_cost'] ?? 0)) }} ₫
                            </div>
                        </div>
                    </div>

                    @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo')
                        <div class="progress-container">
                            <div class="progress-header">
                                <span>TIẾN ĐỘ SỬ DỤNG COMBO</span>
                                <span id="progressText" class="font-bold">
                                    @if (isset($timeInfo['total_minutes']) && $timeInfo['total_minutes'] > 0)
                                        {{ round(min(100, (($timeInfo['elapsed_minutes'] ?? 0) / $timeInfo['total_minutes']) * 100)) }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            </div>
                            <div class="progress-bar">
                                <div id="progressBar" class="progress-fill"
                                    style="width: {{ isset($timeInfo['total_minutes']) && $timeInfo['total_minutes'] > 0 ? min(100, (($timeInfo['elapsed_minutes'] ?? 0) / $timeInfo['total_minutes']) * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- CẢNH BÁO COMBO SẮP HẾT (5-10 phút) -->
                    @if (isset($timeInfo['mode']) &&
                            $timeInfo['mode'] === 'combo' &&
                            isset($timeInfo['remaining_minutes']) &&
                            $timeInfo['remaining_minutes'] <= 10 &&
                            $timeInfo['remaining_minutes'] > 5)
                        <div class="warning-banner">
                            <div class="warning-banner-content">
                                <i class="fas fa-exclamation-triangle text-amber-500"></i>
                                <div class="warning-banner-text">
                                    <div class="warning-banner-title">COMBO SẮP HẾT THỜI GIAN!</div>
                                    <div class="warning-banner-description">
                                        Chỉ còn <strong>{{ $timeInfo['remaining_minutes'] }} phút</strong> trong combo.
                                        Chuẩn bị chuyển sang giờ thường.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- CẢNH BÁO KHẨN CẤP COMBO SẮP HẾT (0-5 phút) -->
                    @if (isset($timeInfo['mode']) &&
                            $timeInfo['mode'] === 'combo' &&
                            isset($timeInfo['remaining_minutes']) &&
                            $timeInfo['remaining_minutes'] <= 5 &&
                            $timeInfo['remaining_minutes'] > 0)
                        <div class="critical-warning-banner">
                            <div class="critical-warning-content">
                                <div class="critical-warning-info">
                                    <i class="fas fa-exclamation-circle text-white text-xl"></i>
                                    <div class="critical-warning-text">
                                        <div class="critical-warning-title">CẢNH BÁO: COMBO SẮP HẾT!</div>
                                        <div class="critical-warning-description">
                                            Chỉ còn <strong>{{ $timeInfo['remaining_minutes'] }} phút</strong>.
                                            Hệ thống sẽ tự động chuyển sang giờ thường khi hết thời gian.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Combo đang chạy -->
                    @if (isset($timeInfo['mode']) && $timeInfo['mode'] === 'combo' && $timeInfo['is_running'] && !$timeInfo['is_paused'])
                        <div class="combo-status-banner">
                            <div class="combo-status-content">
                                <div class="combo-status-info">
                                    <i class="fas fa-gift text-white text-xl"></i>
                                    <div class="combo-status-text">
                                        <div class="combo-status-title">COMBO TIME ĐANG CHẠY</div>
                                        <div class="combo-status-description">
                                            Thời gian còn lại: <strong>{{ $timeInfo['remaining_minutes'] ?? 0 }}
                                                phút</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="combo-actions flex gap-2">
                                    <form action="{{ route('admin.bills.stop-combo', $table->currentBill->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-danger"
                                            style="padding: 0.5rem 1rem;"
                                            onclick="return confirm('Bạn có chắc muốn DỪNG combo thời gian?')">
                                            <i class="fas fa-stop"></i> Tắt Combo
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Thông báo combo đã hết -->
                    @if (isset($timeInfo['needs_switch']) &&
                            $timeInfo['needs_switch'] &&
                            isset($timeInfo['mode']) &&
                            $timeInfo['mode'] === 'combo_ended')
                        <div class="combo-ended-info">
                            <div class="combo-ended-content">
                                <i class="fas fa-info-circle text-amber-500"></i>
                                <div class="combo-ended-text">
                                    <div class="combo-ended-title">COMBO ĐÃ KẾT THÚC</div>
                                    <div class="combo-ended-description">
                                        @if ($timeInfo['is_auto_stopped'] ?? false)
                                            Combo đã tự động dừng khi hết thời gian. Vui lòng bật giờ thường để tiếp tục
                                            tính giờ.
                                        @else
                                            Combo đã được dừng thủ công. Vui lòng bật giờ thường để tiếp tục tính giờ.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Products & Combos Section -->
                <div class="card products-section">
                    <div class="products-tabs">
                        <div class="tab active" data-tab="products">
                            <i class="fas fa-utensils text-green-500"></i>
                            <span class="desktop-only">SẢN PHẨM</span>
                            <span class="mobile-only">SP</span>
                            ({{ $products->count() }})
                        </div>
                        <div class="tab" data-tab="combos">
                            <i class="fas fa-gift text-purple-500"></i>
                            <span class="desktop-only">COMBO</span>
                            <span class="mobile-only">CB</span>
                            ({{ $combos->count() }})
                        </div>
                    </div>

                    <input type="text" id="productSearch" placeholder="Tìm kiếm sản phẩm..." class="search-box">

                    <div class="products-container table-responsive">
                        <!-- Products List -->
                        <div id="productsList" class="products-list">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="p-3 text-left text-sm font-medium text-gray-600">Sản phẩm</th>
                                        <th class="p-3 text-right text-sm font-medium text-gray-600 w-24">Giá</th>
                                        <th class="p-3 text-center text-sm font-medium text-gray-600 w-32">Số lượng
                                        </th>
                                        <th class="p-3 text-center text-sm font-medium text-gray-600 w-20">Thao tác
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 fade-in">
                                            <td class="p-3">
                                                <div class="flex items-center gap-3">
                                                    @if ($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}"
                                                            alt="{{ $product->name }}"
                                                            class="w-10 h-10 rounded-lg object-cover">
                                                    @else
                                                        <div
                                                            class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                                            <i class="fas fa-utensils text-gray-400"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $product->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 flex items-center gap-2">
                                                            @if ($product->stock_quantity <= 0)
                                                                <span class="text-red-500 font-medium">Hết hàng</span>
                                                            @elseif($product->stock_quantity < 10)
                                                                <span class="text-orange-500 font-medium">Còn
                                                                    {{ $product->stock_quantity }}</span>
                                                            @else
                                                                <span class="text-green-500 font-medium">Còn
                                                                    {{ $product->stock_quantity }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3 text-right">
                                                <div class="font-bold text-green-600">
                                                    {{ number_format($product->price) }} ₫</div>
                                            </td>
                                            <td class="p-3">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button
                                                        class="quantity-btn minus w-8 h-8 flex items-center justify-center bg-gray-100 rounded border"
                                                        data-product-id="{{ $product->id }}">-</button>
                                                    <input type="number" min="1"
                                                        max="{{ $product->stock_quantity }}" value="1"
                                                        class="quantity-input product-quantity w-12 text-center border rounded py-1"
                                                        data-product-id="{{ $product->id }}"
                                                        {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                                    <button
                                                        class="quantity-btn plus w-8 h-8 flex items-center justify-center bg-gray-100 rounded border"
                                                        data-product-id="{{ $product->id }}">+</button>
                                                </div>
                                            </td>
                                            <td class="p-3 text-center">
                                                <button
                                                    class="add-btn add-product-btn bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm font-medium transition-colors"
                                                    data-product-id="{{ $product->id }}"
                                                    {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus mr-1"></i>
                                                    <span class="desktop-only">Thêm</span>
                                                    <span class="mobile-only">+</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Combos List -->
                        <div id="combosList" class="products-list" style="display: none;">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="p-3 text-left text-sm font-medium text-gray-600">Combo</th>
                                        <th class="p-3 text-right text-sm font-medium text-gray-600 w-24">Giá</th>
                                        <th class="p-3 text-center text-sm font-medium text-gray-600 w-32">Số lượng
                                        </th>
                                        <th class="p-3 text-center text-sm font-medium text-gray-600 w-20">Thao tác
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($combos as $combo)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50 fade-in">
                                            <td class="p-3">
                                                <div class="flex items-center gap-3">
                                                    @if ($combo->image)
                                                        <img src="{{ asset('storage/' . $combo->image) }}"
                                                            alt="{{ $combo->name }}"
                                                            class="w-10 h-10 rounded-lg object-cover">
                                                    @else
                                                        <div
                                                            class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                                            <i class="fas fa-gift text-purple-400"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $combo->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 flex items-center gap-2">
                                                            @if ($combo->is_time_combo)
                                                                <span
                                                                    class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-medium">
                                                                    <i class="fas fa-clock mr-1"></i>
                                                                    {{ $combo->play_duration_minutes }} phút
                                                                </span>
                                                            @endif
                                                            @if ($combo->actual_value > $combo->price)
                                                                <span class="text-green-600 font-medium">
                                                                    Tiết kiệm
                                                                    {{ number_format($combo->actual_value - $combo->price) }}
                                                                    ₫
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3 text-right">
                                                <div class="font-bold text-green-600">
                                                    {{ number_format($combo->price) }} ₫</div>
                                                @if ($combo->actual_value > $combo->price)
                                                    <div class="text-sm text-gray-400 line-through">
                                                        {{ number_format($combo->actual_value) }} ₫</div>
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button
                                                        class="quantity-btn minus w-8 h-8 flex items-center justify-center bg-gray-100 rounded border"
                                                        data-combo-id="{{ $combo->id }}">-</button>
                                                    <input type="number" min="1" value="1"
                                                        class="quantity-input combo-quantity w-12 text-center border rounded py-1"
                                                        data-combo-id="{{ $combo->id }}">
                                                    <button
                                                        class="quantity-btn plus w-8 h-8 flex items-center justify-center bg-gray-100 rounded border"
                                                        data-combo-id="{{ $combo->id }}">+</button>
                                                </div>
                                            </td>
                                            <td class="p-3 text-center">
                                                <button
                                                    class="add-btn add-combo-btn bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded text-sm font-medium transition-colors"
                                                    data-combo-id="{{ $combo->id }}"
                                                    {{ $table->currentBill && $table->currentBill->status === 'quick' ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus mr-1"></i>
                                                    <span class="desktop-only">Thêm</span>
                                                    <span class="mobile-only">+</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center Panel - Bill Details -->
            <div class="center-panel panel" id="billPanel">
                <div class="right-content">
                    <!-- Bill Details -->
                    <div class="card bill-details">
                        <div class="card-header">
                            <h2 class="section-title">
                                <i class="fas fa-receipt text-gray-600"></i>
                                CHI TIẾT HÓA ĐƠN
                            </h2>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">TỔNG HÓA ĐƠN</div>
                                <div id="finalAmountDisplay" class="text-xl font-bold text-green-600">
                                    {{ number_format(round($table->currentBill->final_amount ?? 0)) }} ₫
                                </div>
                            </div>
                        </div>

                        <div class="bill-container table-responsive">
                            @if ($table->currentBill && $table->currentBill->billDetails->count() > 0)
                                <table class="bill-table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm/Dịch vụ</th>
                                            <th width="80">SL</th>
                                            <th width="120">Đơn giá</th>
                                            <th width="140">Thành tiền</th>
                                            <th width="80">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($table->currentBill->billDetails as $item)
                                            <tr class="fade-in">
                                                <td>
                                                    @if ($item->product_id && $item->product)
                                                        <i class="fas fa-utensils text-green-500 mr-2"></i>
                                                        {{ $item->product->name }}
                                                        @if ($item->is_combo_component)
                                                            <span class="text-xs text-gray-500">(Thành phần
                                                                combo)</span>
                                                        @endif
                                                    @elseif($item->combo_id && $item->combo)
                                                        <i class="fas fa-gift text-purple-500 mr-2"></i>
                                                        {{ $item->combo->name }}
                                                    @else
                                                        <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                                                        {{ $item->note ?? 'Dịch vụ khác' }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-right">{{ number_format(round($item->unit_price)) }} ₫
                                                </td>
                                                <td class="text-right font-semibold">
                                                    {{ number_format(round($item->total_price)) }} ₫</td>
                                                <td class="text-center">
                                                    @if ($item->product_id && !$item->is_combo_component && !$item->combo_id)
                                                        <form
                                                            action="{{ route('admin.bills.remove-product', ['bill' => $table->currentBill->id, 'billDetail' => $item->id]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="delete-product-btn text-red-500 hover:text-red-700 transition-colors duration-200 p-2 rounded hover:bg-red-50"
                                                                title="Xóa sản phẩm"
                                                                data-product-name="{{ $item->product->name }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400 text-xs">Không thể xóa</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-receipt"></i>
                                    <p class="text-lg font-medium mb-2">Chưa có sản phẩm nào trong hóa đơn</p>
                                    <p class="text-sm">Thêm sản phẩm hoặc combo để bắt đầu</p>
                                </div>
                            @endif

                            <!-- PHẦN HIỂN THỊ CHI TIẾT CHUYỂN BÀN -->
                            @if ($table->currentBill && $table->currentBill->billTimeUsages->count() > 1)
                                <div class="table-transfer-details mt-6">
                                    <h3 class="text-lg font-semibold mb-3 text-blue-600 border-b pb-2">
                                        <i class="fas fa-exchange-alt mr-2"></i>LỊCH SỬ CHUYỂN BÀN
                                    </h3>

                                    <div class="space-y-3">
                                        @php
                                            $timeUsages = $table->currentBill->billTimeUsages->sortBy('created_at');
                                            $transferCount = 0;
                                        @endphp

                                        @foreach ($timeUsages as $index => $timeUsage)
                                            @if ($index > 0)
                                                @php
                                                    $previousUsage = $timeUsages[$index - 1];
                                                    $transferCount++;
                                                @endphp
                                                <div
                                                    class="transfer-item bg-blue-50 border border-blue-200 rounded-lg p-3 slide-in">
                                                    <div class="flex justify-between items-start">
                                                        <div class="flex-1">
                                                            <div class="flex items-center mb-1">
                                                                <span
                                                                    class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-2">
                                                                    Lần {{ $transferCount }}
                                                                </span>
                                                                <span class="text-sm font-medium text-blue-900">
                                                                    <i class="fas fa-arrow-right mr-1"></i>
                                                                    Chuyển bàn
                                                                </span>
                                                            </div>

                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                                                <div>
                                                                    <span class="text-gray-600">Từ bàn:</span>
                                                                    <span
                                                                        class="font-medium">{{ $previousUsage->table->table_number ?? 'N/A' }}</span>
                                                                    <span
                                                                        class="text-xs text-gray-500">({{ number_format($previousUsage->hourly_rate) }}₫/h)</span>
                                                                </div>
                                                                <div>
                                                                    <span class="text-gray-600">Sang bàn:</span>
                                                                    <span
                                                                        class="font-medium">{{ $timeUsage->table->table_number ?? $table->table_number }}</span>
                                                                    <span
                                                                        class="text-xs text-gray-500">({{ number_format($timeUsage->hourly_rate) }}₫/h)</span>
                                                                </div>
                                                            </div>

                                                            @if ($previousUsage->end_time)
                                                                <div class="mt-2 text-xs text-gray-600">
                                                                    <i class="far fa-clock mr-1"></i>
                                                                    Thời gian sử dụng:
                                                                    {{ \Carbon\Carbon::parse($previousUsage->start_time)->format('H:i') }}
                                                                    →
                                                                    {{ \Carbon\Carbon::parse($previousUsage->end_time)->format('H:i') }}
                                                                    ({{ $previousUsage->duration_minutes ?? 0 }} phút)
                                                                </div>
                                                            @endif

                                                            @if ($previousUsage->total_price > 0)
                                                                <div class="mt-1 text-sm font-medium text-green-600">
                                                                    <i class="fas fa-coins mr-1"></i>
                                                                    Tiền giờ bàn cũ:
                                                                    {{ number_format($previousUsage->total_price) }}₫
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="text-right">
                                                            <div class="text-xs text-gray-500 mb-1">
                                                                {{ \Carbon\Carbon::parse($previousUsage->end_time ?? $timeUsage->start_time)->format('d/m/Y H:i') }}
                                                            </div>
                                                            @if ($previousUsage->note)
                                                                <div class="text-xs text-blue-600 italic">
                                                                    "{{ $previousUsage->note }}"
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <!-- Hiển thị bàn hiện tại -->
                                        <div
                                            class="current-table bg-green-50 border border-green-200 rounded-lg p-3 fade-in">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <div class="flex items-center mb-1">
                                                        <span
                                                            class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full mr-2">
                                                            Hiện tại
                                                        </span>
                                                        <span class="text-sm font-medium text-green-900">
                                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                                            Bàn {{ $table->table_number }}
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        Giá giờ:
                                                        {{ number_format($table->tableRate->hourly_rate ?? 0) }}₫/h
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-xs text-gray-500">
                                                        Bắt đầu:
                                                        {{ \Carbon\Carbon::parse($timeUsages->last()->start_time)->format('H:i') }}
                                                    </div>
                                                    @if ($timeUsages->last()->note)
                                                        <div class="text-xs text-green-600 italic">
                                                            "{{ $timeUsages->last()->note }}"
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($table->currentBill && $table->currentBill->billTimeUsages->count() == 1)
                                <!-- Hiển thị thông tin bàn hiện tại nếu chưa chuyển bàn -->
                                <div
                                    class="current-table-simple bg-gray-50 border border-gray-200 rounded-lg p-3 mt-4 fade-in">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                Bàn {{ $table->table_number }}
                                            </span>
                                            <span class="text-xs text-gray-600 ml-2">
                                                ({{ number_format($table->tableRate->hourly_rate ?? 0) }}₫/h)
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Bắt đầu:
                                            {{ \Carbon\Carbon::parse($table->currentBill->billTimeUsages->first()->start_time)->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!-- KẾT THÚC PHẦN HIỂN THỊ CHUYỂN BÀN -->
                        </div>

                        @if ($table->currentBill && $table->currentBill->billDetails->count() > 0)
                            <div class="total-amount">
                                Tổng cộng: {{ number_format(round($table->currentBill->final_amount)) }} ₫
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Panel - Table Info & Actions -->
            <div class="right-panel panel" id="infoPanel">
                <div class="right-content">
                    <!-- Table Info -->
                    <div class="card info-section">
                        <h2 class="section-title">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            THÔNG TIN BÀN
                        </h2>

                        <div class="space-y-2">
                            <div class="info-item">
                                <span class="info-label">Tên bàn:</span>
                                <span class="info-value">{{ $table->table_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Số bàn:</span>
                                <span class="info-value">{{ $table->table_number }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Giá giờ:</span>
                                <span class="info-value text-green-600">{{ number_format($table->getHourlyRate()) }}
                                    ₫/h</span>
                            </div>

                            @if ($table->currentBill)
                                <div class="border-t border-gray-200 pt-3 mt-2">
                                    <div class="info-item">
                                        <span class="info-label">Mã bill:</span>
                                        <span class="info-value">{{ $table->currentBill->bill_number }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Nhân viên:</span>
                                        <span
                                            class="info-value">{{ $table->currentBill->staff->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Thời gian mở:</span>
                                        <span class="info-value text-sm">
                                            {{ $table->currentBill->start_time ? $table->currentBill->start_time->format('H:i d/m/Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-3 mt-2">
                                    <div class="info-item">
                                        <span class="info-label">Tổng hiện tại:</span>
                                        <span class="info-value text-green-600 font-bold">
                                            {{ number_format(round($table->currentBill->final_amount)) }} ₫
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card info-section">
                        <h2 class="section-title">
                            <i class="fas fa-bolt text-amber-500"></i>
                            THAO TÁC NHANH
                        </h2>

                        <div class="action-buttons">
                            @if ($table->currentBill)
                                <!-- Xử lý bàn lẻ -->
                                @if ($table->currentBill->status === 'quick')
                                    <form action="{{ route('admin.bills.start-playing', $table->currentBill->id) }}"
                                        method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-primary">
                                            <i class="fas fa-play"></i>
                                            <span class="desktop-only">BẮT ĐẦU TÍNH GIỜ</span>
                                            <span class="mobile-only">BẮT ĐẦU GIỜ</span>
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.payments.payment-page', $table->currentBill->id) }}"
                                        class="action-btn action-btn-success">
                                        <i class="fas fa-credit-card"></i>
                                        <span class="desktop-only">THANH TOÁN BÀN LẺ</span>
                                        <span class="mobile-only">THANH TOÁN</span>
                                    </a>
                                @else
                                    <!-- Thanh toán -->
                                    <a href="{{ route('admin.payments.payment-page', $table->currentBill->id) }}"
                                        class="action-btn action-btn-primary">
                                        <i class="fas fa-credit-card"></i>
                                        <span class="desktop-only">THANH TOÁN</span>
                                        <span class="mobile-only">THANH TOÁN</span>
                                    </a>

                                    <!-- Cập nhật tổng -->
                                    <form action="{{ route('admin.bills.update-total', $table->currentBill->id) }}"
                                        method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-secondary">
                                            <i class="fas fa-sync-alt"></i>
                                            <span class="desktop-only">CẬP NHẬT TỔNG</span>
                                            <span class="mobile-only">CẬP NHẬT</span>
                                        </button>
                                    </form>

                                    <!-- NÚT BẬT GIỜ THƯỜNG KHI COMBO HẾT -->
                                    @if (isset($timeInfo['needs_switch']) &&
                                            $timeInfo['needs_switch'] &&
                                            isset($timeInfo['mode']) &&
                                            $timeInfo['mode'] === 'combo_ended')
                                        <form
                                            action="{{ route('admin.bills.switch-regular', $table->currentBill->id) }}"
                                            method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-success">
                                                <i class="fas fa-play-circle"></i>
                                                <span class="desktop-only">BẬT GIỜ THƯỜNG</span>
                                                <span class="mobile-only">BẬT GIỜ</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Chuyển bàn -->
                                    <a href="{{ route('admin.bills.transfer-form', $table->currentBill->id) }}"
                                        class="action-btn action-btn-secondary">
                                        <i class="fas fa-exchange-alt"></i>
                                        <span class="desktop-only">CHUYỂN BÀN</span>
                                        <span class="mobile-only">CHUYỂN BÀN</span>
                                    </a>
                                @endif
                            @else
                                <!-- Tạo bill mới -->
                                <button onclick="showCreateBillModal()" class="action-btn action-btn-primary">
                                    <i class="fas fa-plus"></i>
                                    <span class="desktop-only">TẠO HÓA ĐƠN TÍNH GIỜ</span>
                                    <span class="mobile-only">TẠO HÓA ĐƠN</span>
                                </button>

                                <!-- Tạo bàn lẻ -->
                                <button onclick="showQuickBillModal()" class="action-btn action-btn-warning">
                                    <i class="fas fa-bolt"></i>
                                    <span class="desktop-only">TẠO BÀN LẺ</span>
                                    <span class="mobile-only">BÀN LẺ</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Info -->
                    @if ($table->currentBill && $table->currentBill->user)
                        <div class="card info-section">
                            <h2 class="section-title">
                                <i class="fas fa-user text-purple-500"></i>
                                THÔNG TIN KHÁCH HÀNG
                            </h2>

                            <div class="space-y-2">
                                <div class="info-item">
                                    <span class="info-label">Tên khách hàng</span>
                                    <span class="info-value">{{ $table->currentBill->user->name }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Số điện thoại</span>
                                    <span class="info-value">{{ $table->currentBill->user->phone }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Loại khách</span>
                                    <span class="info-value">
                                        @php
                                            $customerType = $table->currentBill->user->customer_type ?? 'Mới';
                                            $typeClass = match ($customerType) {
                                                'VIP' => 'text-red-600 font-bold',
                                                'Thân thiết' => 'text-purple-600 font-semibold',
                                                'Quay lại' => 'text-blue-600',
                                                default => 'text-gray-600',
                                            };
                                        @endphp
                                        <span class="{{ $typeClass }}">{{ $customerType }}</span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Số lần đến</span>
                                    <span class="info-value font-bold text-green-600">
                                        {{ $table->currentBill->user->total_visits ?? 1 }} lần
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tổng chi tiêu</span>
                                    <span class="info-value font-bold text-orange-600">
                                        {{ number_format($table->currentBill->user->total_spent ?? 0) }} ₫
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mobile Panel Tabs -->
        <div class="mobile-panel-tabs">
            <button class="mobile-tab active" data-panel="productsPanel">
                <i class="fas fa-clock"></i>
                <span>Thời gian</span>
            </button>
            <button class="mobile-tab" data-panel="billPanel">
                <i class="fas fa-receipt"></i>
                <span>Hóa đơn</span>
            </button>
            <button class="mobile-tab" data-panel="infoPanel">
                <i class="fas fa-info-circle"></i>
                <span>Thông tin</span>
            </button>
        </div>
    </div>

    <!-- Create Bill Modal -->
    <div id="createBillModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tạo Hóa Đơn Tính Giờ</h3>
                <button class="close-btn" onclick="hideCreateBillModal()">&times;</button>
            </div>
            <form id="createBillForm" action="{{ route('admin.bills.create') }}" method="POST">
                @csrf
                <input type="hidden" name="table_id" value="{{ $table->id }}">

                <div class="form-group">
                    <label class="form-label">Số điện thoại khách hàng</label>
                    <input type="text" name="user_phone" class="form-input" placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label class="form-label">Tên khách hàng</label>
                    <input type="text" name="user_name" class="form-input" placeholder="Nhập tên khách hàng">
                </div>

                <div class="form-group">
                    <label class="form-label">Số lượng khách</label>
                    <input type="number" name="guest_count" class="form-input" value="1" min="1"
                        required>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideCreateBillModal()"
                        class="action-btn action-btn-secondary flex-1">
                        Hủy
                    </button>
                    <button type="submit" class="action-btn action-btn-primary flex-1">
                        <i class="fas fa-plus"></i> Tạo Hóa Đơn
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Bill Modal -->
    <div id="quickBillModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tạo Bàn Lẻ</h3>
                <button class="close-btn" onclick="hideQuickBillModal()">&times;</button>
            </div>
            <form id="quickBillForm" action="{{ route('admin.bills.quick-create') }}" method="POST">
                @csrf
                <input type="hidden" name="table_id" value="{{ $table->id }}">

                <div class="form-group">
                    <label class="form-label">Số điện thoại khách hàng (tùy chọn)</label>
                    <input type="text" name="user_phone" class="form-input" placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label class="form-label">Tên khách hàng (tùy chọn)</label>
                    <input type="text" name="user_name" class="form-input" placeholder="Nhập tên khách hàng">
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="hideQuickBillModal()"
                        class="action-btn action-btn-secondary flex-1">
                        Hủy
                    </button>
                    <button type="submit" class="action-btn action-btn-warning flex-1">
                        <i class="fas fa-bolt"></i> Tạo Bàn Lẻ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Xác nhận xóa</h3>
                <button class="close-btn" onclick="hideDeleteConfirmModal()">&times;</button>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                </div>
                <p id="deleteConfirmMessage" class="text-center text-gray-700 mb-6">
                    Bạn có chắc muốn xóa sản phẩm này khỏi hóa đơn?
                </p>
                <div class="flex gap-3">
                    <button type="button" onclick="hideDeleteConfirmModal()"
                        class="action-btn action-btn-secondary flex-1">
                        <i class="fas fa-times mr-2"></i> Hủy
                    </button>
                    <button id="confirmDeleteBtn" class="action-btn action-btn-danger flex-1">
                        <i class="fas fa-trash mr-2"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Server data với giá trị mặc định
        const currentMode = '{{ $timeInfo['mode'] ?? 'none' }}';
        const currentBillId = {{ $table->currentBill->id ?? 'null' }};
        const needsSwitch = {{ isset($timeInfo['needs_switch']) && $timeInfo['needs_switch'] ? 'true' : 'false' }};

        let refreshInterval = null;
        let currentDeleteForm = null;

        // Toast Notification System
        function showToast(message, type = 'info', duration = 5000) {
            const toastContainer = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

            // Set icon based on type
            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';
            if (type === 'warning') icon = 'exclamation-triangle';

            toast.innerHTML = `
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
            `;

            toastContainer.appendChild(toast);

            // Show toast with animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            // Auto hide after duration
            setTimeout(() => {
                hideToast(toast);
            }, duration);

            // Click to dismiss
            toast.addEventListener('click', () => {
                hideToast(toast);
            });
        }

        function hideToast(toast) {
            toast.classList.remove('show');
            toast.classList.add('hide');

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }

        // Loading Overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('active');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }

        // Mobile panel navigation
        function setupMobilePanels() {
            const mobileTabs = document.querySelectorAll('.mobile-tab');
            const panels = document.querySelectorAll('.panel');

            mobileTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const panelId = this.getAttribute('data-panel');

                    // Remove active class from all tabs and panels
                    mobileTabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.remove('active'));

                    // Add active class to clicked tab and corresponding panel
                    this.classList.add('active');
                    document.getElementById(panelId).classList.add('active');

                    // Add animation effect
                    document.getElementById(panelId).classList.add('fade-in');
                    setTimeout(() => {
                        document.getElementById(panelId).classList.remove('fade-in');
                    }, 500);
                });
            });
        }

        // Format functions
        function pad(n) {
            return n.toString().padStart(2, '0');
        }

        // Modal functions with animations
        function showCreateBillModal() {
            const modal = document.getElementById('createBillModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function hideCreateBillModal() {
            const modal = document.getElementById('createBillModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function showQuickBillModal() {
            const modal = document.getElementById('quickBillModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function hideQuickBillModal() {
            const modal = document.getElementById('quickBillModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Delete Confirmation Modal
        function showDeleteConfirmModal(productName, form) {
            currentDeleteForm = form;
            const modal = document.getElementById('deleteConfirmModal');
            const message = document.getElementById('deleteConfirmMessage');

            message.innerHTML = `Bạn có chắc muốn xóa sản phẩm <strong>"${productName}"</strong> khỏi hóa đơn?`;

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Add animation to icon
            const icon = modal.querySelector('.fa-exclamation-triangle');
            icon.classList.add('delete-confirm-icon');
        }

        function hideDeleteConfirmModal() {
            const modal = document.getElementById('deleteConfirmModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            currentDeleteForm = null;

            // Remove animation
            const icon = modal.querySelector('.fa-exclamation-triangle');
            icon.classList.remove('delete-confirm-icon');
        }

        function confirmDelete() {
            if (currentDeleteForm) {
                // Show loading state
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                const originalHtml = confirmBtn.innerHTML;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xóa...';
                confirmBtn.disabled = true;

                // Submit form after a small delay for better UX
                setTimeout(() => {
                    currentDeleteForm.submit();
                }, 500);
            }
        }

        // Setup delete confirmation
        function setupDeleteConfirmations() {
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-product-btn')) {
                    e.preventDefault();
                    const form = e.target.closest('form');
                    const productName = e.target.getAttribute('data-product-name') ||
                        e.target.closest('tr').querySelector('td:first-child').textContent.trim();

                    showDeleteConfirmModal(productName, form);
                }
            });

            // Confirm delete button event
            document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);
        }

        // Tab functionality with animations
        function setupTabs() {
            const tabs = document.querySelectorAll('.tab');
            const productsList = document.getElementById('productsList');
            const combosList = document.getElementById('combosList');
            const searchBox = document.getElementById('productSearch');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));

                    // Add active class to clicked tab
                    tab.classList.add('active');

                    // Show/hide lists with animation
                    const tabName = tab.getAttribute('data-tab');
                    if (tabName === 'products') {
                        productsList.style.display = 'block';
                        combosList.style.display = 'none';
                        searchBox.placeholder = 'Tìm kiếm sản phẩm...';

                        // Add animation
                        productsList.classList.add('fade-in');
                        setTimeout(() => {
                            productsList.classList.remove('fade-in');
                        }, 500);
                    } else {
                        productsList.style.display = 'none';
                        combosList.style.display = 'block';
                        searchBox.placeholder = 'Tìm kiếm combo...';

                        // Add animation
                        combosList.classList.add('fade-in');
                        setTimeout(() => {
                            combosList.classList.remove('fade-in');
                        }, 500);
                    }

                    // Reset search
                    searchBox.value = '';
                    filterProducts(searchBox.value);
                });
            });
        }

        // Search functionality
        function setupSearch() {
            const searchBox = document.getElementById('productSearch');

            searchBox.addEventListener('input', function() {
                filterProducts(this.value);
            });
        }

        function filterProducts(searchTerm) {
            const activeTab = document.querySelector('.tab.active').getAttribute('data-tab');
            const list = activeTab === 'products' ?
                document.getElementById('productsList') :
                document.getElementById('combosList');

            const rows = list.querySelectorAll('tbody tr');
            const term = searchTerm.toLowerCase();

            rows.forEach(row => {
                const name = row.querySelector('.font-medium').textContent.toLowerCase();
                if (name.includes(term)) {
                    row.style.display = '';
                    row.classList.add('fade-in');
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Quantity controls functionality with animations
        function setupQuantityControls() {
            // Plus buttons
            document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const comboId = this.getAttribute('data-combo-id');
                    const input = productId ?
                        document.querySelector(`.product-quantity[data-product-id="${productId}"]`) :
                        document.querySelector(`.combo-quantity[data-combo-id="${comboId}"]`);

                    if (input) {
                        const max = parseInt(input.getAttribute('max')) || 999;
                        const currentValue = parseInt(input.value) || 1;
                        if (currentValue < max) {
                            input.value = currentValue + 1;

                            // Add animation effect
                            this.classList.add('bounce');
                            setTimeout(() => {
                                this.classList.remove('bounce');
                            }, 500);
                        } else {
                            showToast('Đã đạt số lượng tối đa', 'warning', 3000);
                        }
                    }
                });
            });

            // Minus buttons
            document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const comboId = this.getAttribute('data-combo-id');
                    const input = productId ?
                        document.querySelector(`.product-quantity[data-product-id="${productId}"]`) :
                        document.querySelector(`.combo-quantity[data-combo-id="${comboId}"]`);

                    if (input) {
                        const currentValue = parseInt(input.value) || 1;
                        if (currentValue > 1) {
                            input.value = currentValue - 1;

                            // Add animation effect
                            this.classList.add('bounce');
                            setTimeout(() => {
                                this.classList.remove('bounce');
                            }, 500);
                        } else {
                            showToast('Số lượng tối thiểu là 1', 'warning', 3000);
                        }
                    }
                });
            });

            // Input validation
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const min = parseInt(this.getAttribute('min')) || 1;
                    const max = parseInt(this.getAttribute('max')) || 999;
                    let value = parseInt(this.value) || min;

                    if (value < min) {
                        value = min;
                        showToast('Số lượng tối thiểu là ' + min, 'warning', 3000);
                    }
                    if (value > max) {
                        value = max;
                        showToast('Số lượng tối đa là ' + max, 'warning', 3000);
                    }

                    this.value = value;
                });
            });
        }

        // Get quantity from input
        function getQuantity(inputClass, itemId) {
            const input = document.querySelector(
                `${inputClass}[data-${inputClass.includes('product') ? 'product' : 'combo'}-id="${itemId}"]`);
            return input ? parseInt(input.value) || 1 : 1;
        }

        // Add product to bill with improved UX
        function addProductToBill(productId, quantity = null) {
            @if ($table->currentBill)
                const finalQuantity = quantity || getQuantity('.product-quantity', productId);
                const button = event.target;
                const originalText = button.innerHTML;

                // Show loading state
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

                // Add pulse effect to button
                button.classList.add('pulse');

                fetch('{{ route('admin.bills.add-product', $table->currentBill->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: finalQuantity
                    })
                }).then(response => {
                    if (response.ok) {
                        showToast('Đã thêm sản phẩm vào hóa đơn', 'success', 3000);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast('Có lỗi xảy ra khi thêm sản phẩm', 'error', 5000);
                        button.innerHTML = originalText;
                        button.disabled = false;
                        button.classList.remove('pulse');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    showToast('Có lỗi xảy ra khi thêm sản phẩm', 'error', 5000);
                    button.innerHTML = originalText;
                    button.disabled = false;
                    button.classList.remove('pulse');
                });
            @else
                showToast('Vui lòng tạo hóa đơn trước khi thêm sản phẩm', 'warning', 5000);
            @endif
        }

        // Add combo to bill with improved UX
        function addComboToBill(comboId, quantity = null) {
            @if ($table->currentBill)
                const finalQuantity = quantity || getQuantity('.combo-quantity', comboId);
                const button = event.target;
                const originalText = button.innerHTML;

                // Show loading state
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;

                // Add pulse effect to button
                button.classList.add('pulse');

                fetch('{{ route('admin.bills.add-combo', $table->currentBill->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        combo_id: comboId,
                        quantity: finalQuantity
                    })
                }).then(response => {
                    if (response.ok) {
                        showToast('Đã thêm combo vào hóa đơn', 'success', 3000);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast('Có lỗi xảy ra khi thêm combo', 'error', 5000);
                        button.innerHTML = originalText;
                        button.disabled = false;
                        button.classList.remove('pulse');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    showToast('Có lỗi xảy ra khi thêm combo', 'error', 5000);
                    button.innerHTML = originalText;
                    button.disabled = false;
                    button.classList.remove('pulse');
                });
            @else
                showToast('Vui lòng tạo hóa đơn trước khi thêm combo', 'warning', 5000);
            @endif
        }

        // Hàm kiểm tra và cập nhật trạng thái combo từ server
        async function checkComboStatus() {
            if ((currentMode === 'combo' || needsSwitch) && currentBillId) {
                try {
                    const response = await fetch(`/admin/bills/${currentBillId}/check-combo-status`);
                    const data = await response.json();

                    // Cập nhật thời gian còn lại
                    if (data.has_active_combo && data.remaining_minutes !== undefined) {
                        const remainingMinutes = data.remaining_minutes;
                        const hours = Math.floor(remainingMinutes / 60);
                        const minutes = remainingMinutes % 60;

                        document.getElementById('remainingTimeDisplay').textContent =
                            `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

                        // Cập nhật progress bar
                        if (data.total_minutes && data.total_minutes > 0) {
                            const progressPercent = Math.min(100, ((data.total_minutes - remainingMinutes) / data
                                .total_minutes) * 100);
                            document.getElementById('progressBar').style.width = `${progressPercent}%`;
                            document.getElementById('progressText').textContent = `${Math.round(progressPercent)}%`;
                        }

                        // Hiển thị cảnh báo nếu sắp hết thời gian
                        updateWarningBanners(remainingMinutes);
                    }

                    // Nếu combo đã hết, reload trang để hiển thị nút bật giờ thường
                    if (data.needs_switch && !data.has_active_combo) {
                        showToast('Combo đã kết thúc, vui lòng bật giờ thường', 'info', 5000);
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                } catch (error) {
                    console.error('Error checking combo status:', error);
                }
            }
        }

        // Cập nhật cảnh báo combo sắp hết
        function updateWarningBanners(remainingMinutes) {
            // Xóa các banner cũ
            removeExistingBanners();

            // Hiển thị cảnh báo phù hợp
            if (remainingMinutes <= 5 && remainingMinutes > 0) {
                showCriticalWarningBanner(remainingMinutes);
            } else if (remainingMinutes <= 10 && remainingMinutes > 5) {
                showWarningBanner(remainingMinutes);
            }
        }

        // Xóa các banner cảnh báo cũ
        function removeExistingBanners() {
            const warningBanner = document.querySelector('.warning-banner');
            const criticalBanner = document.querySelector('.critical-warning-banner');

            if (warningBanner) warningBanner.remove();
            if (criticalBanner) criticalBanner.remove();
        }

        // Hiển thị cảnh báo thường (5-10 phút)
        function showWarningBanner(remainingMinutes) {
            const timeTrackingCard = document.querySelector('.card');
            const warningBanner = document.createElement('div');
            warningBanner.className = 'warning-banner fade-in';
            warningBanner.innerHTML = `
                <div class="warning-banner-content">
                    <i class="fas fa-exclamation-triangle text-amber-500"></i>
                    <div class="warning-banner-text">
                        <div class="warning-banner-title">COMBO SẮP HẾT THỜI GIAN!</div>
                        <div class="warning-banner-description">
                            Chỉ còn <strong>${remainingMinutes} phút</strong> trong combo. 
                            Chuẩn bị chuyển sang giờ thường.
                        </div>
                    </div>
                </div>
            `;

            insertBanner(warningBanner);
        }

        // Hiển thị cảnh báo khẩn cấp (0-5 phút)
        function showCriticalWarningBanner(remainingMinutes) {
            const timeTrackingCard = document.querySelector('.card');
            const criticalBanner = document.createElement('div');
            criticalBanner.className = 'critical-warning-banner fade-in';
            criticalBanner.innerHTML = `
                <div class="critical-warning-content">
                    <div class="critical-warning-info">
                        <i class="fas fa-exclamation-circle text-white text-xl"></i>
                        <div class="critical-warning-text">
                            <div class="critical-warning-title">CẢNH BÁO: COMBO SẮP HẾT!</div>
                            <div class="critical-warning-description">
                                Chỉ còn <strong>${remainingMinutes} phút</strong>. 
                                Bạn hãy thao tác để chuyển tiếp trạng thái 
                            </div>
                        </div>
                    </div>
                </div>
            `;

            insertBanner(criticalBanner);
        }

        // Chèn banner vào đúng vị trí
        function insertBanner(banner) {
            const timeTrackingCard = document.querySelector('.card');
            const progressContainer = document.querySelector('.progress-container');

            if (progressContainer) {
                progressContainer.parentNode.insertBefore(banner, progressContainer.nextSibling);
            } else {
                const timeTracking = document.querySelector('.time-tracking');
                if (timeTracking) {
                    timeTracking.parentNode.insertBefore(banner, timeTracking.nextSibling);
                } else {
                    timeTrackingCard.appendChild(banner);
                }
            }
        }

        // Event listeners for buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Setup mobile panels
            setupMobilePanels();

            // Product buttons
            document.querySelectorAll('.add-product-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addProductToBill(productId);
                });
            });

            // Combo buttons
            document.querySelectorAll('.add-combo-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const comboId = this.getAttribute('data-combo-id');
                    addComboToBill(comboId);
                });
            });

            // Setup delete confirmations
            setupDeleteConfirmations();

            // Kiểm tra trạng thái combo định kỳ (mỗi 10 giây)
            if (currentMode === 'combo' || needsSwitch) {
                setInterval(checkComboStatus, 10000); // Kiểm tra mỗi 10 giây
                checkComboStatus(); // Kiểm tra ngay khi load
            }

            // Setup tabs and search functionality
            setupTabs();
            setupSearch();
            setupQuantityControls();

            // Close modals when clicking outside
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        if (this.id === 'createBillModal') {
                            hideCreateBillModal();
                        } else if (this.id === 'quickBillModal') {
                            hideQuickBillModal();
                        } else if (this.id === 'deleteConfirmModal') {
                            hideDeleteConfirmModal();
                        }
                    }
                });
            });

            // Add animation to page load
            document.querySelectorAll('.card').forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in');
            });
        });

        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
</body>

</html>