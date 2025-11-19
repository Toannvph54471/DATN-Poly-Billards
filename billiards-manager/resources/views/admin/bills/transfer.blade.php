<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chuyển Bàn - {{ $bill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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
            min-height: 100vh;
        }

        .app-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1.25rem;
            border-left: 4px solid #3b82f6;
        }

        .info-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
        }

        .info-value.bill-number {
            color: #3b82f6;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #374151;
            font-size: 1.125rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-select {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
            background: white;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Alert Styles */
        .alert {
            padding: 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border: 1px solid;
        }

        .alert-info {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1e40af;
        }

        .alert-warning {
            background: #fef3c7;
            border-color: #fcd34d;
            color: #92400e;
        }

        .alert-success {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        /* Table Comparison */
        .table-comparison {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table-comparison th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-comparison td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-comparison tr:last-child td {
            border-bottom: none;
        }

        .rate-difference {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .rate-higher {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .rate-lower {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .rate-same {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #a5b4fc;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            min-width: 160px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }

        /* Loading */
        .loading {
            position: relative;
            color: transparent;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .card {
                padding: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                min-width: auto;
                width: 100%;
            }

            .table-comparison {
                font-size: 0.875rem;
            }

            .table-comparison th,
            .table-comparison td {
                padding: 0.75rem 0.5rem;
            }
        }

        /* Status Badge */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .status-occupied {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Header -->
        <div class="header">
            <div class="table-info">
                <div class="table-title">
                    <a href="{{ route('admin.tables.detail', $bill->table_id) }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                    <div class="table-details">
                        <h1>Chuyển Bàn - {{ $bill->bill_number }}</h1>
                        <div class="table-meta">
                            <span>Bàn: {{ $bill->table->table_name }} ({{ $bill->table->table_number }})</span>
                            <span>•</span>
                            <span>Khách: {{ $bill->user->name ?? 'Khách vãng lai' }}</span>
                            <span>•</span>
                            <span class="status-badge status-occupied">ĐANG SỬ DỤNG</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Current Bill Information -->
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        THÔNG TIN HÓA ĐƠN HIỆN TẠI
                    </h2>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-receipt text-blue-500"></i>
                            Số hóa đơn
                        </div>
                        <div class="info-value bill-number">{{ $bill->bill_number }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-table text-green-500"></i>
                            Bàn hiện tại
                        </div>
                        <div class="info-value">
                            {{ $bill->table->table_name }} ({{ $bill->table->table_number }})
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user text-purple-500"></i>
                            Khách hàng
                        </div>
                        <div class="info-value">{{ $bill->user->name ?? 'Khách vãng lai' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-clock text-orange-500"></i>
                            Thời gian mở
                        </div>
                        <div class="info-value">{{ $bill->start_time->format('H:i d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Transfer Form -->
            <div class="card">
                <div class="card-header">
                    <h2 class="section-title">
                        <i class="fas fa-exchange-alt text-green-500"></i>
                        CHUYỂN BÀN
                    </h2>
                </div>

                <form action="{{ route('admin.bills.transfer') }}" method="POST" id="transferForm">
                    @csrf
                    <input type="hidden" name="bill_id" value="{{ $bill->id }}">

                    <div class="form-group">
                        <label class="form-label" for="targetTableSelect">
                            <i class="fas fa-arrow-right text-blue-500"></i>
                            CHỌN BÀN ĐÍCH
                        </label>

                        @if ($availableTables->count() > 0)
                            <select name="target_table_id" id="targetTableSelect" class="form-select" required>
                                <option value="">-- Chọn bàn đích --</option>
                                @foreach ($availableTables as $table)
                                    <option value="{{ $table->id }}" data-rate="{{ $table->table_rate_id }}"
                                        data-rate-name="{{ $table->tableRate->name ?? 'Không xác định' }}"
                                        data-hourly-rate="{{ $table->tableRate->hourly_rate ?? 0 }}"
                                        data-current-rate="{{ $bill->table->table_rate_id }}"
                                        data-current-hourly-rate="{{ $bill->table->tableRate->hourly_rate ?? 0 }}">
                                        {{ $table->table_name }} ({{ $table->table_number }})
                                        - {{ $table->tableRate->name ?? 'Không xác định' }}
                                        ({{ number_format($table->tableRate->hourly_rate ?? 0) }} ₫/h)
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-table"></i>
                                <h3 class="text-lg font-medium mb-2">Không có bàn trống</h3>
                                <p class="text-sm">Tất cả các bàn đang được sử dụng hoặc bảo trì</p>
                            </div>
                        @endif
                    </div>

                    <!-- Rate Comparison -->
                    <div id="rateComparison" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-chart-line text-lg"></i>
                            <div>
                                <strong class="text-lg">SO SÁNH GIÁ GIỜ</strong>
                                <div id="comparisonDetails" class="mt-2"></div>
                            </div>
                        </div>

                        <table class="table-comparison">
                            <thead>
                                <tr>
                                    <th>LOẠI BÀN</th>
                                    <th>GIÁ GIỜ HIỆN TẠI</th>
                                    <th>GIÁ GIỜ BÀN ĐÍCH</th>
                                    <th>SO SÁNH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="currentRateName" class="font-semibold">-</td>
                                    <td id="currentHourlyRate" class="font-semibold text-blue-600">-</td>
                                    <td id="targetHourlyRate" class="font-semibold text-green-600">-</td>
                                    <td id="rateDifference">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Rate Warning -->
                    <div id="rateWarning" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                        <div>
                            <strong class="text-lg">CẢNH BÁO: KHÁC LOẠI BÀN</strong>
                            <p class="mt-1">Bàn đích khác loại với bàn hiện tại. Giá giờ chơi có thể thay đổi sau khi
                                chuyển bàn.</p>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle text-lg"></i>
                        <div>
                            <strong class="text-lg">LƯU Ý QUAN TRỌNG</strong>
                            <ul class="mt-2 space-y-2">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                    <span>Tất cả sản phẩm, combo và thời gian chơi sẽ được chuyển sang bàn mới</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                    <span>Bàn hiện tại sẽ được giải phóng và có thể sử dụng cho khách khác</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                    <span>Lịch sử chuyển bàn sẽ được ghi lại trong hệ thống</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('admin.tables.detail', $bill->table_id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            HỦY BỎ
                        </a>

                        @if ($availableTables->count() > 0)
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-exchange-alt"></i>
                                XÁC NHẬN CHUYỂN BÀN
                            </button>
                        @else
                            <button type="button" class="btn btn-primary" disabled>
                                <i class="fas fa-ban"></i>
                                KHÔNG THỂ CHUYỂN BÀN
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const targetTableSelect = document.getElementById('targetTableSelect');
            const rateComparison = document.getElementById('rateComparison');
            const rateWarning = document.getElementById('rateWarning');
            const submitBtn = document.getElementById('submitBtn');
            const transferForm = document.getElementById('transferForm');

            // Format currency
            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' ₫';
            }

            // Update rate comparison
            function updateRateComparison(selectedOption) {
                if (!selectedOption || selectedOption.value === '') {
                    rateComparison.style.display = 'none';
                    rateWarning.style.display = 'none';
                    return;
                }

                const currentRateId = selectedOption.getAttribute('data-current-rate');
                const targetRateId = selectedOption.getAttribute('data-rate');
                const currentHourlyRate = parseFloat(selectedOption.getAttribute('data-current-hourly-rate'));
                const targetHourlyRate = parseFloat(selectedOption.getAttribute('data-hourly-rate'));
                const currentRateName = '{{ $bill->table->tableRate->name ?? 'Không xác định' }}';
                const targetRateName = selectedOption.getAttribute('data-rate-name');

                // Update comparison table
                document.getElementById('currentRateName').textContent = currentRateName;
                document.getElementById('targetRateName').textContent = targetRateName;
                document.getElementById('currentHourlyRate').textContent = formatCurrency(currentHourlyRate) + '/h';
                document.getElementById('targetHourlyRate').textContent = formatCurrency(targetHourlyRate) + '/h';

                // Calculate and display difference
                const difference = targetHourlyRate - currentHourlyRate;
                const differenceElement = document.getElementById('rateDifference');

                let differenceHtml = '';
                if (difference > 0) {
                    differenceHtml = `<span class="rate-difference rate-higher">
                        <i class="fas fa-arrow-up"></i>
                        Cao hơn ${formatCurrency(difference)}/h
                    </span>`;
                } else if (difference < 0) {
                    differenceHtml = `<span class="rate-difference rate-lower">
                        <i class="fas fa-arrow-down"></i>
                        Thấp hơn ${formatCurrency(Math.abs(difference))}/h
                    </span>`;
                } else {
                    differenceHtml = `<span class="rate-difference rate-same">
                        <i class="fas fa-equals"></i>
                        Không thay đổi
                    </span>`;
                }

                differenceElement.innerHTML = differenceHtml;

                // Show/hide warning
                if (currentRateId !== targetRateId) {
                    rateWarning.style.display = 'flex';
                } else {
                    rateWarning.style.display = 'none';
                }

                // Show comparison
                rateComparison.style.display = 'block';
            }

            // Event listener for table selection
            targetTableSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                updateRateComparison(selectedOption);
            });

            // Form submission
            transferForm.addEventListener('submit', function(e) {
                const selectedTable = targetTableSelect.options[targetTableSelect.selectedIndex];

                if (!selectedTable || selectedTable.value === '') {
                    e.preventDefault();
                    alert('Vui lòng chọn bàn đích');
                    return;
                }

                const currentRateId = '{{ $bill->table->table_rate_id }}';
                const targetRateId = selectedTable.getAttribute('data-rate');

                // Confirm if rates are different
                if (currentRateId !== targetRateId) {
                    const currentRateName = '{{ $bill->table->tableRate->name ?? 'Không xác định' }}';
                    const targetRateName = selectedTable.getAttribute('data-rate-name');

                    if (!confirm(
                            `Bàn đích (${targetRateName}) khác loại với bàn hiện tại (${currentRateName}).\n\nGiá giờ chơi sẽ thay đổi. Bạn có chắc chắn muốn chuyển bàn?`
                        )) {
                        e.preventDefault();
                        return;
                    }
                }

                // Show loading
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ĐANG CHUYỂN BÀN...';
                submitBtn.disabled = true;
            });

            // Initialize if there's a selected value
            if (targetTableSelect.value) {
                const selectedOption = targetTableSelect.options[targetTableSelect.selectedIndex];
                updateRateComparison(selectedOption);
            }
        });
    </script>
</body>

</html>
