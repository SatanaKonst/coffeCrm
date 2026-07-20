<?php

namespace App\Models;

use App\Enums\CoffeeVolume;
use App\Enums\OrderStatus;
use App\Enums\OrderSubscription;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_name',
        'client_phone',
        'status',
        'total',
        'comment',
        'city',
        'street',
        'building',
        'entrance',
        'apartment',
        'intercom',
        'subscription',
        'volume',
        'grind',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'total' => 'decimal:2',
        'subscription' => OrderSubscription::class,
        'volume' => CoffeeVolume::class,
        'grind' => 'boolean',
    ];

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Полный адрес одной строкой. */
    public function addressLine(): string
    {
        $parts = array_filter([
            $this->city,
            $this->street,
            $this->building ? 'дом '.$this->building : null,
            $this->entrance ? 'под. '.$this->entrance : null,
            $this->apartment ? 'кв. '.$this->apartment : null,
            $this->intercom ? 'домофон '.$this->intercom : null,
        ]);

        return implode(', ', $parts);
    }
}
