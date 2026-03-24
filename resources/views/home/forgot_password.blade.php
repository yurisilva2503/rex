@extends('layouts.main.base')
@section('content')
    <div class="container-fluid my-3 vh-100">
        <div class="row align-items-center justify-content-center">
            <div class="col-6 d-none justify-content-center align-items-center d-md-flex vh-100">
                <img src="{{ asset('/assets/img/forgot.png') }}" alt="Imagem de Recuperar Senha"
                    class="img-fluid opacity-75">
            </div>
            <div class="col-md-6 border bg-body-contrast rounded-start-pill vh-100 d-flex align-items-center">
                <div class="container" style="max-width: 700px;">
                    <x-card>
                        <form action="{{ route('forgotPasswordForm') }}" method="post">
                            @csrf
                            <h1 class="text-center mb-3 fs-4 fw-bold">Recuperar Senha</h1>
                            <hr>
                            <h2 class="lead text-muted" style="font-size: 14px;">Preencha as informações abaixo para recuperar a senha</h2>
                            <!-- Erro de validação -->
                            @if (session('msg_forgot_password'))
                                <div class="alert alert-primary text-center mt-3">
                                    <i class="bi bi-check-circle-fill me-1"></i> {!! session('msg_forgot_password') !!}
                                </div>
                            @endif

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                    id="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Botões -->
                            <div class="row">
                                <div class="col-12">
                                    <i class="bi bi-key me-1"></i><small>Lembrou sua senha? <a href="{{ route('login') }}"
                                            class="fw-light text-primary">Clique aqui para
                                            entrar no sistema.</small></a>
                                </div>
                                <div class="col-12 mt-3 text-center">
                                    <button type="submit" id="btn-confirm" class="btn btn-primary w-50">Confirmar</button>
                                </div>
                            </div>
                        </form>
                    </x-card>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.querySelector('form');

        form.addEventListener('submit', function (event) {
            const btn = document.querySelector('#btn-confirm');
            btn.disabled = true;
            btn.innerHTML = 'Confirmando...';
        });
    </script>
@endsection
