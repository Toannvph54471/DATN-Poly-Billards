<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chuyển Bàn - {{ $bill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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

        .table-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .table-card {
            background: #ffffff;
            border-radius: 6px;
            border: 2px solid #1a202c;
            padding: 0.75rem;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .table-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color: #374151;
        }

        .table-card.selected {
            border-color: #10b981;
            background: #f0fdf4;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .table-number {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.25rem;
        }

        .table-name {
            font-size: 0.7rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .table-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            width: 100%;
        }

        .detail-item {
            text-align: center;
        }

        .detail-label {
            font-size: 0.6rem;
            color: #64748b;
            margin-bottom: 0.125rem;
        }

        .detail-value {
            font-size: 0.7rem;
            font-weight: 600;
            color: #1a202c;
        }

        .table-status {
            position: absolute;
            top: 4px;
            right: 4px;
            padding: 0.15rem 0.4rem;
            border-radius: 8px;
            font-size: 0.55rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-available {
            background: #10b981;
            color: white;
        }

        .selection-badge {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #10b981;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .table-card.selected .selection-badge {
            opacity: 1;
        }

        .rate-comparison {
            background: #f8fafc;
            border-radius: 4px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border: 1px solid #e2e8f0;
            width: 100%;
        }

        .comparison-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.2rem 0;
            font-size: 0.6rem;
        }

        .comparison-row:not(:last-child) {
            border-bottom: 1px solid #e2e8f0;
        }

        .rate-change {
            display: inline-flex;
            align-items: center;
            gap: 0.1rem;
            padding: 0.15rem 0.3rem;
            border-radius: 3px;
            font-size: 0.55rem;
            font-weight: 600;
        }

        .rate-up {
            background: #fef3c7;
            color: #92400e;
        }

        .rate-down {
            background: #dcfce7;
            color: #166534;
        }

        .rate-same {
            background: #e0e7ff;
            color: #3730a3;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #64748b;
            grid-column: 1 / -1;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }

        /* Responsive */
        @media (max-width: 1536px) {
            .table-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        @media (max-width: 1280px) {
            .table-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .table-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .table-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }
            
            .table-card {
                padding: 0.5rem;
                min-height: 90px;
            }
            
            .table-number {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .table-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.tables.detail', $bill->table_id) }}" 
                           class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Quay lại
                        </a>
                        <div class="border-l border-gray-300 h-6"></div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Chuyển Bàn</h1>
                            <p class="text-sm text-gray-600 mt-1">
                                Hóa đơn: {{ $bill->bill_number }} • Bàn: {{ $bill->table->table_name }} ({{ $bill->table->table_number }})
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-clock mr-1"></i>
                            ĐANG SỬ DỤNG
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Current Bill Info -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-receipt text-blue-600 mr-2"></i>
                        Thông tin hóa đơn hiện tại
                    </h2>
                    <div class="text-xs text-gray-500">
                        Mở lúc: {{ $bill->start_time->format('H:i d/m/Y') }}
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-xs text-gray-600 mb-1">Số hóa đơn</div>
                        <div class="text-sm font-bold text-blue-600">{{ $bill->bill_number }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-600 mb-1">Bàn hiện tại</div>
                        <div class="text-sm font-bold text-gray-900">
                            {{ $bill->table->table_name }} ({{ $bill->table->table_number }})
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-600 mb-1">Khách hàng</div>
                        <div class="text-sm font-bold text-gray-900">
                            {{ $bill->user->name ?? 'Khách vãng lai' }}
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-600 mb-1">Giá giờ</div>
                        <div class="text-sm font-bold text-green-600">
                            {{ number_format($bill->table->tableRate->hourly_rate ?? 0) }} ₫/h
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transfer Section -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-exchange-alt text-green-600 mr-2"></i>
                        Chọn bàn đích để chuyển
                    </h2>
                    <div class="text-xs text-gray-500">
                        {{ $availableTables->count() }} bàn có sẵn
                    </div>
                </div>

                <form action="{{ route('admin.bills.transfer') }}" method="POST" id="transferForm">
                    @csrf
                    <input type="hidden" name="bill_id" value="{{ $bill->id }}">
                    <input type="hidden" name="target_table_id" id="targetTableId">

                    @if ($availableTables->count() > 0)
                        <!-- Table Grid - 6 bàn một hàng -->
                        <div class="table-grid" id="tableGrid">
                            @foreach ($availableTables as $table)
                                <div class="table-card" 
                                     data-table-id="{{ $table->id }}"
                                     data-table-number="{{ $table->table_number }}"
                                     data-table-name="{{ $table->table_name }}"
                                     data-hourly-rate="{{ $table->tableRate->hourly_rate ?? 0 }}"
                                     data-rate-name="{{ $table->tableRate->name ?? 'Không xác định' }}"
                                     data-capacity="{{ $table->capacity }}"
                                     data-current-hourly-rate="{{ $bill->table->tableRate->hourly_rate ?? 0 }}"
                                     data-current-rate-name="{{ $bill->table->tableRate->name ?? 'Không xác định' }}">
                                    
                                    <div class="selection-badge">
                                        <i class="fas fa-check"></i>
                                    </div>

                                    <div class="table-status status-available">
                                        Trống
                                    </div>

                                    <div class="table-number">{{ $table->table_number }}</div>
                                    <div class="table-name">{{ $table->table_name }}</div>

                                    <div class="table-details">
                                        <div class="detail-item">
                                            <div class="detail-label">Sức chứa</div>
                                            <div class="detail-value">{{ $table->capacity }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Giá giờ</div>
                                            <div class="detail-value">{{ number_format($table->tableRate->hourly_rate ?? 0) }} ₫</div>
                                        </div>
                                    </div>

                                    <!-- Rate Comparison (hidden by default) -->
                                    <div class="rate-comparison" style="display: none;">
                                        <div class="comparison-row">
                                            <span class="text-gray-600">Loại:</span>
                                            <span class="font-medium">{{ $table->tableRate->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="comparison-row">
                                            <span class="text-gray-600">Thay đổi:</span>
                                            <span class="rate-change" id="rateChange{{ $table->id }}">
                                                <!-- Will be populated by JavaScript -->
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Selected Table Info -->
                        <div id="selectedTableInfo" class="mt-6 p-4 bg-blue-50 rounded border border-blue-200" style="display: none;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-blue-900 mb-2">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Bàn đã chọn
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                        <div>
                                            <span class="text-blue-600 font-medium">Bàn:</span>
                                            <span id="selectedTableNumber" class="ml-1 font-semibold"></span>
                                        </div>
                                        <div>
                                            <span class="text-blue-600 font-medium">Loại:</span>
                                            <span id="selectedRateName" class="ml-1 font-semibold"></span>
                                        </div>
                                        <div>
                                            <span class="text-blue-600 font-medium">Giá:</span>
                                            <span id="selectedHourlyRate" class="ml-1 font-semibold"></span>
                                        </div>
                                    </div>
                                </div>
                                <div id="rateWarning" class="bg-yellow-100 border border-yellow-400 rounded px-3 py-1" style="display: none;">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-1 text-xs"></i>
                                        <span class="text-yellow-800 font-medium text-xs">Khác loại</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.tables.detail', $bill->table_id) }}" 
                               class="px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-1"></i>
                                Hủy
                            </a>
                            <button type="submit" 
                                    id="submitBtn"
                                    class="px-4 py-2 bg-green-600 text-white rounded text-sm font-medium hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                <i class="fas fa-exchange-alt mr-1"></i>
                                Xác nhận
                            </button>
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="empty-state">
                            <i class="fas fa-table"></i>
                            <h3 class="text-base font-medium text-gray-900 mb-2">Không có bàn trống</h3>
                            <p class="text-sm text-gray-500 mb-4">Tất cả các bàn đang được sử dụng hoặc bảo trì</p>
                            <a href="{{ route('admin.tables.detail', $bill->table_id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Quay lại
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Important Notes -->
            <div class="bg-blue-50 rounded border border-blue-200 p-4 mt-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Lưu ý quan trọng</h3>
                        <ul class="space-y-1 text-blue-800 text-xs">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-1 text-xs"></i>
                                <span>Tất cả sản phẩm, combo và thời gian chơi sẽ được chuyển sang bàn mới</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-1 text-xs"></i>
                                <span>Bàn hiện tại sẽ được giải phóng và có thể sử dụng cho khách khác</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-1 text-xs"></i>
                                <span>Lịch sử chuyển bàn sẽ được ghi lại trong hệ thống</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableCards = document.querySelectorAll('.table-card');
            const targetTableIdInput = document.getElementById('targetTableId');
            const submitBtn = document.getElementById('submitBtn');
            const selectedTableInfo = document.getElementById('selectedTableInfo');
            const rateWarning = document.getElementById('rateWarning');
            const transferForm = document.getElementById('transferForm');

            let selectedTable = null;

            // Format currency
            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' ₫';
            }

            // Calculate rate difference
            function calculateRateDifference(currentRate, targetRate) {
                const difference = targetRate - currentRate;
                const percentage = currentRate > 0 ? ((difference / currentRate) * 100) : 0;
                
                return {
                    difference: difference,
                    percentage: percentage,
                    isHigher: difference > 0,
                    isLower: difference < 0,
                    isSame: difference === 0
                };
            }

            // Update rate comparison display
            function updateRateComparison(card, currentRate, targetRate) {
                const comparison = calculateRateDifference(currentRate, targetRate);
                const rateChangeElement = card.querySelector('.rate-change');
                const rateComparison = card.querySelector('.rate-comparison');

                let html = '';
                if (comparison.isHigher) {
                    html = `<span class="rate-up">
                        <i class="fas fa-arrow-up"></i>
                        +${formatCurrency(comparison.difference)}
                    </span>`;
                } else if (comparison.isLower) {
                    html = `<span class="rate-down">
                        <i class="fas fa-arrow-down"></i>
                        ${formatCurrency(comparison.difference)}
                    </span>`;
                } else {
                    html = `<span class="rate-same">
                        <i class="fas fa-equals"></i>
                        Không đổi
                    </span>`;
                }

                rateChangeElement.innerHTML = html;
                rateComparison.style.display = 'block';
            }

            // Handle table selection
            tableCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selection from all cards
                    tableCards.forEach(c => {
                        c.classList.remove('selected');
                        c.querySelector('.rate-comparison').style.display = 'none';
                    });

                    // Add selection to clicked card
                    this.classList.add('selected');
                    
                    // Show rate comparison
                    const currentRate = parseFloat(this.dataset.currentHourlyRate);
                    const targetRate = parseFloat(this.dataset.hourlyRate);
                    updateRateComparison(this, currentRate, targetRate);

                    // Update selected table info
                    selectedTable = {
                        id: this.dataset.tableId,
                        number: this.dataset.tableNumber,
                        name: this.dataset.tableName,
                        rateName: this.dataset.rateName,
                        hourlyRate: targetRate,
                        currentRateName: this.dataset.currentRateName
                    };

                    document.getElementById('selectedTableNumber').textContent = 
                        `${this.dataset.tableNumber} - ${this.dataset.tableName}`;
                    document.getElementById('selectedRateName').textContent = this.dataset.rateName;
                    document.getElementById('selectedHourlyRate').textContent = formatCurrency(targetRate) + '/h';

                    // Show/hide rate warning
                    if (this.dataset.rateName !== this.dataset.currentRateName) {
                        rateWarning.style.display = 'block';
                    } else {
                        rateWarning.style.display = 'none';
                    }

                    // Update hidden input and enable submit button
                    targetTableIdInput.value = this.dataset.tableId;
                    submitBtn.disabled = false;

                    // Show selected table info
                    selectedTableInfo.style.display = 'block';
                });
            });

            // Form submission
            transferForm.addEventListener('submit', function(e) {
                if (!selectedTable) {
                    e.preventDefault();
                    alert('Vui lòng chọn bàn đích');
                    return;
                }

                // Confirm if rates are different
                if (selectedTable.rateName !== selectedTable.currentRateName) {
                    if (!confirm(
                        `Bàn đích (${selectedTable.rateName}) khác loại với bàn hiện tại (${selectedTable.currentRateName}).\n\nGiá giờ chơi sẽ thay đổi. Bạn có chắc chắn muốn chuyển bàn?`
                    )) {
                        e.preventDefault();
                        return;
                    }
                }

                // Show loading
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Đang chuyển...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>

</html>