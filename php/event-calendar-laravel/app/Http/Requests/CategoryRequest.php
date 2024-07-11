<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $model = "App\Models\Category";
        $uniqueRule = "unique:$model";

        if ($this->cat)
        {
            $uniqueRule = Rule::unique($model)->ignore($this->cat->id);
        }

        return [
            "name" => ["bail", "required", "max:100", $uniqueRule]
        ];
    }
}
