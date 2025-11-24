<?php

namespace App\Enums;

enum BillStatus: string
{
    case Open = 'Open';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
    case Quick = 'quick'; // Legacy status, maybe rename to QuickPlay later?

    public function label(): string
    {
        return match($this) {
            self::Open => 'Đang mở',
            self::Completed => 'Đã thanh toán',
            self::Cancelled => 'Đã hủy',
            self::Quick => 'Khách lẻ',
        };
    }
}
