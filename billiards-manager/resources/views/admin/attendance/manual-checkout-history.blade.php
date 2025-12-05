@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Lịch sử Check-out hộ</h1>
            <p class="text-sm text-gray-500 mt-1">Danh sách các lần admin thực hiện check-out thủ công cho nhân viên</p>
        </div>
        <a href="{{ route('admin.attendance.monitor') }}"
            class="bg-gray-100 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-200 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại Giám sát
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian Check-out</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người thực hiện</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lý do</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng giờ làm</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($history as $record)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-3">
                                {{ substr($record->employee->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $record->employee->name }}</div>
                                <div class="text-sm text-gray-500">{{ $record->employee->employee_code }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($record->check_out)->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $record->adminCheckoutUser ? $record->adminCheckoutUser->name : 'N/A' }}</div>
                        <div class="text-xs text-gray-500">Admin</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $record->admin_checkout_reason }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($record->total_minutes / 60, 1) }} giờ
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-history text-4xl mb-3 text-gray-300"></i>
                        <p>Chưa có dữ liệu check-out hộ nào.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $history->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
