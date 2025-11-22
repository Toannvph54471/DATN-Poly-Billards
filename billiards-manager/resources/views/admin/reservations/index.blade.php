@extends('admin.layouts.app')

@section('title', 'Quản lý đặt bàn')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý đặt bàn</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reservations.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Đã check-in</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label>Ngày đặt</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                </div>
                
                <div class="col-md-3">
                    <label>Thanh toán</label>
                    <select name="payment_status" class="form-control">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Khách hàng</th>
                            <th>Bàn</th>
                            <th>Thời gian</th>
                            <th>Số người</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->reservation_code }}</td>
                            <td>
                                {{ $reservation->customer_name }}<br>
                                <small>{{ $reservation->customer_phone }}</small>
                            </td>
                            <td>{{ $reservation->table->table_name }}</td>
                            <td>{{ $reservation->reservation_time->format('d/m/Y H:i') }}</td>
                            <td>{{ $reservation->guest_count }}</td>
                            <td>{{ number_format($reservation->total_amount) }}đ</td>
                            <td>
                                @switch($reservation->status)
                                    @case('pending')
                                        <span class="badge badge-warning">Chờ xác nhận</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge badge-info">Đã xác nhận</span>
                                        @break
                                    @case('checked_in')
                                        <span class="badge badge-primary">Đã check-in</span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-success">Hoàn thành</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-danger">Đã hủy</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($reservation->payment_status === 'paid')
                                    <span class="badge badge-success">Đã thanh toán</span>
                                @else
                                    <span class="badge badge-warning">Chưa thanh toán</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Chưa có đặt bàn nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $reservations->links() }}
        </div>
    </div>
</div>
@endsection