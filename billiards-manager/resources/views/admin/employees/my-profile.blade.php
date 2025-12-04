@extends('admin.layouts.app')

@section('title', 'Thông Tin Cá Nhân - Poly Billiards')

@section('styles')
    <style>
        .profile-card {
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            color: #6b7280;
            font-weight: 500;
            min-width: 120px;
        }

        .info-value {
            color: #1f2937;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Thông báo lỗi/success -->
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tiêu đề trang -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                Thông Tin Cá Nhân
            </h1>
            <a href="{{ route('admin.pos.dashboard') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </a>
        </div>

        <!-- Thông tin nhân viên -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Card thông tin chính -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6 profile-card">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i>
                        Thông Tin Cơ Bản
                    </h2>

                    <div class="space-y-4">
                        <!-- Hàng 1: Mã NV và Họ tên -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-id-badge text-gray-500 mr-2"></i>
                                    <span class="info-label">Mã nhân viên:</span>
                                </div>
                                <div class="info-value text-lg">{{ $employee->employee_code ?? 'N/A' }}</div>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-user text-gray-500 mr-2"></i>
                                    <span class="info-label">Họ và tên:</span>
                                </div>
                                <div class="info-value text-lg">{{ $employee->name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        @php
                            $roles = [
                                1 => ['Admin', 'text-red-600'],
                                2 => ['Manager', 'text-blue-600'],
                                3 => ['Nhân viên', 'text-green-600'],
                            ];

                            $roleName = $roles[$employee->position][0] ?? 'N/A';
                            $roleColor = $roles[$employee->position][1] ?? 'text-gray-600';
                        @endphp

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-briefcase text-gray-500 mr-2"></i>
                                <span class="info-label">Chức vụ:</span>
                            </div>

                            <div class="info-value {{ $roleColor }}">
                                {{ $roleName }}
                            </div>
                        </div>


                        <!-- Thông tin liên hệ -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-address-book text-green-500 mr-2"></i>
                                Thông Tin Liên Hệ
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-phone text-blue-500 mr-2"></i>
                                        <span class="info-label">Số điện thoại:</span>
                                    </div>
                                    <div class="info-value">{{ $employee->phone ?? 'N/A' }}</div>
                                </div>

                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-envelope text-blue-500 mr-2"></i>
                                        <span class="info-label">Email:</span>
                                    </div>
                                    <div class="info-value">{{ $user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Avatar và Thông tin khác -->
            <div>
                <div class="bg-white rounded-xl shadow-lg p-6 profile-card">
                    <!-- Avatar -->
                    <div class="flex flex-col items-center mb-6">
                        <div
                            class="w-32 h-32 rounded-full border-4 border-blue-100 bg-gray-100 flex items-center justify-center overflow-hidden mb-4">
                            @if ($employee->avatar)
                                <img src="{{ asset('storage/' . $employee->avatar) }}" alt="Avatar"
                                    class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-user text-4xl text-gray-400"></i>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-gray-800">{{ $employee->full_name }}</h3>
                    </div>

                    <!-- Trạng thái -->
                    <div class="space-y-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-green-700">Trạng thái làm việc</p>
                                    <p class="font-bold text-green-800 mt-1">Đang hoạt động</p>
                                </div>
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            </div>
                        </div>

                        <!-- Ngày vào làm -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-purple-500 mr-3"></i>
                                <div>
                                    <p class="text-sm text-purple-700">Ngày vào làm</p>
                                    <p class="font-bold text-purple-800 mt-1">
                                        {{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Đổi mật khẩu -->
                        <button onclick="showChangePassword()"
                            class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-3 rounded-lg transition font-medium">
                            <i class="fas fa-key mr-2"></i>Đổi mật khẩu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin chi tiết khác -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b">
                <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                Thông Tin Chi Tiết
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Địa chỉ -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-home text-gray-500 mr-2"></i>
                        <span class="info-label">Địa chỉ:</span>
                    </div>
                    <div class="info-value">{{ $employee->address ?? 'Chưa cập nhật' }}</div>
                </div>

                <!-- Ngày sinh -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-birthday-cake text-gray-500 mr-2"></i>
                        <span class="info-label">Ngày sinh:</span>
                    </div>
                    <div class="info-value">
                        {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d/m/Y') : 'Chưa cập nhật' }}
                    </div>
                </div>

                <!-- Giới tính -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-venus-mars text-gray-500 mr-2"></i>
                        <span class="info-label">Giới tính:</span>
                    </div>
                    <div class="info-value">
                        @if ($employee->gender == 'male')
                            Nam
                        @elseif($employee->gender == 'female')
                            Nữ
                        @else
                            Khác
                        @endif
                    </div>
                </div>

                <!-- CMND/CCCD -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-id-card-alt text-gray-500 mr-2"></i>
                        <span class="info-label">CMND/CCCD:</span>
                    </div>
                    <div class="info-value">{{ $employee->id_card ?? 'Chưa cập nhật' }}</div>
                </div>

                <!-- Học vấn -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-graduation-cap text-gray-500 mr-2"></i>
                        <span class="info-label">Học vấn:</span>
                    </div>
                    <div class="info-value">{{ $employee->education ?? 'Chưa cập nhật' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal đổi mật khẩu -->
    <div id="changePasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-key text-blue-500 mr-2"></i>
                    Đổi mật khẩu
                </h3>
                <button onclick="hideChangePassword()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Form -->
            <form id="changePasswordForm">
                @csrf
                <div class="p-6 space-y-4">
                    <!-- Mật khẩu hiện tại -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-500 mr-1"></i>
                            Mật khẩu hiện tại
                        </label>
                        <input type="password" name="current_password" id="current_password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <div class="text-red-500 text-sm mt-1" id="current_password_error"></div>
                    </div>

                    <!-- Mật khẩu mới -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-500 mr-1"></i>
                            Mật khẩu mới
                        </label>
                        <input type="password" name="new_password" id="new_password" required minlength="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <div class="text-gray-500 text-xs mt-1">Tối thiểu 6 ký tự</div>
                        <div class="text-red-500 text-sm mt-1" id="new_password_error"></div>
                    </div>

                    <!-- Xác nhận mật khẩu mới -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-500 mr-1"></i>
                            Xác nhận mật khẩu mới
                        </label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <div class="text-red-500 text-sm mt-1" id="new_password_confirmation_error"></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end space-x-3 p-6 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="hideChangePassword()"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Hủy
                    </button>
                    <button type="submit" id="submitBtn"
                        class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition font-medium">
                        <span id="submitText">Đổi mật khẩu</span>
                        <span id="loadingSpinner" class="hidden">
                            <i class="fas fa-spinner fa-spin ml-1"></i>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function showChangePassword() {
            document.getElementById('changePasswordModal').classList.remove('hidden');
            document.getElementById('changePasswordModal').classList.add('flex');
        }

        // Hiển thị modal
        function showChangePassword() {
            document.getElementById('changePasswordModal').classList.remove('hidden');
            document.getElementById('changePasswordModal').classList.add('flex');
            // Reset form và lỗi
            document.getElementById('changePasswordForm').reset();
            clearErrors();
        }

        // Ẩn modal
        function hideChangePassword() {
            document.getElementById('changePasswordModal').classList.remove('flex');
            document.getElementById('changePasswordModal').classList.add('hidden');
        }

        // Clear error messages
        function clearErrors() {
            const errorElements = document.querySelectorAll('[id$="_error"]');
            errorElements.forEach(el => el.textContent = '');
        }

        // Xử lý submit form
        document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Show loading
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            submitBtn.disabled = true;
            submitText.textContent = 'Đang xử lý...';
            loadingSpinner.classList.remove('hidden');

            // Clear errors
            clearErrors();

            // Lấy form data
            const formData = new FormData(this);

            try {
                const response = await fetch('{{ route('admin.change-password', $employee->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Thành công
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reset form và đóng modal sau 2 giây
                    setTimeout(() => {
                        hideChangePassword();
                        this.reset();
                    }, 2000);

                } else {
                    // Hiển thị lỗi
                    if (data.errors) {
                        // Laravel validation errors
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const errorElement = document.getElementById(`${field}_error`);
                            if (errorElement) {
                                errorElement.textContent = messages[0];
                            }
                        }
                    } else {
                        // General error
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: data.message || 'Có lỗi xảy ra!'
                        });
                    }
                }

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi kết nối xảy ra!'
                });
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitText.textContent = 'Đổi mật khẩu';
                loadingSpinner.classList.add('hidden');
            }
        });

        // Đóng modal khi click bên ngoài hoặc ESC
        document.getElementById('changePasswordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideChangePassword();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideChangePassword();
            }
        });

        function hideChangePassword() {
            document.getElementById('changePasswordModal').classList.remove('flex');
            document.getElementById('changePasswordModal').classList.add('hidden');
        }

        // Xử lý đổi mật khẩu
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route('admin.change-password', $employee->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đổi mật khẩu thành công!');
                        hideChangePassword();
                        this.reset();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    alert('Có lỗi xảy ra!');
                });
        });

        // Đóng modal khi click bên ngoài
        document.getElementById('changePasswordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideChangePassword();
            }
        });
    </script>
@endsection
