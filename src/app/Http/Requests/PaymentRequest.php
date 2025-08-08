<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "register_code" => "required",
            "pcode" => "required",
            "description" => "required|min:4|max:100",
            "topay" => "required",
            "paid" => "",
            "amount" => "required|numeric",
            "payment_type" => "required"
        ];
    }
}
