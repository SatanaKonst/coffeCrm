@extends('layouts.crm')

@section('title', 'Главная · Coffee CRM')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <h1 class="h2">Добро пожаловать, {{ $client->name }}!</h1>
            <p class="text-muted mb-0">VK ID: {{ $client->vk_user_id }}</p>
        </div>

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

        @if($isAdmin)
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
