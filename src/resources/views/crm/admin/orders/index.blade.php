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
        @foreach ($orders as $order)
            <div class="card shadow-sm mb-2">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-semibold">#{{ $order->id }}</span>
                                @include('crm.partials.order-status-badge', ['status' => $order->status])
                                <span class="text-muted small">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="small">
                                <span class="text-muted">Клиент:</span>
                                <span class="fw-semibold">{{ $order->client_name }}</span>
                                <span class="text-muted ms-2">☎ {{ $order->client_phone }}</span>
                            </div>
                            <div class="small mt-1">
                                <span class="text-muted">Адрес:</span> {{ $order->addressLine() }}
                            </div>
                            <div class="small mt-1">
                                @foreach ($order->items as $item)
                                    <span class="badge text-bg-light">{{ $item->name }} × {{ $item->qty }}</span>
                                @endforeach
                                <span class="badge text-bg-secondary">{{ $order->volume->short() }}</span>
                                @if($order->grind) <span class="badge text-bg-info">помол</span> @endif
                                <span class="badge text-bg-secondary">{{ $order->subscription->label() }}</span>
                            </div>
                            @if ($order->comment)
                                <div class="small text-muted mt-1 fst-italic">«{{ \Illuminate\Support\Str::limit($order->comment, 120) }}»</div>
                            @endif
                        </div>

                        <div class="text-end" style="min-width: 160px;">
                            <div class="fs-5 fw-bold">{{ number_format($order->total, 0, '.', ' ') }} ₽</div>
                            <form method="POST" action="{{ route('crm.admin.orders.update', $order) }}">
                                @csrf
                                @method('PATCH')
                                <div class="input-group input-group-sm mt-2">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        @foreach ($statuses as $s)
                                            <option value="{{ $s->value }}" @selected($order->status === $s)>{{ $s->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="mt-3">{{ $orders->links() }}</div>
    @endif
@endsection
