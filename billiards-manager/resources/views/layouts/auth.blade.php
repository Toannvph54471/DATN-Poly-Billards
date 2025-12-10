<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Poly Billiards')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #e7e7e7 0%, #a7befe 99%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-2xl p-8">
        <!-- Logo -->
        <div class="text-center">
            <div class="flex justify-center items-center space-x-3 mb-6">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center shadow-md">
                    <i class="fas fa-billiard-ball text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-blue-800">Poly Billiards</h1>
                </div>
            </div>
        </div>

        <!-- Content -->
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>