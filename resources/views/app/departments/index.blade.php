@extends('layouts.main.base')

@section('content')
    <div class="container-fluid py-4">
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
                <h1 class="h2 mb-1">Departamentos</h1>
                <p class="text-muted mb-0">Gerencie os departamentos e suas informações</p>
            </div>
            @can('create', App\Models\Department::class)
                <div class="col-md-3 text-end">
                    <button class="btn btn-primary mt-3 mt-md-0" data-bs-toggle="modal" data-bs-target="#departmentModal"
                        onclick="clearModal()">
                        <i class="bi bi-plus-circle me-1"></i> Novo Departamento
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
                                <i class="bi bi-building fs-4 text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total</h6>
                                <h4 class="mb-0">{{ $departments->count() }}</h4>
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
                                <h4 class="mb-0">{{ $departments->where('active', true)->count() }}</h4>
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
                                <h4 class="mb-0">{{ $departments->where('active', false)->count() }}</h4>
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
                                <i class="bi bi-diagram-3 fs-4 text-info"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Indicadores</h6>
                                <h4 class="mb-0">{{ $totalIndicators ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        --}}

        {{-- Tabela de departamentos --}}
        {{-- Preloader --}}
        <div id="tablePreloader" class="text-center my-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-2 text-muted">Carregando dados da tabela...</p>
        </div>

        <div class="table-responsive" style="min-height: 500px;">
            <table id="departmentsTable" class="table table-striped table-hover table-bordered w-100">
                <thead>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Modais --}}
    <div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="departmentForm" method="POST" action="{{ route('departments.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" id="department_id" name="department_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="departmentModalLabel">Novo Departamento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                name="description" rows="3" maxlength="255">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ícone</label>

                            <!-- Preview -->
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div id="iconPreview" style="font-size: 28px;">
                                    @if(isset($link) && $link->icon)
                                        <i class="{{ $link->icon }}"></i>
                                    @else
                                        <i class="bi bi-question-circle"></i>
                                    @endif
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#iconModal">
                                    Selecionar Ícone
                                </button>
                            </div>

                            <!-- Campo real que salva no banco -->
                            <input type="hidden" name="icon" id="iconInput" value="{{ $link->icon ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                                <label class="form-check-label" for="active">Ativo</label>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal do selecionador de ícones -->
    <div class="modal fade" id="iconModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Selecione um Ícone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="iconSearch" class="form-control mb-3" placeholder="Buscar ícone...">
                    <div class="row" id="iconGrid" style="max-height: 400px; overflow-y: auto;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const iconGrid = document.getElementById("iconGrid");
            const iconSearch = document.getElementById("iconSearch");
            const iconInput = document.getElementById("iconInput");
            const iconPreview = document.getElementById("iconPreview");

            function renderIcons(filter = "") {
                iconGrid.innerHTML = "";
                bootstrapIcons
                    .filter(icon =>
                        icon.toLowerCase().includes(filter.toLowerCase())
                    )
                    .forEach(icon => {
                        const col = document.createElement("div");
                        col.className = "col-3 col-md-2 mb-3 text-center";
                        col.style.cursor = "pointer";

                        col.innerHTML = `
                        <i class="bi ${icon}" style="font-size:24px;"></i>
                        <div style="font-size:11px;">
                            ${icon.replace('bi-', '')}
                        </div>`;

                        col.addEventListener("click", function () {

                            const fullClass = `bi ${icon}`;

                            iconInput.value = fullClass;

                            iconPreview.innerHTML =
                                `<i class="${fullClass}" style="font-size:28px;"></i>`;

                            const iconModalEl = document.getElementById('iconModal');
                            const departmentModalEl = document.getElementById('departmentModal');

                            const iconModal = bootstrap.Modal.getInstance(iconModalEl);
                            iconModal.hide();

                            iconModalEl.addEventListener('hidden.bs.modal', function handler() {

                                iconModalEl.removeEventListener('hidden.bs.modal', handler);

                                new bootstrap.Modal(departmentModalEl).show();
                            });
                        });

                        iconGrid.appendChild(col);
                    });
            }

            renderIcons();

            iconSearch.addEventListener("keyup", function () {
                renderIcons(this.value);
            });
        });

        let departmentsTable = null;
        $(document).ready(function () {
            departmentsTable = $('#departmentsTable').DataTable({
                ordering: true,
                order: [[0, 'asc']],
                dom: 'Bftip',
                ajax: {
                    url: '/departamentos/lista',
                    complete: function() {
                        $('#tablePreloader').fadeOut(400, function() {
                            $('#departmentsTable').closest('.table-responsive').fadeIn(600);
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
                                var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6];

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
                                var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6];

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
                                var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6];

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
                                var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6];

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
                        render: function (data, type, row) {
                            let iconHtml = '';
                            if (row.icon) {
                                iconHtml = `<i class="bi ${row.icon} me-1"></i>`;
                            }
                            return iconHtml + data;
                        }
                    },
                    { data: 'description', title: 'Descrição' },
                    {
                        data: 'active',
                        title: 'Status',
                        render: function (data, type, row) {
                            return data ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Ativo</span>' : '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Inativo</span>';
                        }
                    },
                    {
                        data: 'created_by', title: 'Criado por', render: function (data, type, row) {
                            return data ? data.name : 'Sistema';
                        }
                    },
                    {
                        data: 'created_at', title: 'Criado em', render: function (data, type, row) {
                            return moment(data).format('DD/MM/YYYY - HH:mm') + 'h';
                        }
                    },
                    {
                        data: 'updated_by', title: 'Atualizado por', render: function (data, type, row) {
                            return data ? data.name : 'Sistema';
                        }
                    },
                    {
                        data: 'updated_at', title: 'Atualizado em', render: function (data, type, row) {
                            return moment(data).format('DD/MM/YYYY - HH:mm') + 'h';
                        }
                    },
                    {
                        data: 'users_count', title: 'Usuários', render: function (data, type, row) {
                            return `<button type="button" class="btn btn-sm btn-primary" onclick="showUsers(${row.id}, '${row.name}', this)"><i class="bi bi-people"></i> ${data <= 9 ? '0' + data : data}</button>`;
                        }
                    },
                    {
                        data: 'indicators_count', title: 'Indicadores', render: function (data, type, row) {
                            return `<button type="button" class="btn btn-sm btn-dark" onclick="showIndicators(${row.id}, '${row.name}', this)"><i class="bi bi-bar-chart"></i> ${data <= 9 ? '0' + data : data}</button>`;
                        }
                    },
                    {
                        data: null,
                        title: 'Ações',
                        render: function (data, type, row) {

                            const permissions = row.permissions;
                            const hasPermission = permissions.edit || permissions.delete;

                            let dropdown = `
                            <div class="dropdown">
                                <button class="bg-transparent border-0 dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">`;

                            if (permissions.edit) {
                                dropdown += `
                                <li>
                                    <a style="cursor: pointer" class="dropdown-item text-muted"
                                    onclick="editDepartment(${row.id})">
                                        <i class="bi bi-pencil text-warning"></i> Editar
                                    </a>
                                </li>`;

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
                                dropdown += `
                                <li>
                                    <a style="cursor: pointer" class="dropdown-item text-muted"
                                    onclick="deleteDepartment(${row.id})">
                                        <i class="bi bi-trash text-danger"></i> Excluir
                                    </a>
                                </li>`;
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
                        }
                    }
                ],
                // Callback quando a tabela está totalmente inicializada
                initComplete: function(settings, json) {
                    $('#departmentsTable_wrapper .dt-buttons').removeClass('btn-group');
                },
                language: {
                    url: '{{ asset('/assets/json/pt-BR.json') }}'
                }
            });
            $('#departmentsTable').closest('.table-responsive').hide();
        });
    </script>
    <script>
    // Limpa o modal para novo cadastro
    function clearModal() {
        document.getElementById('departmentForm').reset();
        document.getElementById('department_id').value = '';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('departmentModalLabel').innerHTML = '<i class="bi bi-plus-circle me-1"></i>Novo Departamento';
        document.getElementById('departmentForm').action = "{{ route('departments.store') }}";
        document.getElementById('active').checked = true; // ativo por padrão
        document.getElementById('iconInput').value = '';
        document.getElementById('iconPreview').innerHTML = '<i class="bi bi-question-circle" style="font-size:28px;"></i>';
    }

    // Intercepta o submit do formulário
    document.getElementById('departmentForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const method = document.getElementById('formMethod').value;
        const departmentId = document.getElementById('department_id').value;

        const url = method == 'POST'
            ? "{{ route('departments.store') }}"
            : `/departamentos/${departmentId}`;

        // Coleta dados do formulário
        const formData = {
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            icon: document.getElementById('iconInput').value,
            active: document.getElementById('active').checked ? 1 : 0,
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

            if (!data.success) {
                Swal.fire({
                    theme: `${savedThemeAuth}`,
                    icon: 'error',
                    title: 'Erro!',
                    text: data.message || 'Erro ao salvar departamento',
                    confirmButtonColor: '#0D6EFD',
                });
                return
            }

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: method === 'POST' ? 'Departamento criado com sucesso.' : 'Departamento atualizado com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => {
                bootstrap.Modal.getInstance(document.getElementById('departmentModal')).hide();
                departmentsTable.ajax.reload(null, false);
            });
        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao salvar departamento',
                confirmButtonColor: '#0D6EFD',
            });
        }
    });

    // Abre o modal para edição – busca dados via AJAX
    async function editDepartment(id) {
        try {
            const response = await fetch(`/departamentos/${id}/editar`);
            const data = await response.json();

            if (!data) {
                Swal.fire({
                    theme: `${savedThemeAuth}`,
                    icon: 'error',
                    title: 'Erro',
                    text: data.message || 'Departamento não encontrado.',
                })
                return
            }

            document.getElementById('department_id').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description || '';
            document.getElementById('iconInput').value = data.icon || '';

            document.getElementById('iconPreview').innerHTML = data.icon
                ? `<i class="${data.icon}" style="font-size:28px;"></i>`
                : '<i class="bi bi-question-circle" style="font-size:28px;"></i>';

            document.getElementById('active').checked =
                data.active == 1 || data.active === true;

            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('departmentModalLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Departamento';
            document.getElementById('departmentForm').action = `/departamentos/${id}`;

            new bootstrap.Modal(document.getElementById('departmentModal')).show();

        } catch (error) {
            console.error(error);

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro',
                text: 'Não foi possível carregar o departamento.',
            });
        }
    }

    // Abre o modal de confirmação de exclusão
    async function deleteDepartment(id) {
        const result = await Swal.fire({
            theme: `${savedThemeAuth}`,
            title: 'Confirmar exclusão',
            text: 'Tem certeza que deseja excluir este departamento?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            confirmButtonColor: '#0D6EFD',
            cancelButtonText: 'Cancelar',
        });

        if (!result.isConfirmed) return;

        try {
        const response = await fetch(`/departamentos/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (!data.success) {
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro',
                text: data.message,
            })
            return
        };


        Swal.fire({
            theme: `${savedThemeAuth}`,
            icon: 'success',
            title: 'Sucesso!',
            text: 'Departamento excluído com sucesso.',
            confirmButtonColor: '#0D6EFD',
        }).then(() => {
           departmentsTable.ajax.reload(null, false);
        })


        } catch (error) {
            console.error(error);

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro',
                text: 'Não foi possível carregar os dados para exclusão.',
            });
        }
    }

    // Ativa/desativa via requisição AJAX (sem recarregar a página)
    async function toggleStatus(id, button, status) {
        const result = await Swal.fire({
            theme: `${savedThemeAuth}`,
            title: 'Confirmar alteração',
            html: `Tem certeza que deseja alterar para <strong>${status ? 'Inativo' : 'Ativo'}</strong>?`,
            icon: status ? 'warning' : 'success',
            showCancelButton: true,
            confirmButtonText: 'Sim, alterar',
            confirmButtonColor: '#0D6EFD',
            cancelButtonText: 'Cancelar',
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`/departamentos/${id}/mudarStatus`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            });
            const data = await response.json();

            if (!data.success) {
                Swal.fire({
                    theme: `${savedThemeAuth}`,
                    icon: 'error',
                    title: 'Erro',
                    text: data.message,
                })
                return
            }

            Swal.fire({
                icon: 'success',
                theme: `${savedThemeAuth}`,
                title: 'Sucesso',
                text: `Departamento ${status ? 'desativado' : 'ativado'} com sucesso.`,
                timer: 1500,
                showConfirmButton: false,
            }).then(() => {
                departmentsTable.ajax.reload(null, false);
            });

        } catch (error) {
            console.error(error);

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro',
                text: 'Não foi possível alterar o status.',
            });
        }
    }

    async function showUsers(id, name, button) {
        const oldText = button.innerHTML;
        button.innerHTML = 'Carregando...';
        button.disabled = true;

        try {
            const response = await fetch(`/usuarios/${id}/listar`);

            if (!response.ok) {
                throw new Error('Erro ao buscar usuários');
            }

            const data = await response.json();

            Swal.fire({
                theme: `${savedThemeAuth}`,
                width: 800,
                title: 'Usuários vinculados - ' + name,
                html: `
                    <table id="usersTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.data.map(user => `
                                <tr>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `,
                confirmButtonText: 'Fechar',
                confirmButtonColor: '#0D6EFD',
            });

            $('#usersTable').DataTable({
                ordering: true,
                order: [[0, 'asc']],
                dom: 'Bftip',
                buttons: [
                    {
                        extend: 'copy',
                        title: 'Usuários vinculados - ' + name,
                        text: '<i class="bi bi-clipboard"></i> Copiar',
                        className: 'btn-primary',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                var colunasPermitidas = [0, 1];

                                return colunasPermitidas.includes(idx) &&
                                    $(node).is(':visible');
                            },
                        },
                    },
                    {
                        extend: 'print',
                        title: 'Usuários vinculados - ' + name,
                        text: '<i class="bi bi-printer"></i> Imprimir',
                        className: 'btn-dark',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                var colunasPermitidas = [0, 1];

                                return colunasPermitidas.includes(idx) &&
                                    $(node).is(':visible');
                            },
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
                        title: 'Usuários vinculados - ' + name,
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> Excel',
                        className: 'btn-success',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                var colunasPermitidas = [0, 1];

                                return colunasPermitidas.includes(idx) &&
                                    $(node).is(':visible');
                            },
                        },
                    },
                    {
                        extend: 'pdf',
                        title: 'Usuários vinculados - ' + name,
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn-danger',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                var colunasPermitidas = [0, 1];

                                return colunasPermitidas.includes(idx) &&
                                    $(node).is(':visible');
                            },
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
                        render: function (data, type, row) {
                            let iconHtml = '';
                            if (row.icon) {
                                iconHtml = `<i class="${row.icon} me-1"></i>`;
                            }
                            return iconHtml + data;
                        }
                    },
                    { data: 'email', title: 'Email' }
                ],

                language: {
                    url: '{{ asset('/assets/json/pt-BR.json') }}'
                }
            });

           $('#usersTable').on('draw.dt', function () {
                $('#usersTable_wrapper .dt-info').addClass('mb-3 mt-2');
                $('#usersTable_wrapper .dt-buttons').addClass('mb-3 mt-2');
            });

        } catch (error) {
            console.error(error);

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro',
                text: 'Não foi possível carregar os usuários.',
            });

        } finally {
            button.innerHTML = oldText;
            button.disabled = false;
        }
    }

    async function showIndicators(id, name, button) {
        const oldText = button.innerHTML;
        button.innerHTML = 'Carregando...';
        button.disabled = true;

        try {
            const response = await fetch(`/indicadores/${id}/listar`);

            if (!response.ok) {
                throw new Error('Erro ao buscar indicadores');
            }

            const data = await response.json();

            Swal.fire({
                theme: `${savedThemeAuth}`,
                width: 1000,
                title: 'Indicadores - ' + name,
                html: `
                    <table id="indicatorsTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Meta</th>
                                <th>Último valor</th>
                                <th>Média (3 meses)</th>
                                <th>Status</th>
                                <th>Qtd</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.map(indicator => `
                                <tr>
                                    <td>
                                        <span>${indicator.name}</span>
                                        ${!indicator.active ? '<span class="badge bg-secondary ms-1">Inativo</span>' : ''}
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: ${indicator.type_color}; color: ${indicator.type_text_color};">
                                            ${indicator.type_label}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        ${Number(indicator.goal).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${indicator.unit}
                                    </td>
                                    <td class="text-end">
                                        ${indicator.last_value?.formatted || '-'}
                                    </td>
                                    <td class="text-end">
                                        ${indicator.avg_last_3_months}
                                    </td>
                                    <td>
                                        ${indicator.last_value?.status_label ? `
                                            <span class="badge" style="background-color: ${indicator.last_value.status_color}; color: ${indicator.type_text_color};">
                                                ${indicator.last_value.status_label}
                                            </span>
                                        ` : '-'}
                                    </td>
                                    <td class="text-center">
                                        <span>
                                            <i class="bi bi-calendar3 me-1"></i>${indicator.values_count}
                                        </span>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `,
                confirmButtonText: 'Fechar',
                confirmButtonColor: '#0D6EFD',
                didOpen: () => {
                    // Inicializar DataTable
                    $('#indicatorsTable').DataTable({
                        ordering: true,
                        order: [[0, 'asc']],
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'copy',
                                title: 'Indicadores - ' + name,
                                text: '<i class="bi bi-clipboard"></i> Copiar',
                                className: 'btn-primary btn-sm',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6]
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Indicadores - ' + name,
                                text: '<i class="bi bi-printer"></i> Imprimir',
                                className: 'btn-dark btn-sm',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6]
                                },
                                customize: function(win) {
                                    $(win.document.body)
                                        .css('background-color', 'white')
                                        .css('color', 'black');

                                    $(win.document.body).find('table')
                                        .css('background-color', 'white')
                                        .css('border', '1px solid #dee2e6');
                                }
                            },
                            {
                                extend: 'excel',
                                title: 'Indicadores - ' + name,
                                text: '<i class="bi bi-file-earmark-spreadsheet"></i> Excel',
                                className: 'btn-success btn-sm',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6]
                                }
                            },
                            {
                                extend: 'pdf',
                                title: 'Indicadores - ' + name,
                                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                                className: 'btn-danger btn-sm',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 6]
                                }
                            },
                            {
                                extend: 'colvis',
                                text: '<i class="bi bi-list"></i> Filtrar colunas',
                                className: 'border',
                            }
                        ],
                        layout: {
                            topStart: 'buttons'
                        },
                        responsive: true,
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                        language: {
                            url: '/assets/json/pt-BR.json'
                        },
                        columnDefs: [
                            { className: 'text-center', targets: [5, 6] },
                            { className: 'text-end', targets: [2, 3, 4] }
                        ]
                    });

                    // Ajustar classes
                    $('#indicatorsTable_wrapper .dt-info').addClass('mb-3 mt-2');
                    $('#indicatorsTable_wrapper .dt-buttons').addClass('mb-3 mt-2');
                    $('#indicatorsTable_wrapper .dt-buttons .btn').removeClass('btn-secondary').addClass('btn-outline-primary me-1');
                }
            });

        } catch (error) {
            console.error(error);

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro',
                text: 'Não foi possível carregar os indicadores.',
                confirmButtonColor: '#0D6EFD'
            });

        } finally {
            button.innerHTML = oldText;
            button.disabled = false;
        }
    }
    </script>

    {{-- Erros --}}
    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Se for edição, você pode recuperar o ID do departamento via old input
            let deptId = '{{ old('department_id') }}';
            if (deptId) {
                editDepartment(deptId);
            } else {
                new bootstrap.Modal(document.getElementById('departmentModal')).show();
            }
        });
    </script>
@endif
@endsection
