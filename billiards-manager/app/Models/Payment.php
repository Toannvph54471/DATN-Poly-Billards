<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    // === CONSTANTS ===
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_FULL = 'full';

    const METHOD_CASH = 'cash';
    const METHOD_BANK = 'bank';
    const METHOD_CARD = 'card';
    const METHOD_VNPAY = 'vnpay';
    const METHOD_MOMO = 'momo';

    // === FILLABLE - CHỈ GIỮ CÁC TRƯỜNG CÓ TRONG DATABASE ===
    // app/Models/Payment.php

    protected $fillable = [
        'bill_id',
        'amount',
        'currency',
        'payment_method',
        'payment_type',
        'status',
        'transaction_id',
        'payment_url',
        'payment_data',
        'paid_at',
        'completed_at',
        'processed_by',
        'note',
    ];

    public function bill()
    {
        return $this->belongsTo(\App\Models\Bill::class);
    }

    // === CASTS ===
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_data' => 'array',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // === RELATIONSHIPS - CHỈ GIỮ QUAN HỆ VỚI RESERVATION ===
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // === ACCESSORS ===
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Đang chờ',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_FAILED => 'Thất bại',
            self::STATUS_REFUNDED => 'Đã hoàn tiền',
            default => 'Không xác định',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::METHOD_CASH => 'Tiền mặt',
            self::METHOD_BANK => 'Chuyển khoản',
            self::METHOD_CARD => 'Thẻ',
            self::METHOD_VNPAY => 'VNPay',
            self::METHOD_MOMO => 'Momo',
            default => ucfirst($this->payment_method),
        };
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            self::TYPE_DEPOSIT => 'Đặt cọc',
            self::TYPE_FULL => 'Thanh toán toàn bộ',
            default => ucfirst($this->payment_type),
        };
    }

    // === HELPER METHODS ===
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'paid_at' => $this->paid_at ?? now(),
        ]);
    }

    // === SCOPES ===
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
