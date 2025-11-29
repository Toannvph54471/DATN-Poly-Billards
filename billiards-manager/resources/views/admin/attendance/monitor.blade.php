@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Giám sát Chấm công</h1>
        <div class="flex space-x-2 items-center">
            <a href="{{ route('admin.attendance.qr_code') }}" target="_blank" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center">
                <i class="fas fa-qrcode mr-2"></i> Trạm QR
            </a>
            <div class="border-l pl-2 flex space-x-2">
                <div class="flex items-center">
                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                    <span class="text-sm text-gray-600">Đang làm việc</span>
                </div>
                <div class="flex items-center ml-4">
                    <span class="w-3 h-3 bg-gray-300 rounded-full mr-2"></span>
                    <span class="text-sm text-gray-600">Đã nghỉ / Chưa vào</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($employees as $employee)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border-l-4 {{ $employee->is_online ? 'border-green-500' : 'border-gray-300' }}">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg {{ $employee->is_online ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ substr($employee->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $employee->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $employee->employee_code }}</p>
                        </div>
                    </div>
                    @if($employee->is_online)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full animate-pulse">
                            Online
                        </span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                            Offline
                        </span>
                    @endif
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-500">Chức vụ:</span>
                        <span class="text-sm font-medium text-gray-900 capitalize">{{ $employee->position }}</span>
                    </div>
                    
                    @if($employee->is_online)
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Giờ vào ca:</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $employee->check_in_time ? \Carbon\Carbon::parse($employee->check_in_time)->format('H:i:s') : '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Thời gian làm:</span>
                            <span class="text-sm font-bold text-green-600">{{ $employee->work_duration }}</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Trạng thái:</span>
                            <span class="text-sm text-gray-400 italic">Không có ca làm việc</span>
                        </div>
                    @endif
                </div>
            </div>
            @if($employee->is_online)
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-end">
                    <button onclick="adminCheckOut('{{ $employee->employee_code }}')" class="text-sm text-red-600 hover:text-red-800 font-medium transition">
                        Check-out hộ <i class="fas fa-sign-out-alt ml-1"></i>
                    </button>
                </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

@section('scripts')
<script>
    function adminCheckOut(code) {
        Swal.fire({
            title: 'Xác nhận Check-out?',
            text: "Bạn có chắc muốn check-out cho nhân viên này?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/api/attendance/check-out', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ employee_code: code })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Thành công', 'Đã check-out thành công', 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Lỗi', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Lỗi', 'Có lỗi xảy ra', 'error');
                });
            }
        })
    }

    // Auto refresh every 60 seconds
    setInterval(() => {
        location.reload();
    }, 60000);
</script>
@endsection
@endsection
