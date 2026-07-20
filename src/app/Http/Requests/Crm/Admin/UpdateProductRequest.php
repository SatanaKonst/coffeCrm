<?php

namespace App\Http\Requests\Crm\Admin;

use App\Enums\CoffeeVolume;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
            'prices' => ['sometimes', 'array', 'min:1'],
            'prices.*.volume' => ['required', Rule::enum(CoffeeVolume::class)],
            'prices.*.price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }
}
