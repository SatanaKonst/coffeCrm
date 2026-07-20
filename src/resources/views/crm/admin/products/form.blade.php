@extends('layouts.crm')

@section('title', $product->exists ? "Правка · {$product->name}" : 'Новый товар · Coffee CRM')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm.admin.products.index') }}">Товары</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->exists ? 'Правка' : 'Создание' }}</li>
        </ol>
    </nav>

    <h1 class="h2 mb-4">{{ $product->exists ? 'Правка товара' : 'Новый товар' }}</h1>

    <div class="row">
        <div class="col-lg-7">
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

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="price" class="form-label">Цена, ₽</label>
                                <input type="number" step="0.01" min="0" max="999999.99" id="price" name="price" value="{{ old('price', $product->price) }}" class="form-control @error('price') is-invalid @enderror" required>
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">Активен (виден клиентам)</label>
                                </div>
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
