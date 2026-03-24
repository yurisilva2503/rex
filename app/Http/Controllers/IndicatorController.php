<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\Analysis;
use App\Models\Department;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndicatorController extends Controller
{
    /**
     * Listar todos os indicadores
     */

    public function list_all() {

        $this->authorize('viewAny', Indicator::class);

        try {
            $indicators = Indicator::query()->with(['department', 'values', 'analyses'])
                        ->orderBy('type', 'desc')
                        ->orderBy('name', 'asc');

            if (!auth()->user()->is_admin) {
                $indicators->where('department_id', auth()->user()->department_id);
            }

            $indicators = $indicators->get();


            $indicators = $indicators->map(function (Indicator $indicator) {

                $currentUser = auth()->user();
                return [
                    'id' => $indicator->id,
                    'department_id' => $indicator->department_id,
                    'name' => $indicator->name,
                    'type' => $indicator->type,
                    'goal' => $indicator->goal,
                    'unit' => $indicator->unit,
                    'description' => $indicator->description,
                    'formula' => $indicator->formula,
                    'direction' => $indicator->direction,
                    'active' => $indicator->active,
                    'created_by' => $indicator->createdBy,
                    'updated_by' => $indicator->updatedBy,
                    'created_at' => $indicator->created_at,
                    'updated_at' => $indicator->updated_at,
                    'department' => $indicator->department,
                    'values' => $indicator->values,
                    'analyses' => $indicator->analyses,

                    'permissions' => [
                        'create' => $currentUser->can('create', $indicator),
                        'edit' => $currentUser->can('update', $indicator),
                        'delete' => $currentUser->can('delete', $indicator),
                        'view' => $currentUser->can('view', $indicator)
                    ]
                ];
            });

            return response()->json([
                'data' => $indicators
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar indicadores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar todos os valores de indicadores
     */

    public function list_all_values($id) {

        $this->authorize('viewAny', Indicator::class);

        try {
            $indicator_values = IndicatorValue::with(['indicator', 'createdBy', 'updatedBy'])
                        ->orderBy('year', 'desc')
                        ->where('indicator_id', $id)
                        ->orderBy('month', 'asc');

            if (!auth()->user()->is_admin) {
                $indicator_values->whereHas('indicator', function($query) {
                    $query->where('department_id', Auth::user()->department_id);
                });
            }

            $indicator_values = $indicator_values->get();

            $indicator_values = $indicator_values->map(function (IndicatorValue $indicator_value) {

                $currentUser = auth()->user();
                return [
                    'id' => $indicator_value->id,
                    'year' => $indicator_value->year,
                    'month' => $indicator_value->month,
                    'value' => $indicator_value->value,
                    'status' => $indicator_value->status,
                    'notes' => $indicator_value->notes,
                    'created_by' => $indicator_value->createdBy,
                    'updated_by' => $indicator_value->updatedBy,
                    'created_at' => $indicator_value->created_at,
                    'updated_at' => $indicator_value->updated_at,
                    'indicator' => $indicator_value->indicator,

                    'permissions' => [
                        'create' => $currentUser->can('create', $indicator_value->indicator),
                        'edit' => $currentUser->can('update', $indicator_value->indicator),
                        'delete' => $currentUser->can('delete', $indicator_value->indicator),
                        'view' => $currentUser->can('view', $indicator_value->indicator)
                    ]
                ];
            });

            return response()->json([
                'data' => $indicator_values
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar valores de indicadores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar todos os valores de planos de ação
     */

    public function listActionPlans($id) {
        try {

            // PAREI AQUI
            $this->authorize('viewAny', ActionPlan::class);

            $analysis = Analysis::find($id);
            if (!$analysis) {
                return response()->json(['error' => 'Análise não encontrada'], 404);
            }

            $this->authorize('view', Indicator::find($analysis->indicator_id));

            $action_plans = ActionPlan::query()->with(['analysis', 'createdBy', 'updatedBy'])->where('analysis_id', $id)->orderBy('deadline', 'desc')->get();

            $action_plans = $action_plans->map(function (ActionPlan $action_plan) {

                $currentUser = auth()->user();
                return [
                    'id' => $action_plan->id,
                    'action' => $action_plan->action,
                    'responsible' => $action_plan->responsible,
                    'deadline' => $action_plan->deadline,
                    'status' => $action_plan->status,
                    'comments' => $action_plan->comments,
                    'created_by' => $action_plan->createdBy,
                    'updated_by' => $action_plan->updatedBy,
                    'created_at' => $action_plan->created_at,
                    'updated_at' => $action_plan->updated_at,
                    'analysis' => $action_plan->analysis,

                    'permissions' => [
                        'create' => $currentUser->can('create', $action_plan),
                        'edit' => $currentUser->can('update', $action_plan),
                        'delete' => $currentUser->can('delete', $action_plan),
                        'view' => $currentUser->can('view', $action_plan),
                    ]
                ];
            });

            return response()->json(['data' => $action_plans]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar planos de ação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar todos as análises de indicadores
     */

    public function list_all_analyses($id) {
        try {

            $this->authorize('viewAny', Analysis::class);

            $analyses = Analysis::query()->with(['indicator', 'actionPlans', 'createdBy', 'updatedBy'])->orderBy('year', 'desc')->where('indicator_id', $id);

            if (!auth()->user()->is_admin) {
                $analyses->whereHas('indicator', function($query) {
                    $query->where('department_id', Auth::user()->department_id);
                });
            }

            $analyses = $analyses->get();

            $analyses = $analyses->map(function (Analysis $analysis) {

                $currentUser = auth()->user();
                return [
                    'id' => $analysis->id,
                    'year' => $analysis->year,
                    'month' => $analysis->month,
                    'analysis' => $analysis->analysis,
                    'insights' => $analysis->insights,
                    'trend' => $analysis->trend,
                    'created_by' => $analysis->createdBy,
                    'updated_by' => $analysis->updatedBy,
                    'created_at' => $analysis->created_at,
                    'updated_at' => $analysis->updated_at,
                    'indicator' => $analysis->indicator,
                    'action_plans' => $analysis->actionPlans,

                    'permissions' => [
                        'create' => $currentUser->can('create', $analysis),
                        'edit' => $currentUser->can('update', $analysis),
                        'delete' => $currentUser->can('delete', $analysis),
                        'view' => $currentUser->can('view', $analysis),
                    ]
                ];
            });

            return response()->json(['data' => $analyses]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar as análises de indicadores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar indicadores de um departamento (para o modal)
     */
    public function list($id)
    {

        $this->authorize('viewAny', Indicator::class);

        try {
            $indicators = Indicator::where('department_id', $id)
                ->with(['values' => function ($query) {
                    $query->orderBy('year')->orderBy('month');
                }, 'analyses' => function ($query) {
                    $query->orderBy('year')->orderBy('month');
                }])
                ->orderBy('type')
                ->orderBy('name')
                ->get()
                ->map(function ($indicator) {
                    // Calcular média dos últimos 3 meses
                    $lastValues = $indicator->values
                        ->sortByDesc('year')
                        ->sortByDesc('month')
                        ->take(3)
                        ->pluck('value')
                        ->filter()
                        ->values();

                    $avgLast3Months = $lastValues->count() > 0
                        ? round($lastValues->avg(), 2)
                        : null;

                    // Último valor registrado
                    $lastValue = $indicator->values
                        ->sortByDesc('year')
                        ->sortByDesc('month')
                        ->first();

                    return [
                        'id' => $indicator->id,
                        'name' => $indicator->name,
                        'type' => $indicator->type,
                        'type_label' => $indicator->type_label, // Usando accessor do model
                        'type_color' => $indicator->type_color, // Usando accessor do model
                        'type_text_color' => $indicator->type_text_color, // Usando accessor do model
                        'goal' => $indicator->goal,
                        'unit' => $indicator->unit,
                        'direction' => $indicator->direction,
                        'direction_label' => $indicator->direction_label, // Usando accessor do model
                        'active' => $indicator->active,
                        'description' => $indicator->description,
                        'formula' => $indicator->formula,
                        'values_count' => $indicator->values->count(),
                        'last_value' => $lastValue ? [
                            'value' => $lastValue->value,
                            'formatted' => $lastValue->formatted_value, // Usando accessor do model IndicatorValue
                            'month' => $lastValue->month,
                            'year' => $lastValue->year,
                            'status' => $lastValue->status,
                            'status_label' => $lastValue->status_label, // Usando accessor do model IndicatorValue
                            'status_color' => $lastValue->status_color, // Usando accessor do model IndicatorValue
                            'status_text_color' => $lastValue->status_text_color ?? null
                        ] : null,
                        'avg_last_3_months' => $avgLast3Months !== null
                            ? number_format($avgLast3Months, 2, ',', '.') . $indicator->unit
                            : '-',
                        'created_at' => $indicator->created_at?->format('d/m/Y'),
                    ];
                });

            return response()->json($indicators);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar indicadores: ' . $e->getMessage()
            ], 500);
        }
    }

     public function show($id)
    {
        $this->authorize('viewAny', Indicator::class);

        $indicator = Indicator::with([
            'department',
            'analyses.createdBy',
            'analyses.updatedBy',
            'analyses.actionPlans.createdBy',
            'analyses.actionPlans.updatedBy',
            'values' => function($query) {
                $query->with('updatedBy')->orderBy('year', 'desc')->orderBy('month', 'desc');
            }
        ])->findOrFail($id);

        $this->authorize('view', $indicator);

        // Calcular estatísticas
        $values = $indicator->values;

        // Valor atual (mais recente)
        $currentValue = $values->isNotEmpty() ? $values->first()->value : 0;

        // Média do período
        $averageValue = $values->avg('value') ?? 0;

        // Total de meses com dados
        $monthsWithData = $values->count();

        // Meses que atingiram a meta
        $monthsAchieved = $values->filter(function($value) use ($indicator) {
            if ($indicator->direction == 'higher_is_better') {
                return $value->value >= $indicator->goal;
            } else {
                return $value->value <= $indicator->goal;
            }
        })->count();

        // Taxa de atingimento
        $achievementRate = $monthsWithData > 0 ? ($monthsAchieved / $monthsWithData) * 100 : 0;

        // Valores mínimo e máximo
        $minValue = $values->min('value') ?? 0;
        $maxValue = $values->max('value') ?? 0;

        // Amplitude (variação)
        $range = $maxValue - $minValue;

        // Melhor mês
        $bestValue = $values->sortByDesc('value')->first();
        $bestMonth = $bestValue ? \Carbon\Carbon::create($bestValue->year, $bestValue->month, 1) : null;

        // Preparar dados para o gráfico (últimos 12 meses)
        $chartData = $this->prepareChartData($indicator, 12);

        $title = 'Indicador ' . $indicator->name;

        $departments = Department::where('active', true)->orderBy('name')->get();

        return view('app.indicators.show', compact(
            'indicator',
            'values',
            'currentValue',
            'averageValue',
            'monthsWithData',
            'monthsAchieved',
            'achievementRate',
            'minValue',
            'maxValue',
            'range',
            'bestMonth',
            'chartData',
            'title',
            'departments'
        ));
    }

    private function prepareChartData($indicator, $limit = 12)
    {

        $values = $indicator->values->sortBy(function($item) {
            return $item->year * 100 + $item->month;
        })->take(-$limit);

        $labels = [];
        $data = [];

        foreach ($values as $value) {
            $labels[] = $value->year . '/' . str_pad($value->month, 2, '0', STR_PAD_LEFT);
            $data[] = $value->value;
        }

        return [
            'labels' => $labels,
            'values' => $data
        ];
    }

    /**
     * Retornar dados de resumo do indicador para atualização AJAX
     */
    public function getStats($id)
    {
        $this->authorize('viewAny', Indicator::class);

        $indicator = Indicator::with([
            'values' => function($query) {
                $query->orderBy('year', 'desc')->orderBy('month', 'desc');
            }
        ])->findOrFail($id);

        $this->authorize('view', $indicator);

        // Calcular estatísticas
        $values = $indicator->values;

        // Valor atual (mais recente)
        $currentValue = $values->isNotEmpty() ? $values->first()->value : 0;
        $currentValueDate = $values->isNotEmpty() ? $values->first() : null;

        // Média do período
        $averageValue = $values->avg('value') ?? 0;

        // Total de meses com dados
        $monthsWithData = $values->count();

        // Meses que atingiram a meta
        $monthsAchieved = $values->filter(function($value) use ($indicator) {
            if ($indicator->direction == 'higher_is_better') {
                return $value->value >= $indicator->goal;
            } else {
                return $value->value <= $indicator->goal;
            }
        })->count();

        // Taxa de atingimento
        $achievementRate = $monthsWithData > 0 ? ($monthsAchieved / $monthsWithData) * 100 : 0;

        // Valores mínimo e máximo
        $minValue = $values->min('value') ?? 0;
        $maxValue = $values->max('value') ?? 0;

        // Amplitude (variação)
        $range = $maxValue - $minValue;

        // Percentual em relação à meta
        $percentDiff = $indicator->goal > 0 ? (($currentValue - $indicator->goal) / $indicator->goal) * 100 : 0;
        $isAbove = $percentDiff >= 0;

        // Gap para atingir a meta
        $gap = $indicator->goal - $currentValue;
        $gapPercent = $indicator->goal > 0 ? ($gap / $indicator->goal) * 100 : 0;

        // Tendência
        $trend = $values->count() >= 2 ? ($values->first()->value - $values->last()->value) : 0;

        // Melhor mês
        $bestValue = $values->sortByDesc('value')->first();
        $bestMonth = $bestValue ? \Carbon\Carbon::create($bestValue->year, $bestValue->month, 1) : null;

        return response()->json([
            'currentValue' => $currentValue,
            'currentValueDate' => $currentValueDate ? [
                'month' => $currentValueDate->month,
                'year' => $currentValueDate->year
            ] : null,
            'averageValue' => $averageValue,
            'monthsWithData' => $monthsWithData,
            'monthsAchieved' => $monthsAchieved,
            'achievementRate' => $achievementRate,
            'minValue' => $minValue,
            'maxValue' => $maxValue,
            'range' => $range,
            'percentDiff' => $percentDiff,
            'isAbove' => $isAbove,
            'gap' => $gap,
            'gapPercent' => $gapPercent,
            'trend' => $trend,
            'bestMonth' => $bestMonth ? $bestMonth->format('m/Y') : null,
            'unit' => $indicator->unit,
            'goal' => $indicator->goal,
            'direction' => $indicator->direction
        ]);
    }

    /**
     * Retornar dados do gráfico do indicador para atualização AJAX
     */
    public function getChartData($id)
    {
        $this->authorize('viewAny', Indicator::class);

        $indicator = Indicator::with([
            'values' => function($query) {
                $query->orderBy('year', 'desc')->orderBy('month', 'desc');
            }
        ])->findOrFail($id);

        $this->authorize('view', $indicator);

        return response()->json($this->prepareChartData($indicator, 12));
    }

    public function edit($id)
    {
        $indicator = Indicator::findOrFail($id);

        $this->authorize('update', $indicator);

        return response()->json($indicator);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|in:strategic,tactical,monitoring',
            'goal' => 'required|numeric|min:0',
            'unit' => 'required|max:10',
            'direction' => 'required|in:higher_is_better,lower_is_better',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|max:1000',
            'formula' => 'nullable|max:500',
        ], [
            'name.required' => 'O nome do indicador é obrigatório',
            'name.max' => 'O nome do indicador deve ter no máximo 255 caracteres',
            'type.required' => 'O tipo é obrigatório',
            'type.in' => 'O tipo deve ser estratégico, táctico ou monitoramento',
            'goal.required' => 'A meta é obrigatória',
            'goal.numeric' => 'A meta deve ser um valor numérico',
            'unit.required' => 'A unidade é obrigatória',
            'unit.max' => 'A unidade deve ter no máximo 10 caracteres',
            'direction.required' => 'A direção é obrigatória',
            'direction.in' => 'A direção deve ser maior ou menor',
            'department_id.required' => 'O departamento é obrigatório',
            'department_id.exists' => 'Departamento não encontrado',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres',
            'formula.max' => 'A formula deve ter no.maxcdn 500 caracteres',
        ]);

        $this->authorize('create', Indicator::class);

        $indicator = Indicator::create([
            'name' => $request->name,
            'type' => $request->type,
            'goal' => $request->goal,
            'unit' => $request->unit,
            'direction' => $request->direction,
            'department_id' => $request->department_id,
            'description' => $request->description,
            'formula' => $request->formula,
            'active' => $request->has('active') ? true : true,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $indicator]);
    }

    public function destroy($id)
    {
        $indicator = Indicator::findOrFail($id);

        $this->authorize('delete', $indicator);

        $indicator->delete();

        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $indicator = Indicator::findOrFail($id);

        $this->authorize('update', $indicator);

        $indicator->active = !$indicator->active;
        $indicator->updated_by = auth()->id();
        $indicator->save();

        return response()->json(['success' => true, 'active' => $indicator->active]);
    }

    public function update(Request $request, $id)
    {
        $indicator = Indicator::findOrFail($id);

        $this->authorize('update', $indicator);

        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|in:strategic,tactical,monitoring',
            'goal' => 'required|numeric|min:0',
            'unit' => 'required|max:10',
            'direction' => 'required|in:higher_is_better,lower_is_better',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|max:1000',
            'formula' => 'nullable|max:500',
        ], [
            'name.required' => 'O nome do indicador é obrigatório',
            'name.max' => 'O nome do indicador deve ter no máximo 255 caracteres',
            'type.required' => 'O tipo é obrigatório',
            'type.in' => 'O tipo deve ser estratégico, táctico ou monitoramento',
            'goal.required' => 'A meta é obrigatória',
            'goal.numeric' => 'A meta deve ser um valor numérico',
            'unit.required' => 'A unidade é obrigatória',
            'unit.max' => 'A unidade deve ter no máximo 10 caracteres',
            'direction.required' => 'A direção é obrigatória',
            'direction.in' => 'A direção deve ser maior ou menor',
            'department_id.required' => 'O departamento é obrigatório',
            'department_id.exists' => 'Departamento não encontrado',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres',
            'formula.max' => 'A formula deve ter no.maxcdn 500 caracteres',
        ]);

        $indicator->update([
            'name' => $request->name,
            'type' => $request->type,
            'goal' => $request->goal,
            'unit' => $request->unit,
            'direction' => $request->direction,
            'department_id' => $request->department_id,
            'description' => $request->description,
            'formula' => $request->formula,
            'active' => $request->has('active') ? true : false,
            'updated_by' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $indicator]);
        }

        return redirect()->route('indicators.show', $id)->with('success', 'Indicador atualizado com sucesso.');
    }

    // ==================== VALORES ====================

    public function storeValue(Request $request, $id)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'value' => 'required|numeric',
            'notes' => 'nullable|max:1000',
        ], [
            'year.required' => 'O ano é obrigatório',
            'year.integer' => 'O ano deve ser numérico',
            'year.min' => 'O ano deve ser maior ou igual a 2000',
            'year.max' => 'O ano deve ser menor ou igual a 2100',
            'month.required' => 'O mês é obrigatório',
            'month.integer' => 'O mês deve ser numérico',
            'month.min' => 'O mês deve ser maior ou igual a 1',
            'month.max' => 'O mês deve ser menor ou igual a 12',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser numérico',
            'notes.max' => 'As notas devem ter no maximo 1000 caracteres',
        ]);

        // Verificar se já existe valor para o mesmo mês/ano
        $exists = IndicatorValue::where('indicator_id', $id)
            ->where('year', $request->year)
            ->where('month', $request->month)
            ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Já existe um valor registrado para este mês/ano.'
            ], 422);
        }

        $this->authorize('create', Indicator::class);

        $indicatorValue = IndicatorValue::create([
            'indicator_id' => $id,
            'year' => $request->year,
            'month' => $request->month,
            'value' => $request->value,
            'notes' => $request->notes,
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $indicatorValue]);
    }

    public function editValue($id)
    {
        $this->authorize('viewAny', Indicator::class);

        $value = IndicatorValue::findOrFail($id);
        $indicator = Indicator::findOrFail($value->indicator_id);

        $this->authorize('update', $indicator);

        return response()->json($value);
    }

    public function updateValue(Request $request, $id)
    {
        $value = IndicatorValue::findOrFail($id);
        $indicator = Indicator::findOrFail($value->indicator_id);

        $this->authorize('update', $indicator);

        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'value' => 'required|numeric',
            'notes' => 'nullable|max:1000',
        ], [
            'year.required' => 'O ano é obrigatório',
            'year.integer' => 'O ano deve ser numérico',
            'year.min' => 'O ano deve ser maior ou igual a 2000',
            'year.max' => 'O ano deve ser menor ou igual a 2100',
            'month.required' => 'O mês é obrigatório',
            'month.integer' => 'O mês deve ser numérico',
            'month.min' => 'O mês deve ser maior ou igual a 1',
            'month.max' => 'O mês deve ser menor ou igual a 12',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser numérico',
            'notes.max' => 'As notas devem ter no maximo 1000 caracteres',
        ]);

        // Verificar unicidade (excluindo o próprio registro)
        $exists = IndicatorValue::where('indicator_id', $value->indicator_id)
            ->where('year', $request->year)
            ->where('month', $request->month)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Já existe um valor registrado para este mês/ano.'
            ], 422);
        }

        $this->authorize('update', $indicator);

        $value->update([
            'year' => $request->year,
            'month' => $request->month,
            'value' => $request->value,
            'notes' => $request->notes,
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $value]);
    }

    public function destroyValue($id)
    {
        $value = IndicatorValue::findOrFail($id);
        $indicator = Indicator::findOrFail($value->indicator_id);

        $this->authorize('delete', $indicator);

        $value->delete();

        return response()->json(['success' => true]);
    }

    // ==================== ANÁLISES ====================

    public function storeAnalysis(Request $request, $id)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'trend' => 'nullable|in:up,down,stable,volatile',
            'analysis' => 'nullable|max:2000',
            'insights' => 'nullable|max:2000',
        ], [
            'year.required' => 'O ano é obrigatório',
            'year.integer' => 'O ano deve ser numérico',
            'year.min' => 'O ano deve ser maior ou igual a 2000',
            'year.max' => 'O ano deve ser menor ou igual a 2100',
            'month.required' => 'O mês é obrigatório',
            'month.integer' => 'O mês deve ser numérico',
            'month.min' => 'O mês deve ser maior ou igual a 1',
            'month.max' => 'O mês deve ser menor ou igual a 12',
            'trend.in' => 'A tendência deve ser up, down, stable ou volatile',
            'analysis.max' => 'A análise deve ter no maximo 2000 caracteres',
            'insights.max' => 'Os insights devem ter no maximo 2000 caracteres',
        ]);

        $this->authorize('create', Analysis::class);

        $analysis = Analysis::create([
            'indicator_id' => $id,
            'year' => $request->year,
            'month' => $request->month,
            'trend' => $request->trend,
            'analysis' => $request->analysis,
            'insights' => $request->insights,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $analysis]);
    }

    public function editAnalysis($id)
    {
        $this->authorize('viewAny', Analysis::class);

        $analysis = Analysis::findOrFail($id);

        $this->authorize('update', $analysis);

        return response()->json($analysis);
    }

    public function updateAnalysis(Request $request, $id)
    {
        $analysis = Analysis::findOrFail($id);

        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'trend' => 'nullable|in:up,down,stable,volatile',
            'analysis' => 'nullable|max:2000',
            'insights' => 'nullable|max:2000',
        ], [
            'year.required' => 'O ano é obrigatório',
            'year.integer' => 'O ano deve ser numérico',
            'year.min' => 'O ano deve ser maior ou igual a 2000',
            'year.max' => 'O ano deve ser menor ou igual a 2100',
            'month.required' => 'O mês é obrigatório',
            'month.integer' => 'O mês deve ser numérico',
            'month.min' => 'O mês deve ser maior ou igual a 1',
            'month.max' => 'O mês deve ser menor ou igual a 12',
            'trend.in' => 'A tendência deve ser up, down, stable ou volatile',
            'analysis.max' => 'A análise deve ter no maximo 2000 caracteres',
            'insights.max' => 'Os insights devem ter no maximo 2000 caracteres',
        ]);

        $this->authorize('update', $analysis);

        $analysis->update([
            'year' => $request->year,
            'month' => $request->month,
            'trend' => $request->trend,
            'analysis' => $request->analysis,
            'insights' => $request->insights,
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $analysis]);
    }

    public function destroyAnalysis($id)
    {
        $analysis = Analysis::findOrFail($id);

        $this->authorize('delete', $analysis);

        $analysis->delete();

        return response()->json(['success' => true]);
    }

    // ==================== PLANOS DE AÇÃO ====================

    public function storeActionPlan(Request $request, $analysisId)
    {
        $request->validate([
            'action' => 'required|max:500',
            'responsible' => 'nullable|max:255',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed,delayed',
            'comments' => 'nullable|max:2000',
        ], [
            'action.required' => 'A ação é obrigatória',
            'action.max' => 'A ação pode ter no máximo 500 caracteres',
            'responsible.max' => 'O responsável pode ter no.maxcdn 255 caracteres',
            'deadline.date' => 'A data de conclusão deve ser uma data válida',
            'status.in' => 'O status deve ser pendente, em andamento, concluído ou atrasado',
            'comments.max' => 'Os comentários devem ter no.maxcdn 2000 caracteres',
        ]);

        $this->authorize('create', ActionPlan::class);

        $actionPlan = ActionPlan::create([
            'analysis_id' => $analysisId,
            'action' => $request->action,
            'responsible' => $request->responsible,
            'deadline' => $request->deadline,
            'status' => $request->status ?? 'pending',
            'comments' => $request->comments,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $actionPlan]);
    }

    public function editActionPlan($id)
    {
        $this->authorize('viewAny', ActionPlan::class);

        $actionPlan = ActionPlan::findOrFail($id);

        $this->authorize('update', $actionPlan);

        return response()->json($actionPlan);
    }

    public function updateActionPlan(Request $request, $id)
    {
        $actionPlan = ActionPlan::findOrFail($id);

        $request->validate([
            'action' => 'required|max:500',
            'responsible' => 'nullable|max:255',
            'deadline' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed,delayed',
            'comments' => 'nullable|max:2000',
        ], [
            'action.required' => 'A ação é obrigatória',
            'action.max' => 'A ação pode ter no máximo 500 caracteres',
            'responsible.max' => 'O responsável pode ter no.maxcdn 255 caracteres',
            'deadline.date' => 'A data de conclusão deve ser uma data válida',
            'status.in' => 'O status deve ser pendente, em andamento, concluído ou atrasado',
            'comments.max' => 'Os comentários devem ter no.maxcdn 2000 caracteres',
        ]);

        $this->authorize('update', $actionPlan);

        $actionPlan->update([
            'action' => $request->action,
            'responsible' => $request->responsible,
            'deadline' => $request->deadline,
            'status' => $request->status ?? 'pending',
            'comments' => $request->comments,
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $actionPlan]);
    }

    public function destroyActionPlan($id)
    {
        $actionPlan = ActionPlan::findOrFail($id);

        $this->authorize('delete', $actionPlan);

        $actionPlan->delete();

        return response()->json(['success' => true]);
    }
}
