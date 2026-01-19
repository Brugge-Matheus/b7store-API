<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartMountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'regex:/^\d+(,\d+)*$/']
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'A lista de IDs é obrigatória',
            'ids.regex' => 'IDs devem ser números separados por vírgula e sem espaços em branco (ex: 1,2,3)'
        ];
    }

    public function validationData()
    {
        return $this->query();
    }
}
