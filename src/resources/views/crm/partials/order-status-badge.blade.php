@php
    /** @var \App\Enums\OrderStatus $status */
    $cls = match ($status) {
        \App\Enums\OrderStatus::New => 'bg-info-subtle text-info-emphasis border-info-subtle',
        \App\Enums\OrderStatus::Confirmed => 'bg-primary-subtle text-primary-emphasis border-primary-subtle',
        \App\Enums\OrderStatus::Delivered => 'bg-success-subtle text-success-emphasis border-success-subtle',
        \App\Enums\OrderStatus::Cancelled => 'bg-danger-subtle text-danger-emphasis border-danger-subtle',
    };
@endphp

<span class="badge rounded-pill border {{ $cls }}">{{ $status->label() }}</span>
