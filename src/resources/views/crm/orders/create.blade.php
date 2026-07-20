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

                        {{-- Товар --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $product->name }}</h5>
                                @if ($product->description)
                                    <p class="text-muted small mb-0">{{ $product->description }}</p>
                                @endif
                            </div>
                            <span class="fs-5 fw-bold">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                        </div>

                        {{-- Параметры кофе --}}
                        <fieldset class="row g-3 mb-3">
                            <legend class="col-12 h6 fw-semibold border-bottom pb-2 mb-0">Параметры</legend>

                            <div class="col-md-6">
                                <label for="volume" class="form-label">Объём</label>
                                <select id="volume" name="volume" class="form-select @error('volume') is-invalid @enderror">
                                    @foreach ($volumes as $v)
                                        @php($priceForVol = $product->priceFor($v))
                                        <option value="{{ $v->value }}" data-price="{{ $priceForVol !== null ? number_format((float) $priceForVol, 2, '.', '') : '' }}" @selected(old('volume') === $v->value)>
                                            {{ $v->label() }}@if ($priceForVol !== null) — {{ number_format((float) $priceForVol, 0, '.', ' ') }} ₽@endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('volume') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="qty" class="form-label">Количество, шт.</label>
                                <input type="number" id="qty" name="qty" value="{{ old('qty', 1) }}" min="1" max="99" class="form-control @error('qty') is-invalid @enderror" required>
                                @error('qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3 d-flex align-items-center pt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="grind" value="1" id="grind" @checked(old('grind', false))>
                                    <label class="form-check-label" for="grind">Помолоть</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Формат подписки</label>
                                <div>
                                    @foreach (\App\Enums\OrderSubscription::cases() as $s)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="subscription" value="{{ $s->value }}" id="sub-{{ $s->value }}" @checked(old('subscription', 'one_time') === $s->value) required>
                                            <label class="form-check-label" for="sub-{{ $s->value }}">{{ $s->label() }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('subscription') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </fieldset>

                        {{-- Контактные данные --}}
                        <fieldset class="row g-3 mb-3">
                            <legend class="col-12 h6 fw-semibold border-bottom pb-2 mb-0">Контактные данные</legend>

                            <div class="col-md-8">
                                <label for="client_name" class="form-label">ФИО</label>
                                <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $client->name) }}" class="form-control @error('client_name') is-invalid @enderror" required maxlength="255">
                                @error('client_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="client_phone" class="form-label">Телефон</label>
                                <input type="tel" id="client_phone" name="client_phone" value="{{ old('client_phone', $client->phone) }}" class="form-control @error('client_phone') is-invalid @enderror" required placeholder="+7 ...">
                                @error('client_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </fieldset>

                        {{-- Адрес --}}
                        <fieldset class="row g-3 mb-3">
                            <legend class="col-12 h6 fw-semibold border-bottom pb-2 mb-0">Адрес доставки</legend>

                            <div class="col-md-6">
                                <label for="city" class="form-label">Город</label>
                                <input type="text" id="city" name="city" value="{{ old('city') }}" class="form-control @error('city') is-invalid @enderror" required maxlength="100">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="street" class="form-label">Улица</label>
                                <input type="text" id="street" name="street" value="{{ old('street') }}" class="form-control @error('street') is-invalid @enderror" required maxlength="150">
                                @error('street') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="building" class="form-label">Дом</label>
                                <input type="text" id="building" name="building" value="{{ old('building') }}" class="form-control @error('building') is-invalid @enderror" required maxlength="20">
                                @error('building') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="entrance" class="form-label">Подъезд</label>
                                <input type="text" id="entrance" name="entrance" value="{{ old('entrance') }}" class="form-control @error('entrance') is-invalid @enderror" maxlength="20">
                                @error('entrance') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="apartment" class="form-label">Квартира</label>
                                <input type="text" id="apartment" name="apartment" value="{{ old('apartment') }}" class="form-control @error('apartment') is-invalid @enderror" maxlength="20">
                                @error('apartment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="intercom" class="form-label">Домофон</label>
                                <input type="text" id="intercom" name="intercom" value="{{ old('intercom') }}" class="form-control @error('intercom') is-invalid @enderror" maxlength="30">
                                @error('intercom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </fieldset>

                        {{-- Комментарий --}}
                        <div class="mb-3">
                            <label for="comment" class="form-label">Комментарий <span class="text-muted">(необязательно)</span></label>
                            <textarea id="comment" name="comment" rows="3" class="form-control @error('comment') is-invalid @enderror" placeholder="Удобное время доставки, особенности">{{ old('comment') }}</textarea>
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

        {{-- Итог --}}
        @php($initialPrice = $product->priceFor($volumes->first()))
        <div class="col-lg-5">
            <div class="card shadow-sm bg-body-tertiary position-sticky" style="top: 80px;">
                <div class="card-body">
                    <h5 class="card-title">Итог</h5>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Цена за упаковку</span>
                        <span id="price-per-unit">{{ $initialPrice !== null ? number_format((float) $initialPrice, 0, '.', ' ') : '—' }} ₽</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <span>К оплате</span>
                        <span id="total">{{ $initialPrice !== null ? number_format((float) $initialPrice, 0, '.', ' ') : '—' }} ₽</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Оплата — наличными или переводом при получении.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const fmt = new Intl.NumberFormat('ru-RU');
            const volSel = document.getElementById('volume');
            const qtyInput = document.getElementById('qty');
            const perUnit = document.getElementById('price-per-unit');
            const totalEl = document.getElementById('total');

            const recalc = () => {
                const opt = volSel.options[volSel.selectedIndex];
                const unit = parseFloat(opt.dataset.price) || 0;
                const q = Math.max(1, parseInt(qtyInput.value, 10) || 1);
                perUnit.textContent = fmt.format(unit) + ' ₽';
                totalEl.textContent = fmt.format(unit * q) + ' ₽';
            };
            volSel.addEventListener('change', recalc);
            qtyInput.addEventListener('input', recalc);
            recalc();
        </script>
    @endpush
@endsection
