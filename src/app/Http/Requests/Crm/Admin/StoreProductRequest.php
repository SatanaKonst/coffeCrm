<?php

namespace App\Http\Requests\Crm\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название обязательно.',
            'name.max' => 'Название — не более 255 символов.',
            'price.required' => 'Цена обязательна.',
            'price.min' => 'Цена не может быть отрицательной.',
            'price.max' => 'Слишком большая цена.',
        ];
    }
}
