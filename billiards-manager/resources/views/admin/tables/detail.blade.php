@extends('admin.layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Bàn #{{ $table->table_number }}</h1>
                        <p class="text-gray-600 mt-2">{{ $table->table_name }} • Sức chứa: {{ $table->capacity }} người</p>
                    </div>
                    <div class="flex space-x-3">
                        @if ($table->currentBill)
                            <a href="{{ route('bills.payment-page', $table->currentBill->id) }}"
                                class="bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition-colors font-semibold shadow-md">
                                💳 Thanh toán
                            </a>
                        @else
                            <form action="{{ route('bills.create') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="table_id" value="{{ $table->id }}">
                                <input type="hidden" name="guest_count" value="1">
                                <button type="submit"
                                    class="bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition-colors font-semibold shadow-md">
                                    🎱 Tạo hóa đơn
                                </button>
                            </form>
                            <form action="{{ route('bills.quick-create') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="table_id" value="{{ $table->id }}">
                                <button type="submit"
                                    class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-semibold shadow-md">
                                    ⚡ Bàn lẻ
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.tables.index') }}"
                            class="bg-white text-gray-700 border border-gray-300 px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors font-semibold shadow-sm">
                            ← Quay lại
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-8">
                @if ($table->currentBill)
                    @if ($timeInfo['bill_status'] === 'quick')
                        <span class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm font-semibold">
                            ⚡ BÀN LẺ - CHƯA TÍNH GIỜ
                        </span>
                    @elseif($timeInfo['bill_status'] === 'combo')
                        <span class="bg-black text-white px-4 py-2 rounded-full text-sm font-semibold">
                            ⏰ COMBO TIME
                        </span>
                    @elseif($timeInfo['bill_status'] === 'regular')
                        <span class="bg-gray-700 text-white px-4 py-2 rounded-full text-sm font-semibold">
                            ▶️ ĐANG TÍNH GIỜ
                        </span>
                    @endif
                @else
                    <span class="bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold">
                        ✅ BÀN TRỐNG
                    </span>
                @endif
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                <!-- Thông tin bàn -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Thông tin bàn</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Số bàn:</span>
                            <span class="font-semibold text-gray-900">#{{ $table->table_number }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Tên bàn:</span>
                            <span class="font-semibold text-gray-900">{{ $table->table_name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Sức chứa:</span>
                            <span class="font-semibold text-gray-900">{{ $table->capacity }} người</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Loại bàn:</span>
                            <span class="font-semibold text-gray-900">{{ $table->tableRate->name ?? 'Standard' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Giá theo giờ:</span>
                            <span class="font-bold text-gray-900">
                                {{ number_format($table->getHourlyRate(), 0, ',', '.') }} đ/giờ
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Thông tin hóa đơn -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🧾 Thông tin hóa đơn</h2>
                    @if ($table->currentBill)
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Mã HD:</span>
                                <span
                                    class="font-mono font-semibold text-gray-900">{{ $table->currentBill->bill_number }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Khách hàng:</span>
                                <span
                                    class="font-semibold text-gray-900">{{ $table->currentBill->user->name ?? 'Khách vãng lai' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Bắt đầu:</span>
                                <span
                                    class="font-semibold text-gray-900">{{ $table->currentBill->start_time->format('H:i d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Trạng thái:</span>
                                <span class="font-semibold">
                                    @if ($timeInfo['bill_status'] === 'quick')
                                        <span class="text-gray-700">⚡ Bàn lẻ</span>
                                    @elseif($timeInfo['bill_status'] === 'combo')
                                        <span class="text-gray-700">⏰ Combo Time</span>
                                    @elseif($timeInfo['bill_status'] === 'regular')
                                        <span class="text-gray-700">▶️ Tính giờ</span>
                                    @endif
                                </span>
                            </div>
                            @if ($timeInfo['bill_status'] === 'combo')
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-600">Thời gian còn lại:</span>
                                    <span class="font-bold text-gray-900">{{ $timeInfo['remaining_minutes'] }} phút</span>
                                </div>
                            @elseif($timeInfo['bill_status'] === 'regular')
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-gray-600">Thời gian đã chơi:</span>
                                    <span class="font-bold text-gray-900">
                                        {{ floor($timeInfo['elapsed_minutes'] / 60) }}h{{ $timeInfo['elapsed_minutes'] % 60 }}p
                                    </span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center py-2 bg-gray-50 rounded-lg px-3">
                                <span class="text-gray-700 font-bold">Tổng tiền:</span>
                                <span class="text-2xl font-bold text-gray-900">
                                    {{ number_format($table->currentBill->total_amount, 0, ',', '.') }} đ
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-3">📄</div>
                            <p class="font-semibold">Chưa có hóa đơn nào</p>
                        </div>
                    @endif
                </div>

                <!-- Điều khiển -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🎮 Điều khiển</h2>

                    @if ($table->currentBill)
                        <!-- Quick Bill -->
                        @if ($timeInfo['bill_status'] === 'quick')
                            <div class="bg-gray-50 border border-gray-300 rounded-xl p-5 mb-4">
                                <div class="flex items-center mb-4">
                                    <div class="text-2xl mr-3">⚡</div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">Bàn Lẻ</h3>
                                        <p class="text-gray-600 text-sm">Chưa bắt đầu tính giờ</p>
                                    </div>
                                </div>
                                <form action="{{ route('bills.start-playing', $table->currentBill->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-black text-white py-3 rounded-lg hover:bg-gray-800 transition-colors font-bold shadow-md">
                                        ▶️ Bắt đầu tính giờ
                                    </button>
                                </form>
                            </div>

                            <!-- Combo Time -->
                        @elseif($timeInfo['bill_status'] === 'combo')
                            <div class="bg-gray-50 border border-gray-300 rounded-xl p-5 mb-4">
                                <div class="flex items-center mb-4">
                                    <div class="text-2xl mr-3">⏰</div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">Combo Time</h3>
                                        <p class="text-gray-600 text-sm">
                                            {{ $timeInfo['remaining_minutes'] }} phút còn lại
                                            @if ($timeInfo['is_near_end'])
                                                <span
                                                    class="ml-2 bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-semibold">Sắp
                                                    hết giờ!</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    @if ($timeInfo['is_running'])
                                        <form action="{{ route('bills.pause', $table->currentBill->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-gray-700 text-white py-2 rounded-lg hover:bg-gray-600 transition-colors font-semibold">
                                                ⏸️ Tạm dừng
                                            </button>
                                        </form>
                                    @elseif($timeInfo['is_paused'])
                                        <form action="{{ route('bills.resume', $table->currentBill->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-gray-700 text-white py-2 rounded-lg hover:bg-gray-600 transition-colors font-semibold">
                                                ▶️ Tiếp tục
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('bills.switch-regular', $table->currentBill->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-500 transition-colors font-semibold">
                                            🔄 Giờ thường
                                        </button>
                                    </form>
                                    @if ($table->currentBill)
                                        <button onclick="openExtendModal({{ $table->currentBill->id }})"
                                            class="w-full bg-gray-800 text-white py-2 rounded-lg hover:bg-gray-700 transition-colors font-semibold">
                                            ⏱️ Gia hạn
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Regular Time -->
                        @elseif($timeInfo['bill_status'] === 'regular')
                            <div class="bg-gray-50 border border-gray-300 rounded-xl p-5 mb-4">
                                <div class="flex items-center mb-4">
                                    <div class="text-2xl mr-3">▶️</div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">Tính giờ thường</h3>
                                        <p class="text-gray-600 text-sm">
                                            {{ floor($timeInfo['elapsed_minutes'] / 60) }}h{{ $timeInfo['elapsed_minutes'] % 60 }}p
                                            - {{ number_format($timeInfo['current_cost'], 0, ',', '.') }} đ
                                        </p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    @if ($timeInfo['is_running'])
                                        <form action="{{ route('bills.pause', $table->currentBill->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-gray-700 text-white py-2 rounded-lg hover:bg-gray-600 transition-colors font-semibold">
                                                ⏸️ Tạm dừng
                                            </button>
                                        </form>
                                    @elseif($timeInfo['is_paused'])
                                        <form action="{{ route('bills.resume', $table->currentBill->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-gray-700 text-white py-2 rounded-lg hover:bg-gray-600 transition-colors font-semibold">
                                                ▶️ Tiếp tục
                                            </button>
                                        </form>
                                    @endif
                                    @if ($table->currentBill)
                                        <button onclick="openExtendModal({{ $table->currentBill->id }})"
                                            class="w-full bg-gray-800 text-white py-2 rounded-lg hover:bg-gray-700 transition-colors font-semibold">
                                            ⏱️ Gia hạn
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-3">🎮</div>
                            <p class="font-semibold">Bàn đang trống</p>
                            <p class="text-sm mt-2">Tạo hóa đơn để bắt đầu sử dụng</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Products & Combos Grid -->
            @if ($table->currentBill)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                    <!-- Thêm sản phẩm -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">🥤 Thêm sản phẩm</h2>
                        <form action="{{ route('bills.add-product', $table->currentBill->id) }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn sản phẩm</label>
                                <select name="product_id" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-black focus:ring-1 focus:ring-black">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }} đ
                                            (Tồn: {{ $product->stock_quantity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Số lượng</label>
                                <input type="number" name="quantity" value="1" min="1" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-black focus:ring-1 focus:ring-black">
                            </div>
                            <button type="submit"
                                class="w-full bg-black text-white py-3 rounded-lg hover:bg-gray-800 transition-colors font-bold shadow-md">
                                ➕ Thêm sản phẩm
                            </button>
                        </form>
                    </div>

                    <!-- Thêm combo -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">📦 Thêm combo</h2>
                        <form action="{{ route('bills.add-combo', $table->currentBill->id) }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn combo</label>
                                <select name="combo_id" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-black focus:ring-1 focus:ring-black">
                                    <option value="">-- Chọn combo --</option>
                                    @foreach ($combos as $combo)
                                        <option value="{{ $combo->id }}">
                                            {{ $combo->name }} - {{ number_format($combo->price, 0, ',', '.') }} đ
                                            @if ($combo->is_time_combo)
                                                ({{ $combo->play_duration_minutes }} phút)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Số lượng</label>
                                <input type="number" name="quantity" value="1" min="1" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-black focus:ring-1 focus:ring-black">
                            </div>
                            <button type="submit"
                                class="w-full bg-gray-800 text-white py-3 rounded-lg hover:bg-gray-700 transition-colors font-bold shadow-md">
                                📥 Thêm combo
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Chi tiết hóa đơn -->
            @if ($table->currentBill && $table->currentBill->billDetails->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">📋 Chi tiết hóa đơn</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Sản phẩm/Combo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Đơn giá</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        SL</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($table->currentBill->billDetails as $detail)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            @if ($detail->product)
                                                {{ $detail->product->name }}
                                            @elseif($detail->combo)
                                                {{ $detail->combo->name }} (Combo)
                                            @else
                                                Phí dịch vụ
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($detail->unit_price, 0, ',', '.') }} đ
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $detail->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ number_format($detail->total_price, 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                        Tổng cộng:</td>
                                    <td class="px-6 py-4 text-xl font-bold text-gray-900">
                                        {{ number_format($table->currentBill->total_amount, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Extend Time Modal -->
    <script>
        function openExtendModal(billId) {
            const minutes = prompt('Nhập số phút muốn gia hạn:', '30');
            if (minutes && !isNaN(minutes) && minutes > 0) {
                // Gửi request gia hạn
                fetch(`/admin/bills/${billId}/extend-combo`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            extra_minutes: parseInt(minutes)
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi gia hạn');
                    });
            }
        }
    </script>
@endsection
