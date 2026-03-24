    @extends('layouts.main.base')
    @section('content')
    <div class="container-fluid py-4">
        {{-- Cabeçalho e ações rápidas --}}
        <div class="row mb-3 align-items-center">
            <div class="col-md-9">
                <h1 class="h2 mb-1">Dashboard</h1>
                <p class="text-muted mb-0">Visualize informações sobre os departamentos</p>
            </div>
        </div>


        <!-- Cards de resumo -->
        <div class="row g-4 mb-4">
            <div class="{{ auth()->user()->is_admin ? 'col-md-3' : 'col-md-4' }}">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="mb-2 text-muted">Total de Indicadores de Desempenho</small>
                                <p class="m-0 fs-3 fw-bold">{{ $totalIndicators }}</p>
                                <small class="text-muted">Cadastrados no mês de {{ date('M/Y') }}: {{ $indicators->where('created_at', '>=', date('Y-m-01'))->count() }}</small>
                            </div>
                            <i class="bi bi-diagram-3 fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="{{ auth()->user()->is_admin ? 'col-md-3' : 'col-md-4' }}">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="mb-2 text-muted">Indicadores Ativos</small>
                                <p class="m-0 fs-3 fw-bold">{{ $activeIndicators}}</p>
                                <small class="text-muted">Cadastrados no mês de {{ date('M/Y') }}: {{ $indicators->where('created_at', '>=', date('Y-m-01'))->count() }}</small>
                            </div>
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>

            @if (auth()->user()->is_admin)
            <div class="{{ auth()->user()->is_admin ? 'col-md-3' : 'col-md-4' }}">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="mb-2 text-muted">Qtd. de Departamentos</small>
                                <p class="m-0 fs-3 fw-bold">{{ $departments->count() }}</p>
                                <small class="text-muted">Cadastrados no mês de {{ date('M/Y') }}: {{ $departments->where('created_at', '>=', date('Y-m-01'))->count() }}</small>
                            </div>
                            <i class="bi bi-building fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="{{ auth()->user()->is_admin ? 'col-md-3' : 'col-md-4' }}">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="mb-2 text-muted">Últimas Atualizações ({{ date('M/Y')  }})</small>
                                <p class="m-0 fs-3 fw-bold">{{ $latestValues->count() }}</p>
                                <small class="text-muted">Última feita {{ $latestValues->last() ? $latestValues->last()->created_at->format('d/m/Y') : '---' }}</small>

                            </div>
                            <i class="bi bi-clock fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card border">
                    <p class="m-0 p-3 px-4 fw-bold"><i class="bi bi-bookmarks me-2"></i>
                    @if (auth()->user()->is_admin)
                        Indicadores por Departamento
                    @else
                        Indicadores do seu Departamento
                    @endif
                    </p>
                    <div class="card-body">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border">
                    <p class="m-0 p-3 px-4 fw-bold"><i class="bi bi-tags me-2"></i>Indicadores por Tipo</p>
                    <div class="card-body">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    <!-- Tabela de Indicadores -->
    <div class="card border">
        <div class="card-header border-bottom bg-body">
            <div class="d-flex gap-2 align-items-center">
                <p class="fw-semibold mb-0 m-0" >
                    <i class="bi bi-table me-1" ></i>
                    Lista de Indicadores
                </p>
                @can('create', App\Models\Indicator::class)
                <div class="vr"></div>
                <button class="btn btn-sm btn-outline-primary" onclick="openIndicatorModal()"><i class="bi bi-plus-circle me-1"></i>Novo Indicador</button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            {{-- Preloader --}}
            <div id="tablePreloader" class="text-center my-5">
                <div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2 text-muted">Carregando dados da tabela...</p>
            </div>

            <div class="table-responsive" style="min-height: 500px;">
                <table id="indicatorsTable" class="table table-hover table-striped table-bordered w-100">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Indicador -->
<div class="modal fade" id="indicatorModal" tabindex="-1" aria-labelledby="indicatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="indicatorForm" method="POST" action="{{ route('indicators.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="indicator_id" name="indicator_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="indicatorModalLabel">Novo Indicador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departamento <span class="text-danger">*</span></label>
                            <select name="department_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="strategic">Estratégico</option>
                                <option value="tactical">Tático</option>
                                <option value="monitoring">Monitoramento</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Nome do Indicador <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required maxlength="255">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Meta <span class="text-danger">*</span></label>
                            <input type="number" name="goal" class="form-control" step="0.0001" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unidade <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select" required>
                                <option value="%">Percentual (%)</option>
                                <option value="un">Unidade (un)</option>
                                <option value="R$">Real (R$)</option>
                                <option value="kg">Quilograma (kg)</option>
                                <option value="h">Horas (h)</option>
                                <option value="dias">Dias</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Direção <span class="text-danger">*</span></label>
                            <select name="direction" class="form-select" required>
                                <option value="higher_is_better">Maior é melhor</option>
                                <option value="lower_is_better">Menor é melhor</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição / Como calcular</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Fórmula de Cálculo</label>
                            <textarea name="formula" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="active" class="form-check-input" value="1" id="activeSwitch" checked>
                                <label class="form-check-label fw-semibold" for="activeSwitch">Ativo</label>
                            </div>
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

{{-- Modal de confirmação para exclusão --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-trash me-1"></i>Confirmar exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0">Tem certeza que deseja excluir o indicador <strong id="deleteIndicatorName"></strong>?</p>
                <p class="text-muted small mt-2">Esta ação não poderá ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Valor -->
<div class="modal fade" id="valueModal" tabindex="-1" aria-labelledby="valueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="valueForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="valueFormMethod" value="POST">
                <input type="hidden" id="value_indicator_id" name="indicator_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="valueModalLabel">Adicionar Valor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Indicador</label>
                        <p class="form-control-plaintext" id="indicator_name_display"></p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ano <span class="text-danger">*</span></label>
                            <select name="year" class="form-select" required>
                                @for($y = date('Y'); $y >= date('Y')-5; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mês <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Valor <span class="text-danger">*</span></label>
                            <input type="number" name="value" class="form-control" step="0.0001" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Salvar Valor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Cores modernas para os gráficos
    const chartColors = {
        primary: ['#3b82f6', '#ef4444', '#eab308', '#22c55e', '#8b5cf6', '#ec4899'],
        secondary: ['#60a5fa', '#f87171', '#d89c00', '#4ade80', '#a78bfa', '#f472b6']
    };

    let indicatorsTable;
    $(document).ready(function() {
        let indicatorsData = @json($indicators);
        let departments = @json($departments);

        // Oculta a tabela inicialmente
        $('#indicatorsTable').closest('.table-responsive').hide();

        // Inicializar DataTable
        indicatorsTable = $('#indicatorsTable').DataTable({
            ajax: {
                url: '/indicadores/lista',
                complete: function() {
                    $('#tablePreloader').fadeOut(400, function() {
                        $('#indicatorsTable').closest('.table-responsive').fadeIn(600);
                    });
                }
            },
            ordering: true,
            order: [[3, 'desc']],
            dom: 'Bftip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="bi bi-clipboard"></i> Copiar',
                    className: 'btn-primary',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7];

                            return colunasPermitidas.includes(idx) &&
                                $(node).is(':visible');
                        }
                    },
                },
                {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Imprimir',
                        className: 'btn-dark',
                        title: '{{ env('APP_NAME') }} - Lista de Indicadores',
                        exportOptions: {
                            columns: function (idx, data, node) {
                                var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7];

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
                    title: '{{ env('APP_NAME') }} - Lista de Indicadores',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7];

                            return colunasPermitidas.includes(idx) &&
                                $(node).is(':visible');
                        }
                    },
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn-danger',
                    title: '{{ env('APP_NAME') }} - Lista de Indicadores',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7];

                            return colunasPermitidas.includes(idx) &&
                                $(node).is(':visible');
                        }
                    },
                },
                {
                    extend: 'colvis',
                    text: '<i class="bi bi-list"></i> Filtrar Colunas',
                    className: 'border'
                }
            ],
            layout: {
                topStart: 'buttons'
            },
            columns: [
                {
                    data: 'department',
                    title: 'Departamento',
                    render: function(data, type, row) {
                        if (data) {
                            return '<i class="bi ' + data.icon + ' me-1"></i>' + data.name;
                        }
                        return '<span class="badge bg-info">Não definido</span>';
                    }
                },
                { data: 'name', title: 'Título' },
                { data: 'active', title: 'Status', render: function(data, type, row) {
                    return data ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Ativo</span>' : '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Inativo</span>';
                    }
                },
                {
                    data: 'type',
                    title: 'Tipo',
                    render: function(data, dataTableType, row) {  // Renomeado de 'type' para 'dataTableType'
                        const types = {
                            'strategic': { class: 'bg-indicator-strategic', label: 'Estratégico' },
                            'tactical': { class: 'bg-indicator-tactical', label: 'Tático' },
                            'monitoring': { class: 'bg-indicator-monitoring', label: 'Monitoramento' }
                        };
                        const typeInfo = types[data] || { class: 'bg-secondary', label: data };
                        return `<span class="badge ${typeInfo.class}">${typeInfo.label}</span>`;
                    }
                },
                {
                    data: 'goal',
                    title: 'Meta',
                    render: function(data, type, row) {
                        return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data) + ' ' + row.unit;
                    }
                },
                {
                    data: null,
                    title: 'Último Valor',
                    render: function(data) {
                        if (data.values && data.values.length > 0) {
                            const lastValue = data.values[0];
                            let statusClass = 'bg-secondary';
                            let icon = '';

                            if (lastValue.status === 'on_target') {
                                statusClass = 'text-primary';
                                icon = '<i class="bi bi-check-circle me-1 ' + statusClass + '"></i>';
                            } else if (lastValue.status === 'near_target') {
                                statusClass = 'text-warning';
                                icon = '<i class="bi bi-exclamation-triangle me-1 ' + statusClass + '"></i>';
                            } else if (lastValue.status === 'below_target') {
                                statusClass = 'text-danger';
                                icon = '<i class="bi bi-x-circle me-1 ' + statusClass + '"></i>';
                            }

                            return `<span>${icon} ${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(lastValue.value)}${data.unit}</span>`;
                        }
                        return '<span class="badge bg-info"><i class="bi bi-dash-circle me-1"></i>Sem dados</span>';
                    }
                },
                {
                    data: 'created_at',
                    title: 'Data de Criação',
                    render: function(data) {
                        if (data) {
                            return moment(data).format('DD/MM/YYYY - HH:mm') + 'h';
                        }
                        return '-';
                    }
                },
                {
                    data: 'updated_at',
                    title: 'Data de Atualização',
                    render: function(data) {
                        if (data) {
                            return moment(data).format('DD/MM/YYYY - HH:mm') + 'h';
                        }
                        return '-';
                    }
                },
                {
                    data: null,
                    title: 'Ações',
                    render: function(data, type, row) {
                        const permissions = row.permissions;
                        const hasPermission = permissions.edit || permissions.delete || permissions.view;

                        let dropdown = `
                        <div class="dropdown">
                            <button class="bg-transparent border-0 dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">`;

                        if (permissions.view) {
                            // Visualizar
                            dropdown += `
                                <li>
                                    <a class="dropdown-item text-muted" style="cursor: pointer" onclick="viewIndicator(${data.id})">
                                        <i class="bi bi-eye text-primary"></i> Visualizar
                                    </a>
                                </li>`;
                        }

                        if (permissions.edit) {
                            dropdown += `
                            <li>
                                <a style="cursor: pointer" class="dropdown-item text-muted"
                                onclick="editIndicator(${row.id})">
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
                                onclick="deleteIndicator(${row.id})">
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
            initComplete: function(settings, json) {
                $('#tablePreloader').fadeOut(400, function() {
                    $('#indicatorsTable').closest('.table-responsive').fadeIn(600);
                });
                $('#indicatorsTable_wrapper .dt-buttons').removeClass('btn-group');
            },
            language: {
                url: '{{ asset('/assets/json/pt-BR.json') }}'
            }
        });

        // Gráfico de Departamentos
        const deptCanvas = document.getElementById('departmentChart');
        deptCanvas.height = 300; // ou o valor que desejar
        const deptCtx = deptCanvas.getContext('2d');

        new Chart(deptCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($indicatorsByDepartment->pluck('department.name')) !!},
                datasets: [{
                    data: {!! json_encode($indicatorsByDepartment->pluck('total')) !!},
                    backgroundColor: chartColors.primary,
                    borderWidth: 0
                }]
            },
            plugins: [ChartDataLabels], // 👈 importante
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#4b5563',
                            font: { size: 12 }
                        }
                    },
                    datalabels: {
                        color: '#e7e7e7',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: function(value) {
                            return value;
                        }
                    }
                }
            }
        });

        // Gráfico de Tipos
        const typeCanvas = document.getElementById('typeChart');
        typeCanvas.height = 300;
        const typeCtx = typeCanvas.getContext('2d');

        new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($indicatorsByType->pluck('type')->map(function($type) {
                    return $type == 'strategic' ? 'Estratégico' : ($type == 'tactical' ? 'Tático' : 'Monitoramento');
                })) !!},
                datasets: [{
                    label: 'Quantidade de Indicadores',
                    data: {!! json_encode($indicatorsByType->pluck('total')) !!},
                    backgroundColor: chartColors.secondary,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Importante para controlar altura
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1,
                        grid: { color: '#e5e7eb' },
                        ticks: { color: '#6b7280' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' }
                    }
                }
            }
        });
    });

    // Funções do Modal de Indicador
    function clearModal() {
        document.getElementById('indicatorForm').reset();
        document.getElementById('indicator_id').value = '';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('indicatorModalLabel').innerHTML = '<i class="bi bi-plus-circle me-1"></i>Novo Indicador';
        document.getElementById('activeSwitch').checked = true;
        document.getElementById('indicatorForm').action = "{{ route('indicators.store') }}";
    }

    function openIndicatorModal() {
        clearModal();
        new bootstrap.Modal(document.getElementById('indicatorModal')).show();
    }

    // Intercepta o submit do formulário
    document.getElementById('indicatorForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        const indicatorId = document.getElementById('indicator_id').value;
        const isEdit = indicatorId !== '' && indicatorId !== undefined;
        const url = isEdit ? `/indicadores/${indicatorId}` : '/indicadores';
        const method = isEdit ? 'PUT' : 'POST';

        const body = {
            department_id: parseInt(form.querySelector('[name="department_id"]').value),
            type: form.querySelector('[name="type"]').value,
            name: form.querySelector('[name="name"]').value,
            goal: parseFloat(form.querySelector('[name="goal"]').value),
            unit: form.querySelector('[name="unit"]').value,
            direction: form.querySelector('[name="direction"]').value,
            description: form.querySelector('[name="description"]').value || null,
            formula: form.querySelector('[name="formula"]').value || null,
            active: document.getElementById('activeSwitch').checked ? 1 : 0,
        };

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body)
            });

            const data = await response.json();

            if (!response.ok) {
                const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.error || data.message || 'Erro ao salvar');
                throw new Error(errors);
            }

            bootstrap.Modal.getInstance(document.getElementById('indicatorModal')).hide();

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: isEdit ? 'Indicador atualizado com sucesso.' : 'Indicador criado com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => indicatorsTable.ajax.reload(null, false));

        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: error.message,
                confirmButtonColor: '#0D6EFD',
            });
        }
    });

    async function editIndicator(id) {
        try {
            const response = await fetch(`/indicadores/${id}/editar`);

            if (!response.ok) throw new Error('Erro ao buscar indicador');

            const data = await response.json();

            document.getElementById('indicator_id').value = data.id;
            document.getElementById('indicatorForm').querySelector('[name="department_id"]').value = data.department_id;
            document.getElementById('indicatorForm').querySelector('[name="type"]').value = data.type;
            document.getElementById('indicatorForm').querySelector('[name="name"]').value = data.name;
            document.getElementById('indicatorForm').querySelector('[name="goal"]').value = data.goal;
            document.getElementById('indicatorForm').querySelector('[name="unit"]').value = data.unit;
            document.getElementById('indicatorForm').querySelector('[name="direction"]').value = data.direction;
            document.getElementById('indicatorForm').querySelector('[name="description"]').value = data.description || '';
            document.getElementById('indicatorForm').querySelector('[name="formula"]').value = data.formula || '';
            document.getElementById('activeSwitch').checked = data.active == 1;

            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('indicatorModalLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Indicador';

            new bootstrap.Modal(document.getElementById('indicatorModal')).show();
        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível carregar o indicador.',
                confirmButtonColor: '#0D6EFD',
            });
        }
    }

    async function submitIndicatorForm(event) {
        event.preventDefault();

        const indicatorId = document.getElementById('indicator_id').value;
        const isEdit = indicatorId !== '' && indicatorId !== undefined;
        const url = isEdit ? `/indicadores/${indicatorId}` : '/indicadores';
        const method = isEdit ? 'PUT' : 'POST';

        const form = document.getElementById('indicatorForm');
        const body = {
            department_id: parseInt(form.querySelector('[name="department_id"]').value),
            type: form.querySelector('[name="type"]').value,
            name: form.querySelector('[name="name"]').value,
            goal: parseFloat(form.querySelector('[name="goal"]').value),
            unit: form.querySelector('[name="unit"]').value,
            direction: form.querySelector('[name="direction"]').value,
            description: form.querySelector('[name="description"]').value || null,
            formula: form.querySelector('[name="formula"]').value || null,
            active: document.getElementById('activeSwitch').checked ? 1 : 0,
        };

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body)
            });

            const data = await response.json();

            if (!response.ok) {
                const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.error || data.message || 'Erro ao salvar');
                throw new Error(errors);
            }

            bootstrap.Modal.getInstance(document.getElementById('indicatorModal')).hide();

            Swal.fire({
                icon: 'success',
                title: 'Sucesso',
                text: isEdit ? 'Indicador atualizado com sucesso.' : 'Indicador criado com sucesso.',
                timer: 1500,
                showConfirmButton: false,
            }).then(() => indicatorsTable.ajax.reload(null, false));

        } catch (error) {
            console.error(error);
            Swal.fire({ icon: 'error', title: 'Erro', text: error.message});
        }

        return false;
    }

    function viewIndicator(id) {
        window.location.href = `/indicadores/${id}`;
    }

    // Funções do Modal de Valor
    function openValueModal(indicatorId, indicatorName) {
        document.getElementById('valueForm').reset();
        document.getElementById('value_indicator_id').value = indicatorId;
        document.getElementById('indicator_name_display').innerText = indicatorName;
        document.getElementById('valueFormMethod').value = 'POST';
        document.getElementById('valueForm').action = `/indicadores/${indicatorId}/valores`;

        const now = new Date();
        document.getElementById('valueForm').querySelector('[name="year"]').value = now.getFullYear();
        document.getElementById('valueForm').querySelector('[name="month"]').value = now.getMonth() + 1;

        new bootstrap.Modal(document.getElementById('valueModal')).show();
    }

    // Função de toggle status
    async function toggleStatus(id, element) {
        const result = await Swal.fire({
            theme: `${savedThemeAuth}`,
            title: 'Confirmar alteração',
            text: 'Tem certeza que deseja alterar o status deste indicador?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, alterar',
            confirmButtonColor: '#0D6EFD',
            cancelButtonText: 'Cancelar',
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`/indicadores/${id}/mudarStatus`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) throw new Error('Erro ao alterar status');

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: 'Status alterado com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => indicatorsTable.ajax.reload(null, false));

        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível alterar o status.',
                confirmButtonColor: '#0D6EFD',
            });
        }
    }

    // Função de exclusão com SweetAlert
    async function deleteIndicator(id) {
        const result = await Swal.fire({
            theme: `${savedThemeAuth}`,
            title: 'Confirmar exclusão',
            text: 'Tem certeza que deseja excluir este indicador?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            confirmButtonColor: '#0D6EFD',
            cancelButtonText: 'Cancelar',
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`/indicadores/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) throw new Error('Erro ao excluir indicador');

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: 'Indicador excluído com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => indicatorsTable.ajax.reload(null, false));
        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível excluir o indicador.',
                confirmButtonColor: '#0D6EFD',
            });
        }
    }

    function exportToExcel() {
        window.location.href = '/indicadores/export';
    }

    // Tratamento de erros de validação
    @if ($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        let indicatorId = '{{ old('indicator_id') }}';
        if (indicatorId) {
            editIndicator(indicatorId);
        } else {
            clearModal();
            new bootstrap.Modal(document.getElementById('indicatorModal')).show();
        }
    });
    @endif
</script>
@endsection
