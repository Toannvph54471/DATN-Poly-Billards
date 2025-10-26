@extends('admin.layouts.app')

@section('title', 'Quản lý sản phẩm - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý sản phẩm</h1>
            <p class="text-gray-600">Quản lý thông tin và tồn kho sản phẩm</p>
        </div>
        <div>
            <a href="{{ route('admin.products.create') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Thêm sản phẩm
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng sản phẩm</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalProducts }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang kinh doanh</p>
                    <p class="text-xl font-bold text-gray-800">{{ $availableProducts }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Ngừng kinh doanh</p>
                    <p class="text-xl font-bold text-gray-800">{{ $unavailableProducts }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Sắp hết hàng</p>
                    <p class="text-xl font-bold text-gray-800">{{ $lowStockProducts }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form action="{{ route('admin.products.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Mã, tên sản phẩm...">
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                    <select name="category" id="category"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả danh mục</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Loại</label>
                    <select name="type" id="type"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả loại</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="is_available" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="is_available" id="is_available"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" {{ request('is_available') == '1' ? 'selected' : '' }}>Đang kinh doanh</option>
                        <option value="0" {{ request('is_available') == '0' ? 'selected' : '' }}>Ngừng kinh doanh</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center w-full justify-center">
                        <i class="fas fa-filter mr-2"></i>
                        Lọc
                    </button>
                    <a href="{{ route('admin.products.index') }}"
                        class="ml-2 bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Phân loại</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Giá & Tồn kho</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($product->image)
                                            <img class="h-12 w-12 rounded-lg object-cover" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">#{{ $product->product_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900">
                                    <div class="mb-1">
                                        <span class="font-medium">Loại:</span> 
                                        <span class="text-gray-600">{{ $product->product_type ?? 'Chưa phân loại' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Danh mục:</span> 
                                        <span class="text-gray-600">{{ $product->category ?? 'Chưa phân loại' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm">
                                    <div class="mb-1">
                                        <span class="font-medium text-gray-900">{{ number_format($product->price) }} đ</span>
                                        @if($product->cost_price)
                                            <span class="text-gray-500 text-xs ml-1">(Giá vốn: {{ number_format($product->cost_price) }} đ)</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-700">Tồn kho:</span>
                                        <span class="ml-1 {{ $product->stock_quantity <= $product->min_stock ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                            {{ $product->stock_quantity }} {{ $product->unit }}
                                        </span>
                                        @if($product->stock_quantity <= $product->min_stock)
                                            <i class="fas fa-exclamation-triangle text-red-500 ml-1" title="Sắp hết hàng"></i>
                                        @endif
                                    </div>
                                    @if($product->min_stock)
                                        <div class="text-xs text-gray-500 mt-1">
                                            Tồn tối thiểu: {{ $product->min_stock }} {{ $product->unit }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if ($product->status === "Active")
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        Đang kinh doanh
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                        Ngừng kinh doanh
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                {{ $product->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-2">
                                   <!-- Nút Xem chi tiết -->
                               <a href="{{ route('admin.products.show', $product->id) }}"
                                  class="text-green-600 hover:text-green-900 transition" title="Xem chi tiết">
                                 <i class="fas fa-eye"></i>
                               </a>

                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                        class="text-blue-600 hover:text-blue-900 transition" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- <button type="button" class="text-red-600 hover:text-red-900 transition"
                                        title="Xóa" onclick="confirmDelete({{ $product->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $product->id }}" 
                                          action="{{ route('admin.products.destroy', $product->id) }}" 
                                          method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-box text-gray-400 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Không có sản phẩm nào</h3>
                                    <p class="text-gray-500 mb-4">Không tìm thấy sản phẩm phù hợp với tiêu chí tìm kiếm.</p>
                                    <a href="{{ route('admin.products.create') }}" 
                                       class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                                        <i class="fas fa-plus mr-2"></i>
                                        Thêm sản phẩm đầu tiên
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($products->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(productId) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa sản phẩm này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + productId).submit();
                }
            });
        }

        // Auto submit form when filters change (optional)
        document.addEventListener('DOMContentLoaded', function() {
            // Uncomment if you want auto-submit on filter change
            /*
            const categorySelect = document.getElementById('category');
            const typeSelect = document.getElementById('type');
            const statusSelect = document.getElementById('is_available');
            
            categorySelect.addEventListener('change', function() {
                this.form.submit();
            });
            
            typeSelect.addEventListener('change', function() {
                this.form.submit();
            });
            
            statusSelect.addEventListener('change', function() {
                this.form.submit();
            });
            */
        });
    </script>
@endsection

<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .table-row {
        transition: background-color 0.2s ease;
    }
</style>