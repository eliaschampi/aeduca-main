<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomeRequest extends FormRequest
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
            "type" => "required",
            "cash_code" => "required",
            "total_discount" => "required",
            "total" => "required",
            "mod" => "required",
            "customer_identifier" => "",
            "section_code" => "",
            "name" => ""
        ];
    }
}
