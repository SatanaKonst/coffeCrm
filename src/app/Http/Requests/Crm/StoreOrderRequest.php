<?php

namespace App\Http\Requests\Crm;

use App\Enums\CoffeeVolume;
use App\Enums\OrderSubscription;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Создание заказа. ФИО и телефон валидируем здесь же —
 * они пойдут и в заказ (snapshot), и в профиль клиента.
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

            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:30', 'regex:/^[\d\s\+\-\(\)]+$/'],

            'city' => ['required', 'string', 'max:100'],
            'street' => ['required', 'string', 'max:150'],
            'building' => ['required', 'string', 'max:20'],
            'entrance' => ['nullable', 'string', 'max:20'],
            'apartment' => ['nullable', 'string', 'max:20'],
            'intercom' => ['nullable', 'string', 'max:30'],

            'comment' => ['nullable', 'string', 'max:1000'],

            'subscription' => ['required', Rule::enum(OrderSubscription::class)],
            'volume' => ['required', Rule::enum(CoffeeVolume::class)],
            'grind' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.exists' => 'Выбранный товар недоступен для заказа.',
            'qty.min' => 'Минимальное количество — 1 шт.',
            'qty.max' => 'За один раз — не более 99 шт.',
            'client_name.required' => 'Укажите ФИО.',
            'client_phone.required' => 'Укажите телефон.',
            'client_phone.regex' => 'Телефон может содержать только цифры, пробелы, +, -, скобки.',
            'city.required' => 'Укажите город.',
            'street.required' => 'Укажите улицу.',
            'building.required' => 'Укажите номер дома.',
        ];
    }
}
