<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartFinishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.productId' => ['required', 'integer', 'exists:products,id'],
            'cart.*.quantity' => ['required', 'integer', 'min:1'],
            'addressId' => ['required', 'integer', 'exists:addresses,id,user_id,' . $this->user()->id ]
        ];
    }

    public function messages(): array
    {
        return [
            'cart.required' => 'O carrinho é obrigatório',
            'cart.array' => 'O carrinho deve ser um array válido',
            'cart.min' => 'O carrinho deve conter pelo menos um item',
            'cart.*.productId.required' => 'O ID do produto é obrigatório',
            'cart.*.productId.integer' => 'O ID do produto deve ser um número inteiro',
            'cart.*.productId.exists' => 'O produto não existe',
            'cart.*.quantity.required' => 'A quantidade é obrigatória',
            'cart.*.quantity.integer' => 'A quantidade deve ser um número inteiro',
            'cart.*.quantity.min' => 'A quantidade deve ser pelo menos 1',
            'addressId.required' => 'O endereço é obrigatório',
            'addressId.integer' => 'O ID do endereço deve ser um número inteiro',
            'addressId.exists' => 'Endereço inválido ou não pertence ao usuário',
        ];
    }
}
