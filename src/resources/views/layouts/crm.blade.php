<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Coffee CRM')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-body-tertiary">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('crm.dashboard') }}">☕ Coffee CRM</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Меню">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @if(Route::has('crm.products.index'))
                    <li class="nav-item">
                        <a class="nav-link{{ request()->routeIs('crm.products.*') ? ' active' : '' }}" href="{{ route('crm.products.index') }}">Каталог</a>
                    </li>
                @endif
                @if(Route::has('crm.orders.index'))
                    <li class="nav-item">
                        <a class="nav-link{{ request()->routeIs('crm.orders.*') ? ' active' : '' }}" href="{{ route('crm.orders.index') }}">Мои заказы</a>
                    </li>
                @endif

                @if($isAdmin ?? false)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle{{ request()->routeIs('crm.admin.*') ? ' active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Админка</a>
                        <ul class="dropdown-menu">
                            @if(Route::has('crm.admin.orders.index'))
                                <li><a class="dropdown-item" href="{{ route('crm.admin.orders.index') }}">Заказы</a></li>
                            @endif
                            @if(Route::has('crm.admin.products.index'))
                                <li><a class="dropdown-item" href="{{ route('crm.admin.products.index') }}">Товары</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item d-flex align-items-center">
                    @if($isAdmin ?? false)
                        <span class="badge text-bg-warning me-2">Админ</span>
                    @endif
                    <span class="navbar-text text-light">{{ $client->name ?? '' }}</span>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    @foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $key => $cls)
        @if (session($key))
            <div class="alert alert-{{ $cls }} alert-dismissible fade show" role="alert">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
            </div>
        @endif
    @endforeach

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
