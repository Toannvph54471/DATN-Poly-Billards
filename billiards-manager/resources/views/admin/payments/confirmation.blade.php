@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Xác nhận thanh toán</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h5 class="alert-heading">Hóa đơn đã được in thành công!</h5>
                        <p class="mb-0">Vui lòng xác nhận với khách hàng:</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">
                                        <i class="fas fa-check-circle fa-2x mb-3"></i><br>
                                        Khách hàng đã thanh toán
                                    </h5>
                                    <p class="card-text">
                                        Nhấn nút bên dưới để xác nhận thanh toán thành công
                                    </p>
                                    <form action="{{ route('admin.bills.confirm-payment', $billId) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-check"></i> Xác nhận thanh toán thành công
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-warning">
                                        <i class="fas fa-clock fa-2x mb-3"></i><br>
                                        Khách hàng sẽ thanh toán sau
                                    </h5>
                                    <p class="card-text">
                                        Nhấn nút bên dưới nếu khách hàng muốn thanh toán vào lúc khác
                                    </p>
                                    <form action="{{ route('admin.bills.cancel-payment', $billId) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-lg">
                                            <i class="fas fa-times"></i> Hủy - Thanh toán lúc khác
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin hóa đơn</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-3">Số hóa đơn:</dt>
                                <dd class="col-sm-9">{{ $bill->bill_number }}</dd>
                                
                                <dt class="col-sm-3">Bàn:</dt>
                                <dd class="col-sm-9">{{ $bill->table->table_name }} ({{ $bill->table->table_number }})</dd>
                                
                                <dt class="col-sm-3">Khách hàng:</dt>
                                <dd class="col-sm-9">{{ $bill->user->name ?? 'Khách vãng lai' }}</dd>
                                
                                <dt class="col-sm-3">Tổng tiền:</dt>
                                <dd class="col-sm-9">{{ number_format($finalAmount, 0, ',', '.') }}₫</dd>
                                
                                <dt class="col-sm-3">Phương thức thanh toán:</dt>
                                <dd class="col-sm-9">
                                    @if($paymentMethod === 'cash')
                                        <span class="badge bg-success">Tiền mặt</span>
                                    @elseif($paymentMethod === 'bank')
                                        <span class="badge bg-primary">Chuyển khoản</span>
                                    @else
                                        <span class="badge bg-info">Thẻ</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-3">Thời gian in:</dt>
                                <dd class="col-sm-9">{{ $printTime }}</dd>
                                
                                <dt class="col-sm-3">Nhân viên:</dt>
                                <dd class="col-sm-9">{{ $staff }}</dd>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.bills.print', ['id' => $billId, 'reprint' => 'true']) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-print"></i> In lại hóa đơn
                        </a>
                        
                        <a href="{{ route('admin.tables.detail', $bill->table_id) }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại chi tiết bàn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection