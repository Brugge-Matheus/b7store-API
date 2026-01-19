<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductShowRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'numeric', 'exists:products,id']
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O ID do produto é obrigatório',
            'id.exists' => 'Produto não encontrado no sistema',
            'id.numeric' => 'O ID deve ser um número válido'
        ];
    }

    public function validationData(): array 
    {
        return $this->route()->parameters();
    }
}
