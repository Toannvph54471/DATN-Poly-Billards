@extends('admin.layouts.app')

@section('title', 'Thêm Ca Làm Việc Mới')

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
    </style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Thêm Ca Làm Việc Mới</h1>
                <p class="text-gray-600">Tạo ca làm việc mới cho nhân viên</p>
            </div>
            <a href="{{ route('admin.shifts.index') }}"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="form-card p-6">
                <form action="{{ route('admin.shifts.store') }}" method="POST">
                    @csrf

                    <!-- Shift Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên Ca Làm Việc <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
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
                            <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}"
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
                            <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}"
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
                                Vui lòng chọn thời gian bắt đầu và kết thúc
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
                                <input type="radio" name="status" value="1" checked
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Đang hoạt động</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="0"
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
                            placeholder="Mô tả về ca làm việc...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.shifts.index') }}"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Hủy
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-save mr-2"></i> Tạo Ca Làm Việc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Tips -->
            <div class="form-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Hướng Dẫn</h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-info text-blue-600 text-xs"></i>
                        </div>
                        <p class="text-sm text-gray-600">Tên ca nên ngắn gọn, dễ nhận biết</p>
                    </div>
                    <div class="flex items-start">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-info text-blue-600 text-xs"></i>
                        </div>
                        <p class="text-sm text-gray-600">Thời gian kết thúc có thể qua ngày hôm sau</p>
                    </div>
                    <div class="flex items-start">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-info text-blue-600 text-xs"></i>
                        </div>
                        <p class="text-sm text-gray-600">Ca tạm ngừng sẽ không được phân công</p>
                    </div>
                </div>
            </div>

            <!-- Common Shifts -->
            <div class="form-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ca Làm Phổ Biến</h3>
                <div class="space-y-3">
                    <button type="button"
                        class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition shift-template"
                        data-name="Ca sáng" data-start="07:00" data-end="15:00">
                        <div class="font-medium text-gray-900">Ca sáng</div>
                        <div class="text-sm text-gray-500">07:00 - 15:00 (8 giờ)</div>
                    </button>
                    <button type="button"
                        class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition shift-template"
                        data-name="Ca chiều" data-start="15:00" data-end="23:00">
                        <div class="font-medium text-gray-900">Ca chiều</div>
                        <div class="text-sm text-gray-500">15:00 - 23:00 (8 giờ)</div>
                    </button>
                    <button type="button"
                        class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition shift-template"
                        data-name="Ca tối" data-start="23:00" data-end="07:00">
                        <div class="font-medium text-gray-900">Ca tối</div>
                        <div class="text-sm text-gray-500">23:00 - 07:00 (8 giờ)</div>
                    </button>
                </div>
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
            const shiftTemplates = document.querySelectorAll('.shift-template');

            // Calculate duration function
            function calculateDuration() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;

                if (startTime && endTime) {
                    const start = new Date(`2000-01-01T${startTime}`);
                    let end = new Date(`2000-01-01T${endTime}`);

                    // Handle overnight shifts
                    if (end <= start) {
                        end.setDate(end.getDate() + 1);
                    }

                    const diffMs = end - start;
                    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                    let durationText = `${diffHours} giờ`;
                    if (diffMinutes > 0) {
                        durationText += ` ${diffMinutes} phút`;
                    }

                    durationPreview.innerHTML = `
                    <div class="font-medium text-green-600">${durationText}</div>
                    <div class="text-xs text-gray-500">${startTime} → ${endTime}</div>
                `;
                } else {
                    durationPreview.textContent = 'Vui lòng chọn thời gian bắt đầu và kết thúc';
                }
            }

            // Event listeners for time inputs
            startTimeInput.addEventListener('change', calculateDuration);
            endTimeInput.addEventListener('change', calculateDuration);

            // Shift template functionality
            shiftTemplates.forEach(template => {
                template.addEventListener('click', function() {
                    const name = this.getAttribute('data-name');
                    const start = this.getAttribute('data-start');
                    const end = this.getAttribute('data-end');

                    document.getElementById('name').value = name;
                    document.getElementById('start_time').value = start;
                    document.getElementById('end_time').value = end;

                    calculateDuration();

                    // Highlight selected template
                    shiftTemplates.forEach(t => t.classList.remove('border-blue-500',
                    'bg-blue-50'));
                    this.classList.add('border-blue-500', 'bg-blue-50');
                });
            });

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const startTime = document.getElementById('start_time').value;
                const endTime = document.getElementById('end_time').value;

                if (!name) {
                    e.preventDefault();
                    Swal.fire('Lỗi', 'Vui lòng nhập tên ca làm việc', 'error');
                    return;
                }

                if (!startTime || !endTime) {
                    e.preventDefault();
                    Swal.fire('Lỗi', 'Vui lòng chọn thời gian bắt đầu và kết thúc', 'error');
                    return;
                }

                // Show success message
                Swal.fire({
                    title: 'Đang tạo ca làm việc...',
                    text: 'Vui lòng chờ trong giây lát',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
            });
        });
    </script>
@endsection
