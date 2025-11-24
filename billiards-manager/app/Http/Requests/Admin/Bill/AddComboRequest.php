<?php

namespace App\Http\Requests\Admin\Bill;

use Illuminate\Foundation\Http\FormRequest;

class AddComboRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'combo_id' => 'required|exists:combos,id',
            'quantity' => 'required|integer|min:1'
        ];
    }
}
