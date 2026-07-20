<?php

namespace App\Http\Requests\Crm\Admin;

use App\Enums\CoffeeVolume;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'is_active' => ['boolean'],
            'prices' => ['required', 'array', 'min:1'],
            'prices.*.volume' => ['required', Rule::enum(CoffeeVolume::class)],
            'prices.*.price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название обязательно.',
            'name.max' => 'Название — не более 255 символов.',
            'prices.required' => 'Укажите цену хотя бы для одного объёма.',
            'prices.min' => 'Укажите цену хотя бы для одного объёма.',
            'prices.*.price.required' => 'Укажите цену.',
            'prices.*.price.min' => 'Цена не может быть отрицательной.',
            'prices.*.price.max' => 'Слишком большая цена.',
        ];
    }
}
