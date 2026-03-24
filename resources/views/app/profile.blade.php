@extends('layouts.main.base')

@section('content')
    <div class="container-fluid py-4">
        {{-- Cabeçalho e ações rápidas --}}
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div>
                <h1 class="h2 mb-1">Perfil</h1>
                <p class="text-muted mb-0">Gerencie suas informações na plataforma</p>
            </div>
        </div>
        <hr>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <form action="{{ route('profileForm') }}" method="post">
                    @csrf

                    <!-- Erro de validação -->
                    @if (session('invalid_update_profile'))
                        <div class="alert alert-danger text-center mt-3">
                            <i class="bi bi-exclamation-circle-fill me-1"></i> {{ session('invalid_update_profile') }}
                        </div>
                    @endif

                    @if (session('success_update_profile'))
                        <div class="alert alert-primary text-center mt-3">
                            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success_update_profile') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="form-label fw-bold">
                            Nome <span class="text-danger">*</span>
                        </div>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            value="{{ auth()->user()->name }}" id="name" name="name">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="form-label fw-bold">
                            Departamento <span class="text-danger">*</span>
                        </div>
                        <input type="text" class="form-control @error('department') is-invalid @enderror"
                            value="{{ auth()->user()->department->name ?? '-' }}" id="department" name="department">
                        @error('department')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="form-label fw-bold">
                            Permissões
                        </div>
                        @php
                            // Organiza permissões por módulo
                            $modules = [
                                'users' => 'Usuários',
                                'departments' => 'Departamentos',
                                'indicators' => 'Indicadores',
                                'analyses' => 'Análises',
                                'action_plans' => 'Planos de Ação'
                            ];

                            $actions = [
                                'view' => ['label' => 'Visualizar', 'color' => 'info', 'icon' => 'eye'],
                                'create' => ['label' => 'Criar', 'color' => 'success', 'icon' => 'plus-circle'],
                                'edit' => ['label' => 'Editar', 'color' => 'warning', 'icon' => 'pencil'],
                                'delete' => ['label' => 'Excluir', 'color' => 'danger', 'icon' => 'trash']
                            ];

                            $userPermissions = auth()->user()->permissions->pluck('name')->toArray();
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="border-0 fw-bold">Módulo</th>
                                        @foreach($actions as $actionKey => $actionData)
                                            <th class="border-0 text-center fw-bold">
                                                <i class="bi bi-{{ $actionData['icon'] }} me-1"></i>
                                                {{ $actionData['label'] }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $moduleKey => $moduleName)
                                        <tr>
                                            <td class="fw-semibold">{{ $moduleName }}</td>
                                            @foreach($actions as $actionKey => $actionData)
                                                @php
                                                    $permissionName = $actionKey . '_' . $moduleKey;
                                                    $hasPermission = in_array($permissionName, $userPermissions);
                                                @endphp
                                                <td class="text-center">
                                                    @if($hasPermission)
                                                        <span class="badge bg-{{ $actionData['color'] }}">
                                                            <i class="bi bi-check-circle-fill"></i>
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(auth()->user()->permissions->isEmpty())
                            <div class="text-center text-muted fst-italic mt-2">
                                Nenhuma permissão atribuída
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <div class="form-label fw-bold">
                            Email <span class="text-danger">*</span>
                        </div>
                        <input type="text" class="form-control readonly" value="{{ auth()->user()->email }}" disabled
                            readonly>
                    </div>
                    <div class="mb-3">
                        <div class="form-label fw-bold">
                            Atualizado em <span class="text-danger">*</span>
                        </div>
                        <input type="text" class="form-control readonly"
                            value="{{ auth()->user()->updated_at->format('d/m/Y \à\s H:i\h') }}" disabled readonly>

                    </div>

                    <div class="mb-3">
                        <div class="form-label fw-bold">
                            Função <span class="text-danger">*</span>
                        </div>
                        <input type="text" class="form-control readonly" value="{{ ucfirst(auth()->user()->role) }}"
                            disabled readonly>

                    </div>
                    <div class="mb-1">
                        <div class="form-label fw-bold">
                            Senha <span class="text-danger">*</span>
                        </div>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                            id="current_password" name="current_password" required minlength="3" maxlength="255">

                        @error('current_password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="toggleResetPassword()">
                            <i class="bi bi-pencil me-1"></i>Mudar Senha
                        </button>
                    </div>
                    <div id="reset_password" class="d-none">
                        <div class="mb-3 new_password">
                            <label for="new_password" class="form-label @error('new_password') is-invalid @enderror">Defina
                                a nova senha</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            @error('new_password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3 new_password">
                            <label for="new_password_confirmation" class="form-label">Confirmar a nova senha</label>
                            <input type="password"
                                class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                id="new_password_confirmation" name="new_password_confirmation">
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Confirmar
                        Alterações</button>
                </form>
            </div>
            <div class="col-md-6">
                <x-card title="<i class='bi bi-trash me-1'></i>Excluir Conta">
                        <form action="{{ route('deleteProfileForm') }}" method="post">
                            @csrf

                            <!-- Erro de validação -->
                            @if (session('invalid_delete_profile'))
                                <div class="alert alert-danger text-center mt-3">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ session('invalid_delete_profile') }}
                                </div>
                            @endif

                            <p>Se você quiser excluir sua conta, marque a caixa abaixo, e clique no botão "Confirmar Exclusão".
                            </p>
                            <div class="form-control mb-3">
                                <input type="checkbox" required name="delete" id="delete" class="form-check-input">
                                <label for="delete" class="form-check-label">Estou certo de que desejo excluir minha
                                    conta</label>
                            </div>
                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Confirmar
                                Exclusão</button>
                        </form>
                    </x-card>
            </div>
        </div>
    </div>
    <script>
        function toggleResetPassword() {
            const resetPasswordSection = document.getElementById('reset_password');
            const new_password = document.getElementById('new_password');
            const new_password_confirmation = document.getElementById('new_password_confirmation');

            resetPasswordSection.classList.toggle('d-none');
            if (resetPasswordSection.classList.contains('d-none')) {
                new_password.value = '';
                new_password.required = false;
                new_password_confirmation.value = '';
                new_password_confirmation.required = false;
            } else {
                new_password.required = true;
                new_password_confirmation.required = true;
            }
        }
    </script>
@endsection
