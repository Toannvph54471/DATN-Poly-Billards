@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Giám sát Chấm công</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.attendance.manual-history') }}"
                class="bg-white border border-gray-300 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-50 transition flex items-center">
                <i class="fas fa-history mr-2"></i> Lịch sử Check-out hộ
            </a>
            <a href="{{ route('admin.employees.index') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-users mr-2"></i> Quản lý nhân viên
            </a>
        </div>
    </div>

    <!-- Pending Late Requests Section -->
    @if($pendingLate->count() > 0)
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-red-600 mb-4 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i> Yêu cầu duyệt đi muộn ({{ $pendingLate->count() }})
        </h2>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số phút muộn</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lý do</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pendingLate as $request)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $request->employee->name }}</div>
                            <div class="text-sm text-gray-500">{{ $request->employee->employee_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($request->check_in)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $request->late_minutes }} phút
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $request->late_reason }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="approveLate({{ $request->id }})" class="text-green-600 hover:text-green-900 mr-3">Duyệt</button>
                            <button onclick="rejectLate({{ $request->id }})" class="text-red-600 hover:text-red-900">Từ chối</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Active Employees Section -->
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-user-clock mr-2"></i> Nhân viên đang làm việc
    </h2>
    
    @if($activeEmployees->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-500">
            Hiện không có nhân viên nào đang check-in.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($activeEmployees as $attendance)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border-l-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-green-100 text-green-600 font-bold text-lg">
                                {{ substr($attendance->employee->name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $attendance->employee->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $attendance->employee->employee_code }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full animate-pulse">
                            Online
                        </span>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Giờ vào:</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Thời gian:</span>
                            <span class="text-sm font-bold text-green-600">
                                {{ \Carbon\Carbon::parse($attendance->check_in)->diffForHumans(null, true) }}
                            </span>
                        </div>
                        @if($attendance->late_minutes > 0)
                        <div class="mt-2 text-xs text-red-500 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Đi muộn {{ $attendance->late_minutes }} phút
                            @if($attendance->approval_status == 'pending')
                                (Chờ duyệt)
                            @elseif($attendance->approval_status == 'approved')
                                (Đã duyệt)
                            @endif
                        </div>
                        @endif

                        
                        <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                            <button onclick="openCheckoutModal({{ $attendance->id }})" 
                                class="text-xs bg-red-50 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100 transition flex items-center">
                                <i class="fas fa-sign-out-alt mr-1"></i> Check-out hộ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <!-- Admin Checkout Modal -->
    <div id="checkoutModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-bold mb-4">Check-out hộ nhân viên</h3>
            <p class="text-sm text-gray-600 mb-4">Bạn đang thực hiện check-out cho nhân viên này. Vui lòng nhập lý do.</p>
            <input type="hidden" id="checkoutEmployeeId">
            <textarea id="checkoutReason" class="w-full border rounded p-2 mb-4" rows="3" placeholder="Nhập lý do (bắt buộc)..."></textarea>
            <div class="flex justify-end gap-2">
                <button onclick="closeCheckoutModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Hủy</button>
                <button onclick="submitAdminCheckout()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function approveLate(id) {
        if(!confirm('Duyệt yêu cầu đi muộn này?')) return;
        
        fetch(`/admin/attendance/${id}/approve-late`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    function rejectLate(id) {
        if(!confirm('Từ chối yêu cầu đi muộn này?')) return;

        fetch(`/admin/attendance/${id}/reject-late`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }

    // Auto refresh every 60 seconds
    setInterval(() => {
        location.reload();
    }, 60000);

    function openCheckoutModal(id) {
        document.getElementById('checkoutEmployeeId').value = id;
        document.getElementById('checkoutReason').value = '';
        document.getElementById('checkoutModal').classList.remove('hidden');
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').classList.add('hidden');
    }

    function submitAdminCheckout() {
        const id = document.getElementById('checkoutEmployeeId').value;
        const reason = document.getElementById('checkoutReason').value;

        if (!reason.trim()) {
            alert('Vui lòng nhập lý do check-out.');
            return;
        }

        if (!confirm('Xác nhận check-out cho nhân viên này?')) return;

        fetch(`/admin/attendance/${id}/admin-checkout`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Có lỗi xảy ra.');
        });
    }
</script>
@endsection
@endsection
