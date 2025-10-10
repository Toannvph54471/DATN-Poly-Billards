<?php

return [
    // ========== TABLE CONFIG ==========
    'table' => [
        'types' => [
            'standard' => 'Bàn Thường',
            'vip' => 'Bàn VIP', 
            'competition' => 'Bàn Thi Đấu'
        ],
        'statuses' => [
            'available' => 'Trống',
            'occupied' => 'Đang sử dụng',
            'maintenance' => 'Bảo trì',
            'reserved' => 'Đã đặt trước'
        ]
    ],

    // ========== BILL CONFIG ==========
    'bill' => [
        'statuses' => [
            'open' => 'Mở',
            'playing' => 'Đang chơi',
            'pending_payment' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'cancelled' => 'Đã hủy'
        ],
        'payment_methods' => [
            'cash' => 'Tiền mặt',
            'card' => 'Thẻ',
            'transfer' => 'Chuyển khoản',
            'wallet' => 'Ví điện tử'
        ]
    ],

    // ========== PRODUCT CONFIG ==========
    'product' => [
        'types' => [
            'food' => 'Đồ ăn',
            'drink' => 'Đồ uống',
            'other' => 'Khác',
            'service' => 'Dịch vụ'
        ],
        'units' => [
            'bottle' => 'Chai',
            'can' => 'Lon', 
            'plate' => 'Đĩa',
            'cup' => 'Ly',
            'pack' => 'Gói'
        ]
    ],

    // ========== EMPLOYEE CONFIG ==========
    'employee' => [
        'positions' => [
            'manager' => 'Quản lý',
            'staff' => 'Nhân viên',
            'cashier' => 'Thu ngân',
            'waiter' => 'Phục vụ'
        ],
        'salary_types' => [
            'monthly' => 'Lương tháng',
            'hourly' => 'Lương giờ',
            'shift' => 'Lương ca'
        ]
    ],

    // ========== SHIFT CONFIG ==========
    'shift' => [
        'statuses' => [
            'scheduled' => 'Đã lên lịch',
            'active' => 'Đang làm',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ]
    ],

    // ========== RESERVATION CONFIG ==========
    'reservation' => [
        'statuses' => [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'checked_in' => 'Đã check-in',
            'cancelled' => 'Đã hủy',
            'completed' => 'Đã hoàn thành'
        ]
    ],

    // ========== USER ROLES ==========
    'roles' => [
        'admin' => 'Quản trị viên',
        'manager' => 'Quản lý',
        'employee' => 'Nhân viên'
    ],

    // ========== BUSINESS CONFIG ==========
    'business' => [
        'tax_rate' => 0.1, // 10%
        'default_hourly_rates' => [
            'standard' => 50000,
            'vip' => 80000,
            'competition' => 100000
        ],
        'business_hours' => [
            'open' => '08:00',
            'close' => '23:00'
        ]
    ]
];