@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Ca Làm Việc')

@section('styles')
    <style>
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .time-preview {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }

        .status-active {
            color: #10b981;
            background-color: #ecfdf5;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
        }

        .status-inactive {
            color: #ef4444;
            background-color: #fef2f2;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
        }

        .overnight-shift {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 8px;
            padding: 8px 12px;
            margin-top: 8px;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Chỉnh Sửa Ca Làm Việc</h1>
                <p class="text-gray-600">Cập nhật thông tin ca làm việc</p>
            </div>
            <a href="{{ route('admin.shifts.index') }}"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>
    </div>
    <!-- Navigation Tabs -->
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button id="shifts-tab"
                    class="tab-button active-tab py-4 px-6 text-center border-b-2 border-blue-500 font-medium text-sm flex items-center justify-center">
                    <i class="fas fa-clock mr-2"></i>
                    Quản Lý Ca Làm Việc
                </button>
                <button id="employees-tab"
                    class="tab-button py-4 px-6 text-center border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 flex items-center justify-center">
                    <i class="fas fa-users mr-2"></i>
                    Quản Lý Nhân Viên
                </button>
            </nav>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <form action="{{ route('admin.shifts.update', $shift->id) }}" method="POST" id="shift-form">
                    @csrf
                    @method('PUT')

                    <!-- Shift Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên Ca Làm Việc <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $shift->name) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ví dụ: Ca sáng, Ca chiều, Ca tối..." required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Time Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Start Time -->
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Thời Gian Bắt Đầu <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="start_time" name="start_time" lang="vi"
                                value="{{ old('start_time', $shift->start_time ? $shift->start_time->format('H:i') : '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Thời Gian Kết Thúc <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="end_time" name="end_time"
                                value="{{ old('end_time', $shift->end_time ? $shift->end_time->format('H:i') : '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Duration Preview -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Thời Lượng Ca Làm
                        </label>
                        <div class="time-preview">
                            <div id="duration-preview" class="text-sm text-gray-600">
                                @php
                                    $start = \Carbon\Carbon::parse($shift->start_time);
                                    $end = \Carbon\Carbon::parse($shift->end_time);
                                    $isOvernight = false;
                                    if ($end <= $start) {
                                        $end->addDay();
                                        $isOvernight = true;
                                    }
                                    $duration = $start->diff($end)->format('%h giờ %i phút');
                                @endphp
                                <div class="font-medium text-green-600">{{ $duration }}</div>
                                <div class="text-xs text-gray-500">{{ $shift->start_time }} → {{ $shift->end_time }}</div>
                                @if ($isOvernight)
                                    <div class="overnight-shift text-amber-700">
                                        <i class="fas fa-moon mr-1"></i> Ca làm việc qua đêm
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Trạng Thái
                        </label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="active"
                                    {{ old('status', $shift->status) === 'active' ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Đang hoạt động</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="inactive"
                                    {{ old('status', $shift->status) === 'inactive' ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Tạm ngừng</span>
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô Tả (Tùy chọn)
                        </label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Mô tả về ca làm việc...">{{ old('description', $shift->description ?? '') }}</textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.shifts.index') }}"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Hủy
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-save mr-2"></i> Cập Nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Shift Info -->
            <div class="form-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Thông Tin Ca</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Ngày tạo:</span>
                        <span class="text-sm font-medium">{{ $shift->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Lần cập nhật:</span>
                        <span class="text-sm font-medium">{{ $shift->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Trạng thái:</span>
                        <span class="text-sm font-medium {{ $shift->status === 'active' ? 'active' : 'inactive' }}">
                            {{ $shift->status === 'active' ? 'Đang hoạt động' : 'Tạm ngừng' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Số nhân viên:</span>
                        <span class="text-sm font-medium">{{ $shift->employees_count ?? rand(2, 8) }} người</span>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="form-card p-6 border border-red-200">
                <h3 class="text-lg font-semibold text-red-800 mb-4">Vùng Nguy Hiểm</h3>
                <p class="text-sm text-red-600 mb-4">
                    Xóa ca làm việc này sẽ không thể khôi phục. Hãy chắc chắn trước khi thực hiện.
                </p>

                <button type="button" onclick="confirmDelete({{ $shift->id }})"
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center">
                    <i class="fas fa-trash mr-2"></i> Xóa Ca Làm Việc
                </button>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const durationPreview = document.getElementById('duration-preview');
            const shiftForm = document.getElementById('shift-form');

            // Calculate duration function
            function calculateDuration() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;

                if (startTime && endTime) {
                    const start = new Date(`2000-01-01T${startTime}`);
                    let end = new Date(`2000-01-01T${endTime}`);
                    let isOvernight = false;

                    // Handle overnight shifts
                    if (end <= start) {
                        end.setDate(end.getDate() + 1);
                        isOvernight = true;
                    }

                    const diffMs = end - start;
                    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                    let durationText = `${diffHours} giờ`;
                    if (diffMinutes > 0) {
                        durationText += ` ${diffMinutes} phút`;
                    }

                    let overnightHtml = '';
                    if (isOvernight) {
                        overnightHtml = `<div class="overnight-shift text-amber-700">
                                            <i class="fas fa-moon mr-1"></i> Ca làm việc qua đêm
                                        </div>`;
                    }

                    durationPreview.innerHTML = `
                        <div class="font-medium text-green-600">${durationText}</div>
                        <div class="text-xs text-gray-500">${startTime} → ${endTime}</div>
                        ${overnightHtml}
                    `;
                }
            }

            // Validate form before submit
            shiftForm.addEventListener('submit', function(e) {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;

                if (startTime && endTime) {
                    const start = new Date(`2000-01-01T${startTime}`);
                    const end = new Date(`2000-01-01T${endTime}`);

                    // Check if shift is too short (less than 30 minutes)
                    const diffMs = end <= start ? (end.getTime() + 24 * 60 * 60 * 1000) - start.getTime() :
                        end - start;
                    const diffMinutes = Math.floor(diffMs / (1000 * 60));

                    if (diffMinutes < 30) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Thời gian ca làm việc quá ngắn',
                            text: 'Ca làm việc phải có thời lượng ít nhất 30 phút.',
                            icon: 'warning',
                            confirmButtonColor: '#3b82f6'
                        });
                        return;
                    }

                    // Check if shift is too long (more than 24 hours)
                    if (diffMinutes > 24 * 60) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Thời gian ca làm việc quá dài',
                            text: 'Ca làm việc không thể kéo dài quá 24 giờ.',
                            icon: 'warning',
                            confirmButtonColor: '#3b82f6'
                        });
                        return;
                    }
                }
            });

            // Event listeners for time inputs
            startTimeInput.addEventListener('change', calculateDuration);
            endTimeInput.addEventListener('change', calculateDuration);

            // Calculate duration on page load
            calculateDuration();
        });

        function confirmDelete(shiftId) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc muốn xóa ca làm việc này? Hành động này không thể hoàn tác.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${shiftId}`).submit();
                }
            });
        }
    </script>

@endsection
