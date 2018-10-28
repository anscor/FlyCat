<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaserRormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account' => 'required|integer|unique:merchants|min:10000000|max:99999999',
            'alias' => 'required|string',
            'password' => 'required|string|min:6|max:16',
            'blance' => 'required|numeric|min:1000|max:50000',
        ];
    }
}
