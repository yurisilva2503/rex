@extends('layouts.main.base')

@section('content')
    <div class="container-fluid min-vh-100 py-4">
        {{-- Mensagens de sucesso e erro --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Cabeçalho e ações rápidas --}}
        <div class="row mb-3 align-items-center">
            <div class="col-md-9">
                <h1 class="h2 mb-1">Usuários</h1>
                <p class="text-muted mb-0">Gerencie os usuários e suas permissões</p>

            </div>
            @can('create', App\Models\User::class)
            <div class="col-md-3 text-end">
                <button class="btn btn-primary mt-3 mt-md-0" data-bs-toggle="modal" data-bs-target="#userModal"
                onclick="clearModal()">
                    <i class="bi bi-plus-circle me-1"></i> Novo Usuário
                </button>
            </div>
            @endcan
        </div>

        {{-- Indicadores rápidos
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 p-2 px-3 rounded-3">
                                <i class="bi bi-people fs-4 text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total</h6>
                                <h4 class="mb-0">{{ $users->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-success bg-opacity-10 p-2 px-3 rounded-3">
                                <i class="bi bi-check-circle fs-4 text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Ativos</h6>
                                <h4 class="mb-0">{{ $users->where('active', true)->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-warning bg-opacity-10 p-2 px-3 rounded-3">
                                <i class="bi bi-exclamation-triangle fs-4 text-warning"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Inativos</h6>
                                <h4 class="mb-0">{{ $users->where('active', false)->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-info bg-opacity-10 p-2 px-3 rounded-3">
                                <i class="bi bi-shield-check fs-4 text-info"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Administradores</h6>
                                <h4 class="mb-0">{{ $users->where('is_admin', true)->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        --}}

        {{-- Tabela de usuários --}}
        {{-- Preloader --}}
        <div id="tablePreloader" class="text-center my-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-2 text-muted">Carregando dados da tabela...</p>
        </div>
        <div class="table-responsive" style="min-height: 500px;">
            <table id="usersTable" class="table table-striped table-hover table-bordered w-100 h-100">
                <thead>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>

        {{-- Usuários Excluídos --}}
        @if($deletedUsers->count() > 0)
        <hr>
        <div class="mt-2">
            <h3 class="h4 mb-3">
                <i class="bi bi-recycle me-2"></i>Usuários Excluídos
                <small class="text-muted">({{ $deletedUsers->count() }})</small>
            </h3>

            <div class="row g-3">
                @foreach($deletedUsers as $user)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                                        <i class="bi bi-person-x fs-4 text-warning"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title mb-1">{{ $user->name }}</h6>
                                    <p class="card-text small text-muted mb-2">
                                        <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                                    </p>
                                    <p class="card-text small text-muted mb-2">
                                        <i class="bi bi-briefcase me-1"></i>{{ $user->role ?? 'Não informado' }}
                                    </p>
                                    @if($user->department)
                                    <p class="card-text small text-muted mb-2">
                                        <i class="bi bi-building me-1"></i>{{ $user->department->name }}
                                    </p>
                                    @endif
                                    <p class="card-text small text-muted mb-3">
                                        <i class="bi bi-calendar-x me-1"></i>Excluído em {{ $user->deleted_at->format('d/m/Y H:i') }}
                                    </p>

                                    <div class="d-flex gap-2">
                                        @can('update', $user)
                                        <button class="btn btn-sm btn-outline-success" onclick="restoreUser({{ $user->id }}, '{{ $user->name }}')">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar
                                        </button>
                                        @endcan
                                        <button class="btn btn-sm btn-outline-danger" onclick="forceDeleteUser({{ $user->id }}, '{{ $user->name }}')">
                                            <i class="bi bi-trash me-1"></i>Excluir Permanentemente
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Modal de Usuário --}}
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="userForm" method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Novo Usuário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 d-none">
                            <label for="password" class="form-label fw-semibold">Senha <span class="text-danger" id="passwordRequired">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password">
                            <small class="text-muted" id="passwordHelp">Mínimo 8 caracteres</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 d-none">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar Senha</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label fw-semibold">Departamento</label>
                            <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                                <option value="">Selecione um departamento</option>
                                @foreach($departments ?? [] as $department)
                                    <option name="department_id" value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">Função</label>
                            <input type="text" class="form-control @error('role') is-invalid @enderror" id="role" name="role" value="{{ old('role') }}">
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="row">
                            @if (Auth::user()->is_admin)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" {{ old('is_admin') ? 'checked' : '' }} class="form-check-input @error('is_admin') is-invalid @enderror">
                                        <label class="form-check-label fw-semibold" for="is_admin">Administrador</label>
                                        @error('is_admin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('active') is-invalid @enderror" type="checkbox" id="active" name="active" {{ old('active') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="active">Ativo</label>
                                        @error('active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="userSubmitBtn" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de Permissões --}}
    <div class="modal fade" id="permissionsModal" tabindex="-1" aria-labelledby="permissionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="permissionsModalLabel">
                        <i class="bi bi-shield-lock me-1"></i> Gerenciar Permissões
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="permission_user_id">
                    <span>Selecione as permissões que deseja atribuir ao usuário <strong id="permission_user_name"></strong>.</span>
                    <div class="mb-3 mt-1">
                        <input type="text" id="permissionSearch" class="form-control" placeholder="Buscar permissões...">
                    </div>

                    <div class="mb-1">
                        <span>Total de permissões: <span id="totalPermissions">{{ $total_permissions }} | Permissões selecionadas: <span id="selectedPermissions">0</span></span></span>
                    </div>

                    <div class="mb-1 text-end">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" id="selectAllPermissions">
                                <i class="bi bi-check-circle me-1"></i> Selecionar todas
                            </button>
                            <button class="btn btn-sm btn-outline-warning" id="deselectAllPermissions">
                                <i class="bi bi-x-circle me-1"></i> Limpar todas
                            </button>
                        </div>
                    </div>

                    <div id="permissionsList" class="list-group" style="max-height: 400px; overflow-y: auto;">
                        <!-- Permissões serão carregadas aqui via JavaScript -->
                    </div>
                    <span class="text-muted">OBS: As permissões de exclusão são limitadas apenas a administradores. Além disso, a criação de departamento também está limitada apenas a administradores.</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="savePermissions()">
                        <i class="bi bi-save me-1"></i> Salvar Permissões
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let usersTable = null;
        $(document).ready(function () {
            let departments = @json($departments ?? []);

            usersTable = $('#usersTable').DataTable({
                ordering: true,
                order: [[0, 'asc']],
                dom: 'Bftip',
                ajax: {
                    url: '/usuarios/lista',
                    complete: function() {
                        $('#tablePreloader').fadeOut(400, function() {
                            $('#usersTable').closest('.table-responsive').fadeIn(600);
                        });
                    }
                },
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i> Copiar',
                        className: 'btn-primary',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                var colunasPermitidas = [0, 1, 2, 3, 4, 6, 7, 8, 9];

                                return colunasPermitidas.includes(idx) &&
                                    $(node).is(':visible');
                            }
                        },
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Imprimir',
                        className: 'btn-dark',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                var colunasPermitidas = [0, 1, 2, 3, 4, 6, 7, 8, 9];

                                return colunasPermitidas.includes(idx) &&
                                    $(node).is(':visible');
                            }
                        },
                        customize: function (win) {
                            $(win.document.body)
                                .css('background-color', 'white')
                                .css('color', 'black');
                            $(win.document.body).find('table')
                                .css('background-color', 'white');
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> Excel',
                        className: 'btn-success',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                var colunasPermitidas = [0, 1, 2, 3, 4, 6, 7, 8, 9];

                                return colunasPermitidas.includes(idx) &&
                                    $(node).is(':visible');
                            }
                        },
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn-danger',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                var colunasPermitidas = [0, 1, 2, 3, 4, 6, 7, 8, 9];

                                return colunasPermitidas.includes(idx) &&
                                    $(node).is(':visible');
                            }
                        },
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="bi bi-list"></i> Filtrar Colunas',
                        className: 'border',
                    }
                ],
                layout: {
                    topStart: 'buttons'
                },
                responsive: true,
                columns: [
                    {
                        data: 'name',
                        title: 'Nome',
                        render: function(data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: 'email',
                        title: 'E-mail',
                        render: function(data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: 'department',
                        title: 'Departamento',
                        render: function(data, type, row) {
                            if (data) {
                                let iconHtml = '';
                                    if (data.icon) {
                                        iconHtml = `<i class="${data.icon} me-1"></i>`;
                                    }
                                    return iconHtml + data.name;
                            } else {
                                return '<span class="text-muted">Sem departamento</span>';
                            }
                        }
                    },
                    {
                        data: 'role',
                        title: 'Função',
                        render: function (data, type, row) {
                            return data ? `<span class="badge bg-primary"><i class="bi bi-shield-lock me-1"></i>${data.charAt(0).toUpperCase() + data.slice(1)}</span>` : `<span class="badge bg-success"><i class="bi bi-shield-check me-1"></i>Sem função</span>`;
                        }
                    },
                    {
                        data: 'is_admin',
                        title: 'Nível',
                        render: function (data, type, row) {
                            return data ?
                                '<span class="badge bg-dark"><i class="bi bi-shield-lock me-1"></i>Administrador</span>' :
                                '<span class="badge bg-warning"><i class="bi bi-shield-check me-1"></i>Usuário</span>';
                        }
                    },
                    {
                        data: 'active',
                        title: 'Status',
                        render: function (data, type, row) {
                            if (data && row.email_verified_at) {
                                return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Ativo</span>';
                            }

                            if (data && !row.email_verified_at) {
                                return '<span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>Verificação de e-mail pendente</span>';
                            }

                            return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Inativo</span>';
                        }
                    },
                    {
                        data: 'created_by',
                        title: 'Criado por',
                        render: function (data, type, row) {
                            return data ? data.name : 'Sistema';
                        }
                    },
                    {
                        data: 'created_at',
                        title: 'Criado em',
                        render: function (data, type, row) {
                            return moment(data).format('DD/MM/YYYY - HH:mm') + 'h';
                        }
                    },
                    {
                        data: 'updated_by',
                        title: 'Atualizado por',
                        render: function (data, type, row) {
                            return data ? data.name : 'Sistema';
                        }
                    },
                    {
                        data: 'updated_at',
                        title: 'Atualizado em',
                        render: function (data, type, row) {
                            return moment(data).format('DD/MM/YYYY - HH:mm') + 'h';
                        }
                    },
                    {
                    data: null,
                    title: 'Ações',
                    render: function (data, type, row) {
                        const isCurrentUser = row.id === {{ auth()->id() }};
                        const permissions = row.permissions;

                        let dropdown = `
                        <div class="dropdown">
                            <button class="bg-transparent border-0 dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">`;

                        const hasPermission = permissions.edit || permissions.delete;

                        if (permissions.edit) {

                            dropdown += `
                            <li>
                                <a style="cursor: pointer" class="dropdown-item text-muted"
                                onclick="editUser(${row.id})">
                                    <i class="bi bi-pencil text-warning"></i> Editar
                                </a>
                            </li>`;

                            if (isCurrentUser) {

                                dropdown += `
                                <li>
                                    <a style="cursor: pointer" class="dropdown-item disabled"
                                    title="Não é possível alterar suas próprias permissões">
                                        <i class="bi bi-shield-lock text-info"></i> Permissões
                                    </a>
                                </li>`;

                            } else {

                                dropdown += `
                                <li>
                                    <a style="cursor: pointer" class="dropdown-item text-muted"
                                    onclick="openPermissionsModal(${row.id}, '${row.name}')">
                                        <i class="bi bi-shield-lock text-primary"></i> Permissões
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>`;
                            }

                            const statusIcon = row.active ? 'bi-x-circle' : 'bi-check-circle';
                            const statusText = row.active ? 'Desativar' : 'Ativar';

                            dropdown += `
                            <li>
                                <a style="cursor: pointer" class="dropdown-item text-muted"
                                onclick="toggleStatus(${row.id}, this, ${row.active})">
                                    <i class="bi ${statusIcon}"></i> ${statusText}
                                </a>
                            </li>`;
                        }

                        if (permissions.delete) {

                            if (isCurrentUser) {

                                dropdown += `
                                <li>
                                    <a style="cursor: pointer" class="dropdown-item disabled"
                                    title="Não é possível excluir seu próprio usuário">
                                        <i class="bi bi-lock text-danger"></i> Excluir
                                    </a>
                                </li>`;

                            } else {

                                dropdown += `
                                <li>
                                    <a style="cursor: pointer" class="dropdown-item text-muted"
                                    onclick="deleteUser(${row.id})">
                                        <i class="bi bi-trash text-danger"></i> Excluir
                                    </a>
                                </li>`;
                            }
                        }

                        if (!hasPermission) {
                            dropdown += `
                            <li>
                                <span class="dropdown-item text-muted">
                                    <i class="bi bi-lock"></i> Sem permissões
                                </span>
                            </li>`;
                        }

                        dropdown += `</ul></div>`;

                        return dropdown;
                    },

                }
                ],
                initComplete: function(settings, json) {
                    $('#usersTable_wrapper .dt-buttons').removeClass('btn-group');
                },
                language: {
                    url: '{{ asset('/assets/json/pt-BR.json') }}'
                }
            });
            $('#usersTable').closest('.table-responsive').hide();
        });

        // Limpa o modal para novo cadastro
        function clearModal() {
            document.getElementById('userForm').reset();
            document.getElementById('user_id').value = '';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('userModalLabel').innerHTML = '<i class="bi bi-plus-circle me-1"></i>Novo Usuário';
            document.getElementById('userForm').action = "{{ route('users.store') }}";
            document.getElementById('active').checked = true;
            @if (auth()->user()->is_admin)
                document.getElementById('is_admin').checked = false;
            @endif
            document.getElementById('password').closest('div.mb-3').classList.contains('d-none') ? '' : document.getElementById('password').closest('div.mb-3').classList.add('d-none');
            document.getElementById('password_confirmation').closest('div.mb-3').classList.contains('d-none') ? '' : document.getElementById('password_confirmation').closest('div.mb-3').classList.add('d-none');
        }

        // Intercepta o submit do formulário
        document.getElementById('userForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Desativar button do formulário
            const btnSubmit = document.getElementById('userSubmitBtn')

            btnSubmit.disabled = true;
            btnSubmit.innerText = 'Aguarde...';

            const form = this;
            const method = document.getElementById('formMethod').value;
            const userId = document.getElementById('user_id').value;

            const url = method === 'POST'
                ? "{{ route('users.store') }}"
                : `/usuarios/${userId}`;

            // Coleta dados do formulário
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
                department_id: document.getElementById('department_id').value,
                role: document.getElementById('role').value,
                @if (auth()->user()->is_admin)
                    is_admin: document.getElementById('is_admin').checked ? 'on' : 'off',
                @endif
                active: document.getElementById('active').checked ? 'on' : 'off',
            };

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    Swal.fire({
                        theme: `${savedThemeAuth}`,
                        icon: 'success',
                        title: 'Sucesso!',
                        text: method === 'POST' ? 'Usuário criado com sucesso. Um email de confirmação foi enviado para o e-mail cadastrado.' : 'Usuário atualizado com sucesso.',
                        confirmButtonColor: '#0D6EFD',
                    }).then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
                        usersTable.ajax.reload(null, false);
                    });
                } else {
                    const errors = data.errors || {};
                    let errorMsg = Object.values(errors)[0]?.[0] || data.message || 'Erro ao salvar usuário';
                    Swal.fire({
                        theme: `${savedThemeAuth}`,
                        icon: 'error',
                        title: 'Erro!',
                        text: errorMsg,
                        confirmButtonColor: '#0D6EFD',
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    theme: `${savedThemeAuth}`,
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao salvar usuário',
                    confirmButtonColor: '#0D6EFD',
                });
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerText = 'Salvar';
            }
        });

        // Abre o modal para edição
        function editUser(id) {
            fetch(`/usuarios/${id}/editar`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('user_id').value = data.id;
                    document.getElementById('name').value = data.name;
                    document.getElementById('email').value = data.email;
                    document.getElementById('department_id').value = data.department_id || '';
                    @if (auth()->user()->is_admin)
                        document.getElementById('is_admin').checked = data.is_admin == 1 || data.is_admin === true;
                    @endif
                    document.getElementById('active').checked = data.active == 1 || data.active === true;
                    document.getElementById('role').value = data.role || '';

                    // Senha não é obrigatória na edição
                    document.getElementById('password').closest('div.mb-3').classList.contains('d-none') ? document.getElementById('password').closest('div.mb-3').classList.remove('d-none') : document.getElementById('password').closest('div.mb-3').classList.add('d-none');
                    document.getElementById('password_confirmation').closest('div.mb-3').classList.contains('d-none') ? document.getElementById('password_confirmation').closest('div.mb-3').classList.remove('d-none') : document.getElementById('password_confirmation').closest('div.mb-3').classList.add('d-none');

                    document.getElementById('password').required = false;
                    document.getElementById('passwordRequired').style.display = 'none';
                    document.getElementById('passwordHelp').innerText = 'Deixe em branco para manter a atual';

                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('userModalLabel').innerHTML = '<i class="bi bi-pencil-square"></i> Editar Usuário';
                    document.getElementById('userForm').action = `/usuarios/${id}`;

                    new bootstrap.Modal(document.getElementById('userModal')).show();
                })
                .catch(error => console.error('Erro ao carregar usuário:', error));
        }

        // Abre o modal de confirmação de exclusão
        function deleteUser(id) {
            Swal.fire({
                theme: `${savedThemeAuth}`,
                title: 'Confirmar exclusão',
                text: 'Tem certeza que deseja excluir este usuário? Esta ação não poderá ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir',
                confirmButtonColor: '#0D6EFD',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/usuarios/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire({
                                theme: `${savedThemeAuth}`,
                                icon: 'success',
                                title: 'Sucesso!',
                                text: 'Usuário excluído com sucesso.',
                                confirmButtonColor: '#0D6EFD',
                            }).then(() => {
                                usersTable.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                theme: `${savedThemeAuth}`,
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Erro ao excluir usuário.',
                                confirmButtonColor: '#0D6EFD',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao excluir usuário:', error);
                        Swal.fire({
                            theme: `${savedThemeAuth}`,
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro ao excluir usuário.',
                            confirmButtonColor: '#0D6EFD',
                        });
                    });
                }
            });
        }

        // Ativa/desativa via requisição AJAX
        async function toggleStatus(id, button, status) {
            let isActive = button.innerText.toLowerCase().trim() == 'ativar';
            Swal.fire({
                theme: `${savedThemeAuth}`,
                title: 'Confirmar alteração',
                html: `Tem certeza que deseja alterar para <strong>${status ? 'Inativo' : 'Ativo'}</strong>?`,
                icon: isActive ? 'success' : 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, ' + (isActive ? 'ativar' : 'desativar'),
                confirmButtonColor: '#0D6EFD',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/usuarios/${id}/mudarStatus`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                theme: `${savedThemeAuth}`,
                                icon: 'success',
                                title: 'Sucesso!',
                                text: 'Status alterado com sucesso.',
                                confirmButtonColor: '#0D6EFD',
                            })
                            usersTable.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                theme: `${savedThemeAuth}`,
                                confirmButtonColor: '#0D6EFD',
                                title: 'Erro ao alterar status',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK',
                            })
                        }
                    })
                    .catch(error => console.error('Erro ao alterar status:', error));
                }
            });
        }
// Variável global para armazenar todas as permissões
let allPermissionsArray = [];
let currentUserPermissions = [];
const btnSelectAllPermissions = document.getElementById('selectAllPermissions');
const btnDeselectAllPermissions = document.getElementById('deselectAllPermissions');

btnSelectAllPermissions.addEventListener('click', () => {
    selectAllPermissions();
})

btnDeselectAllPermissions.addEventListener('click', () => {
    deselectAllPermissions();
})

// Abre o modal de permissões
function openPermissionsModal(userId, userName) {
    document.getElementById('permission_user_id').value = userId;
    document.getElementById('permission_user_name').innerText = userName;

    // Mostra loading
    Swal.fire({
        theme: savedThemeAuth || 'dark',
        title: 'Carregando...',
        text: 'Buscando permissões do usuário',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Carrega as permissões do usuário
    fetch(`/usuarios/${userId}/permissoes`)
        .then(response => response.json())
        .then(data => {
            Swal.close();

            // Verifica a estrutura dos dados recebidos
            if (data.all_permissions) {
                allPermissionsArray = data.all_permissions;
            } else {
                allPermissionsArray = [];
            }

            // filtrar para nao exibir permissões de exclusão e de criação de departamentos. Isso vai ficar apenas para o admin
            if (!data.user.is_admin) {
                allPermissionsArray = allPermissionsArray.filter(perm => !perm.name.includes('delete') && !perm.name.includes('create_departments'));
            }

            currentUserPermissions = data.user_permissions || [];

            // Renderiza as permissões
            renderListPermissions();
            updateSelectedCount();

            // Abre o modal
            const modal = new bootstrap.Modal(document.getElementById('permissionsModal'));
            modal.show();
        })
        .catch(error => {
            Swal.close();
            Swal.fire({
                theme: savedThemeAuth || 'dark',
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao carregar permissões: ' + error.message,
                confirmButtonColor: '#0D6EFD',
            });
            console.error('Erro ao carregar permissões:', error);
        });
}

// Agrupa permissões por módulo
function groupPermissionsByModule(permissions) {
    const grouped = {};

    permissions.forEach(perm => {
        // Extrai o módulo do nome da permissão (ex: create_analyses -> analyses)
        const parts = perm.name.split('_');
        const module = parts.slice(1).join('_') || parts[0]; // Pega tudo depois do primeiro underscore

        if (!grouped[module]) {
            grouped[module] = [];
        }
        grouped[module].push(perm);
    });

    return grouped;
}



// Renderiza permissões em lista
function renderListPermissions() {
    const container = document.getElementById('permissionsList');
    if (!container) return;

    const searchInput = document.getElementById('permissionSearch');

    searchInput.onkeyup = function() {
        filterAndRender();
    }
    function filterAndRender() {
        const filter = searchInput ? searchInput.value.toLowerCase() : '';

        const filtered = allPermissionsArray.filter(p =>
            p.description.toLowerCase().includes(filter) ||
            p.name.toLowerCase().includes(filter)
        );

        container.innerHTML = filtered.map(permission => {
            let action = permission.name.split('_')[0];
            const actionColor = getActionColor(action);

            switch(action) {
                case 'create':
                    action = 'Criação';
                    break;
                case 'view':
                    action = 'Visualização';
                    break;
                case 'edit':
                    action = 'Edição';
                    break;
                case 'delete':
                    action = 'Exclusão';
                    break;
                case 'manage':
                    action = 'Gerenciamento';
                    break;
                case 'export':
                    action = 'Exportação';
                    break;
                case 'import':
                    action = 'Importação';
                    break;
                case 'reset':
                    action = 'Restauração';
                    break;
                default:
                    action = 'Desconhecido';
            }

            return `
                <div class="list-group-item list-group-item-action py-2">
                    <div class="form-check">
                        <input class="form-check-input permission-checkbox-list"
                               type="checkbox"
                               value="${permission.id}"
                               id="list_perm_${permission.id}"
                               ${currentUserPermissions.includes(permission.id) ? 'checked' : ''}>
                        <label class="form-check-label d-flex flex-column" for="list_perm_${permission.id}">
                            <span>
                                <span class="badge bg-${actionColor} me-2">${action}</span>
                                <strong>${permission.description}</strong>
                            </span>
                        </label>
                    </div>
                </div>
            `;
        }).join('') || '<div class="alert alert-warning m-3">Nenhuma permissão encontrada</div>';

        // Sincroniza com os checkboxes agrupados
        document.querySelectorAll('.permission-checkbox-list').forEach(cb => {
            cb.addEventListener('change', function() {
                const permId = parseInt(this.value);
                const groupedCb = document.getElementById(`perm_${permId}`);
                if (groupedCb) {
                    groupedCb.checked = this.checked;
                }

                // Atualiza o array de permissões
                if (this.checked) {
                    if (!currentUserPermissions.includes(permId)) {
                        currentUserPermissions.push(permId);
                    }
                } else {
                    const index = currentUserPermissions.indexOf(permId);
                    if (index > -1) {
                        currentUserPermissions.splice(index, 1);
                    }
                }

                updateSelectedCount();
                if (groupedCb) {
                    updateModuleCheckbox(groupedCb.dataset.module);
                }
            });
        });
    }

    filterAndRender();

    if (searchInput) {
        searchInput.onkeyup = function() {
            filterAndRender();
        };
    }
}

// Anexa eventos aos checkboxes
function attachCheckboxEvents() {
    // Eventos para checkboxes de módulo
    document.querySelectorAll('.module-checkbox').forEach(cb => {
        cb.removeEventListener('change', handleModuleChange);
        cb.addEventListener('change', handleModuleChange);
    });

    // Eventos para checkboxes individuais
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.removeEventListener('change', handlePermissionChange);
        cb.addEventListener('change', handlePermissionChange);
    });

    updateSelectedCount();
}

// Handler para mudança no checkbox do módulo
function handleModuleChange(e) {
    const module = e.target.dataset.module;
    const checked = e.target.checked;

    document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(p => {
        p.checked = checked;

        // Atualiza o array de permissões
        const permId = parseInt(p.value);
        if (checked) {
            if (!currentUserPermissions.includes(permId)) {
                currentUserPermissions.push(permId);
            }
        } else {
            const index = currentUserPermissions.indexOf(permId);
            if (index > -1) {
                currentUserPermissions.splice(index, 1);
            }
        }

        // Sincroniza com a lista
        const listCb = document.getElementById(`list_perm_${permId}`);
        if (listCb) {
            listCb.checked = checked;
        }
    });

    e.target.indeterminate = false;
    updateSelectedCount();
}

// Handler para mudança no checkbox individual
function handlePermissionChange(e) {
    const permId = parseInt(e.target.value);
    const module = e.target.dataset.module;

    // Atualiza o array de permissões
    if (e.target.checked) {
        if (!currentUserPermissions.includes(permId)) {
            currentUserPermissions.push(permId);
        }
    } else {
        const index = currentUserPermissions.indexOf(permId);
        if (index > -1) {
            currentUserPermissions.splice(index, 1);
        }
    }

    // Sincroniza com a lista
    const listCb = document.getElementById(`list_perm_${permId}`);
    if (listCb) {
        listCb.checked = e.target.checked;
    }

    updateModuleCheckbox(module);
    updateSelectedCount();
}

// Atualiza o checkbox do módulo baseado nos checkboxes individuais
function updateModuleCheckbox(module) {
    const moduleCheckbox = document.getElementById(`module_${module}`);
    if (!moduleCheckbox) return;

    const modulePerms = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
    const allChecked = Array.from(modulePerms).every(cb => cb.checked);
    const someChecked = Array.from(modulePerms).some(cb => cb.checked);

    moduleCheckbox.checked = allChecked;
    moduleCheckbox.indeterminate = !allChecked && someChecked;
}

// Atualiza o contador de permissões selecionadas
function updateSelectedCount() {
    const selected = document.querySelectorAll('.permission-checkbox-list:checked').length;
    const countElement = document.getElementById('selectedPermissions');
    if (countElement) {
        countElement.innerText = selected;
    }
}

// Seleciona todas as permissões
function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox, .permission-checkbox-list').forEach(cb => {
        cb.checked = true;

        // Atualiza o array de permissões
        const permId = parseInt(cb.value);
        if (!currentUserPermissions.includes(permId)) {
            currentUserPermissions.push(permId);
        }
    });

    document.querySelectorAll('.module-checkbox').forEach(cb => {
        cb.checked = true;
        cb.indeterminate = false;
    });

    updateSelectedCount();
}

// Desmarca todas as permissões
function deselectAllPermissions() {
    document.querySelectorAll('.permission-checkbox, .permission-checkbox-list').forEach(cb => {
        cb.checked = false;
    });

    document.querySelectorAll('.module-checkbox').forEach(cb => {
        cb.checked = false;
        cb.indeterminate = false;
    });

    currentUserPermissions = [];
    updateSelectedCount();
}

// Helper para cor da ação
function getActionColor(action) {
    const colors = {
        'view': 'info',
        'create': 'success',
        'edit': 'warning',
        'delete': 'danger',
        'manage': 'primary',
        'export': 'secondary',
        'import': 'secondary',
        'reset': 'danger'
    };
    return colors[action] || 'secondary';
}

// Salva as permissões selecionadas
function savePermissions() {
    const userId = document.getElementById('permission_user_id').value;

    // Pega permissões únicas de ambas as listas
    const selectedPermissions = Array.from(new Set([
        ...Array.from(document.querySelectorAll('.permission-checkbox:checked')).map(cb => parseInt(cb.value)),
        ...Array.from(document.querySelectorAll('.permission-checkbox-list:checked')).map(cb => parseInt(cb.value))
    ]));

    Swal.fire({
        theme: savedThemeAuth || 'dark',
        title: 'Confirmar alteração',
        text: `Deseja salvar ${selectedPermissions.length} permissões para este usuário?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, salvar',
        confirmButtonColor: '#0D6EFD',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/usuarios/${userId}/permissoes`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ permissions: selectedPermissions })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        theme: savedThemeAuth || 'dark',
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message,
                        confirmButtonColor: '#0D6EFD',
                    }).then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('permissionsModal')).hide();
                        // Recarrega a página para mostrar as novas permissões
                        usersTable.ajax.reload(null, false);
                    });
                } else {
                    Swal.fire({
                        theme: savedThemeAuth || 'dark',
                        icon: 'error',
                        title: 'Erro!',
                        text: data.message,
                        confirmButtonColor: '#0D6EFD',
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    theme: savedThemeAuth || 'dark',
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao salvar permissões: ' + error.message,
                    confirmButtonColor: '#0D6EFD',
                });
                console.error('Erro ao salvar permissões:', error);
            });
        }
    });
}

// Restaura um usuário excluído
async function restoreUser(id, name) {
    const result = await Swal.fire({
        theme: savedThemeAuth || 'dark',
        title: 'Restaurar usuário',
        text: `Deseja restaurar o usuário "${name}"? Ele voltará a ter acesso ao sistema.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, restaurar',
        confirmButtonColor: '#198754',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/usuarios/${id}/restaurar`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    theme: savedThemeAuth || 'dark',
                    icon: 'success',
                    title: 'Sucesso!',
                    text: data.message,
                    confirmButtonColor: '#0D6EFD',
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'Erro ao restaurar usuário');
            }
        } catch (error) {
            Swal.fire({
                theme: savedThemeAuth || 'dark',
                icon: 'error',
                title: 'Erro!',
                text: error.message,
                confirmButtonColor: '#0D6EFD',
            });
        }
    }
}

// Exclui permanentemente um usuário
async function forceDeleteUser(id, name) {
    const result = await Swal.fire({
        theme: savedThemeAuth || 'dark',
        title: 'Excluir permanentemente',
        text: `ATENÇÃO: Esta ação não pode ser desfeita. O usuário "${name}" será removido definitivamente do sistema.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir permanentemente',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Cancelar',
        input: 'text',
        inputPlaceholder: 'Digite "EXCLUIR" para confirmar',
        inputValidator: (value) => {
            if (!value || value.toUpperCase() !== 'EXCLUIR') {
                return 'Digite "EXCLUIR" para confirmar a exclusão permanente';
            }
        }
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/usuarios/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ force: true })
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    theme: savedThemeAuth || 'dark',
                    icon: 'success',
                    title: 'Sucesso!',
                    text: data.message || 'Usuário excluído permanentemente.',
                    confirmButtonColor: '#0D6EFD',
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'Erro ao excluir usuário permanentemente');
            }
        } catch (error) {
            Swal.fire({
                theme: savedThemeAuth || 'dark',
                icon: 'error',
                title: 'Erro!',
                text: error.message,
                confirmButtonColor: '#0D6EFD',
            });
        }
    }
}
    </script>

    {{-- Erros --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let userId = '{{ old('user_id') }}';
                if (userId) {
                    editUser(userId);
                } else {
                    clearModal();
                    new bootstrap.Modal(document.getElementById('userModal')).show();
                }
            });
        </script>
    @endif
@endsection
