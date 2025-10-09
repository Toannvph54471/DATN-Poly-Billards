<?php

return [
    'permissions' => [
        // Table permissions
        'tables.view' => 'Xem danh sách bàn',
        'tables.create' => 'Thêm bàn',
        'tables.edit' => 'Sửa bàn',
        'tables.delete' => 'Xóa bàn',
        
        // Bill permissions  
        'bills.view' => 'Xem hóa đơn',
        'bills.create' => 'Tạo hóa đơn',
        'bills.edit' => 'Sửa hóa đơn',
        'bills.delete' => 'Xóa hóa đơn',
        'bills.payment' => 'Thanh toán hóa đơn',
        
        // Customer permissions
        'customers.view' => 'Xem khách hàng',
        'customers.create' => 'Thêm khách hàng',
        'customers.edit' => 'Sửa khách hàng',
        
        // Product permissions
        'products.view' => 'Xem sản phẩm',
        'products.create' => 'Thêm sản phẩm',
        'products.edit' => 'Sửa sản phẩm',
        
        // Report permissions
        'reports.view' => 'Xem báo cáo',
        
        // Employee permissions
        'employees.view' => 'Xem nhân viên',
        'employees.create' => 'Thêm nhân viên',
        'employees.edit' => 'Sửa nhân viên'
    ],

   'role_permissions' => [
    'admin' => [    // ← SLUG, không phải ID hay name
        'tables.*', 'bills.*', 'customers.*', 'products.*', 
        'reports.*', 'employees.*'
    ],
    'manager' => [  // ← SLUG
        'tables.view', 'tables.create', 'tables.edit',
        'bills.view', 'bills.create', 'bills.edit', 'bills.payment',
        'customers.view', 'customers.create', 'customers.edit', 
        'products.view', 'products.create', 'products.edit',
        'reports.view', 'employees.view'
    ],
    'employee' => [ // ← SLUG
        'tables.view', 'bills.view', 'bills.create', 'bills.payment',
        'customers.view', 'customers.create',
        'products.view'
    ]
]
];