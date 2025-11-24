<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'F&B Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background: #f5f7fa;
            color: #2d3748;
        }

        /* Sidebar Styling */
        .sidebar {
            background: #1a202c;
            box-shadow: 4px 0 12px rgba(0, 0, 0, 0.05);
        }

        .logo-section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .nav-item {
            position: relative;
            transition: all 0.2s ease;
            margin: 2px 12px;
            border-radius: 8px;
            color: #a0aec0;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .nav-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        /* Header */
        .header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        /* Search Bar */
        .search-wrapper {
            position: relative;
        }

        .search-input {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .search-input:focus {
            background: #fff;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Stats Card */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .stat-card:hover {
            border-color: #cbd5e0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        /* Table */
        .data-table {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        .table-header {
            background: #f7fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-row {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }

        .table-row:hover {
            background: #fafbfc;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-monthly {
            background: #faf5ff;
            color: #6b46c1;
            border: 1px solid #e9d8fd;
        }

        .badge-hourly {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #dbeafe;
        }

        /* Buttons */
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: #667eea;
            color: #fff;
        }

        .btn-primary:hover {
            background: #5568d3;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: #48bb78;
            color: #fff;
        }

        .btn-success:hover {
            background: #38a169;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Modal */
        .modal-overlay {
            backdrop-filter: blur(4px);
            animation: fadeIn 0.2s;
        }

        .modal-content {
            animation: slideUp 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* User Avatar */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
        }
    </style>
    @yield('styles')
</head>

<body>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="sidebar w-64 flex-shrink-0 flex flex-col">
            <!-- Logo -->
            <div class="logo-section p-5 border-b border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                        <i class="fas fa-utensils text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-white font-bold text-lg">F&B Manager</h1>
                        <p class="text-gray-400 text-xs">tqdong22</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fas fa-chart-line"></i>
                    <span>Tổng quan</span>
                </a>

                <a href="{{ route('admin.tables.index') }}"
                    class="nav-item {{ request()->routeIs('tables.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fa-solid fa-table"></i>
                    <span>Quản lý bàn</span>
                </a>

                <a href="{{ route('admin.table_rates.index') }}"
                    class="nav-item flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fa-solid fa-list"></i>
                    <span>Loại bàn</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fas fa-users"></i>
                    <span>Người dùng</span>
                </a>

                <a href="{{ route('admin.customers.index') }}"
                    class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fas fa-user-friends"></i>
                    <span>Khách hàng</span>
                </a>

                <a href="{{ route('admin.employees.index') }}"
                    class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fas fa-user-tie"></i>
                    <span>Nhân viên</span>
                </a>

                <a href="{{ route('admin.payroll.index') }}" class="flex items-center px-6 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('admin.payroll.*') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : '' }}">
                    <i class="fas fa-money-bill-wave mr-3"></i>
                    Quản lý lương
                </a>
                <a href="{{ route('admin.attendance.monitor') }}" class="flex items-center px-6 py-3 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors {{ request()->routeIs('admin.attendance.monitor') ? 'bg-blue-50 hover:text-blue-600 border-r-4 border-blue-600' : '' }}">
                    <i class="fas fa-user-clock mr-3"></i>
                    Giám sát chấm công
                </a>

                <a href="{{ route('admin.combos.index') }}"
                    class="nav-item {{ request()->routeIs('combos.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fas fa-box"></i>
                    <span>Combos</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Sản phẩm</span>
                </a>

                <a href="{{ route('admin.promotions.index') }}"
                    class="nav-item {{ request()->routeIs('promotions.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fas fa-percent"></i>
                    <span>Khuyến mãi</span>
                </a>

                <a href="{{ route('admin.roles.index') }}"
                    class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 text-sm">
                    <i class="fas fa-shield-alt"></i>
                    <span>Vai trò</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="header">
                <div class="flex items-center justify-between px-6 py-3">
                    <!-- Search -->
                    <div class="flex-1 max-w-md search-wrapper">
                        <input type="text" placeholder="Tìm kiếm..." 
                            class="search-input w-full pl-10 pr-4 py-2 rounded-lg text-sm outline-none">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center gap-4">
                        <button class="relative p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <div class="flex items-center gap-3">
                            <div class="user-avatar">TD</div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-800">Trần Quang Đông</p>
                                <p class="text-xs text-gray-500">Quản lý</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>

</html>