<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'zipcode' => ['required', 'string', 'digits:8'],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string'],
            'complement' => ['sometimes', 'nullable', 'string', 'max:255']
        ];
    }

    public function messages(): array 
    {
        return [
            'zipcode.required' => 'O CEP é obrigatório.',
            'zipcode.digits' => 'O CEP deve conter 8 dígitos.',
            'street.required' => 'A rua é obrigatória.',
            'street.max' => 'A rua deve ter no máximo 255 caracteres.',
            'number.required' => 'O número é obrigatório.',
            'number.max' => 'O número deve ter no máximo 10 caracteres.',
            'city.required' => 'A cidade é obrigatória.',
            'city.max' => 'A cidade deve ter no máximo 100 caracteres.',
            'state.required' => 'O estado é obrigatório.',
            'state.max' => 'O estado deve ter no máximo 50 caracteres.',
            'country.required' => 'O país é obrigatório.',
            'complement.max' => 'O complemento deve ter no máximo 255 caracteres.',
        ];
    }   
}
