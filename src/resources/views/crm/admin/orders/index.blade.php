@extends('layouts.crm')

@section('title', 'Заказы · Админка · Coffee CRM')

@section('content')
    <div class="d-flex justify-content-between align-items-baseline mb-3">
        <h1 class="h2 mb-0">Заказы</h1>
        <span class="text-muted small">Всего: {{ $orders->total() }}</span>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-sm-4">
            <label class="form-label small text-muted mb-1">Статус</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Все</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s->value }}" @selected($filter['status'] === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label class="form-label small text-muted mb-1">Поиск по имени / №</label>
            <input type="text" name="q" value="{{ $filter['q'] }}" class="form-control form-control-sm" placeholder="Имя или № заказа">
        </div>
        <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-outline-primary">Найти</button>
            <a href="{{ route('crm.admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Сброс</a>
        </div>
    </form>

    @if ($orders->isEmpty())
        <div class="alert alert-info">Заказы не найдены.</div>
    @else
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">№</th>
                        <th scope="col">Дата</th>
                        <th scope="col">Клиент</th>
                        <th scope="col">Состав</th>
                        <th scope="col" class="text-end">Сумма</th>
                        <th scope="col">Статус</th>
                        <th scope="col">Изменить</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td class="fw-semibold">#{{ $order->id }}</td>
                            <td class="text-muted small text-nowrap">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ $order->client?->name }}</td>
                            <td>
                                <ul class="list-unstyled mb-0 small">
                                    @foreach ($order->items as $item)
                                        <li>{{ $item->name }} × {{ $item->qty }}</li>
                                    @endforeach
                                </ul>
                                @if ($order->comment)
                                    <div class="small text-muted fst-italic mt-1">«{{ \Illuminate\Support\Str::limit($order->comment, 80) }}»</div>
                                @endif
                            </td>
                            <td class="text-end fw-semibold">{{ number_format($order->total, 0, '.', ' ') }} ₽</td>
                            <td>@include('crm.partials.order-status-badge', ['status' => $order->status])</td>
                            <td>
                                <form method="POST" action="{{ route('crm.admin.orders.update', $order) }}{{ $filter['status'] || $filter['q'] ? '?'.http_build_query($filter) : '' }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="input-group input-group-sm">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            @foreach ($statuses as $s)
                                                <option value="{{ $s->value }}" @selected($order->status === $s)>{{ $s->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $orders->links() }}</div>
    @endif
@endsection
