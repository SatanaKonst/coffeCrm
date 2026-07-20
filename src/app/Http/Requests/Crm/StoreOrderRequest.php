<?php

namespace App\Http\Requests\Crm;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Валидация создания заказа.
 *
 * На старте MVP — один товар за заказ. Корзина будет позже (см. PLAN.md).
 */
class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', Rule::exists(Product::class, 'id')->where('is_active', true)],
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'Выбранный товар недоступен для заказа.',
            'qty.min' => 'Минимальное количество — 1 шт.',
            'qty.max' => 'За один раз — не более 99 шт.',
        ];
    }
}
