<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Dùng cho hệ thống auth
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Tên bảng (nếu khác tên mặc định)
    protected $table = 'users';

    // Các cột có thể gán hàng loạt
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
        'status'
    ];

    // Ẩn khi trả về JSON hoặc mảng
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Ép kiểu dữ liệu nếu cần
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
