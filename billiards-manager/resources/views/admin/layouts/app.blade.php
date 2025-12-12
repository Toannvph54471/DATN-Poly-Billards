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
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 50;
        }

        .sidebar.mobile-open {
            transform: translateX(0);
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

        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }

        .mobile-overlay.active {
            display: block;
        }

        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
                position: relative;
            }

            .mobile-menu-btn {
                display: none;
            }

            .mobile-overlay {
                display: none !important;
            }
        }
    </style>
    @yield('styles')
</head>

<body class="text-gray-800">

    @auth
        <!-- Mobile Overlay -->
        <div id="mobileOverlay" class="mobile-overlay" onclick="closeMobileMenu()"></div>

        <div class="flex h-screen bg-gray-100 overflow-hidden">
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar w-64 flex-shrink-0 text-white flex flex-col fixed md:relative h-full">
                <!-- Logo hoàn chỉnh với kích thước nhỏ hơn -->
                <a href="{{ route('home') }}">
                    <div class="flex items-center space-x-2 p-4 border-b border-white/20">
                        <div class="relative">
                            <!-- Container hình vuông nhỏ hơn -->
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-500 rounded-lg flex items-center justify-center shadow-lg transform perspective-1000 rotate-6 hover:rotate-0 transition-transform duration-300 overflow-visible">
                                <!-- Viên bi đen với kích thước nhỏ hơn -->
                                <div
                                    class="w-8 h-8 bg-black rounded-full flex items-center justify-center relative border border-white shadow-inner">
                                    <!-- Hiệu ứng phản chiếu trên viên bi -->
                                    <div
                                        class="absolute top-0.5 left-1.5 w-2 h-1.5 bg-gray-400 rounded-full opacity-40 blur-sm">
                                    </div>

                                    <!-- Viên bi trắng nhỏ bên trong -->
                                    <div
                                        class="absolute w-3 h-3 bg-white rounded-full opacity-90 flex items-center justify-center">
                                        <!-- Số 8 màu đen trên nền trắng -->
                                        <span class="text-black font-bold text-[10px]">8</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Hiệu ứng ánh sáng cam (nhỏ hơn) -->
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-orange-300/50 rounded-full blur-sm">
                            </div>
                            <!-- Hiệu ứng ánh sáng trắng (nhỏ hơn) -->
                            <div class="absolute top-0.5 left-0.5 w-1.5 h-1.5 bg-white rounded-full opacity-70"></div>
                        </div>

                        <div>
                            <h1
                                class="text-xl font-black uppercase tracking-wider bg-gradient-to-r from-orange-600 via-amber-400 to-yellow-600 bg-clip-text text-transparent drop-shadow-md">
                                Poly Billiards
                            </h1>
                            <p class="text-amber-600 text-xs font-semibold tracking-wide">Đẳng cấp và đam mê</p>
                        </div>
                    </div>
                </a>
                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    @php
                        $userRole = Auth::user()->role->slug ?? '';
                        $isAdminOrManager = in_array($userRole, ['admin', 'manager']);
                        $isStaff = in_array($userRole, ['admin', 'manager', 'employee']);

                        // Lấy tên route hiện tại
                        $currentRoute = request()->route()->getName();

                        // Hàm kiểm tra route có active không
                        function isRouteActive($routePattern, $currentRoute)
                        {
                            // Nếu là route chính xác
                            if ($routePattern === $currentRoute) {
                                return true;
                            }

                            // Nếu có wildcard * (cho các route con)
                            if (strpos($routePattern, '*') !== false) {
                                $pattern = str_replace('*', '.*', $routePattern);
                                $pattern = '/^' . str_replace('.', '\.', $pattern) . '$/';
                                return preg_match($pattern, $currentRoute);
                            }

                            // Kiểm tra route bắt đầu bằng
                            if (strpos($currentRoute, $routePattern . '.') === 0) {
                                return true;
                            }

                            return false;
                        }
                    @endphp

                    <!-- Menu cho Employee -->
                    @if ($userRole === 'employee')
                        <a href="{{ route('admin.pos.dashboard') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.pos.dashboard', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-cash-register w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Bán hàng (POS)</span>
                        </a>
                    @endif
                    @if ($userRole === 'employee')
                        <a href="{{ route('admin.my-profile') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.my-profile', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-user w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Thông tin cá nhân</span>
                        </a>
                    @endif
                    @if ($userRole === 'employee')
                        <a href="{{ route('admin.schedule') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.schedule', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-calendar-check w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Lịch làm</span>
                        </a>
                    @endif


                    <!-- Menu cho Admin & Manager -->
                    @if ($isAdminOrManager)
<<<<<<< HEAD
                        <a href="{{ route('admin.dashboard') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.dashboard', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-chart-pie w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Tổng quan</span>
                        </a>
                        <a href="{{ route('admin.tables.simple-dashboard') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.tables.simple-dashboard', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-th-large w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Tổng quan bàn</span>
                        </a>
=======
                       <a href="{{ route('admin.dashboard') }}"
   onclick="closeMobileMenu()"
   class="nav-item flex items-center p-3 text-white rounded-lg 
          hover:bg-white/10
          {{ isRouteActive('admin.dashboard', $currentRoute) ? 'active bg-white/20' : '' }}">
    <i class="fas fa-chart-pie w-5 md:w-6 mr-3"></i>
    <span class="font-medium text-sm md:text-base">Tổng quan</span>
</a>

<a href="{{ route('admin.statistics') }}"
   onclick="closeMobileMenu()"
   class="nav-item flex items-center p-3 text-white rounded-lg 
          hover:bg-white/10
          {{ isRouteActive('admin.statistics', $currentRoute) ? 'active bg-white/20' : '' }}">
    <i class="fas fa-chart-bar w-5 md:w-6 mr-3"></i>
    <span class="font-medium text-sm md:text-base">Thống kê</span>
</a>

>>>>>>> 2a31992b3e6137bc1abce7c8ad76d4c689c4143d
                        <a href="{{ route('admin.tables.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.tables', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fa-solid fa-table w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Quản lý bàn</span>
                        </a>

                        <a href="{{ route('admin.bills.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.bills', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fa-solid fa-receipt w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Hóa đơn</span>
                        </a>

                        <a href="{{ route('admin.table_rates.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.table_rates', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fa-solid fa-clock w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Giá giờ bàn</span>
                        </a>

                        <a href="{{ route('admin.combos.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.combos', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-th-large w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Quản lý Combo</span>
                        </a>

                        <a href="{{ route('admin.products.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.products', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-cubes w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Sản phẩm</span>
                        </a>

                        <a href="{{ route('admin.promotions.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.promotions', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-percent w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Khuyến mại</span>
                        </a>
                    @endif

                    <!-- Menu chỉ dành cho Admin -->
                    @if ($userRole === 'admin')
                        <a href="{{ route('admin.users.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.users', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-users-cog w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Người dùng hệ thống</span>
                        </a>

                        <a href="{{ route('admin.employees.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.employees', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-user-tie w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Nhân viên</span>
                        </a>

                        <a href="{{ route('admin.roles.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.roles', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-user-shield w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Phân quyền</span>
                        </a>

                        <a href="{{ route('admin.payroll.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.payroll', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-money-bill-wave w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Tính lương</span>
                        </a>

                        <a href="{{ route('admin.attendance.monitor') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.attendance.monitor', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-user-clock w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Giám sát nhân viên</span>
                        </a>

                        <a href="{{ route('admin.shifts.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.shifts', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-calendar-alt w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Ca làm việc</span>
                        </a>

                        <a href="{{ route('admin.shiftEmployee.index') }}" onclick="closeMobileMenu()"
                            class="nav-item flex items-center p-3 text-white rounded-lg hover:bg-white/10 {{ isRouteActive('admin.shiftEmployee', $currentRoute) ? 'active bg-white/20' : '' }}">
                            <i class="fas fa-user-clock w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Phân công ca làm</span>
                        </a>
                    @endif



                    <!-- Đăng xuất -->
                    <form method="POST" action="{{ route('logout') }}" class="mt-6 md:mt-10">
                        @csrf
                        <button type="submit" onclick="closeMobileMenu()"
                            class="nav-item w-full flex items-center p-3 text-left text-red-200 hover:text-white hover:bg-red-600 rounded-lg transition">
                            <i class="fas fa-sign-out-alt w-5 md:w-6 mr-3"></i>
                            <span class="font-medium text-sm md:text-base">Đăng xuất</span>
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden md:ml-0">
                <!-- Header -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="flex justify-between items-center px-4 md:px-6 py-3 md:py-4">
                        <!-- Mobile Menu Button -->
                        <button onclick="toggleMobileMenu()" class="mobile-menu-btn md:hidden p-2 text-gray-600">
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <div class="flex-1 max-w-xl mx-2 md:mx-0">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" placeholder="Tìm kiếm..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm md:text-base">
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 md:space-x-4 relative">
                            <div class="text-right hidden sm:block">
                                <p class="font-medium text-sm md:text-base">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 capitalize">
                                    {{ Str::replace('_', ' ', Auth::user()->role->name ?? 'user') }}
                                </p>
                            </div>
                            <div class="relative">
                                <button id="accountDropdownBtn"
                                    class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm md:text-base hover:opacity-90 transition focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </button>

                                <!-- Dropdown Menu -->
                                <div id="accountDropdown"
                                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                    <a href="{{ route('attendance.my-qr') }}"
                                        class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-qrcode w-5 mr-3 text-blue-600"></i>
                                        <span>Mã QR cá nhân</span>
                                    </a>
                                    <a href="{{ route('admin.my-profile') }}"
                                        class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-user w-5 mr-3 text-green-600"></i>
                                        <span>Thông tin cá nhân</span>
                                    </a>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition text-left">
                                            <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                            <span>Đăng xuất</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
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

    <script>
        // Mobile menu functions
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Account dropdown toggle
        const accountDropdownBtn = document.getElementById('accountDropdownBtn');
        const accountDropdown = document.getElementById('accountDropdown');

        if (accountDropdownBtn && accountDropdown) {
            accountDropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                accountDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!accountDropdown.contains(e.target) && !accountDropdownBtn.contains(e.target)) {
                    accountDropdown.classList.add('hidden');
                }
            });
        }

        // Close menu when clicking on links (for mobile)
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('#sidebar a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        closeMobileMenu();
                    }
                });
            });

            // Close menu when pressing ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                    // Also close account dropdown
                    if (accountDropdown) {
                        accountDropdown.classList.add('hidden');
                    }
                }
            });

            // Close menu when window is resized to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeMobileMenu();
                }
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
