<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
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
        $rule = $this->method() === "POST" ? "unique:person|digits:8" : "required";
        $email = $this->method() === "POST" ? "unique:person" : "";
        return [
            "dni" => $rule,
            "name" => "required|min:3|max:50",
            "lastname" => "required|min:10|max:80",
            "birthdate" => "nullable|min:10|max:10",
            "ubigeo" => "required|digits:6",
            "district" => "required|min:3|max:50",
            "address" => "required|min:3|max:150",
            "email" => $email . "|nullable|email|max:50",
            "phone" => "nullable|min:9|max:50",
            "obs" => "nullable|min:1|max:100",
            "gender" => "nullable|min:1|max:100",
        ];
    }
}
