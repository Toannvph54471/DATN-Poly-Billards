@extends('admin.layouts.app')

@section('title', 'Báo cáo doanh thu hàng ngày')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-6">
            <div class="col-12">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                            Báo cáo doanh thu hàng ngày
                        </h1>
                        <p class="text-gray-600">Thống kê doanh thu và hiệu suất kinh doanh theo ngày</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.daily-reports.dashboard') }}"
                            class="btn bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-chart-line mr-2"></i> Dashboard
                        </a>
                        @if (auth()->user()->role->slug === 'admin')
                            <button type="button" data-bs-toggle="modal" data-bs-target="#generateReportModal"
                                class="btn bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-plus mr-2"></i> Tạo báo cáo
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <form method="GET" action="{{ route('admin.daily-reports.index') }}"
                class="space-y-4 md:space-y-0 md:grid md:grid-cols-4 md:gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Số bản ghi/trang</label>
                    <select id="per_page" name="per_page"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ request('per_page', 15) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2.5 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-filter mr-2"></i> Lọc
                    </button>
                    <a href="{{ route('admin.daily-reports.index') }}"
                        class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-4 py-2.5 rounded-lg font-medium text-center transition duration-200">
                        <i class="fas fa-redo mr-2"></i> Xóa lọc
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        @if (isset($summary))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow border-l-4 border-blue-500 p-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-money-bill-wave text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Tổng doanh thu</p>
                            <p class="text-xl font-bold text-gray-800">
                                {{ number_format($summary['total_revenue'], 0, ',', '.') }} ₫
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border-l-4 border-red-500 p-4">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-tag text-red-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Tổng giảm giá</p>
                            <p class="text-xl font-bold text-gray-800">
                                {{ number_format($summary['total_discount'], 0, ',', '.') }} ₫
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border-l-4 border-green-500 p-4">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-receipt text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Số hóa đơn</p>
                            <p class="text-xl font-bold text-gray-800">
                                {{ $summary['total_bills'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border-l-4 border-purple-500 p-4">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-users text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Số khách hàng</p>
                            <p class="text-xl font-bold text-gray-800">
                                {{ $summary['total_customers'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border-l-4 border-yellow-500 p-4">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-calculator text-yellow-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">TB hóa đơn</p>
                            <p class="text-xl font-bold text-gray-800">
                                {{ number_format($summary['avg_bill_value'], 0, ',', '.') }} ₫
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border-l-4 border-gray-500 p-4">
                    <div class="flex items-center">
                        <div class="bg-gray-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-calendar text-gray-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Số ngày</p>
                            <p class="text-xl font-bold text-gray-800">
                                {{ $reports->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reports Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-list mr-2 text-blue-600"></i>
                        Danh sách báo cáo
                    </h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">
                            Hiển thị {{ $reports->firstItem() ?? 0 }}-{{ $reports->lastItem() ?? 0 }} của
                            {{ $reports->total() }} bản ghi
                        </span>
                        <a href="{{ route('admin.daily-reports.export', request()->query()) }}"
                            class="btn bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-3 py-1.5 rounded-lg text-sm flex items-center">
                            <i class="fas fa-file-excel mr-1"></i> Xuất Excel
                        </a>
                    </div>
                </div>
            </div>

            @if ($reports->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ngày
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Doanh thu
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Giảm giá
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Số hóa đơn
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Số khách hàng
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    TB/HĐ
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tỷ lệ chiếm dụng
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($reports as $report)
                                @php
                                    $occupancyRate =
                                        $report->total_bills > 0
                                            ? round(($report->total_customers / $report->total_bills) * 100, 1)
                                            : 0;
                                    $netRevenue = $report->total_revenue - $report->total_discount;
                                @endphp
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ date('d/m/Y', strtotime($report->report_date)) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($report->report_date)->locale('vi')->translatedFormat('l') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-green-600">
                                            {{ number_format($report->total_revenue, 0, ',', '.') }} ₫
                                        </div>
                                        @if ($report->total_discount > 0)
                                            <div class="text-xs text-red-500">
                                                Thuần: {{ number_format($netRevenue, 0, ',', '.') }} ₫
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if ($report->total_discount > 0)
                                                <span class="text-red-600 font-medium">
                                                    {{ number_format($report->total_discount, 0, ',', '.') }} ₫
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if ($report->total_bills >= 20) bg-green-100 text-green-800
                                @elseif($report->total_bills >= 10) bg-blue-100 text-blue-800
                                @elseif($report->total_bills > 0) bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                            {{ $report->total_bills }} HĐ
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if ($report->total_customers >= 15) bg-purple-100 text-purple-800
                                @elseif($report->total_customers >= 8) bg-indigo-100 text-indigo-800
                                @elseif($report->total_customers > 0) bg-pink-100 text-pink-800
                                @else bg-gray-100 text-gray-800 @endif">
                                            {{ $report->total_customers }} KH
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($report->average_bill_value, 0, ',', '.') }} ₫
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-gradient-to-r 
                                        @if ($occupancyRate >= 80) from-green-400 to-green-500
                                        @elseif($occupancyRate >= 50) from-blue-400 to-blue-500
                                        @elseif($occupancyRate > 0) from-yellow-400 to-yellow-500
                                        @else from-gray-300 to-gray-400 @endif 
                                        h-2 rounded-full"
                                                    style="width: {{ min($occupancyRate, 100) }}%">
                                                </div>
                                            </div>
                                            <span
                                                class="text-xs font-medium 
                                    @if ($occupancyRate >= 80) text-green-600
                                    @elseif($occupancyRate >= 50) text-blue-600
                                    @elseif($occupancyRate > 0) text-yellow-600
                                    @else text-gray-500 @endif">
                                                {{ $occupancyRate }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.daily-reports.show', $report->report_date) }}"
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye mr-1"></i> Chi tiết
                                        </a>
                                        @if (auth()->user()->role->slug === 'admin')
                                            <form action="{{ route('admin.daily-reports.generate') }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <input type="hidden" name="date" value="{{ $report->report_date }}">
                                                <button type="submit" class="text-green-600 hover:text-green-900"
                                                    onclick="return confirm('Tạo lại báo cáo cho ngày {{ date('d/m/Y', strtotime($report->report_date)) }}?')">
                                                    <i class="fas fa-sync-alt mr-1"></i> Tạo lại
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Trang <span class="font-medium">{{ $reports->currentPage() }}</span> trên
                            <span class="font-medium">{{ $reports->lastPage() }}</span>
                        </div>
                        <div class="flex space-x-1">
                            {{ $reports->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-clipboard-list text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Không có dữ liệu</h3>
                    <p class="text-gray-500 mb-4">
                        @if (request()->has('start_date'))
                            Không có báo cáo trong khoảng thời gian đã chọn.
                        @else
                            Chưa có báo cáo nào được tạo.
                        @endif
                    </p>
                    @if (auth()->user()->role->slug === 'admin')
                        <button type="button" data-bs-toggle="modal" data-bs-target="#generateReportModal"
                            class="btn bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i> Tạo báo cáo đầu tiên
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Generate Report Modal -->
    <div class="modal fade" id="generateReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-lg font-semibold text-gray-900">
                        <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                        Tạo báo cáo mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.daily-reports.generate') }}" method="POST" id="generateReportForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="report_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Chọn ngày cần tạo báo cáo
                            </label>
                            <input type="date" id="report_date" name="date" value="{{ date('Y-m-d') }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <p class="text-xs text-gray-500 mt-2">
                                Báo cáo sẽ được tạo từ dữ liệu hóa đơn đã thanh toán trong ngày này.
                            </p>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Lưu ý</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Báo cáo chỉ tính các hóa đơn đã thanh toán</li>
                                            <li>Dữ liệu sẽ được tính toán tự động</li>
                                            <li>Nếu đã có báo cáo, dữ liệu sẽ được cập nhật</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg"
                            data-bs-dismiss="modal">
                            Hủy
                        </button>
                        <button type="submit"
                            class="btn bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-cog mr-2"></i> Tạo báo cáo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto set date range to last 30 days if not set
            document.addEventListener('DOMContentLoaded', function() {
                const today = new Date().toISOString().split('T')[0];
                const startDateInput = document.getElementById('start_date');
                const endDateInput = document.getElementById('end_date');

                // Set max date to today for both inputs
                if (startDateInput) startDateInput.max = today;
                if (endDateInput) endDateInput.max = today;

                // Set default date range (last 30 days) if not already set
                if (startDateInput && !startDateInput.value) {
                    const last30Days = new Date();
                    last30Days.setDate(last30Days.getDate() - 30);
                    startDateInput.value = last30Days.toISOString().split('T')[0];
                }

                if (endDateInput && !endDateInput.value) {
                    endDateInput.value = today;
                }

                // Validate end date is not before start date
                if (startDateInput && endDateInput) {
                    startDateInput.addEventListener('change', function() {
                        if (this.value > endDateInput.value) {
                            endDateInput.value = this.value;
                        }
                    });

                    endDateInput.addEventListener('change', function() {
                        if (this.value < startDateInput.value) {
                            startDateInput.value = this.value;
                        }
                    });
                }

                // Form submission loading
                const generateForm = document.getElementById('generateReportForm');
                if (generateForm) {
                    generateForm.addEventListener('submit', function() {
                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';
                            submitBtn.disabled = true;
                        }
                    });
                }

                // Initialize Bootstrap modal if needed
                const modalElement = document.getElementById('generateReportModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);

                    // Reset form when modal is hidden
                    modalElement.addEventListener('hidden.bs.modal', function() {
                        const form = document.getElementById('generateReportForm');
                        if (form) {
                            form.reset();
                            const submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.innerHTML = '<i class="fas fa-cog mr-2"></i> Tạo báo cáo';
                                submitBtn.disabled = false;
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
