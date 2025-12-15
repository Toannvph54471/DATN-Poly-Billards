<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thanh toán VNPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .result-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }
        
        .result-header {
            padding: 30px;
            text-align: center;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
        }
        
        .result-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .result-body {
            padding: 40px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-label {
            color: #6b7280;
            font-weight: 500;
        }
        
        .info-value {
            font-weight: 600;
            color: #111827;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            text-align: center;
            margin: 5px;
        }
        
        .btn-primary {
            background: #4f46e5;
            color: white;
        }
        
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background: #d1d5db;
        }
        
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4f46e5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="result-card">
        <div class="result-header">
            @if(isset($success) && $success)
                <div class="result-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="text-2xl font-bold">Thanh toán thành công!</h1>
            @else
                <div class="result-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h1 class="text-2xl font-bold">Thanh toán thất bại</h1>
            @endif
        </div>
        
        <div class="result-body">
            @if(isset($processing) && $processing)
                <div class="text-center">
                    <div class="loading-spinner"></div>
                    <p class="text-lg font-medium text-gray-700 mb-4">{{ $message ?? 'Đang xử lý hóa đơn...' }}</p>
                    <p class="text-sm text-gray-500">Vui lòng chờ trong giây lát</p>
                </div>
                
                <script>
                    // Tự động refresh sau 5 giây để kiểm tra trạng thái
                    setTimeout(function() {
                        window.location.reload();
                    }, 5000);
                </script>
            @else
                <div class="mb-6">
                    <p class="text-lg font-medium text-center mb-6 {{ isset($success) && $success ? 'text-green-600' : 'text-red-600' }}">
                        {{ $message ?? 'Không có thông báo' }}
                    </p>
                    
                    <div class="space-y-3">
                        @if(isset($bill_number))
                        <div class="info-item">
                            <span class="info-label">Số hóa đơn:</span>
                            <span class="info-value">{{ $bill_number }}</span>
                        </div>
                        @endif
                        
                        @if(isset($transaction_code) && $transaction_code)
                        <div class="info-item">
                            <span class="info-label">Mã giao dịch:</span>
                            <span class="info-value">{{ $transaction_code }}</span>
                        </div>
                        @endif
                        
                        @if(isset($amount) && $amount > 0)
                        <div class="info-item">
                            <span class="info-label">Số tiền:</span>
                            <span class="info-value">{{ number_format($amount) }} ₫</span>
                        </div>
                        @endif
                        
                        <div class="info-item">
                            <span class="info-label">Thời gian:</span>
                            <span class="info-value">{{ now()->format('H:i d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="flex flex-col space-y-3 mt-8">
                @if(isset($success) && $success && isset($bill_id))
                    <a href="{{ route('admin.bills.print', ['id' => $bill_id, 'auto_print' => 'true']) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-print mr-2"></i> In hóa đơn
                    </a>
                    
                    <a href="{{ route('admin.tables.index') }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-home mr-2"></i> Về trang chủ
                    </a>
                @else
                    @if(isset($bill_id))
                        <a href="{{ route('admin.payments.show', ['id' => $bill_id]) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-redo mr-2"></i> Thử thanh toán lại
                        </a>
                    @endif
                    
                    <a href="{{ route('admin.tables.index') }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-home mr-2"></i> Về trang chủ
                    </a>
                @endif
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Nếu có vấn đề với giao dịch, vui lòng liên hệ hỗ trợ
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Tự động in hóa đơn nếu thành công
        @if(isset($success) && $success && isset($bill_id) && !isset($processing))
            setTimeout(function() {
                window.open('{{ route("admin.bills.print", ["id" => $bill_id, "auto_print" => "true"]) }}', '_blank');
            }, 1000);
        @endif
    </script>
</body>
</html>