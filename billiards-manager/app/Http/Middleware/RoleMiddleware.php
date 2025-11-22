<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Lấy slug của role hiện tại (admin, manager, employee, customer)
        $userRole = auth()->user()->role->slug;

        // Nếu role hiện tại ko nằm trong danh sách roles được phép -> báo lỗi
        if (!in_array($userRole, $roles)) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
