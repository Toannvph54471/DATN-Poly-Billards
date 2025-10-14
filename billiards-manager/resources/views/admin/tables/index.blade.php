@extends('admin.layouts.app')

@section('title', 'Quản lý bàn - Billiards Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý bàn</h1>
            <p class="text-gray-600">Danh sách các bàn billiards trong hệ thống</p>
        </div>
        <div>
            <button type="button" onclick="showAddTableForm()"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Thêm bàn mới
            </button>
            <a href="{{ route('admin.tables.trashed') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Danh bàn ẩn
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng số bàn</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalTables }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-table text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang sử dụng</p>
                    <p class="text-xl font-bold text-gray-800">{{ $inUseCount }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Bảo trì</p>
                    <p class="text-xl font-bold text-gray-800">{{ $maintenanceCount }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tools text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Trống</p>
                    <p class="text-xl font-bold text-gray-800">{{ $availableCount }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-circle text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form id="filterForm" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Tên bàn, số bàn...">
                    </div>
                </div>

                <!-- Type Filter -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Loại bàn</label>
                    <select name="type" id="type"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả loại</option>
                        <option value="pool" {{ request('type') == 'pool' ? 'selected' : '' }}>Pool</option>
                        <option value="snooker" {{ request('type') == 'snooker' ? 'selected' : '' }}>Snooker</option>
                        <option value="carom" {{ request('type') == 'carom' ? 'selected' : '' }}>Carom</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" id="status"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Trống</option>
                        <option value="in_use" {{ request('status') == 'in_use' ? 'selected' : '' }}>Đang sử dụng</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì
                        </option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end">
                    <button type="button" onclick="applyFilter()"
                        class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center w-full justify-center">
                        <i class="fas fa-filter mr-2"></i>
                        Lọc
                    </button>
                    <button type="button" onclick="resetFilter()"
                        class="ml-2 bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tables Grid -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        @if ($tables->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($tables as $table)
                    <div class="table-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300 cursor-pointer"
                        data-table-id="{{ $table->id }}" onclick="showTableDetail({{ $table->id }})">
                        <!-- Table Header -->
                        <div class="relative">
                            <!-- Table Status Badge -->
                            <div class="absolute top-3 left-3">
                                @if ($table->status == 'available')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        <i class="fas fa-circle mr-1" style="font-size:6px;"></i> Trống
                                    </span>
                                @elseif($table->status == 'in_use')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-circle mr-1" style="font-size:6px;"></i> Đang sử dụng
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <i class="fas fa-circle mr-1" style="font-size:6px;"></i> Bảo trì
                                    </span>
                                @endif
                            </div>

                            <!-- Table Actions -->
                            <div class="absolute top-3 right-3 flex space-x-1">
                                <button type="button" onclick="event.stopPropagation(); editTable({{ $table->id }})"
                                    class="w-8 h-8 bg-white bg-opacity-90 rounded-full flex items-center justify-center hover:bg-blue-100 transition"
                                    title="Chỉnh sửa">
                                    <i class="fas fa-edit text-blue-600 text-sm"></i>
                                </button>
                                <button type="button"
                                    onclick="event.stopPropagation(); confirmDelete({{ $table->id }})"
                                    class="w-8 h-8 bg-white bg-opacity-90 rounded-full flex items-center justify-center hover:bg-red-100 transition"
                                    title="Xóa">
                                    <i class="fas fa-trash text-red-600 text-sm"></i>
                                </button>
                            </div>

                            <!-- Billiard Table Visualization -->
                            <div
                                class="billiard-table relative h-32 border-4 border-amber-900 rounded-lg mx-4 mt-4 mb-2 overflow-hidden 
                                @if ($table->status == 'available') bg-gradient-to-b from-gray-400 to-gray-600 @endif
                                @if ($table->status == 'in_use') bg-gradient-to-b from-green-500 to-green-700 @endif
                                @if ($table->status == 'maintenance') bg-gradient-to-b from-yellow-400 to-yellow-600 @endif">
                                <!-- Table Surface -->
                                <div
                                    class="absolute inset-2 rounded-md
                                    @if ($table->status == 'available') bg-gradient-to-b from-gray-300 to-gray-500 @endif
                                    @if ($table->status == 'in_use') bg-gradient-to-b from-green-400 to-green-600 @endif
                                    @if ($table->status == 'maintenance') bg-gradient-to-b from-yellow-300 to-yellow-500 @endif">
                                    <!-- Table Pockets -->
                                    <div
                                        class="absolute -top-1 -left-1 w-6 h-6 bg-gray-800 rounded-full border-2 border-amber-900">
                                    </div>
                                    <div
                                        class="absolute -top-1 right-1/2 translate-x-1/2 w-6 h-6 bg-gray-800 rounded-full border-2 border-amber-900">
                                    </div>
                                    <div
                                        class="absolute -top-1 -right-1 w-6 h-6 bg-gray-800 rounded-full border-2 border-amber-900">
                                    </div>
                                    <div
                                        class="absolute -bottom-1 -left-1 w-6 h-6 bg-gray-800 rounded-full border-2 border-amber-900">
                                    </div>
                                    <div
                                        class="absolute -bottom-1 right-1/2 translate-x-1/2 w-6 h-6 bg-gray-800 rounded-full border-2 border-amber-900">
                                    </div>
                                    <div
                                        class="absolute -bottom-1 -right-1 w-6 h-6 bg-gray-800 rounded-full border-2 border-amber-900">
                                    </div>

                                    <!-- Balls (only show if table is in use) -->
                                    @if ($table->status == 'in_use')
                                        <div class="absolute top-1/2 left-1/3 w-3 h-3 bg-white rounded-full shadow-md">
                                        </div>
                                        <div class="absolute top-2/3 left-2/3 w-3 h-3 bg-red-500 rounded-full shadow-md">
                                        </div>
                                        <div
                                            class="absolute top-1/3 right-1/4 w-3 h-3 bg-yellow-500 rounded-full shadow-md">
                                        </div>
                                    @endif

                                    <!-- Maintenance Icon -->
                                    @if ($table->status == 'maintenance')
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <i class="fas fa-tools text-yellow-800 text-2xl opacity-50"></i>
                                        </div>
                                    @endif

                                    <!-- Available Icon -->
                                    @if ($table->status == 'available')
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <i class="fas fa-pause text-gray-700 text-xl opacity-50"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Table Info -->
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-900">Bàn {{ $table->table_number }}</h3>
                                    <p class="text-gray-600 text-sm">{{ $table->table_name }}</p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded capitalize">
                                        {{ $table->type ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-2 text-gray-400"></i>
                                        Giá/giờ:
                                    </span>
                                    <span
                                        class="font-semibold text-gray-900">{{ number_format($table->hourly_rate, 0, ',', '.') }}
                                        đ</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                        Vị trí:
                                    </span>
                                    <span class="font-medium">{{ $table->position ?? 'Không rõ' }}</span>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <div class="flex space-x-2">
                                    @if ($table->status == 'available')
                                        <button type="button"
                                            onclick="event.stopPropagation(); startTable({{ $table->id }})"
                                            class="flex-1 bg-gray-600 text-white py-2 px-3 rounded-lg text-sm font-medium hover:bg-gray-700 transition flex items-center justify-center">
                                            <i class="fas fa-play mr-1"></i>
                                            Bắt đầu
                                        </button>
                                    @elseif($table->status == 'in_use')
                                        <button type="button"
                                            onclick="event.stopPropagation(); stopTable({{ $table->id }})"
                                            class="flex-1 bg-green-600 text-white py-2 px-3 rounded-lg text-sm font-medium hover:bg-green-700 transition flex items-center justify-center">
                                            <i class="fas fa-stop mr-1"></i>
                                            Kết thúc
                                        </button>
                                    @else
                                        <button type="button"
                                            class="flex-1 bg-yellow-600 text-white py-2 px-3 rounded-lg text-sm font-medium cursor-not-allowed"
                                            disabled>
                                            <i class="fas fa-tools mr-1"></i>
                                            Đang bảo trì
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <div class="flex space-x-2">
                                    <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa bàn này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex-1 bg-red-600 text-white py-2 px-3 rounded-lg text-sm font-medium hover:bg-red-700 transition flex items-center justify-center">
                                            <i class="fas fa-trash mr-1"></i>
                                            Ẩn Bàn
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-table text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Không có bàn nào</h3>
                <p class="text-gray-500 mb-6">Không tìm thấy bàn phù hợp với tiêu chí lọc hiện tại.</p>
                <button type="button" onclick="showAddTableForm()"
                    class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Thêm bàn mới
                </button>
            </div>
        @endif

        <!-- Pagination -->
        @if ($tables->hasPages())
            <div class="mt-6 pt-6 border-t border-gray-200">
                {{ $tables->links() }}
            </div>
        @endif
    </div>

    <!-- Modal chi tiết bàn -->
    <div id="tableDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4" id="modalContent">
            <!-- Modal content sẽ được cập nhật bằng JavaScript -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Biến toàn cục để theo dõi trạng thái modal
        let isModalOpen = false;

        // Dữ liệu bàn từ server
        const tablesData = @json($tables->keyBy('id')->toArray());

        function showTableDetail(tableId) {
            if (isModalOpen) return;

            event?.stopPropagation();
            isModalOpen = true;

            const table = tablesData[tableId];
            if (!table) return;

            const modal = document.getElementById('tableDetailModal');
            const modalContent = document.getElementById('modalContent');

            modalContent.innerHTML = `
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Chi tiết bàn ${table.table_number}</h2>
                    <p class="text-gray-600">${table.table_name}</p>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="flex items-center text-gray-600">
                        <i class="fas fa-tag mr-3 text-blue-500"></i>
                        Loại bàn:
                    </span>
                    <span class="font-medium text-gray-800 capitalize">${table.type || 'N/A'}</span>
                </div>

                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="flex items-center text-gray-600">
                        <i class="fas fa-clock mr-3 text-green-500"></i>
                        Giá/giờ:
                    </span>
                    <span class="font-bold text-gray-900">${formatCurrency(table.hourly_rate)} đ</span>
                </div>

                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="flex items-center text-gray-600">
                        <i class="fas fa-map-marker-alt mr-3 text-purple-500"></i>
                        Vị trí:
                    </span>
                    <span class="font-medium text-gray-800">${table.position || 'Không rõ'}</span>
                </div>

                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="flex items-center text-gray-600">
                        <i class="fas fa-info-circle mr-3 text-orange-500"></i>
                        Trạng thái:
                    </span>
                    <span class="font-medium">${getStatusBadge(table.status)}</span>
                </div>
            </div>

            <div class="flex space-x-3">
                <button type="button" onclick="closeModal()"
                    class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-400 transition">
                    Đóng
                </button>
                ${table.status === 'available' ? `
                                            <button type="button" onclick="handleStartTable(${table.id})"
                                                class="flex-1 bg-gray-600 text-white py-3 rounded-lg font-medium hover:bg-gray-700 transition">
                                                Bắt đầu
                                            </button>
                                            ` : table.status === 'in_use' ? `
                                            <button type="button" onclick="handleStopTable(${table.id})"
                                                class="flex-1 bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition">
                                                Kết thúc
                                            </button>
                                            ` : ''}
            </div>
        </div>
    `;

            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('tableDetailModal');
            modal.classList.add('hidden');
            isModalOpen = false;
        }

        // Hàm hỗ trợ
        function getStatusBadge(status) {
            const statusMap = {
                'available': '<span class="text-gray-600">⏸️ Chưa bật</span>',
                'in_use': '<span class="text-green-600">▶️ Đang sử dụng</span>',
                'maintenance': '<span class="text-yellow-600">🔧 Bảo trì</span>'
            };
            return statusMap[status] || '<span class="text-gray-600">Không xác định</span>';
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }

        // Đóng modal khi click bên ngoài
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('tableDetailModal');
            if (isModalOpen && e.target === modal) {
                closeModal();
            }
        });

        // Ngăn chặn sự kiện click trong modal lan ra ngoài
        document.getElementById('tableDetailModal').addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Xử lý filter không reload trang
        function applyFilter() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            // Chuyển hướng với params filter
            window.location.href = '{{ url()->current() }}?' + params.toString();
        }

        function resetFilter() {
            window.location.href = '{{ url()->current() }}';
        }

        // Xử lý các action
        function startTable(tableId) {
            event?.stopPropagation();
            handleStartTable(tableId);
        }

        function stopTable(tableId) {
            event?.stopPropagation();
            handleStopTable(tableId);
        }

        function handleStartTable(tableId) {
            closeModal();

            Swal.fire({
                title: 'Bắt đầu sử dụng?',
                text: "Bạn có chắc muốn bắt đầu sử dụng bàn này?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Bắt đầu',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gọi API bắt đầu sử dụng bàn
                    fetch(`/tables/${tableId}/start`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Thành công!', 'Đã bắt đầu sử dụng bàn', 'success');
                                // Reload trang để cập nhật trạng thái
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                Swal.fire('Lỗi!', data.message || 'Có lỗi xảy ra', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Lỗi!', 'Không thể kết nối đến server', 'error');
                        });
                }
            });
        }

        function handleStopTable(tableId) {
            closeModal();

            Swal.fire({
                title: 'Kết thúc sử dụng?',
                text: "Bạn có chắc muốn kết thúc phiên chơi?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Kết thúc',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gọi API kết thúc sử dụng bàn
                    fetch(`/tables/${tableId}/stop`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Thành công!', 'Đã kết thúc phiên chơi', 'success');
                                // Reload trang để cập nhật trạng thái
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                Swal.fire('Lỗi!', data.message || 'Có lỗi xảy ra', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Lỗi!', 'Không thể kết nối đến server', 'error');
                        });
                }
            });
        }

        function editTable(tableId) {
            // Chuyển hướng đến trang edit
            window.location.href = `/admin/tables/${tableId}/edit`;
        }

        function confirmDelete(tableId) {
            event?.stopPropagation();

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa bàn này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gọi API xóa
                    fetch(`/admin/tables/${tableId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Thành công!', 'Đã xóa bàn', 'success');
                                // Reload trang để cập nhật danh sách
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                Swal.fire('Lỗi!', data.message || 'Có lỗi xảy ra', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Lỗi!', 'Không thể kết nối đến server', 'error');
                        });
                }
            });
        }

        function showAddTableForm() {
            window.location.href = '/admin/tables/create';
        }

        // Ngăn chặn form submit mặc định
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    return false;
                });
            });
        });
    </script>
@endsection

<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .table-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .table-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .billiard-table {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .billiard-table::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .billiard-table::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 1px;
        height: 80%;
        background: linear-gradient(180deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    #tableDetailModal {
        backdrop-filter: blur(5px);
    }
</style>
