<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        $student_dni = $this->input("student_dni");
        $section_code = $this->input("section_code");
        if ($this->input("mode") === "Nueva") {
            $unique = [
                'required',
                Rule::unique('register')
                    ->where(function ($query) use ($student_dni, $section_code) {
                        return $query->where('student_dni', $student_dni)
                            ->where('section_code', $section_code);
                    }),
            ];
        } else {
            $unique = ['required'];
        }
        return [
            "student_dni" => $unique,
            "invoicing" => "required",
            "monthly" => "required|numeric",
            "priority" => "required",
            "section_code" => "required|max:10",
            "state" => "",
            "consV" => ""
        ];
    }
}
