@extends('layouts.crm')

@section('title', 'Главная · Coffee CRM')

@section('content')
    <h1 class="h2 mb-1">Добро пожаловать{{ isset($client) ? ', '.$client->name : '' }}!</h1>
    @isset($client)
        <p class="text-muted mb-4">VK ID: {{ $client->vk_user_id }}</p>
    @endisset

    @if (!empty($stats))
        <div class="row g-3 mb-4">
            @foreach ($stats as $label => $s)
                <div class="col-sm-6 col-lg-4">
                    <a href="{{ $s['route'] }}" class="text-decoration-none">
                        <div class="card border-{{ $s['color'] }} shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-{{ $s['color'] }} fs-2 fw-bold lh-1">{{ $s['value'] }}</div>
                                <div class="text-muted small mt-1">{{ $label }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">☕ Каталог</h5>
                    <p class="card-text text-muted">Выбрать кофе и оформить заказ.</p>
                    @if(Route::has('crm.products.index'))
                        <a href="{{ route('crm.products.index') }}" class="btn btn-primary">Открыть каталог</a>
                    @else
                        <span class="badge text-bg-secondary">Скоро</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📦 Мои заказы</h5>
                    <p class="card-text text-muted">История и статусы ваших заказов.</p>
                    @if(Route::has('crm.orders.index'))
                        <a href="{{ route('crm.orders.index') }}" class="btn btn-outline-primary">Открыть заказы</a>
                    @else
                        <span class="badge text-bg-secondary">Скоро</span>
                    @endif
                </div>
            </div>
        </div>

        @if($isAdmin ?? false)
            <div class="col-12">
                <hr class="my-4">
                <h2 class="h4 text-warning">Администрирование</h2>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-warning shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">📋 Заказы клиентов</h5>
                        <p class="card-text text-muted">Все заказы, смена статусов.</p>
                        @if(Route::has('crm.admin.orders.index'))
                            <a href="{{ route('crm.admin.orders.index') }}" class="btn btn-warning">К заказам</a>
                        @else
                            <span class="badge text-bg-secondary">Скоро</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-warning shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">🛒 Управление товарами</h5>
                        <p class="card-text text-muted">CRUD ассортимента, активности.</p>
                        @if(Route::has('crm.admin.products.index'))
                            <a href="{{ route('crm.admin.products.index') }}" class="btn btn-outline-warning">К товарам</a>
                        @else
                            <span class="badge text-bg-secondary">Скоро</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
