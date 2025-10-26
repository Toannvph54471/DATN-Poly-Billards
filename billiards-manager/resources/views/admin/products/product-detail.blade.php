@extends('admin.layouts.app')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Chi tiết sản phẩm</h1>
            <p class="text-gray-600">Thông tin chi tiết về sản phẩm</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.products.index') }}"
                class="bg-gray-500 text-white rounded-lg px-4 py-2 hover:bg-gray-600 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
            <a href="{{ route('admin.products.edit', $product->id) }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Chỉnh sửa
            </a>
        </div>
    </div>

    <!-- Product Detail -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Product Image & Basic Info -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col items-center mb-6">
                @if($product->image)
                    <img class="h-48 w-48 rounded-lg object-cover mb-4" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                @else
                    <div class="h-48 w-48 rounded-lg bg-gray-100 flex items-center justify-center mb-4">
                        <i class="fas fa-box text-gray-400 text-6xl"></i>
                    </div>
                @endif
                <h2 class="text-xl font-bold text-gray-800">{{ $product->name }}</h2>
                <p class="text-gray-500">#{{ $product->product_code }}</p>
            </div>

            <!-- Status & Price -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Trạng thái</p>
                    @if ($product->status === "Active")
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                            <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                            Đang kinh doanh
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                            <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                            Ngừng kinh doanh
                        </span>
                    @endif
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Giá bán</p>
                    <p class="text-xl font-bold text-green-600 mt-1">{{ number_format($product->price) }} đ</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 gap-3">
                <button class="bg-blue-50 text-blue-600 rounded-lg py-2 px-3 hover:bg-blue-100 transition flex items-center justify-center text-sm">
                    <i class="fas fa-chart-line mr-2"></i>
                    Thống kê
                </button>
                <button class="bg-green-50 text-green-600 rounded-lg py-2 px-3 hover:bg-green-100 transition flex items-center justify-center text-sm">
                    <i class="fas fa-history mr-2"></i>
                    Lịch sử
                </button>
            </div>
        </div>

        <!-- Product Details -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Thông tin chi tiết</h3>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Loại sản phẩm</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->product_type ?? 'Chưa phân loại' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Danh mục</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->category ?? 'Chưa phân loại' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Giá vốn</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->cost_price ? number_format($product->cost_price) . ' đ' : 'Chưa cập nhật' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Đơn vị tính</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->unit ?? 'Chưa cập nhật' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tồn kho hiện tại</label>
                        <p class="mt-1 text-sm font-semibold {{ $product->stock_quantity <= $product->min_stock ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->stock_quantity }} {{ $product->unit }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tồn tối thiểu</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->min_stock ?? 0 }} {{ $product->unit }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Mô tả</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->description ?? 'Chưa có mô tả' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Ngày tạo</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Cập nhật cuối</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Alert -->
    @if($product->stock_quantity <= $product->min_stock)
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-orange-500 text-xl mr-3"></i>
            <div>
                <h4 class="font-semibold text-orange-800">Cảnh báo tồn kho</h4>
                <p class="text-orange-700 text-sm">Sản phẩm sắp hết hàng. Vui lòng nhập thêm hàng.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Sản phẩm liên quan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($relatedProducts as $relatedProduct)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                <div class="flex items-center mb-3">
                    @if($relatedProduct->image)
                        <img class="h-10 w-10 rounded-lg object-cover mr-3" src="{{ asset('storage/' . $relatedProduct->image) }}" alt="{{ $relatedProduct->name }}">
                    @else
                        <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ Str::limit($relatedProduct->name, 20) }}</h4>
                        <p class="text-xs text-gray-500">#{{ $relatedProduct->product_code }}</p>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-green-600">{{ number_format($relatedProduct->price) }} đ</span>
                    <a href="{{ route('admin.products.show', $relatedProduct->id) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        Xem chi tiết
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script>
    // Có thể thêm các script xử lý tại đây nếu cần
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Product detail page loaded');
    });
</script>
@endsection