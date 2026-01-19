<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryMetadataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'exists:categories,slug']
        ];
    }

    public function messages(): array
    {
        return [
            'slug.required' => 'O slug é obrigatório',
            'slug.string' => 'O slug deve ser um texto válido',
            'slug.unique' => 'Categoria não encontrada no sistema'
        ];
    }

    public function validationData()
    {
        return $this->route()->parameters();
    }
}
