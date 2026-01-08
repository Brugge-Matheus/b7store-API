<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metadata' => ['sometimes', 'string'],
            'orderby' => ['sometimes', 'in:views,selling,price'],
            'limit' => ['sometimes', 'numeric']
        ];
    }

    public function messages():array
    {
        return [
            'metadata' => 'O metadata deve ser um json válido',
            'orderby' => 'O campo orderBy deve conter um dos seguintes valores: views, selling ou price',
            'limit' => 'O limite deve ser um número válido'
        ];
    }

    public function validationData():array 
    {
        return $this->query();
    }
}
