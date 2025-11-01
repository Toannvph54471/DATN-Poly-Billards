@extends('admin.layouts.app')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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

            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Danh mục</p>
                    <p class="font-medium text-gray-900 mt-1">{{ $product->category?->name ?? '—' }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Loại sản phẩm</p>
                    <p class="font-medium text-gray-900 mt-1">
                        <span class="px-2 py-1 text-xs rounded-full {{ $product->product_type == 'Service' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $product->product_type == 'Service' ? 'Dịch vụ' : 'Hàng tiêu dùng' }}
                        </span>
                    </p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Giá bán</p>
                    <p class="font-medium text-green-600 mt-1">{{ number_format($product->price) }} đ</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Giá vốn</p>
                    <p class="font-medium text-gray-900 mt-1">{{ number_format($product->cost_price ?? 0) }} đ</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Tồn kho</p>
                    <p class="font-medium text-gray-900 mt-1">{{ $product->stock_quantity }} {{ $product->unit }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Trạng thái</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 {{ $product->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $product->status == 'Active' ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
                    </span>
                </div>
            </div>

            @if($product->isLowStock())
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mt-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-xl mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-orange-800">Cảnh báo tồn kho</h4>
                        <p class="text-orange-700 text-sm">Sản phẩm sắp hết hàng. Vui lòng nhập thêm.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Thông tin bổ sung</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Mô tả</p>
                    <p class="text-gray-900">{{ $product->description ?? 'Không có mô tả' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Cảnh báo tồn kho</p>
                    <p class="text-gray-900">{{ $product->min_stock_level }} {{ $product->unit }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ngày tạo</p>
                    <p class="text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Cập nhật lần cuối</p>
                    <p class="text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Sản phẩm liên quan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($relatedProducts as $related)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                <div class="flex items-center mb-3">
                    @if($related->image)
                        <img class="h-10 w-10 rounded-lg object-cover mr-3" src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->name }}">
                    @else
                        <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ Str::limit($related->name, 20) }}</h4>
                        <p class="text-xs text-gray-500">#{{ $related->product_code }}</p>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-green-600">{{ number_format($related->price) }} đ</span>
                    <a href="{{ route('admin.products.show', $related->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        Xem chi tiết
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection