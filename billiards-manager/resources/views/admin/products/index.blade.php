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
            <br>
            <a href="{{ route('admin.products.trashed') }}" 
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition mt-2 inline-block">
                <i class="fas fa-trash-restore mr-1"></i> Sản phẩm đã xóa
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

    <!-- Filters -->
    <form method="GET" class="mb-6 bg-white p-4 rounded-xl shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên hoặc mã..."
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">

            <select name="category_id" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <select name="product_type" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả loại</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ request('product_type') == $type ? 'selected' : '' }}>
                        {{ $type == 'Service' ? 'Dịch vụ' : 'Hàng tiêu dùng' }}
                    </option>
                @endforeach
            </select>

            <select name="is_available" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('is_available') == '1' ? 'selected' : '' }}>Đang kinh doanh</option>
                <option value="0" {{ request('is_available') == '0' ? 'selected' : '' }}>Ngừng kinh doanh</option>
            </select>
        </div>
        <div class="mt-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Lọc
            </button>
        </div>
    </form>

    <!-- Products Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hình</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tồn kho</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="h-10 w-10 rounded object-cover">
                            @else
                                <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">#{{ $product->product_code }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $product->product_type == 'Service' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $product->product_type == 'Service' ? 'Dịch vụ' : 'Hàng tiêu dùng' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm font-medium">{{ number_format($product->price) }} đ</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $product->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->status == 'Active' ? 'Hoạt động' : 'Dừng' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $product->stock_quantity }} {{ $product->unit }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.products.show', $product->id) }}" class="text-blue-600 hover:text-blue-800">Xem</a>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="text-yellow-600 hover:text-yellow-800">Sửa</a>
                            <form id="delete-form-{{ $product->id }}" action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $product->id }})" class="text-red-600 hover:text-red-800">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <p class="mb-4">Không tìm thấy sản phẩm phù hợp.</p>
                            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Thêm sản phẩm đầu tiên
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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
</style>