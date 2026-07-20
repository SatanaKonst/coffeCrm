@extends('layouts.crm')

@section('title', $product->exists ? "Правка · {$product->name}" : 'Новый товар · Coffee CRM')

@php
    $volumes = \App\Enums\CoffeeVolume::cases();
    // Существующие цены товара по объёму: volume => price
    $existing = $product->exists
        ? $product->prices->keyBy(fn ($p) => $p->volume->value)->map->price
        : collect();
@endphp

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm.admin.products.index') }}">Товары</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->exists ? 'Правка' : 'Создание' }}</li>
        </ol>
    </nav>

    <h1 class="h2 mb-4">{{ $product->exists ? 'Правка товара' : 'Новый товар' }}</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ $product->exists ? route('crm.admin.products.update', $product) : route('crm.admin.products.store') }}">
                        @csrf
                        @method($product->exists ? 'PATCH' : 'POST')

                        <div class="mb-3">
                            <label for="name" class="form-label">Название</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Описание <span class="text-muted">(необязательно)</span></label>
                            <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <fieldset class="mb-3">
                            <legend class="h6 fw-semibold border-bottom pb-2 mb-3">Цены по объёмам</legend>
                            <div class="row g-3">
                                @foreach ($volumes as $v)
                                    @php
                                        $key = "prices.{$v->value}";
                                        $oldVal = old("prices.{$v->value}", $existing->get($v->value));
                                    @endphp
                                    <div class="col-md-4">
                                        <label for="price-{{ $v->value }}" class="form-label">{{ $v->label() }}</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" max="999999.99"
                                                id="price-{{ $v->value }}"
                                                name="prices[{{ $v->value }}][price]"
                                                value="{{ $oldVal !== null ? number_format((float) $oldVal, 2, '.', '') : '' }}"
                                                class="form-control" placeholder="0.00">
                                            <input type="hidden" name="prices[{{ $v->value }}][volume]" value="{{ $v->value }}">
                                            <span class="input-group-text">₽</span>
                                        </div>
                                        <div class="form-text small">Если объём недоступен — оставьте пустым.</div>
                                    </div>
                                @endforeach
                            </div>
                            @error('prices') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        </fieldset>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active ?? true))>
                                <label class="form-check-label" for="is_active">Активен (виден клиентам)</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <a href="{{ route('crm.admin.products.index') }}" class="btn btn-outline-secondary">Отмена</a>
                            <button type="submit" class="btn btn-primary">{{ $product->exists ? 'Сохранить' : 'Создать' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
