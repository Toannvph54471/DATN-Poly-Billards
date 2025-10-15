<?php

namespace App\Helpers;

class CommonHelper
{
    /**
     * Format currency
     */
    public static function formatCurrency($amount)
    {
        return number_format($amount, 0, ',', '.') . ' VNĐ';
    }

    /**
     * Format date
     */
    public static function formatDate($date)
    {
        return date('d/m/Y', strtotime($date));
    }

    /**
     * Generate random string
     */
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $randomString;
    }

    // Thêm các hàm helper khác tại đây
}