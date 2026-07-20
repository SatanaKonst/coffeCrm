@extends('layouts.crm')

@section('title', 'Мои заказы · Coffee CRM')

@section('content')
    <div class="d-flex justify-content-between align-items-baseline mb-4">
        <h1 class="h2 mb-0">Мои заказы</h1>
        <a href="{{ route('crm.products.index') }}" class="btn btn-outline-primary btn-sm">+ Новый заказ</a>
    </div>

    @if ($orders->isEmpty())
        <div class="alert alert-info">У вас пока нет заказов.</div>
    @else
        <div class="table-responsive">
            <table class="table table align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">№</th>
                        <th scope="col">Дата</th>
                        <th scope="col">Состав</th>
                        <th scope="col" class="text-end">Сумма</th>
                        <th scope="col">Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td class="fw-semibold">#{{ $order->id }}</td>
                            <td class="text-muted small">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <ul class="list-unstyled mb-0 small">
                                    @foreach ($order->items as $item)
                                        <li>{{ $item->name }} × {{ $item->qty }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end fw-semibold">{{ number_format($order->total, 0, '.', ' ') }} ₽</td>
                            <td>
                                @include('crm.partials.order-status-badge', ['status' => $order->status])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $orders->links() }}</div>
    @endif
@endsection
