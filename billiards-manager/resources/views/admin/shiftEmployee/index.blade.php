@extends('admin.layouts.app')

@section('title', 'Quản Lý Phân Ca')

@section('styles')
    <style>
        .schedule-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .schedule-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        .schedule-table td {
            border-bottom: 1px solid #f3f4f6;
        }

        .shift-cell {
            min-height: 80px;
            padding: 8px;
            border-right: 1px solid #f3f4f6;
            vertical-align: top;
            cursor: pointer;
            transition: all 0.2s;
        }

        .shift-badge {
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 12px;
            display: block;
            border: 2px solid transparent;
            transition: all 0.2s;
        }

        .shift-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .shift-time {
            font-size: 10px;
            opacity: 0.8;
            margin-top: 2px;
        }

        .add-shift-btn {
            color: #6b7280;
            border: 2px dashed #d1d5db;
            padding: 12px 8px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: #f9fafb;
        }

        .add-shift-btn:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #eff6ff;
        }

        .employee-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .shift-option {
            border: 2px solid transparent;
            transition: all 0.2s;
        }

        .shift-option.active {
            border-color: #3b82f6 !important;
            transform: scale(1.02);
        }

        .save-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .cell-unsaved {
            background-color: #fef3cd !important;
            position: relative;
        }

        .cell-unsaved::after {
            content: "●";
            color: #f59e0b;
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 8px;
        }

        /* Đảm bảo màu sắc cho các ca */
        .bg-blue-100 {
            background-color: #dbeafe;
        }

        .bg-orange-100 {
            background-color: #ffedd5;
        }

        .bg-purple-100 {
            background-color: #f3e8ff;
        }

        .bg-gray-100 {
            background-color: #f3f4f6;
        }

        .text-blue-800 {
            color: #1e40af;
        }

        .text-orange-800 {
            color: #9a3412;
        }

        .text-purple-800 {
            color: #6b21a8;
        }

        .text-gray-800 {
            color: #374151;
        }

        .border-blue-200 {
            border-color: #bfdbfe;
        }

        .border-orange-200 {
            border-color: #fed7aa;
        }

        .border-purple-200 {
            border-color: #e9d5ff;
        }

        .border-gray-200 {
            border-color: #e5e7eb;
        }
    </style>
@endsection

@section('content')
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Đang lưu dữ liệu...</span>
            </div>
        </div>
    </div>

    <!-- Save Indicator -->
    <div class="save-indicator" id="saveIndicator">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="saveMessage">Đã lưu thành công!</span>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản Lý Phân Ca</h1>
        <p class="text-gray-600">Phân công ca làm việc cho nhân viên theo tuần</p>
    </div>

    <!-- Week Navigation -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button class="p-2 hover:bg-gray-100 rounded-lg transition" id="prev-week">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h3 class="text-lg font-semibold" id="week-display">
                    Tuần {{ $weekStart->format('d/m') }} - {{ $weekStart->copy()->addDays(6)->format('d/m/Y') }}
                </h3>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition" id="next-week">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" id="current-week">
                    Tuần này
                </button>
            </div>
            <div class="flex space-x-2">
                <button onclick="window.history.back()"
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-gray-700 flex items-center">
                   <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    onclick="saveAllChanges()" id="saveAllBtn">
                    <i class="fas fa-save mr-2"></i>Lưu Tất Cả Thay Đổi
                </button>
            </div>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="schedule-table rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium">Nhân viên</th>
                        @foreach ($weekDays as $day)
                            <th class="px-4 py-3 text-center text-sm font-medium">
                                {{ $day['day_name'] }}<br>{{ $day['date'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white" id="scheduleBody">
                    @foreach ($employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium">
                                <div class="flex items-center">
                                    <div class="employee-avatar bg-blue-100 text-blue-600 mr-3">
                                        {{ substr($employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $employee->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $employee->position ?? 'Nhân viên' }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            @foreach ($weekDays as $day)
                                @php
                                    $shift = $employee->employeeShifts->first(function ($s) use ($day) {
                                        return $s->shift_date->format('Y-m-d') === $day['full_date'];
                                    });
                                @endphp
                                <td class="shift-cell text-center" data-employee="{{ $employee->id }}"
                                    data-date="{{ $day['full_date'] }}" onclick="openShiftModal(this)">

                                    @if ($shift && $shift->shift)
                                        <div class="shift-badge {{ $shift->color_class }}"
                                            data-shift-id="{{ $shift->shift_id }}">
                                            <div class="font-semibold">{{ $shift->shift->name }}</div>
                                            <div class="shift-time">
                                                {{ $shift->shift->start_time->format('H:i') }} -
                                                {{ $shift->shift->end_time->format('H:i') }}
                                            </div>
                                            <div class="text-xs mt-1 text-gray-500">
                                                @switch($shift->status)
                                                    @case('scheduled')
                                                        Đã lên lịch
                                                    @break

                                                    @case('active')
                                                        Đang làm
                                                    @break

                                                    @case('completed')
                                                        Hoàn thành
                                                    @break

                                                    @default
                                                        Đã hủy
                                                @endswitch
                                            </div>
                                        </div>
                                    @else
                                        <div class="add-shift-btn">
                                            <i class="fas fa-plus mr-1"></i> Thêm ca
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    @if ($employees->isEmpty())
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-users text-3xl mb-2 block"></i>
                                Không có nhân viên nào
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Shift Legend -->
    <div class="bg-white rounded-xl shadow-sm p-4 mt-6">
        <h3 class="text-sm font-semibold mb-3">Chú thích ca làm việc:</h3>
        <div class="flex flex-wrap gap-4">
            @foreach ($shifts as $shift)
                <div class="flex items-center">
                    <div class="w-4 h-4 {{ $shift->color_class }} rounded mr-2"></div>
                    <span class="text-sm">{{ $shift->code }} - {{ $shift->name }}
                        ({{ $shift->start_time }}-{{ $shift->end_time }})
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Shift Selection Modal -->
    <div id="shiftModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-96 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">Chọn Ca Làm Việc</h3>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Nhân viên & Ngày</label>
                <div id="modal-info" class="text-sm text-gray-600 p-2 bg-gray-50 rounded"></div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Chọn ca</label>
                <div class="grid grid-cols-1 gap-2" id="shift-options">
                    @foreach ($shifts as $shift)
                        <button type="button"
                            class="shift-option {{ $shift->color_class }} p-3 rounded-lg text-left hover:scale-105 transition transform"
                            data-shift-id="{{ $shift->id }}">
                            <div class="font-semibold">{{ $shift->code }} - {{ $shift->name }}</div>
                            <div class="text-xs opacity-75">{{ $shift->start_time }} - {{ $shift->end_time }}</div>
                        </button>
                    @endforeach
                    <button type="button"
                        class="shift-option bg-gray-100 text-gray-600 p-3 rounded-lg text-left hover:scale-105 transition transform"
                        data-shift-id="">
                        <div class="font-semibold">Xóa ca</div>
                        <div class="text-xs opacity-75">Không phân ca này</div>
                    </button>
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-4 border-t">
                <button type="button" onclick="closeShiftModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Hủy
                </button>
                <button type="button" onclick="saveShift()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Lưu Thay Đổi
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentWeekStart = new Date('{{ $weekStart->format('Y-m-d') }}');
        let selectedCell = null;
        let selectedShiftId = null;
        let pendingChanges = new Map();

        document.addEventListener('DOMContentLoaded', function() {
            // Week navigation
            document.getElementById('prev-week').addEventListener('click', function() {
                navigateWeek(-7);
            });

            document.getElementById('next-week').addEventListener('click', function() {
                navigateWeek(7);
            });

            document.getElementById('current-week').addEventListener('click', function() {
                currentWeekStart = new Date();
                loadWeekSchedule();
            });

            // Shift option selection
            document.querySelectorAll('.shift-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.shift-option').forEach(opt => {
                        opt.classList.remove('active');
                    });
                    this.classList.add('active');
                    selectedShiftId = this.dataset.shiftId;
                });
            });

            // Update save button state
            updateSaveButton();
        });

        function navigateWeek(days) {
            if (pendingChanges.size > 0 && !confirm('Bạn có thay đổi chưa lưu. Tiếp tục?')) {
                return;
            }
            currentWeekStart.setDate(currentWeekStart.getDate() + days);
            loadWeekSchedule();
        }

        function loadWeekSchedule() {
            const weekStartStr = currentWeekStart.toISOString().split('T')[0];
            window.location.href = `{{ route('admin.shiftEmployee.index') }}?week_start=${weekStartStr}`;
        }

        function openShiftModal(cell) {
            selectedCell = cell;
            const employeeId = cell.dataset.employee;
            const date = cell.dataset.date;
            const employeeName = cell.closest('tr').querySelector('.font-medium').textContent;

            const existingShift = cell.querySelector('.shift-badge');
            const currentShiftId = existingShift ? existingShift.dataset.shiftId : null;

            document.getElementById('modal-info').innerHTML = `
                <strong>${employeeName}</strong><br>
                Ngày: ${formatDate(date)}
            `;

            document.getElementById('shiftModal').classList.remove('hidden');
            selectedShiftId = currentShiftId;

            // Reset và highlight option hiện tại
            document.querySelectorAll('.shift-option').forEach(opt => {
                opt.classList.remove('active');
                if (opt.dataset.shiftId === currentShiftId) {
                    opt.classList.add('active');
                }
            });
        }

        function closeShiftModal() {
            document.getElementById('shiftModal').classList.add('hidden');
            selectedCell = null;
            selectedShiftId = null;
        }

        function saveShift() {
            if (selectedShiftId === null && !confirm('Bạn có chắc muốn xóa ca làm việc này?')) {
                return;
            }

            const employeeId = selectedCell.dataset.employee;
            const date = selectedCell.dataset.date;
            const key = `${employeeId}-${date}`;

            // Lưu thay đổi
            pendingChanges.set(key, {
                employee_id: parseInt(employeeId),
                shift_date: date,
                shift_id: selectedShiftId ? parseInt(selectedShiftId) : null
            });

            // Cập nhật UI
            updateCellUI(selectedCell, selectedShiftId);
            updateSaveButton();

            showSaveIndicator('Thay đổi đã được lưu tạm');
            closeShiftModal();
        }

        function updateCellUI(cell, shiftId) {
            if (shiftId) {
                const shift = {!! $shifts->toJson() !!}.find(s => s.id == shiftId);
                if (shift) {
                    cell.innerHTML = `
                        <div class="shift-badge ${shift.color_class}" data-shift-id="${shiftId}">
                            <div class="font-semibold">${shift.name}</div>
                            <div class="shift-time">${shift.start_time} - ${shift.end_time}</div>
                            <div class="text-xs mt-1 text-yellow-600">Chưa lưu</div>
                        </div>
                    `;
                }
            } else {
                cell.innerHTML = `
                    <div class="add-shift-btn">
                        <i class="fas fa-plus mr-1"></i> Thêm ca
                    </div>
                `;
            }

            cell.classList.add('cell-unsaved');
        }

        function updateSaveButton() {
            const saveBtn = document.getElementById('saveAllBtn');
            if (pendingChanges.size > 0) {
                saveBtn.classList.remove('border-gray-300', 'hover:bg-gray-50');
                saveBtn.classList.add('border-yellow-400', 'bg-yellow-50', 'hover:bg-yellow-100');
                saveBtn.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i>Lưu ${pendingChanges.size} Thay Đổi`;
            } else {
                saveBtn.classList.remove('border-yellow-400', 'bg-yellow-50', 'hover:bg-yellow-100');
                saveBtn.classList.add('border-gray-300', 'hover:bg-gray-50');
                saveBtn.innerHTML = `<i class="fas fa-save mr-2"></i>Lưu Tất Cả Thay Đổi`;
            }
        }

        async function saveAllChanges() {
            if (pendingChanges.size === 0) {
                Swal.fire('Thông báo', 'Không có thay đổi nào để lưu', 'info');
                return;
            }

            showLoading(true);

            try {
                const assignments = Array.from(pendingChanges.values());

                const response = await fetch('{{ route('admin.shiftEmployee.bulkSchedule') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        assignments: assignments
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showSaveIndicator('Đã lưu tất cả thay đổi thành công!');
                    pendingChanges.clear();
                    updateSaveButton();

                    // Remove unsaved indicators
                    document.querySelectorAll('.cell-unsaved').forEach(cell => {
                        cell.classList.remove('cell-unsaved');
                    });

                    // Reload after delay
                    setTimeout(() => {
                        loadWeekSchedule();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Lỗi khi lưu dữ liệu');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Lỗi!', error.message || 'Có lỗi xảy ra khi lưu thay đổi', 'error');
            } finally {
                showLoading(false);
            }
        }

        function showLoading(show) {
            document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
        }

        function showSaveIndicator(message) {
            const indicator = document.getElementById('saveIndicator');
            const messageEl = document.getElementById('saveMessage');

            messageEl.textContent = message;
            indicator.style.display = 'block';

            setTimeout(() => {
                indicator.style.display = 'none';
            }, 3000);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', {
                weekday: 'long',
                year: 'numeric',
                month: 'numeric',
                day: 'numeric'
            });
        }
    </script>
@endsection
