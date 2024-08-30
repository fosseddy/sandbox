<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["bail", "required", "max:200"],
            "date" => ["bail", "required", "date"],
            "category_id" => [
                "bail",
				"required",
                "exists:App\Models\Category,id"
            ],
            "duration" => ["nullable", "bail", "integer", "numeric", "gte:0"],
            "location" => ["nullable", "bail", "max:300"],
            "organizer" => ["nullable", "bail", "max:250"],
            "file" => [
                "nullable",
				"bail",
				"image",
				"mimes:jpeg,png",
                "max:" . 5 << 20
            ]
        ];
    }
}
