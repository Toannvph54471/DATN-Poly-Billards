@extends('admin.layouts.app')

@section('title', 'Quản Lý Phân Ca')

@section('styles')
    <style>
        .schedule-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .schedule-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .schedule-table td {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s ease;
        }

        .schedule-table tr:hover td:not(.past-cell) {
            background-color: #f8fafc;
        }

        .shift-cell {
            min-height: 90px;
            padding: 10px 8px;
            border-right: 1px solid #f1f5f9;
            vertical-align: top;
            position: relative;
        }

        .shift-cell.editable {
            cursor: pointer;
        }

        .shift-cell.editable:hover {
            background-color: #f1f5f9 !important;
        }

        .shift-badge {
            padding: 8px 6px;
            border-radius: 6px;
            font-size: 11px;
            display: block;
            border: 1px solid;
            transition: all 0.2s ease;
            background: white;
        }

        .shift-cell.editable .shift-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .shift-time {
            font-size: 9px;
            opacity: 0.8;
            margin-top: 3px;
            font-weight: 500;
        }

        .add-shift-btn {
            color: #64748b;
            border: 1px dashed #cbd5e1;
            padding: 12px 6px;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 100%;
            min-height: 70px;
        }

        .shift-cell.editable .add-shift-btn:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #eff6ff;
        }

        .employee-avatar {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            background: #3b82f6;
            color: white;
        }

        .shift-option {
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            background: white;
        }

        .shift-option:hover {
            border-color: #3b82f6;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }

        .shift-option.active {
            border-color: #3b82f6 !important;
            background: #eff6ff;
            box-shadow: 0 0 0 1px #3b82f6;
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
            font-size: 14px;
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

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .cell-unsaved {
            background: #fef3c7 !important;
            position: relative;
        }

        .cell-unsaved::after {
            content: "●";
            color: #f59e0b;
            position: absolute;
            top: 4px;
            right: 4px;
            font-size: 8px;
        }

        .week-nav-btn {
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            font-size: 14px;
        }

        .week-nav-btn:hover {
            background: #2563eb;
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.2s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .today-cell {
            background: #eff6ff;
            position: relative;
        }

        .today-cell::before {
            content: 'Hôm nay';
            position: absolute;
            top: 2px;
            right: 2px;
            background: #3b82f6;
            color: white;
            font-size: 7px;
            padding: 1px 3px;
            border-radius: 3px;
        }

        .past-cell {
            background: #f8fafc;
            opacity: 0.7;
        }

        .past-cell .shift-badge,
        .past-cell .add-shift-btn {
            cursor: default;
        }

        /* Màu sắc cho các ca */
        .bg-blue-100 { background: #dbeafe; border-color: #93c5fd; }
        .bg-orange-100 { background: #ffedd5; border-color: #fdba74; }
        .bg-purple-100 { background: #f3e8ff; border-color: #d8b4fe; }
        .bg-gray-100 { background: #f3f4f6; border-color: #d1d5db; }
        
        .locked-cell {
            background: #f1f5f9;
            cursor: not-allowed !important;
            opacity: 0.9;
        }
        
        .locked-cell .shift-badge {
            border-color: #cbd5e1;
            background: #f8fafc;
        }
        
        .locked-icon {
            position: absolute;
            top: 2px;
            right: 2px;
            color: #64748b;
            font-size: 10px;
        }

        .text-blue-800 { color: #1e40af; }
        .text-orange-800 { color: #9a3412; }
        .text-purple-800 { color: #6b21a8; }
        .text-gray-800 { color: #374151; }

        /* Sticky first column */
        .sticky-column {
            position: sticky;
            left: 0;
            background: white;
            z-index: 5;
            border-right: 2px solid #e2e8f0;
        }

        /* Button styles */
        .btn-primary {
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: white;
            color: #475569;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-warning:hover {
            background: #d97706;
        }
    </style>
@endsection

@section('content')
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <div class="loading-spinner mx-auto mb-3"></div>
            <p class="text-gray-700 font-medium">Đang xử lý...</p>
        </div>
    </div>

    <!-- Save Indicator -->
    <div class="save-indicator" id="saveIndicator">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="saveMessage">Đã lưu thành công</span>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Quản Lý Phân Ca</h1>
        <p class="text-gray-600">Phân công ca làm việc cho nhân viên theo tuần</p>
    </div>

    <!-- Week Navigation -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <button class="week-nav-btn" id="prev-week">
                    <i class="fas fa-chevron-left mr-1"></i>Trước
                </button>
                
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-800" id="week-display">
                        Tuần {{ $weekStart->format('d/m') }} - {{ $weekStart->copy()->addDays(6)->format('d/m/Y') }}
                    </h3>
                    <p class="text-gray-500 text-sm">Tuần {{ $weekStart->weekOfYear }}, {{ $weekStart->year }}</p>
                </div>
                
                <button class="week-nav-btn" id="next-week">
                    Sau<i class="fas fa-chevron-right ml-1"></i>
                </button>
                
                <button class="btn-primary" id="current-week">
                    <i class="fas fa-calendar-day mr-1"></i>Tuần này
                </button>
            </div>
            
            <div class="flex space-x-2">
                <button onclick="window.history.back()" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </button>
                <button class="btn-warning" onclick="saveAllChanges()" id="saveAllBtn">
                    <i class="fas fa-save mr-1"></i>Lưu Thay Đổi
                </button>
            </div>
        </div>
    </div>

    <!-- Thông báo tuần đã qua -->
    @if($isPastWeek)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
            <div>
                <p class="text-yellow-800 font-medium text-sm">Tuần đã qua</p>
                <p class="text-yellow-700 text-xs">Không thể chỉnh sửa phân ca của tuần đã qua</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Schedule Table -->
    <div class="schedule-table mb-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 sticky-column">
                            Nhân viên
                        </th>
                        @foreach ($weekDays as $day)
                            <th class="px-3 py-3 text-center text-sm font-semibold text-gray-700"
                                style="min-width: 120px;">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs text-gray-500 font-normal">{{ $day['day_name'] }}</span>
                                    <span class="text-sm font-bold {{ $day['is_today'] ? 'text-blue-600' : 'text-gray-800' }}">
                                        {{ $day['date'] }}
                                    </span>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($employees as $employee)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap sticky-column">
                                <div class="flex items-center">
                                    <div class="employee-avatar mr-3">
                                        {{ substr($employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 text-sm">{{ $employee->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $employee->employee_code }}</div>
                                        <div class="text-xs text-gray-500">{{ $employee->position ?? 'Nhân viên' }}</div>
                                    </div>
                                </div>
                            </td>
                            
                                @foreach ($weekDays as $day)
                                @php
                                    $shift = $employee->employeeShifts->first(function ($s) use ($day) {
                                        return $s->shift_date->format('Y-m-d') === $day['full_date'];
                                    });
                                    $isPast = $day['is_past'] || $isPastWeek;
                                    $isToday = $day['is_today'];
                                    
                                    // Check if locked
                                    $isLocked = $shift && $shift->is_locked;
                                    
                                    // If locked, NOT editable. If past, NOT editable.
                                    $isEditable = !$isPast && !$isLocked;
                                @endphp
                                <td class="shift-cell text-center {{ $isToday ? 'today-cell' : '' }} {{ $isPast ? 'past-cell' : '' }} {{ $isLocked ? 'locked-cell' : '' }} {{ $isEditable ? 'editable' : '' }}" 
                                    data-employee="{{ $employee->id }}"
                                    data-date="{{ $day['full_date'] }}"
                                    data-past="{{ $isPast ? 'true' : 'false' }}"
                                    data-locked="{{ $isLocked ? 'true' : 'false' }}"
                                    onclick="{{ $isEditable ? 'openShiftModal(this)' : '' }}">

                                    @if ($shift && $shift->shift)
                                        <div class="shift-badge {{ $isLocked ? '' : $shift->color_class }}"
                                            data-shift-id="{{ $shift->shift_id }}">
                                            
                                            @if($isLocked)
                                                <i class="fas fa-lock locked-icon" title="Ca đã khóa (Đã check-in)"></i>
                                            @endif
                                            
                                            <div class="font-semibold">{{ $shift->shift->name }}</div>
                                            <div class="shift-time">
                                                {{ \Carbon\Carbon::parse($shift->shift->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($shift->shift->end_time)->format('H:i') }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="add-shift-btn">
                                            <i class="fas fa-plus text-sm mb-1"></i>
                                            <span class="font-medium">Thêm ca</span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    @if ($employees->isEmpty())
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-users text-2xl mb-2 block text-gray-300"></i>
                                <p class="font-medium">Không có nhân viên nào</p>
                                <p class="text-sm">Vui lòng thêm nhân viên trước khi phân ca</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Shift Legend -->
    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Chú thích ca làm việc</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ($shifts as $shift)
                <div class="flex items-center p-2 bg-gray-50 rounded border border-gray-200">
                    <div class="w-4 h-4 {{ $shift->color_class }} rounded mr-2 border"></div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800 text-xs">{{ $shift->code }}</div>
                        <div class="text-gray-600 text-xs">{{ $shift->name }}</div>
                        <div class="text-gray-500 text-xs">
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Shift Selection Modal -->
    <div id="shiftModal" class="fixed inset-0 modal-overlay flex items-center justify-center hidden z-50 p-4">
        <div class="modal-content w-full max-w-sm">
            <div class="p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Chọn Ca Làm Việc</h3>
                    <button onclick="closeShiftModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Thông tin</label>
                    <div id="modal-info" class="text-sm text-gray-600 p-2 bg-gray-50 rounded border"></div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Chọn ca</label>
                    <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto" id="shift-options">
                        @foreach ($shifts as $shift)
                            <button type="button"
                                class="shift-option {{ $shift->color_class }} p-3 rounded text-left"
                                data-shift-id="{{ $shift->id }}">
                                <div class="font-semibold text-sm">{{ $shift->code }} - {{ $shift->name }}</div>
                                <div class="text-xs text-gray-600 mt-1">
                                    {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                </div>
                            </button>
                        @endforeach
                        <button type="button"
                            class="shift-option bg-gray-100 text-gray-600 p-3 rounded text-left border border-dashed border-gray-300"
                            data-shift-id="">
                            <div class="font-semibold text-sm flex items-center">
                                <i class="fas fa-times mr-2 text-red-500"></i>
                                Xóa ca
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Không phân ca làm việc</div>
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2 pt-3 border-t">
                    <button type="button" onclick="closeShiftModal()" class="btn-secondary text-sm">
                        Hủy
                    </button>
                    <button type="button" onclick="saveShift()" class="btn-primary text-sm">
                        <i class="fas fa-check mr-1"></i>Xác nhận
                    </button>
                </div>
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
        let isPastWeek = {{ $isPastWeek ? 'true' : 'false' }};

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
                    selectShiftOption(this);
                });
            });

            updateSaveButton();
        });

        function navigateWeek(days) {
            if (pendingChanges.size > 0) {
                Swal.fire({
                    title: 'Thay đổi chưa lưu',
                    text: 'Bạn có thay đổi chưa lưu. Tiếp tục?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Tiếp tục',
                    cancelButtonText: 'Ở lại',
                }).then((result) => {
                    if (result.isConfirmed) {
                        currentWeekStart.setDate(currentWeekStart.getDate() + days);
                        loadWeekSchedule();
                    }
                });
                return;
            }
            currentWeekStart.setDate(currentWeekStart.getDate() + days);
            loadWeekSchedule();
        }

        function loadWeekSchedule() {
            showLoading(true);
            const weekStartStr = currentWeekStart.toISOString().split('T')[0];
            setTimeout(() => {
                window.location.href = `{{ route('admin.shiftEmployee.index') }}?week_start=${weekStartStr}`;
            }, 300);
        }

        function openShiftModal(cell) {
            if (cell.dataset.past === 'true' || isPastWeek) {
                return;
            }

            selectedCell = cell;
            const employeeId = cell.dataset.employee;
            const date = cell.dataset.date;
            const employeeName = cell.closest('tr').querySelector('.font-semibold').textContent;

            const existingShift = cell.querySelector('.shift-badge');
            const currentShiftId = existingShift ? existingShift.dataset.shiftId : null;

            document.getElementById('modal-info').innerHTML = `
                <strong>${employeeName}</strong><br>
                Ngày: ${formatDate(date)}
            `;

            const modal = document.getElementById('shiftModal');
            modal.classList.remove('hidden');
            selectedShiftId = currentShiftId;

            selectShiftOption(null, currentShiftId);
        }

        function selectShiftOption(optionElement, shiftId = null) {
            document.querySelectorAll('.shift-option').forEach(opt => {
                opt.classList.remove('active');
            });

            if (optionElement) {
                optionElement.classList.add('active');
                selectedShiftId = optionElement.dataset.shiftId;
            } else if (shiftId !== null) {
                const targetOption = document.querySelector(`.shift-option[data-shift-id="${shiftId}"]`);
                if (targetOption) {
                    targetOption.classList.add('active');
                    selectedShiftId = shiftId;
                }
            }
        }

        function closeShiftModal() {
            document.getElementById('shiftModal').classList.add('hidden');
            selectedCell = null;
            selectedShiftId = null;
        }

        function saveShift() {
            if (selectedShiftId === null && !confirm('Xóa ca làm việc này?')) {
                return;
            }

            const employeeId = selectedCell.dataset.employee;
            const date = selectedCell.dataset.date;
            const key = `${employeeId}-${date}`;

            pendingChanges.set(key, {
                employee_id: parseInt(employeeId),
                shift_date: date,
                shift_id: selectedShiftId ? parseInt(selectedShiftId) : null
            });

            updateCellUI(selectedCell, selectedShiftId);
            updateSaveButton();

            showSaveIndicator('Đã lưu tạm thay đổi');
            closeShiftModal();
        }

        function updateCellUI(cell, shiftId) {
            cell.classList.add('cell-unsaved');
            
            if (shiftId) {
                const shift = {!! $shifts->toJson() !!}.find(s => s.id == shiftId);
                if (shift) {
                    cell.innerHTML = `
                        <div class="shift-badge ${shift.color_class}" data-shift-id="${shiftId}">
                            <div class="font-semibold">${shift.name}</div>
                            <div class="shift-time">${shift.start_time} - ${shift.end_time}</div>
                            <div class="text-xs text-yellow-600 mt-1">Chưa lưu</div>
                        </div>
                    `;
                }
            } else {
                cell.innerHTML = `
                    <div class="add-shift-btn">
                        <i class="fas fa-plus text-sm mb-1"></i>
                        <span class="font-medium">Đã xóa ca</span>
                        <div class="text-xs text-yellow-600 mt-1">Chưa lưu</div>
                    </div>
                `;
            }
        }

        function updateSaveButton() {
            const saveBtn = document.getElementById('saveAllBtn');
            if (pendingChanges.size > 0) {
                saveBtn.innerHTML = `<i class="fas fa-save mr-1"></i>Lưu ${pendingChanges.size} Thay Đổi`;
                saveBtn.classList.remove('btn-warning');
                saveBtn.classList.add('btn-primary');
            } else {
                saveBtn.innerHTML = `<i class="fas fa-save mr-1"></i>Lưu Thay Đổi`;
                saveBtn.classList.remove('btn-primary');
                saveBtn.classList.add('btn-warning');
            }
        }

        async function saveAllChanges() {
            if (pendingChanges.size === 0) {
                showToast('Không có thay đổi để lưu', 'info');
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
                    showSaveIndicator('Đã lưu thành công');
                    pendingChanges.clear();
                    updateSaveButton();

                    document.querySelectorAll('.cell-unsaved').forEach(cell => {
                        cell.classList.remove('cell-unsaved');
                    });

                    setTimeout(() => {
                        loadWeekSchedule();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Lỗi khi lưu');
                }
            } catch (error) {
                showToast(error.message, 'error');
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
            }, 2000);
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-3 rounded text-white font-medium z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
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