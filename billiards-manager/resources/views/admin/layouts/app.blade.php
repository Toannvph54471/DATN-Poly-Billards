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
            transition: all 0.3s ease;
            z-index: 1000;
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

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .sidebar {
                position: fixed;
                left: -100%;
                top: 0;
                height: 100vh;
                width: 280px;
                z-index: 1000;
            }

            .sidebar.mobile-open {
                left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.mobile-open {
                display: block;
            }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
                padding: 10px;
            }

            .search-container {
                width: 100%;
                max-width: 100%;
            }

            .user-menu {
                width: 100%;
                justify-content: space-between;
            }

            .user-info {
                display: none;
            }

            .nav-text {
                display: inline !important;
            }

            /* Table responsive */
            .table-container {
                overflow-x: auto;
            }

            /* Card responsive */
            .stat-card {
                margin-bottom: 15px;
            }

            /* Button group responsive */
            .btn-group-mobile {
                flex-direction: column;
                gap: 10px;
            }

            .btn-group-mobile .btn {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .mobile-menu-btn {
                top: 10px;
                left: 10px;
                padding: 8px;
            }

            .sidebar {
                width: 85%;
            }

            .header-content {
                padding: 8px;
            }

            .user-menu {
                gap: 10px;
            }

            .notification-badge {
                font-size: 10px;
                width: 16px;
                height: 16px;
            }
        }

        /* Hide nav text on mobile by default, show when sidebar is open */
        @media (max-width: 768px) {
            .nav-text {
                display: none;
            }

            .sidebar.mobile-open .nav-text {
                display: inline;
            }
        }
    </style>
    @yield('styles')
</head>

<body class="text-gray-800">
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars text-lg"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 flex-shrink-0 text-white" id="sidebar">
            <!-- Logo -->
            <div class="p-4 md:p-6 border-b border-blue-600">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-blue-600 text-lg md:text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg md:text-xl font-bold">F&B POS</h1>
                        <p class="text-blue-200 text-xs">tqdong22</p>
                    </div>
                    <!-- Close button for mobile -->
                    <button class="md:hidden ml-auto text-white" id="closeSidebar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-3 md:p-4 space-y-1" style="height: calc(100vh - 80px); overflow-y: auto;">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-chart-pie w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Tổng quan</span>
                </a>

                <a href="{{ route('admin.tables.index') }}"
                    class="nav-item {{ request()->routeIs('tables.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fa-solid fa-table w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Quản lý bàn</span>
                </a>
                
                <a href="{{ route('admin.table_rates.index') }}"
                    class="nav-item {{ request()->routeIs('tables.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fa-solid fa-table w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Quản lý loại bàn</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-users w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Quản lý người dùng</span>
                </a>

                <a href="{{ route('admin.customers.index') }}"
                    class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-users w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Quản lý khách hàng</span>
                </a>

                <a href="{{ route('admin.employees.index') }}"
                    class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-user-tie w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Quản lý nhân viên</span>
                </a>

                <a href=""
                    class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-file-invoice-dollar w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Hóa đơn</span>
                </a>

                <a href="{{ route('admin.combos.index') }}"
                    class="nav-item {{ request()->routeIs('combos.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-layer-group w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Quản lý Combos</span>
                </a>
                
                <a href="{{ route('admin.products.index') }}"
                    class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-cubes w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Quản lý sản phẩm</span>
                </a>
                
                <a href="{{ route('admin.promotions.index') }}"
                    class="nav-item {{ request()->routeIs('promotions.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-tag w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Quản lý khuyến mại</span>
                </a>

                <a href="{{ route('admin.roles.index') }}"
                    class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-user-tag w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Vai Trò</span>
                </a>

                <a href=""
                    class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }} flex items-center p-3">
                    <i class="fas fa-tag w-5 md:w-6 mr-3 text-center"></i>
                    <span class="font-medium nav-text">Quản lý danh mục</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden main-content">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-center px-4 md:px-6 py-3 md:py-4 header-content">
                    <!-- Search -->
                    <div class="w-full md:flex-1 md:max-w-2xl mb-3 md:mb-0 search-container">
                        <div class="relative">
                            <input type="text" placeholder="Tìm kiếm..."
                                class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm md:text-base">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center justify-between w-full md:w-auto user-menu">
                        <div class="flex items-center space-x-3 md:space-x-4">
                            <!-- Notifications -->
                            <button class="relative p-2 text-gray-600 hover:text-blue-600 transition">
                                <i class="fas fa-bell text-lg md:text-xl"></i>
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center notification-badge">3</span>
                            </button>

                            <!-- Messages -->
                            <button class="relative p-2 text-gray-600 hover:text-blue-600 transition">
                                <i class="fas fa-envelope text-lg md:text-xl"></i>
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center notification-badge">5</span>
                            </button>
                        </div>

                        <!-- User -->
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm md:text-base">
                                TD
                            </div>
                            <div class="hidden md:block user-info">
                                <p class="text-sm font-medium">Trần Quang Đông</p>
                                <p class="text-xs text-gray-500">Quản lý</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 hidden md:block"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const closeSidebar = document.getElementById('closeSidebar');

            function toggleSidebar() {
                sidebar.classList.toggle('mobile-open');
                sidebarOverlay.classList.toggle('mobile-open');
            }

            mobileMenuBtn.addEventListener('click', toggleSidebar);
            closeSidebar.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);

            // Close sidebar when clicking on a nav link (mobile)
            const navLinks = document.querySelectorAll('.nav-item');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        toggleSidebar();
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('mobile-open');
                }
            });

            // Add CSS variables
            document.documentElement.style.setProperty('--primary', '#1e40af');
            document.documentElement.style.setProperty('--primary-dark', '#1e3a8a');
            document.documentElement.style.setProperty('--secondary', '#f59e0b');
        });
    </script>
</body>

</html>