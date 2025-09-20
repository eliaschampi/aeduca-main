<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
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
            "cashactiontype_code" => "required",
            "description" => "required|min:10|max:150",
            "voucher_num" => "nullable|max:20",
            "cash_code" => "required",
            "total" => "required",
            "details" => ""
        ];
    }
}
