<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
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

    public function rules()
    {
        $degree = $this->input("degree_code");
        return [
            "code" => "required|max:10",
            "degree_code" => "required|max:10",
            "name" => "required|max:1|unique:section,name,NULL,NULL,degree_code," . $degree,
        ];
    }

    public function messages()
    {
        return [
            "unique" => "Querido Usuario, esta Seccion ya esta registrado en el Grado",
        ];
    }
}
