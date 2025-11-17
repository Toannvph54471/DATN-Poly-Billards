@extends('layouts.app')

@section('title', 'Yêu cầu sửa hóa đơn - Poly Billiards')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-elegant-navy to-purple-900 rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center text-white">
                <i class="fas fa-edit text-elegant-gold text-3xl mr-4"></i>
                <div>
                    <h1 class="text-3xl font-bold mb-1">Yêu cầu chỉnh sửa hóa đơn</h1>
                    <p class="text-gray-300">{{ $bill->bill_number }} - {{ $bill->table->table_name }}</p>
                </div>
            </div>
        </div>

        <!-- Warning Notice -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-8">
            <div class="flex">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-4 mt-1"></i>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Lưu ý quan trọng</h3>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Chức năng này chỉ dành cho hóa đơn đang mở hoặc tạm dừng</li>
                        <li>• Mọi thay đổi sẽ được ghi nhận và cần xác nhận từ nhân viên</li>
                        <li>• Vui lòng kiểm tra kỹ trước khi gửi yêu cầu</li>
                        <li>• Liên hệ nhân viên trực tiếp để được hỗ trợ nhanh hơn</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Current Bill Details -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-file-invoice text-blue-600 mr-2"></i>
                Thông tin hóa đơn hiện tại
            </h2>

            <!-- Time Info -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Bắt đầu</p>
                    <p class="text-base font-medium text-gray-900">{{ $bill->start_time->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Kết thúc</p>
                    <p class="text-base font-medium text-gray-900">
                        {{ $bill->end_time ? $bill->end_time->format('d/m/Y H:i') : 'Đang chơi' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Thời gian</p>
                    <p class="text-lg font-bold text-elegant-gold">{{ $bill->total_time }} phút</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Trạng thái</p>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $bill->status_label }}
                    </span>
                </div>
            </div>

            <!-- Products List -->
            @if($bill->billDetails->count() > 0)
            <h3 class="text-lg font-bold text-gray-900 mb-4">Sản phẩm đã gọi</h3>
            <div class="space-y-3 mb-6">
                @foreach($bill->billDetails as $detail)
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">
                            {{ $detail->product->name ?? $detail->combo->name ?? 'N/A' }}
                        </h4>
                        <p class="text-sm text-gray-600">Số lượng: {{ $detail->quantity }} × {{ number_format($detail->unit_price) }}₫</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-elegant-gold">{{ number_format($detail->total_price) }}₫</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 bg-gray-50 rounded-lg">
                <i class="fas fa-shopping-bag text-gray-300 text-4xl mb-3"></i>
                <p class="text-gray-500">Chưa có sản phẩm nào</p>
            </div>
            @endif

            <!-- Total -->
            <div class="border-t-2 border-gray-200 pt-6 mt-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Tiền bàn: {{ number_format($bill->table_price) }}₫</p>
                        <p class="text-sm text-gray-600">Sản phẩm: {{ number_format($bill->product_total) }}₫</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-1">Tổng tạm tính</p>
                        <p class="text-3xl font-bold text-elegant-gold">{{ number_format($bill->total_amount) }}₫</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Request Form -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-pen text-purple-600 mr-2"></i>
                Nội dung yêu cầu chỉnh sửa
            </h2>

            <form action="#" method="POST" id="editRequestForm">
                @csrf
                
                <!-- Request Type -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Loại yêu cầu <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-elegant-gold transition-colors">
                            <input type="radio" name="request_type" value="add_product" class="mr-3" checked>
                            <div>
                                <p class="font-medium text-gray-900">Thêm sản phẩm</p>
                                <p class="text-sm text-gray-600">Gọi thêm đồ ăn/uống</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-elegant-gold transition-colors">
                            <input type="radio" name="request_type" value="remove_product" class="mr-3">
                            <div>
                                <p class="font-medium text-gray-900">Hủy sản phẩm</p>
                                <p class="text-sm text-gray-600">Hủy món đã gọi (chưa phục vụ)</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-elegant-gold transition-colors">
                            <input type="radio" name="request_type" value="change_quantity" class="mr-3">
                            <div>
                                <p class="font-medium text-gray-900">Thay đổi số lượng</p>
                                <p class="text-sm text-gray-600">Tăng/giảm số lượng sản phẩm</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-elegant-gold transition-colors">
                            <input type="radio" name="request_type" value="other" class="mr-3">
                            <div>
                                <p class="font-medium text-gray-900">Khác</p>
                                <p class="text-sm text-gray-600">Yêu cầu khác</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="mb-6">
                    <label for="request_details" class="block text-sm font-semibold text-gray-700 mb-3">
                        Chi tiết yêu cầu <span class="text-red-500">*</span>
                    </label>
                    <textarea id="request_details" 
                              name="request_details" 
                              rows="6" 
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200" 
                              placeholder="Vui lòng mô tả chi tiết yêu cầu của bạn...&#10;&#10;Ví dụ:&#10;- Thêm 2 ly Coca Cola&#10;- Hủy món Salad (chưa làm)&#10;- Đổi từ 3 chai bia thành 5 chai"
                              required></textarea>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Nhân viên sẽ xem xét và xác nhận yêu cầu của bạn
                    </p>
                </div>

                <!-- Contact Info -->
                <div class="mb-6">
                    <label for="contact_note" class="block text-sm font-semibold text-gray-700 mb-3">
                        Ghi chú liên hệ (tùy chọn)
                    </label>
                    <input type="text" 
                           id="contact_note" 
                           name="contact_note"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-elegant-gold focus:border-elegant-gold transition duration-200"
                           placeholder="Số điện thoại hoặc vị trí bàn để nhân viên liên hệ">
                </div>

                <!-- Actions -->
                <div class="flex justify-between gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('customer.bills.show', $bill->id) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-8 py-3 rounded-lg transition duration-200 inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Hủy
                    </a>
                    
                    <button type="button"
                            onclick="submitEditRequest()"
                            class="bg-gradient-to-r from-elegant-navy to-purple-900 hover:opacity-90 text-white font-bold px-8 py-3 rounded-lg transition duration-200 inline-flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>

        <!-- Contact Staff -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-headset text-blue-600 text-2xl mr-4 mt-1"></i>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Cần hỗ trợ nhanh?</h3>
                    <p class="text-blue-800 mb-3">
                        Vui lòng liên hệ trực tiếp nhân viên tại quầy hoặc gọi hotline để được hỗ trợ nhanh chóng hơn
                    </p>
                    <a href="tel:1900xxxx" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                        <i class="fas fa-phone mr-2"></i>
                        Hotline: 1900 xxxx
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function submitEditRequest() {
    const form = document.getElementById('editRequestForm');
    const requestType = form.querySelector('input[name="request_type"]:checked');
    const requestDetails = form.querySelector('#request_details');
    
    if (!requestType || !requestDetails.value.trim()) {
        alert('Vui lòng điền đầy đủ thông tin yêu cầu!');
        return;
    }
    
    if (confirm('Bạn có chắc chắn muốn gửi yêu cầu chỉnh sửa này?')) {
        // TODO: Implement AJAX submission
        alert('Yêu cầu của bạn đã được ghi nhận!\n\nNhân viên sẽ xử lý trong thời gian sớm nhất.');
        window.location.href = '{{ route("customer.bills.show", $bill->id) }}';
    }
}

// Radio button styling
document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('input[type="radio"]').forEach(r => {
            r.closest('label').classList.remove('border-elegant-gold', 'bg-yellow-50');
        });
        if (this.checked) {
            this.closest('label').classList.add('border-elegant-gold', 'bg-yellow-50');
        }
    });
});

// Initialize first option
document.querySelector('input[name="request_type"]:checked').closest('label').classList.add('border-elegant-gold', 'bg-yellow-50');
</script>
@endsection