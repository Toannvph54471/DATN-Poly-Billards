<?php

namespace App\Services;

class VNPayService
{
    private $tmnCode;
    private $hashSecret;
    private $url;
    private $returnUrl;
    private $ipnUrl;

    public function __construct()
    {
        $this->tmnCode = env('VNPAY_TMN_CODE');
        $this->hashSecret = env('VNPAY_HASH_SECRET');
        $this->url = env('VNPAY_URL');
        $this->returnUrl = env('VNPAY_RETURN_URL');
        $this->ipnUrl = env('VNPAY_IPN_URL');
    }

    public function createPayment(array $params)
    {
        $vnp_TxnRef = $params['txn_ref'];
        $vnp_OrderInfo = $params['order_info'];
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $params['amount'] * 100; // VNPay yêu cầu nhân 100
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();
        
        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $this->tmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $this->returnUrl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        // Bank code nếu có
        if (!empty($params['bank_code'])) {
            $inputData['vnp_BankCode'] = $params['bank_code'];
        }

        ksort($inputData);
        $hashData = '';
        
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        
        $hashData = rtrim($hashData, '&');
        
        $vnp_Url = $this->url . '?' . $hashData;
        $vnpSecureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        
        return $vnp_Url;
    }

    public function verifySignature($inputData, $secureHash)
    {
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        
        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');
        
        $checkHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        return $checkHash === $secureHash;
    }
}