@extends('admin.layouts.app')
@section('title', 'Danh sách ca làm việc')

@section('content')
<style>
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

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .shift-card {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .shift-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .shift-card:hover::before {
        left: 100%;
    }

    .shift-card:hover {
        transform: translateY(-15px) scale(1.05);
        box-shadow: 0 25px 50px -12px rgba(102, 126, 234, 0.5);
    }

    .shift-card:nth-child(1) { animation-delay: 0.1s; }
    .shift-card:nth-child(2) { animation-delay: 0.2s; }
    .shift-card:nth-child(3) { animation-delay: 0.3s; }
    .shift-card:nth-child(4) { animation-delay: 0.4s; }
    .shift-card:nth-child(5) { animation-delay: 0.5s; }
    .shift-card:nth-child(6) { animation-delay: 0.6s; }

    .shift-morning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .shift-afternoon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .shift-evening {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .shift-night {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .icon-wrapper {
        width: 70px;
        height: 70px;
        margin: 0 auto 1rem;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        animation: float 3s ease-in-out infinite;
    }

    .employee-badge {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        padding: 0.5rem 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s;
    }

    .shift-card:hover .employee-badge {
        background: rgba(255, 255, 255, 0.35);
        transform: scale(1.1);
    }

    .time-display {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        font-size: 1.1rem;
        letter-spacing: 1px;
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 1rem;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        animation: fadeInUp 0.5s ease-out;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
</style>

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    ⏰ Quản Lý Ca Làm Việc
                </h1>
                <p class="text-white/80">Tổng quan các ca làm việc trong hệ thống</p>
            </div>
            <div class="glass-effect">
                <p class="text-white font-semibold">
                    <span class="pulse-dot"></span>
                    {{ $shifts->count() }} Ca làm việc
                </p>
            </div>
        </div>
    </div>

    <!-- Shift Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($shifts as $index => $shift)
        <a href="{{ route('admin.shifts.show', $shift->id) }}" 
            class="shift-card shift-{{ strtolower($shift->name) }} block">
            <div class="p-8 text-center relative z-10">
                <!-- Icon -->
                <div class="icon-wrapper">
                    @if(stripos($shift->name, 'sáng') !== false || stripos($shift->name, 'morning') !== false)
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                        </svg>
                    @elseif(stripos($shift->name, 'chiều') !== false || stripos($shift->name, 'afternoon') !== false)
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0z"/>
                        </svg>
                    @elseif(stripos($shift->name, 'tối') !== false || stripos($shift->name, 'evening') !== false)
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                    @else
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </div>

                <!-- Shift Name -->
                <h2 class="text-3xl font-bold text-white mb-4">
                    {{ $shift->name }}
                </h2>

                <!-- Time Display -->
                <div class="glass-effect mb-6">
                    <p class="time-display text-white">
                        {{ $shift->start_time }} → {{ $shift->end_time }}
                    </p>
                </div>

                <!-- Employee Count -->
                <div class="employee-badge">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    <span class="text-white font-semibold">
                        {{ $shift->employees->count() }} Nhân viên
                    </span>
                </div>

                <!-- Hover Effect Indicator -->
                <div class="mt-6 opacity-0 group-hover:opacity-100 transition-opacity">
                    <p class="text-white text-sm">
                        ✨ Nhấn để xem chi tiết
                    </p>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    @if($shifts->isEmpty())
    <div class="text-center py-16">
        <div class="inline-block p-8 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl shadow-lg">
            <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-700 mb-2">Chưa có ca làm việc</h3>
            <p class="text-gray-500">Hãy thêm ca làm việc đầu tiên của bạn!</p>
        </div>
    </div>
    @endif
</div>
@endsection