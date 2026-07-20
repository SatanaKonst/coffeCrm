<?php

namespace App\Models;

use App\Enums\CoffeeVolume;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read Collection<int, ProductPrice> $prices
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return HasMany<ProductPrice, $this>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Цена для конкретного объёма или null, если не задана. */
    public function priceFor(CoffeeVolume $volume): ?string
    {
        $price = $this->relationLoaded('prices')
            ? $this->prices->firstWhere('volume', $volume)
            : $this->prices()->where('volume', $volume->value)->first();

        return $price ? (string) $price->price : null;
    }

    /** Минимальная цену среди всех объёмов (для каталога «от X ₽»). */
    public function minPrice(): ?string
    {
        $min = $this->prices->min('price');

        return $min !== null ? (string) $min : null;
    }

    /** Доступен ли товар в указанном объёме. */
    public function hasVolume(CoffeeVolume $volume): bool
    {
        return $this->priceFor($volume) !== null;
    }
}
