@extends('errors.generic')

@section('title', '404 · Не найдено')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-7 col-lg-5 text-center">
            <div class="display-1 fw-bold text-muted">404</div>
            <h1 class="h3 mt-2">Страница не найдена</h1>
            <p class="text-muted mt-3">Возможно, страница была удалена или вы перешли по неверной ссылке.</p>
            <div class="d-flex justify-content-center gap-2 mt-4">
                <a href="{{ route('crm.dashboard') }}" class="btn btn-primary">На главную</a>
                <a href="{{ route('crm.dashboard') }}" class="btn btn-outline-primary">В каталог</a>
            </div>
        </div>
    </div>
@endsection
