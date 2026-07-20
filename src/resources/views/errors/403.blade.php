@extends('errors.generic')

@section('title', '403 · Доступ запрещён')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-7 col-lg-5 text-center">
            <div class="display-1 fw-bold text-warning">403</div>
            <h1 class="h3 mt-2">Доступ запрещён</h1>
            <p class="text-muted mt-3">
                @if ($exception?->getMessage())
                    {{ $exception->getMessage() }}
                @else
                    У вас нет прав на это действие.
                @endif
            </p>
            <div class="d-flex justify-content-center gap-2 mt-4">
                <a href="{{ route('crm.dashboard') }}" class="btn btn-primary">На главную</a>
            </div>
        </div>
    </div>
@endsection
