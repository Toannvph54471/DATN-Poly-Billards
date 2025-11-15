<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'F&B Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- CSRF --}}

    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #1e40af;
            --primary-dark: #1e3a8a;
            --secondary: #f59e0b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-100: #f8fafc;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-600: #475569;
            --gray-800: #1e293b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .nav-item {
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 4px 8px;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid var(--secondary);
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .table-row {
            transition: all 0.3s ease;
        }

        .table-row:hover {
            background: #f8fafc;
        }

        .badge-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .badge-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
    </style>
    @yield('styles')
</head>

<body class="text-gray-800">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 flex-shrink-0 text-white">
            <!-- Logo -->
            <div class="p-6 border-b border-blue-600">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">F&B POS</h1>
                        <p class="text-blue-200 text-xs">tqdong22</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1" style="height: 100vh; overflow-y: auto; position: fixed; padding-bottom: 150px;scrollbar-gutter: stable;box-sizing: border-box;padding-right: 0px; width: 260px;">
                <a href=""
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-chart-pie w-6 mr-3"></i>
                    <span class="font-medium">Tổng quan</span>
                </a>

                <a href="{{ route('admin.tables.index') }}"
                    class="nav-item {{ request()->routeIs('tables.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fa-solid fa-table w-6 mr-3"></i>
                    <span class="font-medium">Quản lý bàn</span>
                </a>
                <a href="{{ route('admin.table_rates.index') }}"
                    class="nav-item {{ request()->routeIs('tables.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fa-solid fa-table w-6 mr-3"></i>
                    <span class="font-medium">Quản lý loại bàn</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-users w-6 mr-3"></i>
                    <span class="font-medium">Quản lý người dùng</span>
                </a>

               <a href="{{ route('admin.customers.index') }}"
                    class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }} flex items-center p-3">
                     <i class="fas fa-users w-6 mr-3"></i>
                     <span class="font-medium">Quản lý khách hàng</span>
               </a>

                <a href="{{ route('admin.employees.index') }}"
                    class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }} flex items-center px-4 py-3 text-white hover:text-white">
                    <i class="fas fa-user-tie mr-3"></i>
                    Quản lý nhân viên
                </a>

                <a href=""
                    class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-file-invoice-dollar w-6 mr-3"></i>
                    <span class="font-medium">Hóa đơn</span>
                </a>

                <a href="{{ route('admin.combos.index') }}"
                    class="nav-item {{ request()->routeIs('combos.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-layer-group w-6 mr-3"></i>
                    <span class="font-medium">Quản lý Combos</span>
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-cubes text-white text-lg w-6 mr-3"></i>
                    <span class="font-medium">Quản lý sản phẩm</span>
                </a>
                <a href="{{ route('admin.promotions.index') }}"
                    class="nav-item {{ request()->routeIs('promotions.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-tag w-6 mr-3"></i>
                    <span class="font-medium">Quản lý khuyến mại</span>
                </a>
               
                <a href="{{ route('admin.roles.index') }}"
                    class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-user-tag mr-3 w-5 text-center text-white"></i>
                    <span class="font-medium">Vai Trò</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <!-- Search -->
                    <div class="flex-1 max-w-2xl">
                        <div class="relative">
                            <input type="text" placeholder="Tìm kiếm..."
                                class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:text-blue-600 transition">
                            <i class="fas fa-bell text-xl"></i>
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                        </button>

                        <!-- Messages -->
                        <button class="relative p-2 text-gray-600 hover:text-blue-600 transition">
                            <i class="fas fa-envelope text-xl"></i>
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">5</span>
                        </button>

                        <!-- User -->
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                TD
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium">Trần Quang Đông</p>
                                <p class="text-xs text-gray-500">Quản lý</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
    <script>
        // Add CSS variables
        document.documentElement.style.setProperty('--primary', '#1e40af');
        document.documentElement.style.setProperty('--primary-dark', '#1e3a8a');
        document.documentElement.style.setProperty('--secondary', '#f59e0b');
    </script>
</body>

</html>
