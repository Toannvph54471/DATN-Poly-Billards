<div class="reservation-card bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition duration-300"
     data-status="{{ $reservation->status }}">
    <div class="flex justify-between items-start mb-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-sm font-medium text-elegant-gold">
                    {{ $reservation->reservation_code }}
                </span>
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    @if($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($reservation->status === 'confirmed') bg-blue-100 text-blue-800
                    @elseif($reservation->status === 'checked_in') bg-green-100 text-green-800
                    @elseif($reservation->status === 'completed') bg-gray-100 text-gray-800
                    @elseif($reservation->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-purple-100 text-purple-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                </span>
            </div>
            <h3 class="text-xl font-bold text-gray-900">
                {{ $reservation->table->table_name ?? 'Bàn không xác định' }}
            </h3>
            <p class="text-sm text-gray-600">
                {{ $reservation->guest_count }} khách • 
                {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-2xl font-bold text-elegant-navy">
                {{ number_format($reservation->total_amount) }}đ
            </p>
            @if($reservation->deposit_amount > 0)
                <p class="text-xs text-gray-500">Đặt cọc: {{ number_format($reservation->deposit_amount) }}đ</p>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mt-4">
        @if($reservation->status === 'confirmed' && $reservation->reservation_time->gt(now()->subMinutes(30)))
            <button onclick="openCheckinModal({{ $reservation->id }}, '{{ $reservation->reservation_code }}')"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                <i class="fas fa-sign-in-alt mr-1"></i> Check-in
            </button>
        @endif

        @if(in_array($reservation->status, ['pending', 'confirmed']) && 
             $reservation->reservation_time->gt(now()->addHour()))
            <button onclick="openCancelModal({{ $reservation->id }}, '{{ $reservation->reservation_code }}')"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                <i class="fas fa-times mr-1"></i> Hủy
            </button>
        @endif

        <a href="{{ route('reservations.show', $reservation->id) }}"
           class="px-4 py-2 bg-elegant-navy hover:bg-blue-800 text-white text-sm font-medium rounded-lg transition">
            <i class="fas fa-eye mr-1"></i> Xem chi tiết
        </a>
    </div>
</div>