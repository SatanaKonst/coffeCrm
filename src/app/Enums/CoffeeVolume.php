<?php

namespace App\Enums;

/**
 * Объём упаковки кофе.
 *
 * 200гр — базовый (цена товара = цена за 200гр).
 * Остальные — множитель от базовой цены.
 *
 * ponytail: коэффициенты — догадка. Согласовать с бизнесом и,
 * при необходимости, вынести в конфиг или в таблицу volumes.
 */
enum CoffeeVolume: string
{
    case G200 = 'g200';
    case G500 = 'g500';
    case KG1 = 'kg1';

    public function label(): string
    {
        return match ($this) {
            self::G200 => '200 г (базовый)',
            self::G500 => '500 г',
            self::KG1 => '1 кг',
        };
    }

    /** Множитель к базовой цене товара. */
    public function multiplier(): float
    {
        return match ($this) {
            self::G200 => 1.0,
            self::G500 => 2.4,
            self::KG1 => 4.5,
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
