@extends('admin.layouts.app')

@section('title', 'Ca làm việc - ' . $shift->name)

@section('content')
<style>
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .max-w-5xl > h1 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: fadeInDown 0.6s ease-out;
        font-size: 2.5rem;
        font-weight: 900;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .bg-white {
        animation: fadeInUp 0.8s ease-out;
        border-radius: 20px !important;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1) !important;
        border: 2px solid transparent;
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(135deg, #667eea, #764ba2) border-box;
    }

    .min-w-full {
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
    }

    thead tr {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    thead th {
        color: white !important;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.875rem;
        border: none !important;
        padding: 1rem !important;
        position: relative;
    }

    thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: rgba(255,255,255,0.3);
    }

    .table-row {
        animation: slideInLeft 0.5s ease-out forwards;
        opacity: 0;
        transition: all 0.3s ease;
        position: relative;
    }

    .table-row:nth-child(1) { animation-delay: 0.1s; }
    .table-row:nth-child(2) { animation-delay: 0.15s; }
    .table-row:nth-child(3) { animation-delay: 0.2s; }
    .table-row:nth-child(4) { animation-delay: 0.25s; }
    .table-row:nth-child(5) { animation-delay: 0.3s; }
    .table-row:nth-child(6) { animation-delay: 0.35s; }
    .table-row:nth-child(7) { animation-delay: 0.4s; }
    .table-row:nth-child(8) { animation-delay: 0.45s; }
    .table-row:nth-child(9) { animation-delay: 0.5s; }
    .table-row:nth-child(10) { animation-delay: 0.55s; }

    .table-row:hover {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }

    .table-row::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .table-row:hover::before {
        opacity: 1;
    }

    .table-row td {
        border-color: #e5e7eb !important;
        padding: 1rem !important;
        font-weight: 500;
        color: #374151;
    }

    .table-row:nth-child(even) {
        background-color: rgba(249, 250, 251, 0.5);
    }

    .badge-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        animation: pulse 2s ease-in-out infinite;
    }

    .badge-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        animation: pulse 2s ease-in-out infinite;
    }

    .badge-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        animation: pulse 2s ease-in-out infinite;
    }

    .table-row td span {
        display: inline-block;
        transition: all 0.3s;
    }

    .table-row:hover td span {
        transform: scale(1.1);
    }

    tbody tr:empty ~ tr td,
    tbody tr:has(+ tr:empty) td {
        animation: fadeInUp 0.8s ease-out;
    }

    .text-center.text-gray-500 {
        padding: 3rem !important;
        font-size: 1.125rem;
        color: #9ca3af !important;
        font-weight: 600;
    }

    .text-center.text-gray-500::before {
        content: '📋';
        display: block;
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Hover effect for table cells */
    .table-row td:first-child {
        font-weight: 700;
        color: #667eea;
        font-size: 1.1rem;
    }

    .table-row td:nth-child(2) {
        font-weight: 700;
        color: #1f2937;
    }

    /* Add glow effect on hover */
    .table-row:hover td:first-child {
        text-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
    }
</style>

<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Ca {{ $shift->name }}</h1>

    <div class="bg-white shadow rounded-lg p-6">
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-3 border">#</th>
                    <th class="p-3 border">Tên nhân viên</th>
                    <th class="p-3 border">Chức vụ</th>
                    <th class="p-3 border">Trạng thái</th>
                    <th class="p-3 border">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shift->employees as $index => $employee)
                <tr class="table-row">
                    <td class="p-3 border">{{ $index + 1 }}</td>
                    <td class="p-3 border">{{ $employee->user->name ?? 'N/A' }}</td>
                    <td class="p-3 border">{{ $employee->position }}</td>
                    <td class="p-3 border">
                        <span class="px-3 py-1 rounded-full text-sm
                            {{ $employee->pivot->status == 'Completed' ? 'badge-success' : 
                               ($employee->pivot->status == 'Working' ? 'badge-warning' : 'badge-danger') }}">
                            {{ $employee->pivot->status }}
                        </span>
                    </td>
                    <td class="p-3 border">{{ $employee->pivot->note ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-3 text-center text-gray-500">Chưa có nhân viên nào trong ca này.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection