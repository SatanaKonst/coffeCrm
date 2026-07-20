<?php

namespace App\Enums;

/**
 * Формат подписки на доставку кофе.
 */
enum OrderSubscription: string
{
    case OneTime = 'one_time';
    case Monthly = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::OneTime => 'Разовый',
            self::Monthly => 'Ежемесячный',
        };
    }
}
