@extends('layouts.crm')

@section('title', $status_code ?? 500)

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-7 col-lg-5 text-center">
            <div class="display-1 fw-bold text-muted">{{ $status_code ?? 500 }}</div>
            <h1 class="h3 mt-2">{{ $title ?? 'Что-то пошло не так' }}</h1>
            <p class="text-muted mt-3">{{ $message ?? 'Произошла ошибка. Попробуйте обновить страницу или вернуться позже.' }}</p>

            @if (isset($exception) && $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface && ($status_code ?? 0) === 419)
                <p class="text-muted small">Сессия истекла — обновите страницу и попробуйте снова.</p>
            @endif

            <div class="d-flex justify-content-center gap-2 mt-4">
                <a href="{{ route('crm.dashboard') }}" class="btn btn-primary">На главную</a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">Назад</a>
            </div>
        </div>
    </div>
@endsection
