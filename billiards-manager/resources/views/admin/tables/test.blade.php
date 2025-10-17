@extends('layouts.admin')

@section('title', 'Quản lý bàn - Billiards Management')

@section('content')
    <div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Email address</label>
  <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
</div>
<div class="mb-3">
  <label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>
  <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
</div>
@endsection

{{-- @section('scripts')
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
@endsection --}}

