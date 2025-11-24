<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Billiard Club - Đăng nhập / Đăng ký')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html { scroll-behavior: smooth; }
        .fade-in { animation: fadeIn 0.6s ease-in; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Header (giống customer.blade.php) -->
    <header class="fixed w-full top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <a href="{{ url('/') }}" class="flex items-center">
                    <span class="text-3xl font-bold text-amber-600">BILLIARD</span>
                    <span class="text-gray-800 text-sm ml-2 font-semibold">CLUB</span>
                </a>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-full font-semibold hover:bg-gray-200 transition">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="bg-amber-600 text-white px-4 py-2 rounded-full font-semibold hover:bg-amber-700 transition shadow-md">
                        Đăng ký
                    </a>
                </div>

                <button id="mobile-menu-btn" class="md:hidden text-gray-800">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <nav id="mobile-menu" class="hidden md:hidden pb-4 bg-white">
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-amber-600 font-medium">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-amber-600 font-medium">Đăng ký</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow pt-24 pb-12">
        <div class="container mx-auto px-4 max-w-md">
            <div class="bg-white rounded-xl shadow-lg p-8 fade-in">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 text-center text-sm text-gray-400">
            © 2025 Billiard Club. Bản quyền thuộc về chúng tôi.
        </div>
    </footer>

    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            const icon = mobileMenuBtn.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Prevent double submission
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý...';
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>