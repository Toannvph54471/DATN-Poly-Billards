@extends('admin.layouts.app')

@section('title', 'Quản lý lương - F&B Management')

@section('content')
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bảng lương nhân viên</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý và tính toán lương cho đội ngũ</p>
        </div>
        <a href="{{ route('admin.employees.index') }}"
            class="btn btn-primary">
            <i class="fas fa-arrow-left"></i>
            <span>Danh sách nhân viên</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form action="{{ route('admin.payroll.index') }}" method="GET" class="flex items-center gap-4">
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Tháng</label>
                <input type="month" name="month" value="{{ $month }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div class="flex gap-2 pt-5">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i>
                    Lọc
                </button>
                <button type="button" onclick="calculateAllSalaries()" class="btn btn-success">
                    <i class="fas fa-calculator"></i>
                    Tính tất cả
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        @php
            $totalEmployees = $employees->total();
            $calculatedCount = $employees->filter(fn($e) => $e->payrolls->isNotEmpty())->count();
            $totalSalary = $employees->sum(fn($e) => $e->payrolls->first()?->total_amount ?? 0);
            $totalBonus = $employees->sum(fn($e) => $e->payrolls->first()?->bonus ?? 0);
        @endphp
        
        <div class="stat-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Tổng NV</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalEmployees }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Đã tính</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $calculatedCount }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Tổng lương</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalSalary/1000000, 1) }}M</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Thưởng</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalBonus/1000000, 1) }}M</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <i class="fas fa-gift text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-table">
        <table class="w-full">
            <thead>
                <tr class="table-header">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Nhân viên</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Loại</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Lương CB</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Công/Giờ</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Thưởng</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Phạt</th>
                    <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Thực lĩnh</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    @php
                        $payroll = $employee->payrolls->first();
                        $isCalculated = $payroll ? true : false;
                        $calculatedSalary = $payroll ? $payroll->total_amount : 0;
                        $bonus = $payroll ? $payroll->bonus : 0;
                        $deductions = $payroll ? $payroll->deductions : 0;
                        $notes = $payroll ? $payroll->notes : '';
                    @endphp
                    <tr class="table-row">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-sm font-semibold">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $employee->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $employee->employee_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="badge {{ $employee->salary_type === 'monthly' ? 'badge-monthly' : 'badge-hourly' }}">
                                {{ $employee->salary_type === 'monthly' ? 'Tháng' : 'Giờ' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <p class="text-sm font-medium text-gray-800">{{ number_format($employee->salary_rate) }}</p>
                            <p class="text-xs text-gray-500">{{ $employee->salary_type === 'monthly' ? 'đ/tháng' : 'đ/giờ' }}</p>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($isCalculated)
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-sm font-medium text-gray-700">
                                    {{ $payroll->total_hours }}
                                    <span class="text-xs text-gray-500">{{ $employee->salary_type === 'monthly' ? 'ngày' : 'giờ' }}</span>
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="text-sm font-medium text-green-600">{{ number_format($bonus) }}</span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="text-sm font-medium text-red-600">{{ number_format($deductions) }}</span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            @if($isCalculated)
                                <p class="text-sm font-bold text-gray-900">{{ number_format($calculatedSalary) }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($calculatedSalary/1000000, 2) }}tr</p>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-50 text-yellow-700 rounded text-xs font-medium">
                                    <i class="fas fa-clock text-xs"></i>
                                    Chưa tính
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="openSalaryModal({{ $employee->id }}, '{{ $employee->salary_type }}', {{ $employee->salary_rate }})"
                                    class="btn-icon bg-blue-50 text-blue-600 hover:bg-blue-100" title="Sửa lương CB">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                <button onclick="openPayrollModal({{ $employee->id }}, {{ $bonus }}, {{ $deductions }}, '{{ $notes }}')"
                                    class="btn-icon bg-purple-50 text-purple-600 hover:bg-purple-100" title="Thưởng/Phạt">
                                    <i class="fas fa-coins text-xs"></i>
                                </button>
                                <button onclick="calculateSalary({{ $employee->id }}, '{{ $month }}')"
                                    class="btn-icon bg-green-50 text-green-600 hover:bg-green-100" title="Tính lương">
                                    <i class="fas fa-calculator text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Không có dữ liệu</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="border-t border-gray-200 px-4 py-3">
            {{ $employees->appends(['month' => $month])->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Salary Modal -->
    <div id="salaryModal" class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 modal-overlay" onclick="if(event.target === this) closeSalaryModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md modal-content">
                <div class="flex items-center justify-between p-5 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Cập nhật lương cơ bản</h3>
                    <button onclick="closeSalaryModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="salaryForm" class="p-5 space-y-4">
                    <input type="hidden" id="employeeId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Loại lương</label>
                        <select id="salaryType" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="hourly">Theo giờ</option>
                            <option value="monthly">Theo tháng</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mức lương (VNĐ)</label>
                        <input type="number" id="salaryRate" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                            placeholder="Nhập mức lương">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeSalaryModal()" 
                            class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium text-sm">
                            Hủy
                        </button>
                        <button type="button" onclick="submitSalaryUpdate()" 
                            class="flex-1 btn btn-primary">
                            <i class="fas fa-check"></i>
                            Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payroll Modal -->
    <div id="payrollModal" class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 modal-overlay" onclick="if(event.target === this) closePayrollModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md modal-content">
                <div class="flex items-center justify-between p-5 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Điều chỉnh thưởng phạt</h3>
                    <button onclick="closePayrollModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="payrollForm" class="p-5 space-y-4">
                    <input type="hidden" id="payrollEmployeeId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thưởng (VNĐ)</label>
                        <input type="number" id="bonus" value="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phạt (VNĐ)</label>
                        <input type="number" id="deductions" value="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                        <textarea id="notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                            placeholder="Lý do thưởng/phạt..."></textarea>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closePayrollModal()" 
                            class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium text-sm">
                            Hủy
                        </button>
                        <button type="button" onclick="submitPayrollUpdate()" 
                            class="flex-1 btn btn-primary">
                            <i class="fas fa-calculator"></i>
                            Lưu & Tính
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function openSalaryModal(id, type, rate) {
            document.getElementById('employeeId').value = id;
            document.getElementById('salaryType').value = type;
            document.getElementById('salaryRate').value = rate;
            document.getElementById('salaryModal').classList.remove('hidden');
        }

        function closeSalaryModal() {
            document.getElementById('salaryModal').classList.add('hidden');
        }

        function openPayrollModal(id, bonus, deductions, notes) {
            document.getElementById('payrollEmployeeId').value = id;
            document.getElementById('bonus').value = bonus;
            document.getElementById('deductions').value = deductions;
            document.getElementById('notes').value = notes;
            document.getElementById('payrollModal').classList.remove('hidden');
        }

        function closePayrollModal() {
            document.getElementById('payrollModal').classList.add('hidden');
        }

        function submitSalaryUpdate() {
            const id = document.getElementById('employeeId').value;
            const type = document.getElementById('salaryType').value;
            const rate = document.getElementById('salaryRate').value;

            if (!rate || rate <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Vui lòng nhập mức lương hợp lệ'
                });
                return;
            }

            fetch(`/api/employees/${id}/salary`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ salary_type: type, salary_rate: rate })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Đã cập nhật lương cơ bản',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Có lỗi xảy ra' });
                }
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Có lỗi xảy ra' }));
        }

        function submitPayrollUpdate() {
            const id = document.getElementById('payrollEmployeeId').value;
            const bonus = document.getElementById('bonus').value;
            const deductions = document.getElementById('deductions').value;
            const notes = document.getElementById('notes').value;
            const urlParams = new URLSearchParams(window.location.search);
            const month = urlParams.get('month') || '{{ now()->format("Y-m") }}';

            calculateSalary(id, month, { bonus, deductions, notes });
        }

        function calculateSalary(id, month, extraData = {}) {
            closePayrollModal();
            
            Swal.fire({
                title: 'Đang xử lý',
                text: 'Vui lòng đợi...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/api/payroll/generate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ employee_id: id, month, ...extraData })
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Hoàn tất',
                    text: 'Đã tính lương thành công',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Có lỗi xảy ra' }));
        }

        function calculateAllSalaries() {
            const urlParams = new URLSearchParams(window.location.search);
            const month = urlParams.get('month') || '{{ now()->format("Y-m") }}';

            Swal.fire({
                title: 'Xác nhận',
                text: 'Tính lương cho tất cả nhân viên?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Đang xử lý',
                        text: 'Đang tính lương...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(`/api/payroll/generate-all`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ month })
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Hoàn tất',
                            text: 'Đã tính lương cho tất cả',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    })
                    .catch(() => Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Có lỗi xảy ra' }));
                }
            });
        }
    </script>
@endsection