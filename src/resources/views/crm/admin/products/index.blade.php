@extends('layouts.crm')

@section('title', 'Товары · Админка · Coffee CRM')

@section('content')
    <div class="d-flex justify-content-between align-items-baseline mb-3">
        <h1 class="h2 mb-0">Товары</h1>
        <a href="{{ route('crm.admin.products.create') }}" class="btn btn-primary btn-sm">+ Новый товар</a>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-sm-6">
            <label class="form-label small text-muted mb-1">Поиск по названию</label>
            <input type="text" name="q" value="{{ $filter['q'] }}" class="form-control form-control-sm" placeholder="Название или часть">
        </div>
        <div class="col-auto d-flex align-items-center gap-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="inactive" value="1" id="f-inactive" @checked($filter['inactive']) onchange="this.form.submit()">
                <label class="form-check-label small" for="f-inactive">Показать неактивные</label>
            </div>
            <button type="submit" class="btn btn-sm btn-outline-primary">Найти</button>
            <a href="{{ route('crm.admin.products.index') }}" class="btn btn-sm btn-outline-secondary">Сброс</a>
        </div>
    </form>

    @if ($products->isEmpty())
        <div class="alert alert-info">Товары не найдены.</div>
    @else
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Название</th>
                        <th scope="col">Описание</th>
                        <th scope="col" class="text-end">Цена</th>
                        <th scope="col">Статус</th>
                        <th scope="col" class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td class="text-muted small">
                                @if ($product->description)
                                    {{ \Illuminate\Support\Str::limit($product->description, 80) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($product->price, 0, '.', ' ') }} ₽</td>
                            <td>
                                @if ($product->is_active)
                                    <span class="badge text-bg-success">Активен</span>
                                @else
                                    <span class="badge text-bg-secondary">Скрыт</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('crm.admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Изменить</a>
                                <form method="POST" action="{{ route('crm.admin.products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Удалить товар «{{ e($product->name) }}»?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $products->links() }}</div>
    @endif
@endsection
