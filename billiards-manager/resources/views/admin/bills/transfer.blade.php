@extends('admin.layouts.app')

@section('title', 'Chuyển Bàn - F&B Management')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Chuyển Bàn</h1>
                <p class="text-sm text-gray-500">Hóa đơn: {{ $bill->bill_number }} • Bàn: {{ $bill->table->table_name }} ({{ $bill->table->table_number }})</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.tables.detail', $bill->table_id) }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <i class="fas fa-clock mr-1"></i>
                    ĐANG SỬ DỤNG
                </span>
            </div>
        </div>

        <!-- Current Bill Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-receipt text-blue-600 mr-2"></i>
                Thông tin hóa đơn hiện tại
            </h3>
            
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
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-exchange-alt text-green-600 mr-2"></i>
                        Chọn bàn đích để chuyển
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Chọn bàn trống để chuyển hóa đơn hiện tại</p>
                </div>
                <div class="text-sm text-gray-500">
                    {{ $availableTables->count() }} bàn có sẵn
                </div>
            </div>

            <form action="{{ route('admin.bills.transfer') }}" method="POST" id="transferForm">
                @csrf
                <input type="hidden" name="bill_id" value="{{ $bill->id }}">
                <input type="hidden" name="target_table_id" id="targetTableId">

                @if ($availableTables->count() > 0)
                    <!-- Grid bàn dạng ô vuông -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4" id="tableGrid">
                        @foreach ($availableTables as $table)
                            @php
                                $currentRate = $bill->table->tableRate->hourly_rate ?? 0;
                                $targetRate = $table->tableRate->hourly_rate ?? 0;
                                $rateDifference = $targetRate - $currentRate;
                                $isSameType = ($bill->table->tableRate->name ?? '') === ($table->tableRate->name ?? '');
                            @endphp
                            
                            <div class="table-card bg-white border-2 border-green-500 rounded-lg p-4 h-32 flex flex-col justify-between transition-all duration-300 hover:shadow-lg hover:scale-105 cursor-pointer relative"
                                 data-table-id="{{ $table->id }}"
                                 data-table-number="{{ $table->table_number }}"
                                 data-table-name="{{ $table->table_name }}"
                                 data-hourly-rate="{{ $targetRate }}"
                                 data-rate-name="{{ $table->tableRate->name ?? 'Không xác định' }}"
                                 data-capacity="{{ $table->capacity }}"
                                 data-current-hourly-rate="{{ $currentRate }}"
                                 data-current-rate-name="{{ $bill->table->tableRate->name ?? 'Không xác định' }}">
                                
                                <!-- Selection Badge -->
                                <div class="absolute top-2 left-2 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center opacity-0 transition-opacity duration-200">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>

                                <!-- Status Badge -->
                                <div class="absolute top-2 right-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Trống
                                    </span>
                                </div>

                                <!-- Header: Tên bàn -->
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $table->table_number }}</h4>
                                        <p class="text-xs text-gray-600">{{ $table->table_name }}</p>
                                    </div>
                                </div>

                                <!-- Thông tin giá -->
                                <div class="text-center">
                                    <div class="text-gray-900">
                                        <div class="text-sm font-mono font-bold">
                                            {{ number_format($targetRate) }} ₫
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">Giá giờ</div>
                                    </div>
                                </div>

                                <!-- Footer: Sức chứa và loại bàn -->
                                <div class="flex justify-between items-center text-xs text-gray-600">
                                    <span>{{ $table->capacity }} người</span>
                                    <span class="truncate max-w-[80px]" title="{{ $table->tableRate->name ?? 'N/A' }}">
                                        {{ $table->tableRate->name ?? 'N/A' }}
                                    </span>
                                </div>

                                <!-- Rate Comparison Tooltip -->
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                                    <div class="bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                        <div class="font-medium">So sánh giá</div>
                                        <div class="flex items-center gap-1 mt-1">
                                            @if($rateDifference > 0)
                                                <i class="fas fa-arrow-up text-yellow-400"></i>
                                                <span class="text-yellow-400">+{{ number_format($rateDifference) }} ₫</span>
                                            @elseif($rateDifference < 0)
                                                <i class="fas fa-arrow-down text-green-400"></i>
                                                <span class="text-green-400">{{ number_format($rateDifference) }} ₫</span>
                                            @else
                                                <i class="fas fa-equals text-blue-400"></i>
                                                <span class="text-blue-400">Không đổi</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="w-3 h-3 bg-gray-900 transform rotate-45 absolute -bottom-1 left-1/2 -translate-x-1/2"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Selected Table Info -->
                    <div id="selectedTableInfo" class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200 hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-blue-900 mb-2 flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Bàn đã chọn
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-blue-600 font-medium">Bàn:</span>
                                        <span id="selectedTableNumber" class="ml-2 font-semibold text-gray-900"></span>
                                    </div>
                                    <div>
                                        <span class="text-blue-600 font-medium">Loại:</span>
                                        <span id="selectedRateName" class="ml-2 font-semibold text-gray-900"></span>
                                    </div>
                                    <div>
                                        <span class="text-blue-600 font-medium">Giá:</span>
                                        <span id="selectedHourlyRate" class="ml-2 font-semibold text-gray-900"></span>
                                    </div>
                                </div>
                            </div>
                            <div id="rateWarning" class="bg-yellow-100 border border-yellow-400 rounded-lg px-3 py-2 hidden">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                    <span class="text-yellow-800 font-medium text-sm">Khác loại bàn</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.tables.detail', $bill->table_id) }}" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Hủy
                        </a>
                        <button type="submit" 
                                id="submitBtn"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                disabled>
                            <i class="fas fa-exchange-alt mr-2"></i>
                            Xác nhận chuyển bàn
                        </button>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="text-gray-400">
                            <i class="fas fa-table text-4xl mb-3"></i>
                            <p class="text-lg font-medium">Không có bàn trống</p>
                            <p class="text-sm mt-1">Tất cả các bàn đang được sử dụng hoặc bảo trì</p>
                            <a href="{{ route('admin.tables.detail', $bill->table_id) }}" 
                               class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Quay lại
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Important Notes -->
        <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3 text-lg"></i>
                <div>
                    <h3 class="text-base font-semibold text-blue-900 mb-3">Lưu ý quan trọng</h3>
                    <div class="space-y-2 text-blue-800 text-sm">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                            <span>Tất cả sản phẩm, combo và thời gian chơi sẽ được chuyển sang bàn mới</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                            <span>Bàn hiện tại sẽ được giải phóng và có thể sử dụng cho khách khác</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                            <span>Lịch sử chuyển bàn sẽ được ghi lại trong hệ thống</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableCards = document.querySelectorAll('.table-card');
            const targetTableIdInput = document.getElementById('targetTableId');
            const submitBtn = document.getElementById('submitBtn');
            const selectedTableInfo = document.getElementById('selectedTableInfo');
            const rateWarning = document.getElementById('rateWarning');

            let selectedTable = null;

            // Format currency
            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' ₫';
            }

            // Handle table selection
            tableCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selection from all cards
                    tableCards.forEach(c => {
                        c.classList.remove('selected');
                        c.style.backgroundColor = '';
                        c.style.borderColor = '#10B981'; // green-500
                        c.querySelector('.absolute.top-2.left-2').classList.add('opacity-0');
                    });

                    // Add selection to clicked card
                    this.classList.add('selected');
                    this.style.backgroundColor = '#f0fdf4'; // green-50
                    this.style.borderColor = '#059669'; // green-600
                    this.querySelector('.absolute.top-2.left-2').classList.remove('opacity-0');

                    // Update selected table info
                    selectedTable = {
                        id: this.dataset.tableId,
                        number: this.dataset.tableNumber,
                        name: this.dataset.tableName,
                        rateName: this.dataset.rateName,
                        hourlyRate: parseFloat(this.dataset.hourlyRate),
                        currentRateName: this.dataset.currentRateName
                    };

                    document.getElementById('selectedTableNumber').textContent = 
                        `${this.dataset.tableNumber} - ${this.dataset.tableName}`;
                    document.getElementById('selectedRateName').textContent = this.dataset.rateName;
                    document.getElementById('selectedHourlyRate').textContent = 
                        formatCurrency(parseFloat(this.dataset.hourlyRate)) + '/h';

                    // Show/hide rate warning
                    if (this.dataset.rateName !== this.dataset.currentRateName) {
                        rateWarning.classList.remove('hidden');
                    } else {
                        rateWarning.classList.add('hidden');
                    }

                    // Update hidden input and enable submit button
                    targetTableIdInput.value = this.dataset.tableId;
                    submitBtn.disabled = false;

                    // Show selected table info
                    selectedTableInfo.classList.remove('hidden');
                });
            });

            // Form submission
            document.getElementById('transferForm').addEventListener('submit', function(e) {
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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang chuyển...';
                submitBtn.disabled = true;
            });

            // Add hover effects for table cards
            tableCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('selected')) {
                        this.classList.add('group');
                    }
                });

                card.addEventListener('mouseleave', function() {
                    this.classList.remove('group');
                });
            });
        });
    </script>
@endsection