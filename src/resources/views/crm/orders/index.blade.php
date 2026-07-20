@extends('layouts.crm')

@section('title', 'Мои заказы · Coffee CRM')

@section('content')
    <div class="d-flex justify-content-between align-items-baseline mb-4">
        <h1 class="h2 mb-0">Мои заказы</h1>
        <a href="{{ route('crm.dashboard') }}" class="btn btn-outline-primary btn-sm">+ Новый заказ</a>
    </div>

    @if ($orders->isEmpty())
        <div class="alert alert-info">У вас пока нет заказов.</div>
    @else
        <div class="row g-3">
            @foreach ($orders as $order)
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <span class="fw-semibold">Заказ #{{ $order->id }}</span>
                                    <span class="text-muted small ms-2">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                @include('crm.partials.order-status-badge', ['status' => $order->status])
                            </div>

                            <div class="row small g-2">
                                <div class="col-md-6">
                                    <div class="text-muted">Состав:</div>
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($order->items as $item)
                                            <li>{{ $item->name }} × {{ $item->qty }} — {{ number_format($item->price * $item->qty, 0, '.', ' ') }} ₽</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted">Параметры:</div>
                                    <div>
                                        Объём: {{ $order->volume->short() }}
                                        @if($order->grind) · помол @endif
                                        · {{ $order->subscription->label() }}
                                    </div>
                                    <div class="text-muted mt-1">Адрес:</div>
                                    <div>{{ $order->addressLine() }}</div>
                                    @if ($order->comment)
                                        <div class="text-muted mt-1">Комментарий:</div>
                                        <div class="fst-italic">«{{ $order->comment }}»</div>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-2">
                            <div class="d-flex justify-content-end align-items-center">
                                <span class="text-muted me-2">Итого:</span>
                                <span class="fs-5 fw-bold">{{ number_format($order->total, 0, '.', ' ') }} ₽</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">{{ $orders->links() }}</div>
    @endif
@endsection
