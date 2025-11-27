@extends('admin.layouts.app')

@section('title', 'Quản lý hóa đơn')

@section('content')
    <div class="p-6 bg-white rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6">Quản lý hóa đơn</h1>

        {{-- Thống kê tổng quan --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center transition-all hover:shadow-md">
                <div class="bg-blue-500 text-white p-3 rounded-lg mr-3">
                    <i class="fa-solid fa-file-invoice text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Tổng hóa đơn</p>
                    <p class="text-xl font-semibold" id="total-bills">{{ $bills->total() }}</p>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center transition-all hover:shadow-md">
                <div class="bg-green-500 text-white p-3 rounded-lg mr-3">
                    <i class="fa-solid fa-play text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Đang mở</p>
                    <p class="text-xl font-semibold" id="open-bills">{{ $bills->where('status', 'Open')->count() }}</p>
                </div>
            </div>

            <div
                class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center transition-all hover:shadow-md">
                <div class="bg-yellow-500 text-white p-3 rounded-lg mr-3">
                    <i class="fa-solid fa-pause text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Quick Service</p>
                    <p class="text-xl font-semibold" id="quick-bills">{{ $bills->where('status', 'quick')->count() }}</p>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center transition-all hover:shadow-md">
                <div class="bg-red-500 text-white p-3 rounded-lg mr-3">
                    <i class="fa-solid fa-stop text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Đã đóng</p>
                    <p class="text-xl font-semibold" id="closed-bills">{{ $bills->where('status', 'Closed')->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Bộ lọc --}}
        <div class="flex flex-col md:flex-row items-center gap-3 mb-6">
            <input type="text" id="search-input" placeholder="Tìm kiếm mã hóa đơn, bàn..."
                class="w-full md:w-1/3 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 transition-all">

            <select id="status-filter" class="border border-gray-300 rounded-lg px-3 py-2 transition-all">
                <option value="">Tất cả trạng thái</option>
                <option value="Open">Đang mở</option>
                <option value="Closed">Đã đóng</option>
                <option value="quick">Quick Service</option>
            </select>

            <select id="payment-filter" class="border border-gray-300 rounded-lg px-3 py-2 transition-all">
                <option value="">Tất cả thanh toán</option>
                <option value="Pending">Chờ thanh toán</option>
                <option value="Paid">Đã thanh toán</option>
            </select>

            <button id="filter-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all">
                <i class="fa-solid fa-filter mr-1"></i> Lọc
            </button>

            <button id="reset-btn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-all">
                <i class="fa-solid fa-refresh mr-1"></i> Đặt lại
            </button>
        </div>

        {{-- Thông báo hóa đơn mới --}}
        <div id="new-bills-alert" class="hidden mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fa-solid fa-bell text-green-500 mr-2"></i>
                    <span class="text-green-700">Có <span id="new-bills-count">0</span> hóa đơn mới được thanh toán!</span>
                </div>
                <div class="flex space-x-2">
                    <button id="view-new-bills"
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-all">
                        Xem ngay
                    </button>
                    <button id="close-alert"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-1 rounded text-sm transition-all">
                        Đóng
                    </button>
                </div>
            </div>
        </div>

        {{-- Bảng hóa đơn --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Mã hóa đơn</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Bàn</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Nhân viên</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Tổng tiền</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Trạng thái</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Thanh toán</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Ngày tạo</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="bills-table-body">
                    @forelse ($bills as $bill)
                        <tr class="border-t hover:bg-gray-50 transition-all duration-200 bill-row 
                              {{ $bill->is_new ? 'new-bill bg-blue-50 border-l-4 border-l-blue-500' : '' }}"
                            data-id="{{ $bill->id }}" data-status="{{ $bill->status }}"
                            data-payment-status="{{ $bill->payment_status }}"
                            data-bill-number="{{ $bill->bill_number }}">

                            <td class="px-4 py-3 font-medium">
                                <div class="flex items-center">
                                    {{ $bill->bill_number }}
                                    @if ($bill->is_new)
                                        <span
                                            class="new-badge ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">NEW</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium">{{ $bill->table->table_name ?? 'N/A' }}</span>
                                <br>
                                <span class="text-sm text-gray-500">{{ $bill->table->table_number ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $bill->staff->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-medium">
                                {{ number_format($bill->final_amount) }} ₫
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 rounded text-sm
                                {{ $bill->status === 'Open'
                                    ? 'bg-green-100 text-green-700'
                                    : ($bill->status === 'quick'
                                        ? 'bg-yellow-100 text-yellow-700'
                                        : 'bg-gray-100 text-gray-700') }}">
                                    @if ($bill->status === 'Open')
                                        Đang mở
                                    @elseif($bill->status === 'quick')
                                        Quick
                                    @else
                                        Đã đóng
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 rounded text-sm
                                {{ $bill->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $bill->payment_status === 'Pending' ? 'Chờ thanh toán' : 'Đã thanh toán' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $bill->created_at ? $bill->created_at->format('d/m/Y H:i') : 'N/A' }}
                                @if ($bill->is_new)
                                    <br>
                                    <span class="text-xs text-red-500 font-medium">Vừa thanh toán</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.bills.show', $bill->id) }}"
                                        class="text-blue-600 hover:text-blue-800 transition-colors bill-action"
                                        title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if ($bill->status === 'Open')
                                        <a href="#"
                                            class="text-green-600 hover:text-green-800 transition-colors bill-action"
                                            title="Tiếp tục">
                                            <i class="fa-solid fa-play"></i>
                                        </a>
                                    @endif
                                    <a href="#"
                                        class="text-red-600 hover:text-red-800 transition-colors bill-action delete-bill"
                                        title="Xóa" data-id="{{ $bill->id }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <i class="fa-solid fa-file-invoice text-3xl mb-2 text-gray-300"></i>
                                <p>Không có hóa đơn nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Hiển thị <span id="showing-from">1</span> đến <span id="showing-to">{{ $bills->count() }}</span> của
                <span id="showing-total">{{ $bills->total() }}</span> hóa đơn
            </div>
            <div class="pagination">
                {{ $bills->links() }}
            </div>
        </div>
    </div>

    {{-- Modal xác nhận xóa --}}
    <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Xác nhận xóa</h3>
            <p class="mb-6">Bạn có chắc chắn muốn xóa hóa đơn này không?</p>
            <div class="flex justify-end space-x-3">
                <button id="cancel-delete"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-all">Hủy</button>
                <button id="confirm-delete"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-all">Xóa</button>
            </div>
        </div>
    </div>

    <style>
        .new-badge {
            animation: pulse 2s infinite;
        }

        .bill-row {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .bill-row:hover {
            background-color: #f8fafc !important;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .new-bill {
            animation: highlightFade 3s ease-in-out;
        }

        @keyframes highlightFade {
            0% {
                background-color: #dbeafe;
            }
            70% {
                background-color: #dbeafe;
            }
            100% {
                background-color: #f0f9ff;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo biến
            let billToDelete = null;

            // Lấy các phần tử DOM
            const searchInput = document.getElementById('search-input');
            const statusFilter = document.getElementById('status-filter');
            const paymentFilter = document.getElementById('payment-filter');
            const filterBtn = document.getElementById('filter-btn');
            const resetBtn = document.getElementById('reset-btn');
            const billsTableBody = document.getElementById('bills-table-body');
            const newBillsAlert = document.getElementById('new-bills-alert');
            const newBillsCount = document.getElementById('new-bills-count');
            const viewNewBillsBtn = document.getElementById('view-new-bills');
            const closeAlertBtn = document.getElementById('close-alert');
            const deleteModal = document.getElementById('delete-modal');
            const cancelDelete = document.getElementById('cancel-delete');
            const confirmDelete = document.getElementById('confirm-delete');

            // Đếm số hóa đơn mới ban đầu
            const initialNewBills = document.querySelectorAll('.new-bill').length;
            if (initialNewBills > 0) {
                showNewBillsAlert(initialNewBills);
            }

            // Hiển thị thông báo hóa đơn mới
            function showNewBillsAlert(count) {
                newBillsCount.textContent = count;
                newBillsAlert.classList.remove('hidden');
            }

            // Đóng thông báo
            closeAlertBtn.addEventListener('click', function() {
                newBillsAlert.classList.add('hidden');
            });

            // Sự kiện click vào hàng hóa đơn - chuyển đến trang chi tiết
            document.querySelectorAll('.bill-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    // Chỉ chuyển trang nếu không click vào các nút thao tác
                    if (!e.target.closest('.bill-action')) {
                        const billId = this.getAttribute('data-id');
                        window.location.href = `/admin/bills/${billId}`;
                    }
                });
            });

            // Sự kiện xem hóa đơn mới
            viewNewBillsBtn.addEventListener('click', function() {
                // Lọc chỉ hiển thị hóa đơn mới
                const allRows = document.querySelectorAll('.bill-row');
                allRows.forEach(row => {
                    if (row.classList.contains('new-bill')) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Cập nhật số lượng hiển thị
                const visibleCount = document.querySelectorAll('.bill-row:not([style*="display: none"])').length;
                updateShowingCount(visibleCount);

                // Ẩn thông báo
                newBillsAlert.classList.add('hidden');
            });

            // Hàm lọc hóa đơn
            function filterBills() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const paymentValue = paymentFilter.value;

                const rows = billsTableBody.querySelectorAll('.bill-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const billNumber = row.getAttribute('data-bill-number').toLowerCase();
                    const tableName = row.cells[1].textContent.toLowerCase();
                    const status = row.getAttribute('data-status');
                    const paymentStatus = row.getAttribute('data-payment-status');

                    const matchesSearch = billNumber.includes(searchTerm) || tableName.includes(searchTerm);
                    const matchesStatus = !statusValue || status === statusValue;
                    const matchesPayment = !paymentValue || paymentStatus === paymentValue;

                    if (matchesSearch && matchesStatus && matchesPayment) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateShowingCount(visibleCount);
            }

            // Cập nhật số lượng hiển thị
            function updateShowingCount(count) {
                document.getElementById('showing-from').textContent = '1';
                document.getElementById('showing-to').textContent = count;
            }

            // Sự kiện lọc
            filterBtn.addEventListener('click', filterBills);

            // Sự kiện đặt lại bộ lọc
            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                statusFilter.value = '';
                paymentFilter.value = '';
                filterBills();
            });

            // Sự kiện tìm kiếm khi nhập
            searchInput.addEventListener('input', filterBills);

            // Sự kiện xóa hóa đơn
            document.querySelectorAll('.delete-bill').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    billToDelete = this.getAttribute('data-id');
                    deleteModal.classList.remove('hidden');
                });
            });

            // Hủy xóa
            cancelDelete.addEventListener('click', function() {
                deleteModal.classList.add('hidden');
                billToDelete = null;
            });

            // Xác nhận xóa
            confirmDelete.addEventListener('click', function() {
                if (billToDelete) {
                    // Gửi yêu cầu AJAX để xóa hóa đơn
                    fetch(`/admin/bills/${billToDelete}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Xóa hàng khỏi bảng
                                const rowToDelete = document.querySelector(`.bill-row[data-id="${billToDelete}"]`);
                                if (rowToDelete) {
                                    rowToDelete.style.opacity = '0';
                                    setTimeout(() => {
                                        rowToDelete.remove();

                                        // Cập nhật số liệu
                                        const totalBills = document.getElementById('total-bills');
                                        totalBills.textContent = parseInt(totalBills.textContent) - 1;

                                        // Cập nhật số lượng hiển thị
                                        const visibleRows = billsTableBody.querySelectorAll('.bill-row:not([style*="display: none"])');
                                        document.getElementById('showing-to').textContent = visibleRows.length;
                                        document.getElementById('showing-total').textContent = totalBills.textContent;
                                    }, 300);
                                }
                            } else {
                                alert('Lỗi khi xóa hóa đơn: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Lỗi khi xóa hóa đơn');
                        });

                    deleteModal.classList.add('hidden');
                    billToDelete = null;
                }
            });
        });
    </script>
@endsection