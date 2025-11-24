<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Refunded = 'Refunded';
    case Failed = 'Failed';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Chờ thanh toán',
            self::Paid => 'Đã thanh toán',
            self::Refunded => 'Đã hoàn tiền',
            self::Failed => 'Thất bại',
        };
    }
}
