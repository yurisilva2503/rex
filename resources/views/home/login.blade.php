@extends('layouts.main.base')
@section('content')
    <div class="container-fluid my-3 vh-100">
        <div class="row align-items-center justify-content-center">
            <div class="col-6 d-none justify-content-center align-items-center d-md-flex vh-100">
                <img src="{{ asset('/assets/img/login.png') }}" alt="Imagem de login" class="img-fluid opacity-75">
            </div>
            <div class="col-md-6 bg-body-contrast border rounded-start-pill vh-100 d-flex align-items-center">
                <div class="container" style="max-width: 700px;">
                    <x-card>
                        <form action="{{ route('loginForm') }}" method="post">
                            @csrf

                            <h1 class="text-center mb-3 fs-4 fw-bold">Login</h1>
                            <hr>
                            <h2 class="lead text-muted" style="font-size: 14px;">Preencha as informações abaixo para fazer
                                o login</h2>

                            <!-- Erro de validação -->
                            @if (session('invalid_login'))
                                <div class="alert alert-danger text-center mt-3">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ session('invalid_login') }}
                                </div>
                            @endif

                            <!-- Sucesso mensagem -->
                            @if (session('success_message'))
                                <div class="alert alert-primary text-center mt-3">
                                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success_message') }}
                                </div>
                            @endif

                            <!-- Email -->
                            <div class="mb-3 mt-2">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                    id="email" value="{{ old('email') }}" autofocus required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Senha -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password" id="password" required minlength="3" maxlength="255">

                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Botões -->
                            <div class="row">
                                <div class="col-12">
                                    <i class="bi bi-key me-1"></i><small>Esqueceu
                                        sua senha? <a href="{{ route('forgotPassword') }}"
                                            class="fw-light text-primary">Clique aqui para
                                            recuperar.</small></a>
                                </div>
                                <div class="col-12 mt-3 text-center">
                                    <button type="submit" class="btn btn-primary w-50">Entrar</button>
                                </div>
                            </div>
                        </form>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
@endsection
