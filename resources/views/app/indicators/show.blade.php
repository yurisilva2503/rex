@extends('layouts.main.base')
@section('content')

@php
    $percentDiff = $indicator->goal > 0 ? (($currentValue - $indicator->goal) / $indicator->goal) * 100 : 0;
    $isAbove     = $percentDiff >= 0;
    $gap         = $indicator->goal - $currentValue;
    $gapPercent  = $indicator->goal > 0 ? ($gap / $indicator->goal) * 100 : 0;
    $trend       = $values->count() >= 2 ? ($values->first()->value - $values->last()->value) : 0;
@endphp

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
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Cabeçalho --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="h2 mb-1">{{ $indicator->name }}</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-building me-1"></i> {{ $indicator->department->name ?? 'Sem departamento' }}
                <span class="mx-2">•</span>
                <i class="bi bi-tag me-1"></i>
                @switch($indicator->type)
                    @case('strategic') Estratégico @break
                    @case('tactical') Tático @break
                    @default Monitoramento
                @endswitch
                <span class="mx-2">•</span>
                <i class="bi bi-{{ $indicator->active ? 'check-circle text-success' : 'x-circle text-danger' }} me-1"></i>
                {{ $indicator->active ? 'Ativo' : 'Inativo' }}
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('home') }}" class="btn btn-outline-danger">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
            <button class="btn btn-warning me-2" onclick="openIndicatorModal()">
                <i class="bi bi-pencil me-1"></i> Editar Indicador
            </button>
        </div>
    </div>

    {{-- Cards de estatísticas --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div id="lastValue">
                            {{-- Ponto de atenção por conta do first e last, vou ver como fica depois que alimentar os dados --}}
                            <small class="text-muted">Último valor</small>
                            <p id="lastValueNumber" class="m-0 fs-3 fw-bold">{{ number_format($currentValue, 2, ',', '.') }}{{ $indicator->unit }} <small id="lastValueDate" class="text-muted fw-light" style="font-size: 0.8rem">({{ $values->first()->month?? '-' }}/{{ $values->first()->year ?? '-' }})</small></p>
                            <small id="lastValuePercent" class="text-muted">
                                <i class="bi bi-arrow-{{ $isAbove ? 'up' : 'down' }} me-1"></i>
                                {{ number_format(abs($percentDiff), 1) }}% {{ $isAbove ? 'acima' : 'abaixo' }} da meta
                            </small>
                        </div>
                        <i class="bi bi-calendar-check fs-1 text-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Média do Período</small>
                            <p id="averageValueNumber" class="m-0 fs-3 fw-bold">{{ number_format($averageValue, 2, ',', '.') }}{{ $indicator->unit }}</p>
                            <small id="averageValueMonths" class="text-muted">{{ $monthsWithData }} meses com dados</small>
                        </div>
                        <i class="bi bi-bar-chart fs-1 text-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Taxa de Atingimento</small>
                            <p id="achievementRateNumber" class="m-0 fs-3 fw-bold">{{ number_format($achievementRate, 1) }}%</p>
                            <small id="achievementRateMonths" class="text-muted">{{ $monthsAchieved }} de {{ $monthsWithData }} meses</small>
                        </div>
                        <i class="bi bi-trophy fs-1 text-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Amplitude</small>
                            <p id="rangeNumber" class="m-0 fs-3 fw-bold">{{ number_format($range, 2, ',', '.') }}{{ $indicator->unit }}</p>
                            <small id="rangeMinMax" class="text-muted">{{ number_format($minValue, 2, ',', '.') }} — {{ number_format($maxValue, 2, ',', '.') }}{{ $indicator->unit }}</small>
                        </div>
                        <i class="bi bi-graph-up fs-1 text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="card border">
                        <div class="d-flex justify-content-between align-items-center p-3 pb-0">
                            <div>
                                <p class="m-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Informações</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="bi bi-calendar me-2"></i>Criado em:</span>
                                    <span class="fw-semibold">{{ $indicator->created_at->format('d/m/Y') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="bi bi-compass me-2"></i>Direção:</span>
                                    <span class="fw-semibold">{{ $indicator->direction == 'higher_is_better' ? 'Maior é melhor' : 'Menor é melhor' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="bi bi-bullseye me-2"></i>Meta:</span>
                                    <span class="fw-semibold">{{ number_format($indicator->goal, 2, ',', '.') }}{{ $indicator->unit }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="bi bi-rulers me-2"></i>Unidade:</span>
                                    <span class="fw-semibold">{{ $indicator->unit }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">

                    @if($indicator->description || $indicator->formula)
                    <div class="card border">
                        <div class="d-flex justify-content-between align-items-center p-3 pb-0">
                            <div>
                                <p class="m-0 fw-bold"><i class="bi bi-table me-2"></i>Descrição e Cálculo</p>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($indicator->description)
                            <p class="mb-3">{{ $indicator->description }}</p>
                            @endif
                            @if($indicator->formula)
                            <div class="border p-3 rounded">
                                <strong>Fórmula:</strong>
                                <code>{{ $indicator->formula }}</code>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
        <div class="col-md-6">
             {{-- Insights --}}
            <div class="card border" style="flex-shrink:0;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2 text-primary"></i>Insights e Análise</h6>
                    <p id="insightsTrend" class="mb-2 text-body">
                        @if($trend == 0)
                            <span>Sem dados para análise.</span>
                        @else
                            @if($trend > 0)
                                <i class="bi bi-graph-up-arrow text-success me-1"></i>
                                <strong>Tendência crescente:</strong> O indicador apresentou crescimento de {{ number_format($trend, 2, ',', '.') }}{{ $indicator->unit }} durante todo o período de análise.
                            @elseif($trend < 0)
                                <i class="bi bi-graph-down-arrow text-danger me-1"></i>
                                <strong>Tendência decrescente:</strong> O indicador apresentou queda de {{ number_format(abs($trend), 2, ',', '.') }}{{ $indicator->unit }} durante todo o período de análise.
                            @else
                                <i class="bi bi-dash-lg text-body me-1"></i>
                                <strong>Tendência estável:</strong> O indicador manteve-se estável durante todo o período de análise.
                            @endif
                        @endif
                    </p>
                    @if($gap > 0)
                    <p id="insightsGap" class="mb-2 text-body">
                        <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                        <strong>Atenção necessária:</strong> O valor atual está {{ number_format(abs($gapPercent), 1) }}% abaixo da meta. Gap de {{ number_format($gap, 2, ',', '.') }}{{ $indicator->unit }} para atingir o objetivo.
                    </p>
                    @endif
                    @if($bestMonth)
                    <p id="insightsBest" class="mb-0 text-body">
                        <i class="bi bi-award text-warning me-1"></i>
                        <strong>Melhor desempenho:</strong> {{ $bestMonth->format('m/Y') }} com {{ number_format($maxValue, 2, ',', '.') }}{{ $indicator->unit }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico principal --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border">
                <div class="d-flex justify-content-between align-items-center pe-3">
                    <p class="m-0 p-3 px-4 fw-bold"><i class="bi bi-graph-up me-2"></i>Evolução do Indicador</p>
                    <div class="btn-group" id="periodBtnGroup">
                        <button class="btn btn-sm btn-outline-primary active" onclick="changeChartPeriod(12, this)">12 meses</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="changeChartPeriod(6, this)">6 meses</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="changeChartPeriod(3, this)">3 meses</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="indicatorChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Histórico + Informações --}}
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card border">
                <div class="d-flex gap-2 align-items-center p-3 pb-0">
                    <div>
                        <p class="m-0 fw-bold"><i class="bi bi-table me-2"></i>Histórico de Valores</p>
                    </div>
                    <div>
                        @can('create', App\Models\Indicator::class)
                            <div class="vr"></div>
                            <button class="btn btn-sm btn-outline-primary" onclick="openValueModal()"><i class="bi bi-plus-circle me-1"></i>Adicionar Valor</button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    {{-- Preloader --}}
                    <div id="valuesTablePreloader" class="text-center my-4">
                        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2 text-muted">Carregando dados da tabela...</p>
                    </div>
                    <div class="card-body table-responsive" style="min-height: 500px;">
                        <table id="valuesTable" class="table table-hover table-striped table-bordered w-100">
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-4">
            <div class="card border">
                <div class="d-flex gap-2 align-items-center p-3 pb-0">
                    <div>
                        <p class="m-0 fw-bold"><i class="bi bi-table me-2"></i>Análises</p>
                    </div>
                    <div>
                        @can('create', App\Models\Indicator::class)
                            <div class="vr"></div>
                            <button class="btn btn-sm btn-outline-primary" onclick="openAnalysisModal()"><i class="bi bi-plus-circle me-1"></i>Adicionar Análise</button>
                        @endcan
                    </div>
                </div>

                <div class="card-body table-responsive" style="min-height: 500px;">
                    <div id="analysesTablePreloader" class="text-center my-4">
                        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2 text-muted">Carregando dados da tabela...</p>
                    </div>
                  <table class="table table-hover table-striped table-bordered w-100" id="analysesTable"></table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Indicador --}}
<div class="modal fade" id="indicatorModal" tabindex="-1" aria-labelledby="indicatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="indicatorForm" method="POST" action="{{ route('indicators.update', $indicator->id) }}">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-header">
                    <h5 class="modal-title" id="indicatorModalLabel"><i class="bi bi-pencil-square me-2"></i>Editar Indicador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ind_name" class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ind_name" name="name" value="{{ $indicator->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ind_department" class="form-label fw-semibold">Departamento <span class="text-danger">*</span></label>
                            <select class="form-select" id="ind_department" name="department_id" required>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $indicator->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ind_type" class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                            <select class="form-select" id="ind_type" name="type" required>
                                <option value="strategic" {{ $indicator->type == 'strategic' ? 'selected' : '' }}>Estratégico</option>
                                <option value="tactical" {{ $indicator->type == 'tactical' ? 'selected' : '' }}>Tático</option>
                                <option value="monitoring" {{ $indicator->type == 'monitoring' ? 'selected' : '' }}>Monitoramento</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ind_goal" class="form-label fw-semibold">Meta <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="ind_goal" name="goal" value="{{ $indicator->goal }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ind_unit" class="form-label fw-semibold">Unidade <span class="text-danger">*</span></label>
                            <select class="form-select" id="ind_unit" name="unit" required>
                                <option value="%" {{ $indicator->unit == '%' ? 'selected' : '' }}>%</option>
                                <option value="un" {{ $indicator->unit == 'un' ? 'selected' : '' }}>un</option>
                                <option value="R$" {{ $indicator->unit == 'R$' ? 'selected' : '' }}>R$</option>
                                <option value="T" {{ $indicator->unit == 'T' ? 'selected' : '' }}>T</option>
                                <option value="Kg" {{ $indicator->unit == 'Kg' ? 'selected' : '' }}>Kg</option>
                                <option value="Ct" {{ $indicator->unit == 'Ct' ? 'selected' : '' }}>Ct</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ind_direction" class="form-label fw-semibold">Direção <span class="text-danger">*</span></label>
                            <select class="form-select" id="ind_direction" name="direction" required>
                                <option value="higher_is_better" {{ $indicator->direction == 'higher_is_better' ? 'selected' : '' }}>Maior é melhor</option>
                                <option value="lower_is_better" {{ $indicator->direction == 'lower_is_better' ? 'selected' : '' }}>Menor é melhor</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ind_active" name="active" value="1" {{ $indicator->active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="ind_active">Ativo</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ind_description" class="form-label fw-semibold">Descrição</label>
                        <textarea class="form-control" id="ind_description" name="description" rows="3" maxlength="1000">{{ $indicator->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ind_formula" class="form-label fw-semibold">Fórmula</label>
                        <input type="text" class="form-control" id="ind_formula" name="formula" value="{{ $indicator->formula }}" maxlength="500">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Criar/Editar Valor --}}
<div class="modal fade" id="valueModal" tabindex="-1" aria-labelledby="valueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="valueForm" onsubmit="return submitValueForm(event)">
                <input type="hidden" id="value_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="valueModalLabel"><i class="bi bi-plus-circle me-1"></i>Adicionar Valor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="val_year" class="form-label fw-semibold">Ano <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="val_year" name="year" value="{{ date('Y') }}" min="2000" max="2100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="val_month" class="form-label fw-semibold">Mês <span class="text-danger">*</span></label>
                            <select class="form-select" id="val_month" name="month" required>
                                <option value="1">Janeiro</option>
                                <option value="2">Fevereiro</option>
                                <option value="3">Março</option>
                                <option value="4">Abril</option>
                                <option value="5">Maio</option>
                                <option value="6">Junho</option>
                                <option value="7">Julho</option>
                                <option value="8">Agosto</option>
                                <option value="9">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="val_value" class="form-label fw-semibold">Valor <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="val_value" name="value" required>
                    </div>
                    <div class="mb-3">
                        <label for="val_notes" class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control" id="val_notes" name="notes" rows="3" maxlength="1000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="valueSubmitBtn"><i class="bi bi-save me-1"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Criar/Editar Análise --}}
<div class="modal fade" id="analysisModal" tabindex="-1" aria-labelledby="analysisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="analysisForm" onsubmit="return submitAnalysisForm(event)">
                <input type="hidden" id="analysis_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="analysisModalLabel"><i class="bi bi bi-plus-circle me-1"></i>Adicionar Análise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ana_year" class="form-label fw-semibold">Ano <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="ana_year" name="year" value="{{ date('Y') }}" min="2000" max="2100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ana_month" class="form-label fw-semibold">Mês <span class="text-danger">*</span></label>
                            <select class="form-select" id="ana_month" name="month" required>
                                <option value="1">Janeiro</option>
                                <option value="2">Fevereiro</option>
                                <option value="3">Março</option>
                                <option value="4">Abril</option>
                                <option value="5">Maio</option>
                                <option value="6">Junho</option>
                                <option value="7">Julho</option>
                                <option value="8">Agosto</option>
                                <option value="9">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ana_trend" class="form-label fw-semibold">Tendência</label>
                            <select class="form-select" id="ana_trend" name="trend">
                                <option value="">Selecione...</option>
                                <option value="up">Crescente</option>
                                <option value="down">Decrescente</option>
                                <option value="stable">Estável</option>
                                <option value="volatile">Volátil</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ana_analysis" class="form-label fw-semibold">Análise</label>
                        <textarea class="form-control" id="ana_analysis" name="analysis" rows="4" maxlength="2000"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ana_insights" class="form-label fw-semibold">Insights</label>
                        <textarea class="form-control" id="ana_insights" name="insights" rows="4" maxlength="2000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="analysisSubmitBtn"><i class="bi bi-save me-1"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Visualizar Planos de Ação --}}
<div class="modal fade" id="actionPlansModal" tabindex="-1" aria-labelledby="actionPlansModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="actionPlansModalLabel"><i class="bi bi-journal-text me-2"></i>Plano de Ação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="actionPlansModalBody" style="min-height:500px">
                <div id="actionPlansEmpty" class="text-center py-5" style="display:none">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Nenhum plano de ação cadastrado para esta análise.</p>
                </div>
                <div id="actionPlansTableWrapper">
                    <table id="actionPlansTable" class="table table-hover table-striped table-bordered w-100"></table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Criar/Editar Plano de Ação --}}
<div class="modal fade" id="actionPlanFormModal" tabindex="-1" aria-labelledby="actionPlanFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="actionPlanForm" onsubmit="return submitActionPlanForm(event)">
                <input type="hidden" id="ap_id" value="">
                <input type="hidden" id="ap_analysis_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionPlanFormModalLabel"><i class="bi bi-plus-circle me-2"></i>Adicionar Plano de Ação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ap_action" class="form-label fw-semibold">Ação <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="ap_action" name="action" rows="3" maxlength="500" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ap_responsible" class="form-label fw-semibold">Responsável</label>
                            <input type="text" class="form-control" id="ap_responsible" name="responsible" maxlength="255">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ap_deadline" class="form-label fw-semibold">Prazo</label>
                            <input type="date" class="form-control" id="ap_deadline" name="deadline">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ap_status" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="ap_status" name="status">
                                <option value="pending">Pendente</option>
                                <option value="in_progress">Em Andamento</option>
                                <option value="completed">Concluído</option>
                                <option value="delayed">Atrasado</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ap_comments" class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control" id="ap_comments" name="comments" rows="3" maxlength="2000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="actionPlanSubmitBtn"><i class="bi bi-save me-1"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let indicatorChart  = null;
    let fullscreenChart = null;

    // Helper para formatar datas ISO para dd/mm/yyyy hh:mm
    function fmtDate(isoStr) {
        if (!isoStr) return '<span class="text-muted">—</span>';
        const d = new Date(isoStr);
        return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    // Todos os dados históricos (do mais antigo para o mais recente)
    // Dados do gráfico (serão atualizados dinamicamente)
    window.allLabels = {!! json_encode($chartData['labels']) !!};
    window.allValues = {!! json_encode($chartData['values']) !!};

    const indicatorMeta = {
        goal: {{ $indicator->goal }},
        unit: '{{ $indicator->unit }}',
        name: '{{ $indicator->name }}'
    };

    // Filtra os dados pelo número de meses e monta o objeto para o chart
    function buildChartData(months) {
        const labels = window.allLabels.slice(-months);
        const values = window.allValues.slice(-months);
        return { labels, values, goal: indicatorMeta.goal, unit: indicatorMeta.unit, name: indicatorMeta.name };
    }

    document.addEventListener('DOMContentLoaded', () => {
        createChart('indicatorChart', buildChartData(12));
        initValuesTable('{{ $indicator->id }}');
        initAnalysesTable('{{ $indicator->id }}');
    });

    function createChart(canvasId, data) {
        document.getElementById(canvasId).height = 50;
        const ctx   = document.getElementById(canvasId).getContext('2d');
        const color = v => v >= data.goal ? '#22c55e' : (v >= data.goal * 0.8 ? '#eab308' : '#ef4444');

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Valor Real',
                        data: data.values,
                        backgroundColor: data.values.map(color),
                        borderRadius: 6,
                        barPercentage: 0.7,
                        order: 2
                    },
                    {
                        label: `Meta (${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2 }).format(data.goal)}${data.unit})`,
                        data: Array(data.labels.length).fill(data.goal),
                        type: 'line',
                        borderColor: '#3b82f6',
                        borderWidth: 3,
                        borderDash: [8, 4],
                        pointRadius: 4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        fill: false,
                        tension: 0,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 8, color: '#4b5563', font: { size: 12 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const val = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(ctx.parsed.y);
                                return `${ctx.dataset.label.split(' (')[0]}: ${val}${data.unit}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e5e7eb' },
                        ticks: { color: '#6b7280', callback: v => v + data.unit }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' }
                    }
                }
            }
        });

        if (canvasId === 'indicatorChart') indicatorChart = chart;
        else fullscreenChart = chart;

        return chart;
    }

    function changeChartPeriod(months, btn) {
        // Atualiza botão ativo
        document.querySelectorAll('#periodBtnGroup .btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Reconstrói o gráfico com os dados filtrados
        if (indicatorChart) indicatorChart.destroy();
        createChart('indicatorChart', buildChartData(months));
    }

    // --- DataTable do Histórico ---
    function initValuesTable(id) {
        $('#valuesTable').closest('.table-responsive').hide();

        $('#valuesTable').DataTable({
            ordering: true,
            ajax: {
                url: `/indicadores/valores/${id}/lista`,
                complete: function() {
                    $('#valuesTablePreloader').fadeOut(400, function() {
                        $('#valuesTable').closest('.table-responsive').fadeIn(600);
                    });
                }
            },
            responsive: true,
            order: [[0, 'desc']],
            dom: 'Bftip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="bi bi-clipboard"></i> Copiar',
                    className: 'btn-primary',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7, 8];

                            return colunasPermitidas.includes(idx) &&
                                $(node).is(':visible');
                        }
                    },
                },
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-spreadsheet"></i> Excel',
                    className: 'btn-success',
                    title: '{{ $indicator->name }} - Histórico',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7, 8];

                            return colunasPermitidas.includes(idx) &&
                                $(node).is(':visible');
                        }
                    },
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn-danger',
                    title: '{{ $indicator->name }} - Histórico',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7, 8];

                            return colunasPermitidas.includes(idx) &&
                                $(node).is(':visible');
                        }
                    },
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Imprimir',
                    className: 'btn-dark',
                    title: '{{ $indicator->name }} - Histórico',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7, 8];

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
            layout: { topStart: 'buttons' },
            columns: [
                {
                    data: null,
                    title: 'Período',
                    render: (data) => `${data.year}/${String(data.month).padStart(2, '0')}`
                },
                {
                    data: null,
                    title: 'Meta',
                    render: () => {
                        const fmt = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format({{ $indicator->goal }});
                        return `${fmt}{{ $indicator->unit }}`;
                    }
                },
                {
                    data: 'value',
                    title: 'Valor',
                    render: (data) => {
                        const fmt = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(data);
                        return `<strong>${fmt}{{ $indicator->unit }}</strong>`;
                    }
                },
                {
                    data: 'value',
                    title: 'Status',
                    render: (data) => {
                        const goal = {{ $indicator->goal }};
                        const diff = data - goal;
                        const pct  = goal > 0 ? Math.abs((diff / goal) * 100).toFixed(1) : 0;
                        const ok   = diff >= 0;
                        const icon = ok ? 'check-circle' : 'x-circle';
                        const cls  = ok ? 'bg-success' : 'bg-danger';
                        const lbl  = diff > 0
                            ? `Atingiu (${pct}% acima)`
                            : diff === 0
                                ? 'Atingiu a meta'
                                : `Não atingiu (${pct}% abaixo)`;

                        return `<span class="badge ${cls}"><i class="bi bi-${icon} me-1"></i>${lbl}</span>`;

                    }
                },
                {
                    data: 'notes',
                    title: 'Observações',
                    render: (data) => data ?? '-'
                },
                {
                    data: 'created_by',
                    title: 'Criado por',
                    render: (data, type, row) => {
                        const user = row.updated_by;
                        return user && typeof user === 'object' ? user.name : '<span class="text-muted">—</span>';
                    }
                },
                {
                    data: 'created_at',
                    title: 'Criado em',
                    render: (data) => fmtDate(data)
                },
                {
                    data: 'updated_by',
                    title: 'Atualizado por',
                    render: (data, type, row) => {
                        const user = row.updated_by;
                        return user && typeof user === 'object' ? user.name : '<span class="text-muted">—</span>';
                    }
                },
                {
                    data: 'updated_at',
                    title: 'Atualizado em',
                    render: (data) => fmtDate(data)
                },
                {
                    data: null,
                    title: 'Ações',
                    orderable: false,
                    render: function(data, type, row) {
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
                                onclick="editValue(${row.id})">
                                    <i class="bi bi-pencil text-warning"></i> Editar
                                </a>
                            </li>`;
                        }

                        if (permissions.delete) {
                            dropdown += `
                            <li>
                                <a style="cursor: pointer" class="dropdown-item text-muted"
                                onclick="deleteValue(${row.id})">
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
            initComplete: function() {
                $('#valuesTablePreloader').fadeOut(300, function() {
                    $('#valuesTable').closest('.table-responsive').fadeIn(500);
                });
                $('#valuesTable_wrapper .dt-buttons').removeClass('btn-group');
            },
            language: {
                url: '{{ asset('/assets/json/pt-BR.json') }}'
            }
        });
    }

    // --- DataTable de Análises ---
    function initAnalysesTable(id) {
        $('#analysesTable').closest('.table-responsive').hide();

        const trendMap = {
            up:       { icon: 'graph-up-arrow',   cls: 'badge bg-success', label: 'Crescente'  },
            down:     { icon: 'graph-down-arrow', cls: 'badge bg-danger',  label: 'Decrescente' },
            stable:   { icon: 'dash-lg',          cls: 'badge bg-primary', label: 'Estável'    },
            volatile: { icon: 'activity',         cls: 'badge bg-warning', label: 'Volátil'    },
        };

        $('#analysesTable').DataTable({
            ordering: true,
            responsive: true,
            order: [[0, 'desc']],
            dom: 'Bftip',
            ajax: {
                url: `/indicadores/analises/${id}/lista`,
                complete: function() {
                    $('#analysesTablePreloader').fadeOut(400, function() {
                        $('#analysesTable').closest('.table-responsive').fadeIn(600);
                    });
                }
            },
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="bi bi-clipboard"></i> Copiar',
                    className: 'btn-primary',
                    title: '{{ $indicator->name }} - Análises',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7, 8];

                            return colunasPermitidas.includes(idx) &&
                                $(node).is(':visible');
                        }
                    },
                },
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-spreadsheet"></i> Excel',
                    className: 'btn-success',
                    title: '{{ $indicator->name }} - Análises',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7, 8];

                            return colunasPermitidas.includes(idx) &&
                                $(node).is(':visible');
                        }
                    },
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn-danger',
                    title: '{{ $indicator->name }} - Análises',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7, 8];

                            return colunasPermitidas.includes(idx) &&
                                $(node).is(':visible');
                        }
                    },
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Imprimir',
                    className: 'btn-dark',
                    title: '{{ $indicator->name }} - Análises',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            var colunasPermitidas = [0, 1, 2, 3, 4, 5, 6, 7, 8];

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
            layout: { topStart: 'buttons' },
            columns: [
                {
                    data: null,
                    title: 'Período',
                    render: (data) => `${data.year}/${String(data.month).padStart(2, '0')}`
                },
                {
                    data: 'trend',
                    title: 'Tendência',
                    render: (data) => {
                        if (!data) return '<span class="text-muted">—</span>';
                        const t = trendMap[data] ?? { icon: 'question-circle', cls: 'text-muted', label: data };
                        return `<span class="${t.cls}"><i class="bi bi-${t.icon} me-1"></i>${t.label}</span>`;
                    }
                },
                {
                    data: 'analysis',
                    title: 'Análise',
                    render: (data) => data
                        ? `<span title="${data}">${data.length > 100 ? data.substring(0, 100) + '…' : data}</span>`
                        : '<span class="text-muted">—</span>'
                },
                {
                    data: 'insights',
                    title: 'Insights',
                    render: (data) => data
                        ? `<span title="${data}">${data.length > 100 ? data.substring(0, 100) + '…' : data}</span>`
                        : '<span class="text-muted">—</span>'
                },
                {
                    data: 'action_plans',
                    title: 'Planos de Ação',
                    render: (data) => data.length
                },
                {
                    data: 'created_by',
                    title: 'Criado por',
                    render: (data, type, row) => {
                        const user = row.created_by;
                        return user && typeof user === 'object' ? user.name : '<span class="text-muted">—</span>';
                    }
                },
                {
                    data: 'created_at',
                    title: 'Criado em',
                    render: (data) => fmtDate(data)
                },
                {
                    data: 'updated_by',
                    title: 'Atualizado por',
                    render: (data, type, row) => {
                        const user = row.updated_by;
                        return user && typeof user === 'object' ? user.name : '<span class="text-muted">—</span>';
                    }
                },
                {
                    data: 'updated_at',
                    title: 'Atualizado em',
                    render: (data) => fmtDate(data)
                },
                {
                    data: null,
                    title: 'Ações',
                    orderable: false,
                    render: function(data, type, row) {
                        const permissions = row.permissions;
                        const hasPermission = permissions.edit || permissions.delete || permissions.view;
                        const hasPlans = row.action_plans && row.action_plans.length > 0;
                        const badgeCount = hasPlans ? `<span class="badge bg-primary rounded-pill ms-1">${row.action_plans.length}</span>` : '';

                        let dropdown = `
                        <div class="dropdown">
                            <button class="bg-transparent border-0 dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">`;


                        if (permissions.view) {
                            dropdown += `
                            <li>
                                <a class="dropdown-item text-muted" style="cursor: pointer" onclick='viewActionPlans(${JSON.stringify(row).replace(/'/g, "&#39;")})'>
                                    <i class="bi bi-journal-text text-primary"></i> Plano de Ação ${badgeCount}
                                </a>
                            </li>`;
                        }

                        if (permissions.edit) {
                            dropdown += `
                            <li>
                                <a style="cursor: pointer" class="dropdown-item text-muted"
                                onclick="editAnalysis(${row.id})">
                                    <i class="bi bi-pencil text-warning"></i> Editar
                                </a>
                            </li>`;
                        }

                        if (permissions.delete) {
                            dropdown += `
                            <li>
                                <a style="cursor: pointer" class="dropdown-item text-muted"
                                onclick="deleteAnalysis(${row.id})">
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
            initComplete: function() {
                $('#analysesTablePreloader').fadeOut(300, function() {
                    $('#analysesTable').closest('.table-responsive').fadeIn(500);
                });
                $('#analysesTable_wrapper .dt-buttons').removeClass('btn-group');
            },
            language: {
                url: '{{ asset('/assets/json/pt-BR.json') }}'
            }
        });
    }

    // ==================== ATUALIZAR ESTATÍSTICAS ====================

    async function refreshIndicatorStats() {
        try {
            const indicatorId = {{ $indicator->id }};
            const response = await fetch(`/indicadores/${indicatorId}/stats`);
            if (!response.ok) throw new Error('Erro ao atualizar estatísticas');

            const data = await response.json();

            // Atualizar Último Valor
            const lastValueNumberElement = document.getElementById('lastValueNumber');
            if (lastValueNumberElement) {
                const dateText = data.currentValueDate
                    ? ` <small id="lastValueDate" class="text-muted fw-light" style="font-size: 0.8rem">(${data.currentValueDate.month}/${data.currentValueDate.year})</small>`
                    : '';
                lastValueNumberElement.innerHTML = `${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data.currentValue)}${data.unit}${dateText}`;
            }

            // Atualizar indicador de acima/abaixo da meta
            const lastValuePercentElement = document.getElementById('lastValuePercent');
            if (lastValuePercentElement) {
                const arrowIcon = data.isAbove ? 'up' : 'down';
                const aboveBelow = data.isAbove ? 'acima' : 'abaixo';
                lastValuePercentElement.innerHTML = `<i class="bi bi-arrow-${arrowIcon} me-1"></i> ${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(Math.abs(data.percentDiff))}% ${aboveBelow} da meta`;
            }

            // Atualizar Média do Período
            const averageValueNumberElement = document.getElementById('averageValueNumber');
            if (averageValueNumberElement) {
                averageValueNumberElement.innerHTML = `${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data.averageValue)}${data.unit}`;
            }

            // Atualizar "meses com dados" na média
            const averageValueMonthsElement = document.getElementById('averageValueMonths');
            if (averageValueMonthsElement) {
                averageValueMonthsElement.innerHTML = `${data.monthsWithData} meses com dados`;
            }

            // Atualizar Taxa de Atingimento
            const achievementRateNumberElement = document.getElementById('achievementRateNumber');
            if (achievementRateNumberElement) {
                achievementRateNumberElement.innerHTML = `${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(data.achievementRate)}%`;
            }

            // Atualizar "de X meses" na taxa
            const achievementRateMonthsElement = document.getElementById('achievementRateMonths');
            if (achievementRateMonthsElement) {
                achievementRateMonthsElement.innerHTML = `${data.monthsAchieved} de ${data.monthsWithData} meses`;
            }

            // Atualizar Amplitude
            const rangeNumberElement = document.getElementById('rangeNumber');
            if (rangeNumberElement) {
                rangeNumberElement.innerHTML = `${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data.range)}${data.unit}`;
            }

            // Atualizar valores min/max na amplitude
            const rangeMinMaxElement = document.getElementById('rangeMinMax');
            if (rangeMinMaxElement) {
                rangeMinMaxElement.innerHTML = `${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data.minValue)} — ${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data.maxValue)}${data.unit}`;
            }

            // Atualizar Insights - Tendência
            const insightsTrendElement = document.getElementById('insightsTrend');
            if (insightsTrendElement) {
                if (data.trend == 0) {
                    insightsTrendElement.innerHTML = '<span>Sem dados para análise.</span>';
                } else if (data.trend > 0) {
                    insightsTrendElement.innerHTML = '<i class="bi bi-graph-up-arrow text-success me-1"></i> <strong>Tendência crescente:</strong> O indicador apresentou crescimento de ' + new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data.trend) + data.unit + ' durante todo o período de análise.';
                } else {
                    insightsTrendElement.innerHTML = '<i class="bi bi-graph-down-arrow text-danger me-1"></i> <strong>Tendência decrescente:</strong> O indicador apresentou queda de ' + new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(Math.abs(data.trend)) + data.unit + ' durante todo o período de análise.';
                }
            }

            // Atualizar Insights - Gap (se existir)
            const insightsGapElement = document.getElementById('insightsGap');
            if (data.gap > 0) {
                if (insightsGapElement) {
                    insightsGapElement.innerHTML = '<i class="bi bi-exclamation-triangle text-warning me-1"></i> <strong>Atenção necessária:</strong> O valor atual está ' + new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(Math.abs(data.gapPercent)) + '% abaixo da meta. Gap de ' + new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data.gap) + data.unit + ' para atingir o objetivo.';
                    insightsGapElement.style.display = 'block';
                } else {
                    // Criar elemento se não existir
                    const newGapElement = document.createElement('p');
                    newGapElement.id = 'insightsGap';
                    newGapElement.className = 'mb-2 text-body';
                    newGapElement.innerHTML = '<i class="bi bi-exclamation-triangle text-warning me-1"></i> <strong>Atenção necessária:</strong> O valor atual está ' + new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(Math.abs(data.gapPercent)) + '% abaixo da meta. Gap de ' + new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data.gap) + data.unit + ' para atingir o objetivo.';
                    insightsTrendElement.insertAdjacentElement('afterend', newGapElement);
                }
            } else if (insightsGapElement) {
                insightsGapElement.style.display = 'none';
            }

            // Atualizar Melhor desempenho
            const insightsBestElement = document.getElementById('insightsBest');
            if (data.bestMonth) {
                if (insightsBestElement) {
                    insightsBestElement.innerHTML = '<i class="bi bi-award text-warning me-1"></i> <strong>Melhor desempenho:</strong> ' + data.bestMonth + ' com ' + new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(data.maxValue) + data.unit;
                    insightsBestElement.style.display = 'block';
                }
            } else if (insightsBestElement) {
                insightsBestElement.style.display = 'none';
            }

            // Atualizar gráfico
            await refreshChartData();

        } catch (error) {
            console.error('Erro ao atualizar estatísticas:', error);
        }
    }

    async function refreshChartData() {
        try {
            const indicatorId = {{ $indicator->id }};
            const response = await fetch(`/indicadores/${indicatorId}/chart-data`);
            if (!response.ok) throw new Error('Erro ao buscar dados do gráfico');

            const chartData = await response.json();

            // Atualizar variáveis globais
            window.allLabels = chartData.labels;
            window.allValues = chartData.values;

            // Recriar gráfico com dados atualizados
            const currentPeriod = document.querySelector('#periodBtnGroup .btn.active');
            const months = currentPeriod ? parseInt(currentPeriod.textContent.trim()) : 12;

            if (indicatorChart) indicatorChart.destroy();
            createChart('indicatorChart', buildChartData(months));

        } catch (error) {
            console.error('Erro ao atualizar gráfico:', error);
        }
    }

    // ==================== CRUD: INDICADOR ====================

    function openIndicatorModal() {
        new bootstrap.Modal(document.getElementById('indicatorModal')).show();
    }

    // ==================== CRUD: VALORES ====================

    function openValueModal() {
        document.getElementById('valueForm').reset();
        document.getElementById('value_id').value = '';
        document.getElementById('valueModalLabel').innerHTML = '<i class="bi bi-plus-circle me-1"></i>Adicionar Valor';
        document.getElementById('val_year').value = new Date().getFullYear();
        new bootstrap.Modal(document.getElementById('valueModal')).show();
    }

    async function editValue(id) {
        try {
            const response = await fetch(`/indicadores/valores/${id}/editar`);
            // if (!response.ok) throw new Error(''Erro ao buscar valor);
            const data = await response.json();

            console.log(data);

            document.getElementById('value_id').value = data.id;
            document.getElementById('val_year').value = data.year;
            document.getElementById('val_month').value = data.month;
            document.getElementById('val_value').value = data.value;
            document.getElementById('val_notes').value = data.notes || '';
            document.getElementById('valueModalLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Valor';

            new bootstrap.Modal(document.getElementById('valueModal')).show();
        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível carregar o valor.',
                confirmButtonColor: '#0D6EFD',
            });
        }
    }

    async function submitValueForm(event) {
        event.preventDefault();

        const valueId = document.getElementById('value_id').value;
        const isEdit = valueId !== '';
        const indicatorId = {{ $indicator->id }};

        const url = isEdit
            ? `/indicadores/valores/${valueId}`
            : `/indicadores/${indicatorId}/valores`;

        const method = isEdit ? 'PUT' : 'POST';

        const body = {
            year: parseInt(document.getElementById('val_year').value),
            month: parseInt(document.getElementById('val_month').value),
            value: parseFloat(document.getElementById('val_value').value),
            notes: document.getElementById('val_notes').value || null,
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
                throw new Error(data.error || data.message || 'Erro ao salvar valor');
            }

            bootstrap.Modal.getInstance(document.getElementById('valueModal')).hide();

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: isEdit ? 'Valor atualizado com sucesso.' : 'Valor adicionado com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => {
                $('#valuesTable').DataTable().ajax.reload(null, false);
                refreshIndicatorStats();
            });

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

        return false;
    }

    async function deleteValue(id) {
        const result = await Swal.fire({
            theme: `${savedThemeAuth}`,
            title: 'Confirmar exclusão',
            text: 'Tem certeza que deseja excluir este valor?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            confirmButtonColor: '#0D6EFD',
            cancelButtonText: 'Cancelar',
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`/indicadores/valores/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) throw new Error('Erro ao excluir valor');

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: 'Valor excluído com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => {
                $('#valuesTable').DataTable().ajax.reload(null, false);
                refreshIndicatorStats();
            });

        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível excluir o valor.',
                confirmButtonColor: '#0D6EFD',
            });
        }
    }

    // ==================== CRUD: ANÁLISES ====================

    function openAnalysisModal() {
        document.getElementById('analysisForm').reset();
        document.getElementById('analysis_id').value = '';
        document.getElementById('analysisModalLabel').innerHTML = '<i class="bi bi-plus-circle me-1"></i>Adicionar Análise';
        document.getElementById('ana_year').value = new Date().getFullYear();
        new bootstrap.Modal(document.getElementById('analysisModal')).show();
    }

    async function editAnalysis(id) {
        try {
            const response = await fetch(`/indicadores/analises/${id}/editar`);
            if (!response.ok) throw new Error('Erro ao buscar análise');
            const data = await response.json();

            document.getElementById('analysis_id').value = data.id;
            document.getElementById('ana_year').value = data.year;
            document.getElementById('ana_month').value = data.month;
            document.getElementById('ana_trend').value = data.trend || '';
            document.getElementById('ana_analysis').value = data.analysis || '';
            document.getElementById('ana_insights').value = data.insights || '';
            document.getElementById('analysisModalLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Análise';

            new bootstrap.Modal(document.getElementById('analysisModal')).show();
        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível carregar a análise.',
                confirmButtonColor: '#0D6EFD',
            });
        }
    }

    async function submitAnalysisForm(event) {
        event.preventDefault();

        const analysisId = document.getElementById('analysis_id').value;
        const isEdit = analysisId !== '';
        const indicatorId = {{ $indicator->id }};

        const url = isEdit
            ? `/indicadores/analises/${analysisId}`
            : `/indicadores/${indicatorId}/analises`;

        const method = isEdit ? 'PUT' : 'POST';

        const body = {
            year: parseInt(document.getElementById('ana_year').value),
            month: parseInt(document.getElementById('ana_month').value),
            trend: document.getElementById('ana_trend').value || null,
            analysis: document.getElementById('ana_analysis').value || null,
            insights: document.getElementById('ana_insights').value || null,
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
                throw new Error(data.error || data.message || 'Erro ao salvar análise');
            }

            bootstrap.Modal.getInstance(document.getElementById('analysisModal')).hide();

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: isEdit ? 'Análise atualizada com sucesso.' : 'Análise adicionada com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => {
                $('#analysesTable').DataTable().ajax.reload(null, false);
                refreshIndicatorStats();
            });

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

        return false;
    }

    async function deleteAnalysis(id) {
        const result = await Swal.fire({
            theme: `${savedThemeAuth}`,
            title: 'Confirmar exclusão',
            text: 'Tem certeza que deseja excluir esta análise?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            confirmButtonColor: '#0D6EFD',
            cancelButtonText: 'Cancelar',
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`/indicadores/analises/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) throw new Error('Erro ao excluir análise');

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: 'Análise excluída com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => {
                $('#analysesTable').DataTable().ajax.reload(null, false);
                refreshIndicatorStats();
            });

        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível excluir a análise.',
                confirmButtonColor: '#0D6EFD',
            });
        }
    }

    // ==================== CRUD: PLANOS DE AÇÃO ====================

    let currentAnalysisRow = null; // guarda a row da análise para o modal de planos

    const statusMap = {
        pending:     { cls: 'bg-secondary', icon: 'hourglass-split', label: 'Pendente'     },
        in_progress: { cls: 'bg-primary',   icon: 'arrow-repeat',   label: 'Em Andamento' },
        completed:   { cls: 'bg-success',   icon: 'check-circle',   label: 'Concluído'    },
        delayed:     { cls: 'bg-danger',     icon: 'exclamation-triangle', label: 'Atrasado' },
    };

    const monthNames = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

    function viewActionPlans(row) {
        currentAnalysisRow = row;

        const title = `Plano de Ação — ${monthNames[row.month] || row.month}/${row.year}`;
        document.getElementById('actionPlansModalLabel').innerHTML = `
            <i class="bi bi-journal-text me-2"></i>${title}
            @can('create', App\Models\ActionPlan::class)
                <div class="vr"></div>
                <button class="btn btn-sm btn-outline-primary" onclick="openActionPlanForm(${row.id})">
                <i class="bi bi-plus-circle me-1"></i>Adicionar
            </button>
            @endcan
        `;

        renderActionPlansTable(row.action_plans || []);
        new bootstrap.Modal(document.getElementById('actionPlansModal')).show();
    }

    function renderActionPlansTable(plans) {
        // Destruir DataTable anterior se existir
        if ($.fn.DataTable.isDataTable('#actionPlansTable')) {
            $('#actionPlansTable').DataTable().destroy();
            $('#actionPlansTable').empty();
        }

        if (plans.length === 0) {
            $('#actionPlansEmpty').show();
            $('#actionPlansTableWrapper').hide();
            return;
        }

        $('#actionPlansEmpty').hide();
        $('#actionPlansTableWrapper').show();
        $('#actionPlansTable').DataTable({
            ordering: true,
            responsive: true,
            order: [[0, 'asc']],
            dom: 'Bftip',
            ajax: {
                url: `/indicadores/analises/${currentAnalysisRow.id}/planos`,
                dataSrc: function(json) {
                    // Se houver erro ou sem dados, retorna array vazio
                    if (json.error || !json.data) {
                        $('#actionPlansEmpty').show();
                        $('#actionPlansTableWrapper').hide();
                        return [];
                    }
                    return json.data || [];
                },
                error: function(xhr, status, error) {
                    // Se falhar a requisição, mostra "sem dados"
                    $('#actionPlansEmpty').show();
                    $('#actionPlansTableWrapper').hide();
                    console.error('Erro ao carregar planos:', error);
                }
            },
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="bi bi-clipboard"></i> Copiar',
                    className: 'btn-primary',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                },
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-spreadsheet"></i> Excel',
                    className: 'btn-success',
                    title: 'Plano de Ação',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn-danger',
                    title: 'Plano de Ação',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Imprimir',
                    className: 'btn-dark',
                    title: 'Plano de Ação',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                },
                {
                    extend: 'colvis',
                    text: '<i class="bi bi-list"></i> Filtrar Colunas',
                    className: 'border'
                }
            ],
            layout: { topStart: 'buttons' },
            columns: [
                {
                    data: null,
                    title: '#',
                    className: 'text-center',
                    width: '50px',
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: 'action',
                    title: 'Ação',
                    render: (data) => data || '—'
                },
                {
                    data: 'responsible',
                    title: 'Responsável',
                    render: (data) => data || '<span class="text-muted">—</span>'
                },
                {
                    data: 'deadline',
                    title: 'Prazo',
                    render: (data, type, row) => {
                        if (!data) return '<span class="text-muted">Sem prazo</span>';
                        const formatted = new Date(data).toLocaleDateString('pt-BR');
                        const isOverdue = new Date(data) < new Date() && row.status !== 'completed';
                        return isOverdue
                            ? `<span class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>${formatted}</span>`
                            : formatted;
                    }
                },
                {
                    data: 'status',
                    title: 'Status',
                    render: (data) => {
                        const s = statusMap[data] || { cls: 'bg-secondary', icon: 'question-circle', label: data };
                        return `<span class="badge ${s.cls}"><i class="bi bi-${s.icon} me-1"></i>${s.label}</span>`;
                    }
                },
                {
                    data: 'comments',
                    title: 'Observações',
                    render: (data) => data || '<span class="text-muted">—</span>'
                },
                {
                    data: 'created_by',
                    title: 'Criado por',
                    render: (data, type, row) => row.created_by ? row.created_by.name : '<span class="text-muted">—</span>'
                },
                {
                    data: 'created_at',
                    title: 'Criado em',
                    render: (data) => fmtDate(data)
                },
                {
                    data: 'updated_by',
                    title: 'Atualizado por',
                    render: (data, type, row) => row.updated_by ? row.updated_by.name : '<span class="text-muted">—</span>'
                },
                {
                    data: 'updated_at',
                    title: 'Atualizado em',
                    render: (data) => fmtDate(data)
                },
                {
                    data: null,
                    title: 'Ações',
                    orderable: false,
                    width: '60px',
                    render: function(data, type, row) {
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
                                onclick="editActionPlan(${row.id})">
                                    <i class="bi bi-pencil text-warning"></i> Editar
                                </a>
                            </li>`;
                        }

                        if (permissions.delete) {
                            dropdown += `
                            <li>
                                <a style="cursor: pointer" class="dropdown-item text-muted"
                                onclick="deleteActionPlan(${row.id})">
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
            initComplete: function() {
                $('#actionPlansTable_wrapper .dt-buttons').removeClass('btn-group');
            },
            language: {
                url: '{{ asset('/assets/json/pt-BR.json') }}'
            }
        });
    }

    function openActionPlanForm(analysisId) {
        document.getElementById('actionPlanForm').reset();
        document.getElementById('ap_id').value = '';
        document.getElementById('ap_analysis_id').value = analysisId;
        document.getElementById('ap_status').value = 'pending';
        document.getElementById('actionPlanFormModalLabel').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Adicionar Plano de Ação';
        new bootstrap.Modal(document.getElementById('actionPlanFormModal')).show();
    }

    async function editActionPlan(id) {
        try {
            const response = await fetch(`/indicadores/planos/${id}/editar`);
            if (!response.ok) {
                Swal.fire({
                    theme: `${savedThemeAuth}`,
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Nao foi possivel carregar o plano de acao.',
                    confirmButtonColor: '#0D6EFD',
                })
            }
            const data = await response.json();

            document.getElementById('ap_id').value = data.id;
            document.getElementById('ap_analysis_id').value = data.analysis_id;
            document.getElementById('ap_action').value = data.action || '';
            document.getElementById('ap_responsible').value = data.responsible || '';
            document.getElementById('ap_deadline').value = data.deadline ? data.deadline.substring(0, 10) : '';
            document.getElementById('ap_status').value = data.status || 'pending';
            document.getElementById('ap_comments').value = data.comments || '';
            document.getElementById('actionPlanFormModalLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Plano de Ação';

            new bootstrap.Modal(document.getElementById('actionPlanFormModal')).show();
        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível carregar o plano de ação.',
                confirmButtonColor: '#0D6EFD',
            });
        }
    }

    async function submitActionPlanForm(event) {
        event.preventDefault();

        const planId = document.getElementById('ap_id').value;
        const analysisId = document.getElementById('ap_analysis_id').value;
        const isEdit = planId !== '';

        const url = isEdit
            ? `/indicadores/planos/${planId}`
            : `/indicadores/analises/${analysisId}/planos`;

        const method = isEdit ? 'PUT' : 'POST';

        const body = {
            action: document.getElementById('ap_action').value,
            responsible: document.getElementById('ap_responsible').value || null,
            deadline: document.getElementById('ap_deadline').value || null,
            status: document.getElementById('ap_status').value,
            comments: document.getElementById('ap_comments').value || null,
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
                throw new Error(data.error || data.message || 'Erro ao salvar plano de ação');
            }

            bootstrap.Modal.getInstance(document.getElementById('actionPlanFormModal')).hide();

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: isEdit ? 'Plano de ação atualizado com sucesso.' : 'Plano de ação adicionado com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => {
                // Verificar se a tabela de planos foi inicializada
                if ($.fn.DataTable.isDataTable('#actionPlansTable')) {
                    $('#actionPlansTable').DataTable().ajax.reload(null, false);
                } else {
                    // Se a tabela está vazia, recarrega os dados e reinicializa
                    fetch(`/indicadores/analises/${currentAnalysisRow.id}/planos`)
                        .then(res => res.json())
                        .then(json => renderActionPlansTable(json.data || []));
                }
                $('#analysesTable').DataTable().ajax.reload(null, false)
                refreshIndicatorStats();
            });

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

        return false;
    }

    async function deleteActionPlan(id) {
        const result = await Swal.fire({
            theme: `${savedThemeAuth}`,
            title: 'Confirmar exclusão',
            text: 'Tem certeza que deseja excluir este plano de ação?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            confirmButtonColor: '#0D6EFD',
            cancelButtonText: 'Cancelar',
        });

        if (!result.isConfirmed) return;

        try {
            const response = await fetch(`/indicadores/planos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) throw new Error('Erro ao excluir plano de ação');

            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'success',
                title: 'Sucesso!',
                text: 'Plano de ação excluído com sucesso.',
                confirmButtonColor: '#0D6EFD',
            }).then(() => {
                $('#actionPlansTable').DataTable().ajax.reload(null, false)
                $('#analysesTable').DataTable().ajax.reload(null, false)
                refreshIndicatorStats();
            });

        } catch (error) {
            console.error(error);
            Swal.fire({
                theme: `${savedThemeAuth}`,
                icon: 'error',
                title: 'Erro!',
                text: 'Não foi possível excluir o plano de ação.',
                confirmButtonColor: '#0D6EFD',
            });
        }
    }

</script>

@endsection
