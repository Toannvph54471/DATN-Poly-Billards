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
    const TYPE_PARTIAL = 'partial';
    const TYPE_REMAINING = 'remaining';
    const TYPE_REFUND = 'refund';

    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';
    const METHOD_VNPAY = 'vnpay';
    const METHOD_MOMO = 'momo';
    const METHOD_ZALOPAY = 'zalopay';
    const METHOD_BANK_TRANSFER = 'bank_transfer';

    // === FILLABLE ===
    protected $fillable = [
        'reservation_id',
        'payable_type',
        'payable_id',
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
        'failed_at',
        'refunded_at',
        'note',
        'processed_by',
    ];

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

    // === RELATIONSHIPS ===

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
    public function payable()
    {
        return $this->morphTo();
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
            self::METHOD_CARD => 'Thẻ tín dụng',
            self::METHOD_VNPAY => 'VNPay',
            self::METHOD_MOMO => 'Momo',
            self::METHOD_ZALOPAY => 'ZaloPay',
            self::METHOD_BANK_TRANSFER => 'Chuyển khoản',
            default => ucfirst($this->payment_method),
        };
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            self::TYPE_DEPOSIT => 'Đặt cọc',
            self::TYPE_FULL => 'Thanh toán đủ',
            self::TYPE_PARTIAL => 'Thanh toán 1 phần',
            self::TYPE_REMAINING => 'Thanh toán còn lại',
            self::TYPE_REFUND => 'Hoàn tiền',
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
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed($reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'failed_at' => now(),
            'note' => $reason ?? $this->note,
        ]);
    }

    public function refund($reason = null): bool
    {
        if (!$this->isCompleted()) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_REFUNDED,
            'refunded_at' => now(),
            'note' => $reason ?? $this->note,
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

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}
