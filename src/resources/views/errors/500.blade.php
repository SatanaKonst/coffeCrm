@extends('errors.generic')

@section('title', '500 · Ошибка сервера')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-7 col-lg-5 text-center">
            <div class="display-1 fw-bold text-danger">500</div>
            <h1 class="h3 mt-2">Ошибка сервера</h1>
            <p class="text-muted mt-3">Что-то сломалось на нашей стороне. Уже разбираемся.</p>
            <div class="d-flex justify-content-center gap-2 mt-4">
                <a href="{{ route('crm.dashboard') }}" class="btn btn-primary">На главную</a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">Назад</a>
            </div>
        </div>
    </div>
@endsection
