@extends('errors.generic')

@section('title', '419 · Сессия истекла')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-7 col-lg-5 text-center">
            <div class="display-1 fw-bold text-warning">419</div>
            <h1 class="h3 mt-2">Сессия истекла</h1>
            <p class="text-muted mt-3">Долго не были активны — обновите страницу и попробуйте снова.</p>
            <div class="d-flex justify-content-center gap-2 mt-4">
                <a href="{{ route('crm.dashboard') }}" class="btn btn-primary">На главную</a>
            </div>
        </div>
    </div>
@endsection
