<?php

namespace App\Enums;

/**
 * Объём упаковки кофе.
 *
 * ponytail: коэффициенты убраны — теперь цена задаётся индивидуально
 * для каждого товара через product_prices (см. ProductPrice).
 */
enum CoffeeVolume: string
{
    case G200 = 'g200';
    case G500 = 'g500';
    case KG1 = 'kg1';

    public function label(): string
    {
        return match ($this) {
            self::G200 => '200 г',
            self::G500 => '500 г',
            self::KG1 => '1 кг',
        };
    }

    /** Короткая метка для таблиц. */
    public function short(): string
    {
        return match ($this) {
            self::G200 => '200г',
            self::G500 => '500г',
            self::KG1 => '1кг',
        };
    }
}
