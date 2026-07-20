@extends('layouts.crm')

@section('title', 'Каталог · Coffee CRM')

@section('content')
    <div class="d-flex justify-content-between align-items-baseline mb-4">
        <h1 class="h2 mb-0">Каталог</h1>
        <span class="text-muted small">{{ $products->total() }} товаров</span>
    </div>

    @if ($products->isEmpty())
        <div class="alert alert-info">Ассортимент скоро появится.</div>
    @else
        <div class="row g-3">
            @foreach ($products as $product)
                <div class="col-sm-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>

                            @if ($product->description)
                                <p class="card-text text-muted small flex-grow-1">{{ $product->description }}</p>
                            @else
                                <div class="flex-grow-1"></div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fs-5 fw-bold">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>

                                @if (Route::has('crm.orders.create'))
                                    <a href="{{ route('crm.orders.create', ['product' => $product->id]) }}" class="btn btn-sm btn-primary">Заказать</a>
                                @else
                                    <span class="badge text-bg-secondary">Скоро</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    @endif
@endsection
