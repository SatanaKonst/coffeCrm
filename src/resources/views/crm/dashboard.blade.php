<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('crm.dashboard') }}">Coffee CRM</a>
        <span class="navbar-text text-light">
            @if($isAdmin)
                <span class="badge text-bg-warning">Админ</span>
            @endif
            {{ $client->name }}
        </span>
    </div>
</nav>

<main class="container py-4">
    <h1>Добро пожаловать, {{ $client->name }}</h1>
    <p class="text-muted">VK ID: {{ $client->vk_user_id }}</p>

    <div class="alert alert-info">
        Этап 2: VK-авторизация работает. Каталог и заказы — в следующих этапах.
    </div>
</main>
</body>
</html>
