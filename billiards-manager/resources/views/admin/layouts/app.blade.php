<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Poly Billiards')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        :root {
            --primary: #1e40af;
            --primary-dark: #1e3a8a;
            --secondary: #f59e0b;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc; 
        }
        .sidebar { 
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%); 
            box-shadow: 2px 0 10px rgba(0,0,0,0.1); 
        }
        .nav-item { 
            transition: all 0.3s ease; 
            border-radius: 8px; 
            margin: 4px 8px; 
        }
        .nav-item:hover { 
            background: rgba(255,255,255,0.1); 
        }
        .nav-item.active { 
            background: rgba(255,255,255,0.15); 
            border-left: 4px solid var(--secondary); 
        }
    </style>
    @yield('styles')
</head>

<body class="text-gray-800">

@auth
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="sidebar w-64 flex-shrink-0 text-white flex flex-col">
        <!-- Logo -->
        <div class="p-6 border-b border-blue-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-billiard-ball text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Poly Billiards</h1>
                    <p class="text-blue-200 text-xs">{{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @php
                $userRole = Auth::user()->role->slug ?? '';
                $isAdminOrManager = in_array($userRole, ['admin', 'manager']);
                $isStaff = in_array($userRole, ['admin', 'manager', 'employee']);
            @endphp

            <!-- Menu cho Admin & Manager -->
            @if($isAdminOrManager)
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fas fa-chart-pie w-6 mr-3"></i>
                    <span class="font-medium">Tổng quan</span>
                </a>

                <a href="{{ route('admin.tables.index') }}"
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.tables.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fa-solid fa-table w-6 mr-3"></i>
                    <span class="font-medium">Quản lý bàn</span>
                </a>

                <a href="{{ route('admin.table_rates.index') }}"
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.table_rates.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fa-solid fa-clock w-6 mr-3"></i>
                    <span class="font-medium">Giá giờ bàn</span>
                </a>

                <a href="{{ route('admin.combos.index') }}"
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.combos.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fas fa-th-large w-6 mr-3"></i>
                    <span class="font-medium">Quản lý Combo</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.products.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fas fa-cubes w-6 mr-3"></i>
                    <span class="font-medium">Sản phẩm</span>
                </a>

                <a href="{{ route('admin.promotions.index') }}"
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.promotions.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fas fa-percent w-6 mr-3"></i>
                    <span class="font-medium">Khuyến mại</span>
                </a>
            @endif

            <!-- Menu chỉ dành cho Admin -->
            @if($userRole === 'admin')
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.users.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fas fa-users-cog w-6 mr-3"></i>
                    <span class="font-medium">Người dùng hệ thống</span>
                </a>

                <a href="{{ route('admin.employees.index') }}"
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.employees.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fas fa-user-tie w-6 mr-3"></i>
                    <span class="font-medium">Nhân viên</span>
                </a>

                <a href="{{ route('admin.roles.index') }}"
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.roles.*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fas fa-user-shield w-6 mr-3"></i>
                    <span class="font-medium">Phân quyền</span>
                </a>
            @endif

            <!-- Menu cho Employee -->
            @if($isStaff)
                <a href=""
                   class="flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ request()->is('employee*') ? 'bg-white/20 border-l-4 border-amber-400' : '' }}">
                    <i class="fas fa-cash-register w-6 mr-3"></i>
                    <span class="font-medium">Bán hàng (POS)</span>
                </a>
            @endif

            <!-- Đăng xuất -->
            <form method="POST" action="{{ route('logout') }}" class="mt-10">
                @csrf
                <button type="submit"
                        class="w-full flex items-center p-3 text-left text-red-200 hover:text-white hover:bg-red-600 rounded-lg transition">
                    <i class="fas fa-sign-out-alt w-6 mr-3"></i>
                    <span class="font-medium">Đăng xuất</span>
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex justify-between items-center px-6 py-4">
                <div class="flex-1 max-w-xl">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" placeholder="Tìm kiếm..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="font-medium">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">
                            {{ Str::replace('_', ' ', Auth::user()->role->name ?? 'user') }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
            @yield('content')
        </main>
    </div>
</div>

@else
    <!-- Guest Layout -->
    <div class="min-h-screen bg-gray-50">
        @yield('content')
    </div>
@endauth

@yield('scripts')
</body>
</html>