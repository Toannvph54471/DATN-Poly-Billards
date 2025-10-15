<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // ========== PHƯƠNG THỨC CHIA SẺ DỮ LIỆU CHUNG ==========
    
    /**
     * Chia sẻ dữ liệu chung cho tất cả views
     */
    protected function shareCommonData($additionalData = [])
    {
        $commonData = [
            'pageTitle' => 'Hệ thống quản lý Billiards',
            'currentUser' => auth()->user(),
            'currentTime' => now()->format('d/m/Y H:i:s'),
            'appName' => 'Billiards Manager',
            'version' => '1.0.0'
        ];

        return array_merge($commonData, $additionalData);
    }

    /**
     * Hiển thị view với dữ liệu chung
     */
    protected function view(string $view, array $data = []): View
    {
        $sharedData = $this->shareCommonData($data);
        return view($view, $sharedData);
    }

    /**
     * Hiển thị view với thông báo thành công
     */
    protected function viewWithSuccess(string $view, array $data = [], string $message = null): View
    {
        $sharedData = $this->shareCommonData($data);
        $sharedData['successMessage'] = $message;
        
        return view($view, $sharedData);
    }

    /**
     * Hiển thị view với thông báo lỗi
     */
    protected function viewWithError(string $view, array $data = [], string $message = null): View
    {
        $sharedData = $this->shareCommonData($data);
        $sharedData['errorMessage'] = $message;
        
        return view($view, $sharedData);
    }

    // ========== PHƯƠNG THỨC CHUYỂN HƯỚNG ==========
    
    /**
     * Chuyển hướng với thông báo thành công
     */
    protected function redirectWithSuccess(string $route, string $message = 'Thao tác thành công')
    {
        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Chuyển hướng với thông báo lỗi
     */
    protected function redirectWithError(string $route, string $message = 'Có lỗi xảy ra')
    {
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Quay lại trang trước với thông báo thành công
     */
    protected function backWithSuccess(string $message = 'Thao tác thành công')
    {
        return back()->with('success', $message);
    }

    /**
     * Quay lại trang trước với thông báo lỗi
     */
    protected function backWithError(string $message = 'Có lỗi xảy ra')
    {
        return back()->with('error', $message);
    }

    /**
     * Chuyển hướng đến URL dự định hoặc trang dự phòng với thông báo thành công
     */
    protected function intendedWithSuccess(string $fallbackRoute, string $message = 'Thao tác thành công')
    {
        return redirect()->intended(route($fallbackRoute))->with('success', $message);
    }

    // ========== KIỂM TRA QUYỀN TRUY CẬP ==========
    
    /**
     * Lấy thông tin user đang đăng nhập
     */
    protected function getUser()
    {
        return auth()->user();
    }

    /**
     * Lấy ID của user đang đăng nhập
     */
    protected function getUserId()
    {
        return auth()->id();
    }

    /**
     * Kiểm tra user có phải là admin không
     */
    protected function isAdmin(): bool
    {
        $user = $this->getUser();
        return $user && $user->role->slug === 'admin';  // ← DÙNG SLUG
    }

    /**
     * Kiểm tra user có phải là manager không
     */
    protected function isManager(): bool
    {
        $user = $this->getUser();
        return $user && $user->role->slug === 'manager';  // ← DÙNG SLUG
    }

    /**
     * Kiểm tra user có phải là employee không
     */
    protected function isEmployee(): bool
    {
        $user = $this->getUser();
        return $user && $user->role->slug === 'employee';  // ← DÙNG SLUG
    }

    /**
     * Chỉ cho phép admin truy cập
     */
    protected function authorizeAdmin(): void
    {
        if (!$this->isAdmin()) {
            abort(403, 'Bạn không có quyền truy cập trang này');
        }
    }

    /**
     * Chỉ cho phép manager hoặc admin truy cập
     */
    protected function authorizeManager(): void
    {
        $user = $this->getUser();
        if (!$user || !in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Bạn không có quyền truy cập trang này');
        }
    }

    // ========== PHÂN TRANG VÀ LỌC DỮ LIỆU ==========
    
    /**
     * Lấy số bản ghi mỗi trang từ request
     */
    protected function getPerPage(int $default = 15): int
    {
        return request('per_page', $default);
    }

    /**
     * Lấy thông tin sắp xếp từ request
     */
    protected function getSortParams(string $defaultSort = 'id', string $defaultOrder = 'desc'): array
    {
        return [
            'sort_by' => request('sort_by', $defaultSort),
            'sort_order' => request('sort_order', $defaultOrder)
        ];
    }

    /**
     * Áp dụng bộ lọc chung cho query
     */
    protected function applyCommonFilters($query, array $filterable = [])
    {
        // Tìm kiếm
        if (request()->has('search') && !empty(request('search'))) {
            $search = request('search');
            $query->where(function($q) use ($search, $filterable) {
                foreach ($filterable as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        // Lọc theo trạng thái
        if (request()->has('status') && !empty(request('status'))) {
            $query->where('status', request('status'));
        }

        // Lọc theo khoảng thời gian
        if (request()->has('from_date') && !empty(request('from_date'))) {
            $query->whereDate('created_at', '>=', request('from_date'));
        }

        if (request()->has('to_date') && !empty(request('to_date'))) {
            $query->whereDate('created_at', '<=', request('to_date'));
        }

        return $query;
    }

    // ========== XỬ LÝ FILE UPLOAD ==========
    
    /**
     * Upload file và trả về đường dẫn
     */
    protected function uploadFile($file, string $folder = 'uploads', array $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'])
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $extension = $file->getClientOriginalExtension();
        
        if (!in_array(strtolower($extension), $allowedTypes)) {
            throw new \Exception('Loại file không được hỗ trợ');
        }

        $fileName = time() . '_' . uniqid() . '.' . $extension;
        $filePath = $file->storeAs($folder, $fileName, 'public');

        return $filePath;
    }

    /**
     * Xóa file từ storage
     */
    protected function deleteFile(string $filePath): bool
    {
        if ($filePath && \Storage::disk('public')->exists($filePath)) {
            return \Storage::disk('public')->delete($filePath);
        }
        return false;
    }

    // ========== XỬ LÝ SESSION ==========
    
    /**
     * Lưu dữ liệu vào session
     */
    protected function setSession(string $key, $value): void
    {
        session([$key => $value]);
    }

    /**
     * Lấy dữ liệu từ session
     */
    protected function getSession(string $key, $default = null)
    {
        return session($key, $default);
    }

    /**
     * Flash dữ liệu vào session (chỉ tồn tại trong 1 request)
     */
    protected function flash(string $key, $value): void
    {
        session()->flash($key, $value);
    }
}