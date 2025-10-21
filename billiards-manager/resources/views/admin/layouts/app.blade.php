<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'F&B Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #1e40af;
            --primary-dark: #1e3a8a;
            --secondary: #f59e0b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --sidebar-bg: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .layout-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar {
            background: var(--sidebar-bg);
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
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

        .header-shadow {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .mobile-menu-btn {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block;
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

            .sidebar-overlay.active {
                display: block;
            }
        }

        /* Scrollbar styling */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
    @yield('styles')
</head>

<body class="text-gray-800">
    <div class="layout-container">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Logo Section -->
            <div class="p-6 border-b border-blue-600">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-blue-600 text-xl"></i>
                        <a href="/"></a>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-white font-bold text-lg">F&B Manager</h1>
                        <div class="flex items-center mt-1">
                            <i class="fa-solid fa-user text-amber-400 mr-2 text-xs"></i>
                            <span class="text-blue-200 text-sm">{{ Auth::user()->name }}</span>
                            @if (Auth::user()->isAdmin())
                                <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">Admin</span>
                            @elseif(Auth::user()->isManager())
                                <span class="ml-2 bg-purple-500 text-white text-xs px-2 py-1 rounded-full">Quản
                                    lý</span>
                            @else
                                <span class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Nhân
                                    viên</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="p-4 flex-1">
                <ul class="space-y-2">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-4 py-3 text-blue-100 hover:text-white rounded-lg transition">
                            <i class="fas fa-chart-line mr-3 w-5 text-center"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Users Management (Only for Admin/Manager) -->
                    @if (Auth::user()->isAdmin() || Auth::user()->isManager())
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center px-4 py-3 text-blue-100 hover:text-white rounded-lg transition">
                                <i class="fas fa-users mr-3 w-5 text-center"></i>
                                <span>Quản lý người dùng</span>
                            </a>
                        </li>
                    @endif

                    <!-- Products Management -->
                    <li class="nav-item">
                        <a href="{{ route('admin.products.index') }}"
                            class="flex items-center px-4 py-3 text-blue-100 hover:text-white rounded-lg transition">
                            <i class="fas fa-cubes mr-3 w-5 text-center"></i>
                            <span>Quản lý sản phẩm</span>
                        </a>
                    </li>

                    <!-- Inventory Management -->
                    <li class="nav-item">
                        <a href=""
                            class="flex items-center px-4 py-3 text-blue-100 hover:text-white rounded-lg transition">
                            <i class="fas fa-clipboard-list mr-3 w-5 text-center"></i>
                            <span>Nhập tồn kho</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.tables.index') }}"
                            class="flex items-center px-4 py-3 text-blue-100 hover:text-white rounded-lg transition">
                            <i class="fas fa-circle mr-3 w-5 text-center"></i>
                            <span>Quản lý bàn</span>
                        </a>
                    </li>

                    <!-- Orders Management -->
                    <li class="nav-item">
                        <a href=""
                            class="flex items-center px-4 py-3 text-blue-100 hover:text-white rounded-lg transition">
                            <i class="fas fa-shopping-cart mr-3 w-5 text-center"></i>
                            <span>Đơn hàng</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.employees.index') }}"
                            class="flex items-center px-4 py-3 text-blue-100 hover:text-white rounded-lg transition">
                            <i class="fas fa-user-tie mr-3 w-5 text-center"></i>
                            <span>Nhân Viên</span>
                        </a>
                    </li>

                    <!-- Reports (Only for Admin/Manager) -->
                    @if (Auth::user()->isAdmin() || Auth::user()->isManager())
                        <li class="nav-item">
                            <a href="{{ route("admin.roles.index") }}"
                                class="flex items-center px-4 py-3 text-blue-100 hover:text-white rounded-lg transition">
                                <i class="fas fa-user-shield text-blue-500 mr-3 w-5 text-center"></i>
                                <span>Vai trò</span>
                            </a>
                        </li>
                    @endif

                    <!-- Settings (Only for Admin) -->
                    @if (Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a href=""
                                class="flex items-center px-4 py-3 text-blue-100 hover:text-white rounded-lg transition">
                                <i class="fas fa-cog mr-3 w-5 text-center"></i>
                                <span>Cài đặt hệ thống</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t border-blue-600 mt-auto">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-white font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-blue-200">
                                @if (Auth::user()->isAdmin())
                                    Quản trị viên
                                @elseif(Auth::user()->isManager())
                                    Quản lý
                                @else
                                    Nhân viên
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="relative">
                        <button onclick="toggleUserDropdown()"
                            class="text-blue-200 hover:text-white transition p-1 rounded">
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>

                        <!-- User Dropdown -->
                        <div id="userDropdown"
                            class="absolute bottom-full right-0 mb-2 w-48 bg-white rounded-lg shadow-xl py-2 hidden z-50 border border-gray-200">
                            <a href=""
                                class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-user-edit mr-3 text-gray-400 w-4 text-center"></i>
                                <span class="text-sm">Hồ sơ</span>
                            </a>
                            <a href=""
                                class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-cog mr-3 text-gray-400 w-4 text-center"></i>
                                <span class="text-sm">Cài đặt</span>
                            </a>
                            <div class="border-t border-gray-200 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-gray-50 transition text-sm">
                                    <i class="fas fa-sign-out-alt mr-3 w-4 text-center"></i>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Header -->
            <header class="bg-white header-shadow border-b border-gray-200 sticky top-0 z-50">
                <div class="flex items-center justify-between px-4 py-3 md:px-6">
                    <!-- Mobile Menu Button -->
                    <button class="mobile-menu-btn p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition"
                        onclick="toggleSidebar()">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    <!-- Search -->
                    <div class="flex-1 max-w-2xl mx-4">
                        <div class="relative">
                            <input type="text" placeholder="Tìm kiếm..."
                                class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Header Right -->
                    <div class="flex items-center space-x-3">
                        <!-- Notifications -->
                        <button
                            class="relative p-2 text-gray-600 hover:text-blue-600 transition rounded-lg hover:bg-gray-100">
                            <i class="fas fa-bell text-lg"></i>
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                        </button>

                        <!-- Messages -->
                        <button
                            class="relative p-2 text-gray-600 hover:text-blue-600 transition rounded-lg hover:bg-gray-100">
                            <i class="fas fa-envelope text-lg"></i>
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">5</span>
                        </button>

                        <!-- User Info (Desktop) -->
                        <div class="hidden md:flex items-center space-x-3 pl-3 border-l border-gray-200">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">
                                    @if (Auth::user()->isAdmin())
                                        Quản trị viên
                                    @elseif(Auth::user()->isManager())
                                        Quản lý
                                    @else
                                        Nhân viên
                                    @endif
                                </p>
                            </div>
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
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');

            if (window.innerWidth < 768) {
                document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : 'auto';
            }
        }

        // Toggle user dropdown
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const userDropdown = document.getElementById('userDropdown');
            const userButton = event.target.closest('button[onclick="toggleUserDropdown()"]');

            if (!userDropdown.contains(event.target) && !userButton) {
                userDropdown.classList.add('hidden');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth >= 768) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Set active nav item based on current URL
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.href;
            const navItems = document.querySelectorAll('.nav-item a');

            navItems.forEach(item => {
                if (item.href === currentUrl) {
                    item.classList.add('text-white', 'bg-blue-700');
                    item.classList.remove('text-blue-100');
                }
            });
        });

        // Initialize tooltips and other UI enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Add any additional initialization code here
        });
    </script>
</body>

</html>
