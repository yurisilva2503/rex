@extends('layouts.main.base')
@section('content')
    <div class="container-fluid my-3 vh-100">
        <div class="row align-items-center justify-content-center">
            <div class="col-6 d-none justify-content-center align-items-center d-md-flex vh-100">
                <img src="{{ asset('/assets/img/password.png') }}" alt="Imagem de Resetar Senha"
                    class="img-fluid opacity-75">
            </div>
            <div class="col-md-6 border bg-body-contrast rounded-start-pill vh-100 d-flex align-items-center">
                <div class="container" style="max-width: 700px;">
                    <x-card>
                        <form action="{{ route('resetPasswordForm') }}" method="post">
                            @csrf

                            <h1 class="text-center mb-3 fs-4 fw-bold">Redefinir Senha</h1>
                            <hr>
                            <h2 class="lead text-muted" style="font-size: 14px;">Preencha as informações abaixo para redefinir a senha</h2>
                            <!-- Erro de validação -->
                            @if (session('msg_reset_password'))
                                <div class="alert alert-primary text-center mt-3">
                                    <i class="bi bi-check-circle-fill me-1"></i> {!! session('msg_reset_password') !!}
                                </div>
                            @endif

                            <!-- Token -->
                            <input type="hidden" name="token" id="token" value="{{ $token }}" required>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                    id="email" value="{{ $email }}" readonly required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Senhas -->
                            <div class="mb-3">
                                <div class="form-label">
                                    Nova senha
                                </div>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required minlength="3" maxlength="255">

                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3 password_confirm">
                                <label for="password_confirm"
                                    class="form-label @error('password_confirm') is-invalid @enderror">Confirme a nova
                                    senha</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                                    required minlength="3" maxlength="255">
                                @error('password_confirm')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- Botões -->
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{ route('login') }}" class="text-decoration-none text-primary"><strong>Já
                                            lembrou
                                            a senha?
                                            Clique aqui
                                            para logar.</strong></a>
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
