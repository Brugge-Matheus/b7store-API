<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRelatedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'numeric', 'exists:products,id'],
            'limit' => ['sometimes', 'numeric']
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O ID do produto é obrigatório',
            'id.exists' => 'Produto não encontrado no sistema',
            'id.numeric' => 'O ID deve ser um número válido',
            'limit' => 'O limite deve ser um número válido'
        ];
    }

   public function validationData():array 
    {
        return array_merge($this->query(), $this->route()->parameters());
    }
}
