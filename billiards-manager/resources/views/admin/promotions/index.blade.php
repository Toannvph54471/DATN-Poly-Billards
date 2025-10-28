@extends('admin.layouts.app')

@section('title', 'Quản lý khuyến mại - F&B Management')

@section('content')
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý khuyến mại</h1>
            <p class="text-gray-600">Quản lý chương trình khuyến mại và voucher</p>
        </div>
        <div>
            <a href="{{ route('admin.promotions.create') }}"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Thêm khuyến mại
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Tổng khuyến mại</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalPromotions }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tags text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang diễn ra</p>
                    <p class="text-xl font-bold text-gray-800">{{ $ongoingPromotions }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Đang kích hoạt</p>
                    <p class="text-xl font-bold text-gray-800">{{ $activePromotions }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Ngừng kích hoạt</p>
                    <p class="text-xl font-bold text-gray-800">{{ $inactivePromotions }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pause-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

 <!-- Filter Section -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form action="{{ route('admin.promotions.index') }}" method="GET">
        <div class="flex flex-col sm:flex-row gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 min-w-0">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="Mã, tên khuyến mại...">
                </div>
            </div>

            <!-- Discount Type Filter -->
            <div class="w-full sm:w-48">
                <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-1">Loại giảm giá</label>
                <select name="discount_type" id="discount_type"
                    class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Tất cả loại</option>
                    <option value="percentage" {{ request('discount_type') == 'percentage' ? 'selected' : '' }}>Giảm giá %</option>
                    <option value="fixed" {{ request('discount_type') == 'fixed' ? 'selected' : '' }}>Giảm giá cố định</option>
                    <option value="combo" {{ request('discount_type') == 'combo' ? 'selected' : '' }}>Combo khuyến mại</option>
                </select>
            </div>

            <!-- Status Filter -->
<div class="w-full sm:w-48">
    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
    <select name="status" id="status"
        class="block w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
        <option value="">Tất cả trạng thái</option>
        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Hết hạn</option>
        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
    </select>
</div>
            <!-- Action Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('admin.promotions.index') }}"
                    class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-300 transition flex items-center text-sm whitespace-nowrap">
                    <i class="fas fa-redo mr-2"></i>
                    Làm mới
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center text-sm whitespace-nowrap">
                    <i class="fas fa-filter mr-2"></i>
                    Lọc
                </button>
            </div>
        </div>
    </form>
</div>

    <!-- Promotions Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Khuyến mại</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Thông tin</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Giá trị & Điều kiện</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="text-left py-4 px-6 text-sm font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($promotions as $promotion)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-r from-blue-100 to-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-tag text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $promotion->name }}</div>
                                        <div class="text-sm text-gray-500">#{{ $promotion->promotion_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm text-gray-900">
                                    <div class="mb-1">
                                        <span class="font-medium">Thời gian:</span> 
                                        <span class="text-gray-600">
                                            {{ \Carbon\Carbon::parse($promotion->start_date)->format('d/m/Y') }} - 
                                            {{ \Carbon\Carbon::parse($promotion->end_date)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Áp dụng:</span> 
                                        <span class="text-gray-600">
                                            @if($promotion->applies_to_combo && $promotion->applies_to_time_combo)
                                                Combo bàn & Combo giờ
                                            @elseif($promotion->applies_to_combo)
                                                Combo bàn
                                            @elseif($promotion->applies_to_time_combo)
                                                Combo giờ
                                            @else
                                                Sản phẩm
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm">
                                    <div class="mb-1">
                                        <span class="font-medium text-gray-900">
                                            @if($promotion->discount_type === 'percentage')
                                                Giảm {{ $promotion->discount_value }}%
                                            @elseif($promotion->discount_type === 'fixed')
                                                Giảm {{ number_format($promotion->discount_value) }} đ
                                            @else
                                                Combo khuyến mại
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-700">Điều kiện:</span>
                                        <span class="ml-1 text-gray-600">
                                            @if($promotion->min_play_minutes)
                                                Tối thiểu {{ $promotion->min_play_minutes }} phút
                                            @else
                                                Không có điều kiện
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                           <td class="py-4 px-6 whitespace-nowrap">
    @php
        $now = now();
        $startDate = \Carbon\Carbon::parse($promotion->start_date);
        $endDate = \Carbon\Carbon::parse($promotion->end_date);
        
        // Status chỉ có 0 và 1
        if ($promotion->status == 0) {
            // Ngừng kích hoạt
            $statusClass = 'bg-gray-100 text-gray-800';
            $statusText = 'Ngừng kích hoạt';
            $icon = 'fas fa-pause-circle';
        } elseif ($startDate > $now) {
            // Sắp diễn ra (status = 1 nhưng chưa đến ngày bắt đầu)
            $statusClass = 'bg-orange-100 text-orange-800';
            $statusText = 'Sắp diễn ra';
            $icon = 'fas fa-clock';
        } elseif ($endDate < $now) {
            // Đã kết thúc (status = 1 nhưng đã qua ngày kết thúc)
            $statusClass = 'bg-red-100 text-red-800';
            $statusText = 'Đã kết thúc';
            $icon = 'fas fa-stop-circle';
        } else {
            // Đang diễn ra (status = 1 và trong thời gian hiệu lực)
            $statusClass = 'bg-green-100 text-green-800';
            $statusText = 'Đang diễn ra';
            $icon = 'fas fa-play-circle';
        }
    @endphp
    <div class="flex justify-start">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
            <i class="{{ $icon }} mr-2" style="font-size: 8px;"></i>
            {{ $statusText }}
        </span>
    </div>
</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                {{ $promotion->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-2">
                                    {{-- <a href="{{ route('admin.promotions.show', $promotion->id) }}"
                                      class="text-green-600 hover:text-green-900 transition" title="Xem chi tiết"> --}}
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- <a href="{{ route('admin.promotions.edit', $promotion->id) }}"
                                        class="text-blue-600 hover:text-blue-900 transition" title="Chỉnh sửa"> --}}
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button type="button" class="text-red-600 hover:text-red-900 transition"
                                        title="Xóa" onclick="confirmDelete({{ $promotion->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    {{-- <form id="delete-form-{{ $promotion->id }}" 
                                          action="{{ route('admin.promotions.destroy', $promotion->id) }}" 
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
                                        <i class="fas fa-tag text-gray-400 text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Không có khuyến mại nào</h3>
                                    <p class="text-gray-500 mb-4">Không tìm thấy khuyến mại phù hợp với tiêu chí tìm kiếm.</p>
                                    <a href="{{ route('admin.promotions.create') }}" 
                                       class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition flex items-center">
                                        <i class="fas fa-plus mr-2"></i>
                                        Thêm khuyến mại đầu tiên
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($promotions->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(promotionId) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa khuyến mại này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + promotionId).submit();
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
