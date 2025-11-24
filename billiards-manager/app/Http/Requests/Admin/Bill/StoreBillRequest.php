<?php

namespace App\Http\Requests\Admin\Bill;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'table_id' => 'required|exists:tables,id',
            'user_phone' => 'nullable|string',
            'user_name' => 'nullable|string|max:255',
            'guest_count' => 'required|integer|min:1',
            'reservation_id' => 'nullable|exists:reservations,id'
        ];
    }
}
