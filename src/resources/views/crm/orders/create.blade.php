@extends('layouts.crm')

@section('title', 'Оформление заказа · Coffee CRM')

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm.dashboard') }}">Каталог</a></li>
            <li class="breadcrumb-item active" aria-current="page">Оформление</li>
        </ol>
    </nav>

    <h1 class="h2 mb-4">Оформление заказа</h1>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('crm.orders.store') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $product->name }}</h5>
                                @if ($product->description)
                                    <p class="text-muted small mb-0">{{ $product->description }}</p>
                                @endif
                            </div>
                            <span class="fs-5 fw-bold">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                        </div>

                        <div class="mb-3">
                            <label for="qty" class="form-label">Количество, шт.</label>
                            <input type="number" id="qty" name="qty" value="1" min="1" max="99" class="form-control @error('qty') is-invalid @enderror" required>
                            @error('qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">Комментарий <span class="text-muted">(необязательно)</span></label>
                            <textarea id="comment" name="comment" rows="3" class="form-control @error('comment') is-invalid @enderror" placeholder="Как приготовить, куда доставить и т.д.">{{ old('comment') }}</textarea>
                            @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('crm.dashboard') }}" class="btn btn-outline-secondary">Отмена</a>
                            <button type="submit" class="btn btn-primary">Оформить заказ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm bg-body-tertiary">
                <div class="card-body">
                    <h5 class="card-title">Итог</h5>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Цена за шт.</span>
                        <span>{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <span>К оплате</span>
                        <span>{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Оплата — наличными или переводом при получении.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const price = {{ $product->price }};
            const qtyInput = document.getElementById('qty');
            const totalEl = document.querySelector('.col-lg-5 .fs-5.fw-bold span:last-child');
            const fmt = new Intl.NumberFormat('ru-RU');
            const recalc = () => {
                const q = Math.max(1, parseInt(qtyInput.value, 10) || 1);
                totalEl.textContent = fmt.format(price * q) + ' ₽';
            };
            qtyInput.addEventListener('input', recalc);
        </script>
    @endpush
@endsection
