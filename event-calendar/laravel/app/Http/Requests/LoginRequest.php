<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    function rules(): array
    {
        return [
            "name" => "bail|required|min:3|max:30|regex:/^[a-zA-Z]\w+$/",
            "password" => "bail|required|min:3|max:30"
        ];
    }
}
