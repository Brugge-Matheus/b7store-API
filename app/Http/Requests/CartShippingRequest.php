<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartShippingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'zipcode' => ['required', 'numeric', 'digits:8']
        ];
    }

    public function messages(): array
    {
        return [
            'zipcode.required' => 'O zipcode é obrigatório',
            'zipcode.numeric' => 'O zipcode deve ser um número válido',
            'zipcode.digits' => 'O zipcode deve conter 8 digitos'
        ];
    }

    public function validationData()
    {
        return $this->query();
    }
}
