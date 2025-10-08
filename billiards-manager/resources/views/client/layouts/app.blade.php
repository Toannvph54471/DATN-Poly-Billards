<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Billiard Club - Premium Billiard Experience')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="fixed w-full top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <span class="text-3xl font-bold text-amber-600">BILLIARD</span>
                        <span class="text-gray-800 text-sm ml-2 font-semibold">CLUB</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-amber-600 transition font-medium">Trang chủ</a>
                    <a href="#about" class="text-gray-700 hover:text-amber-600 transition font-medium">Giới thiệu</a>
                    <a href="#services" class="text-gray-700 hover:text-amber-600 transition font-medium">Dịch vụ</a>
                    <a href="#gallery" class="text-gray-700 hover:text-amber-600 transition font-medium">Thư viện</a>
                    <a href="#pricing" class="text-gray-700 hover:text-amber-600 transition font-medium">Bảng giá</a>
                    <a href="#contact" class="text-gray-700 hover:text-amber-600 transition font-medium">Liên hệ</a>
                </nav>

                <!-- Contact Button -->
                <button onclick="window.location.href='#contact'" class="hidden md:block bg-amber-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-amber-700 transition shadow-lg">
                    Đặt bàn ngay
                </button>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-800">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav id="mobile-menu" class="hidden md:hidden pb-4 bg-white">
                <div class="flex flex-col space-y-4">
                    <a href="#home" class="text-gray-700 hover:text-amber-600 transition font-medium">Trang chủ</a>
                    <a href="#about" class="text-gray-700 hover:text-amber-600 transition font-medium">Giới thiệu</a>
                    <a href="#services" class="text-gray-700 hover:text-amber-600 transition font-medium">Dịch vụ</a>
                    <a href="#gallery" class="text-gray-700 hover:text-amber-600 transition font-medium">Thư viện</a>
                    <a href="#pricing" class="text-gray-700 hover:text-amber-600 transition font-medium">Bảng giá</a>
                    <a href="#contact" class="text-gray-700 hover:text-amber-600 transition font-medium">Liên hệ</a>
                    <button onclick="window.location.href='#contact'" class="bg-amber-600 text-white px-6 py-2 rounded-full font-semibold w-full hover:bg-amber-700">
                        Đặt bàn ngay
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-2xl font-bold text-amber-500 mb-4">BILLIARD CLUB</h3>
                    <p class="text-gray-300 text-sm">
                        Điểm đến hàng đầu cho những người đam mê bi-a. Bàn chuyên nghiệp, không gian đẳng cấp và trải nghiệm khó quên.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Liên kết nhanh</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#home" class="text-gray-300 hover:text-amber-500 transition">Trang chủ</a></li>
                        <li><a href="#about" class="text-gray-300 hover:text-amber-500 transition">Giới thiệu</a></li>
                        <li><a href="#services" class="text-gray-300 hover:text-amber-500 transition">Dịch vụ</a></li>
                        <li><a href="#gallery" class="text-gray-300 hover:text-amber-500 transition">Thư viện</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Thông tin liên hệ</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-center text-gray-300">
                            <i class="fas fa-phone mr-2 text-amber-500"></i>
                            +84 123 456 789
                        </li>
                        <li class="flex items-center text-gray-300">
                            <i class="fas fa-envelope mr-2 text-amber-500"></i>
                            info@billiardclub.com
                        </li>
                        <li class="flex items-center text-gray-300">
                            <i class="fas fa-map-marker-alt mr-2 text-amber-500"></i>
                            Hà Nội, Việt Nam
                        </li>
                    </ul>
                </div>

                <!-- Opening Hours -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Giờ mở cửa</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-2 text-amber-500"></i>
                            T2 - T6: 10:00 - 02:00
                        </li>
                        <li class="pl-6">T7 - CN: 10:00 - 04:00</li>
                    </ul>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-300 hover:text-amber-500 transition">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-amber-500 transition">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-amber-500 transition">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; 2025 Billiard Club. Bản quyền thuộc về chúng tôi.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            const icon = mobileMenuBtn.querySelector('i');
            if (mobileMenu.classList.contains('hidden')) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            } else {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            }
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    </script>

    @stack('scripts')
</body>
</html>