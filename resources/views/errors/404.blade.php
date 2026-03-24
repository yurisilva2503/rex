@extends('layouts.main.base')
@section('content')
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="row justify-content-center">
            <div class="col-12 d-flex flex-column align-items-center">
                <img src="{{ asset('/assets/img/404.png') }}" alt="Erro 404" class="img-fluid mb-4"
                    style="max-width: 300px;">
                <p>Página não encontrada</p>
                <a class="btn btn-primary" href="{{ route('login') }}">Voltar para página inicial</a>
            </div>
        </div>
    </div>
@endsection
