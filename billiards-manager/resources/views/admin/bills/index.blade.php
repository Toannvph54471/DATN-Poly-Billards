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
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if ($bills->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mã hóa đơn</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bàn</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nhân viên</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tổng tiền</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thanh toán</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ngày tạo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="bills-table-body">
                            @forelse ($bills as $bill)
                                @php
                                    $roundedFinalAmount = ceil($bill->final_amount / 1000) * 1000;
                                @endphp
                                <tr class="hover:bg-gray-50 group relative cursor-pointer 
                                    {{ $bill->is_new ? 'new-bill bg-blue-50 border-l-4 border-l-blue-500' : '' }}"
                                    onclick="window.location='{{ route('admin.bills.show', $bill->id) }}'"
                                    data-id="{{ $bill->id }}" data-status="{{ $bill->status }}"
                                    data-payment-status="{{ $bill->payment_status }}"
                                    data-bill-number="{{ $bill->bill_number }}">

                                    {{-- Mã hóa đơn --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $bill->bill_number }}
                                                @if ($bill->is_new)
                                                    <span
                                                        class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">NEW</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Bàn --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $bill->table->table_name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $bill->table->table_number ?? '' }}</div>
                                    </td>

                                    {{-- Nhân viên --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $bill->staff->name ?? 'N/A' }}
                                    </td>

                                    {{-- Tổng tiền --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($roundedFinalAmount) }} ₫
                                    </td>

                                    {{-- Trạng thái --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $bill->status === 'Open'
                                                ? 'bg-green-100 text-green-800'
                                                : ($bill->status === 'quick'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-gray-100 text-gray-700') }}">
                                            @if ($bill->status === 'Open')
                                                <i class="fas fa-play-circle mr-1"></i>Đang mở
                                            @elseif($bill->status === 'quick')
                                                <i class="fas fa-bolt mr-1"></i>Quick
                                            @else
                                                <i class="fas fa-stop-circle mr-1"></i>Đã đóng
                                            @endif
                                        </span>
                                    </td>

                                    {{-- Thanh toán --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $bill->payment_status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            @if ($bill->payment_status === 'Pending')
                                                <i class="fas fa-clock mr-1"></i>Chờ thanh toán
                                            @else
                                                <i class="fas fa-check-circle mr-1"></i>Đã thanh toán
                                            @endif
                                        </span>
                                    </td>

                                    {{-- Ngày tạo --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $bill->created_at ? $bill->created_at->format('d/m/Y H:i') : 'N/A' }}
                                        @if ($bill->is_new)
                                            <div class="text-xs text-red-500 font-medium mt-1">
                                                <i class="fas fa-star mr-1"></i>Vừa thanh toán
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Thao tác --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2" onclick="event.stopPropagation()">
                                            {{-- Xem chi tiết --}}
                                            <a href="{{ route('admin.bills.show', $bill->id) }}"
                                                class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- In hóa đơn --}}
                                            <a href="{{ route('admin.bills.print', $bill->id) }}" target="_blank"
                                                class="text-purple-600 hover:text-purple-900 transition-colors duration-200"
                                                title="In hóa đơn">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            {{-- Xóa --}}
                                            <button type="button"
                                                class="text-red-600 hover:text-red-900 transition-colors duration-200 delete-bill"
                                                title="Xóa" data-id="{{ $bill->id }}"
                                                onclick="event.stopPropagation()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fa-solid fa-file-invoice text-3xl mb-2 text-gray-300"></i>
                                        <p class="text-lg font-medium text-gray-900">Không có hóa đơn nào</p>
                                        <p class="text-gray-500 mt-2">Hãy bắt đầu bằng cách tạo hóa đơn mới từ quản lý bàn.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $bills->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-file-invoice fa-3x text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">Không tìm thấy hóa đơn nào</h3>
                    <p class="text-gray-500 mt-2">Hãy bắt đầu bằng cách tạo hóa đơn mới từ quản lý bàn.</p>
                    <a href="{{ route('admin.tables.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mt-4">
                        <i class="fas fa-table mr-2"></i>Đến quản lý bàn
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal xác nhận xóa --}}
    <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Xác nhận xóa</h3>
            <p class="mb-6">Bạn có chắc chắn muốn xóa hóa đơn này không? Hành động này không thể hoàn tác.</p>
            <div class="flex justify-end space-x-3">
                <button id="cancel-delete"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-all">Hủy</button>
                <button id="confirm-delete"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-all">Xóa</button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Hiệu ứng hover cho toàn bộ hàng */
            tr[onclick]:hover {
                background-color: #f3f4f6 !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                transition: all 0.2s ease-in-out;
            }

            /* Con trỏ chuột cho toàn bộ hàng */
            tr[onclick] {
                cursor: pointer;
                transition: all 0.2s ease-in-out;
            }

            /* Đảm bảo các link trong actions không bị ảnh hưởng */
            tr[onclick] .flex.space-x-2 a,
            tr[onclick] .flex.space-x-2 button,
            tr[onclick] .flex.space-x-2 form {
                position: relative;
                z-index: 10;
            }

            /* Style cho icon bị disabled */
            .cursor-not-allowed {
                opacity: 0.5;
            }

            /* Animation cho hóa đơn mới */
            .new-badge {
                animation: pulse 2s infinite;
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
    @endpush

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

            // Sự kiện xem hóa đơn mới
            viewNewBillsBtn.addEventListener('click', function() {
                // Lọc chỉ hiển thị hóa đơn mới
                const allRows = document.querySelectorAll('tr[onclick]');
                allRows.forEach(row => {
                    if (row.classList.contains('new-bill')) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Cập nhật số lượng hiển thị
                const visibleCount = document.querySelectorAll('tr[onclick]:not([style*="display: none"])')
                    .length;
                updateShowingCount(visibleCount);

                // Ẩn thông báo
                newBillsAlert.classList.add('hidden');
            });

            // Hàm lọc hóa đơn
            function filterBills() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const paymentValue = paymentFilter.value;

                const rows = billsTableBody.querySelectorAll('tr[onclick]');
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Xóa hàng khỏi bảng
                                const rowToDelete = document.querySelector(
                                    `tr[data-id="${billToDelete}"]`);
                                if (rowToDelete) {
                                    rowToDelete.style.opacity = '0';
                                    setTimeout(() => {
                                        rowToDelete.remove();

                                        // Cập nhật số liệu
                                        const totalBills = document.getElementById(
                                            'total-bills');
                                        totalBills.textContent = parseInt(totalBills
                                            .textContent) - 1;

                                        // Cập nhật số lượng hiển thị
                                        const visibleRows = billsTableBody.querySelectorAll(
                                            'tr[onclick]:not([style*="display: none"])');
                                        document.getElementById('showing-to').textContent =
                                            visibleRows.length;
                                        document.getElementById('showing-total').textContent =
                                            totalBills.textContent;
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
