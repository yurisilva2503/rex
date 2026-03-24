<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\Department;
use App\Models\IndicatorValue;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AppController extends Controller
{
    public function index()
    {
        // Verificando se o usuário pode visualizar indicadores
        if (!Auth::user()->can('viewAny', Indicator::class)) {
            return abort(403);
        }

        // Buscar todos os indicadores com seus departamentos
        $indicators = Indicator::with(['department', 'values', 'analyses'])
            ->orderBy('type')
            ->orderBy('name');

        // Se o usuário não for admin, mostrar apenas os indicadores do departamento dele
        if (!Auth::user()->is_admin) {
            $indicators->where('department_id', Auth::user()->department_id);
        }

        $indicators = $indicators->get();

        // Estatísticas gerais
        $totalIndicators = Indicator::query();
        // Se o usuário nao for admin, mostrar apenas os indicadores do departamento dele
        if (!Auth::user()->is_admin) {
            $totalIndicators->where('department_id', Auth::user()->department_id);
        }

        $totalIndicators = $totalIndicators->count();

        $activeIndicators = Indicator::where('active', true);
        // Se o usuário nao for admin, mostrar apenas os indicadores do departamento dele
        if (!Auth::user()->is_admin) {
            $activeIndicators->where('department_id', Auth::user()->department_id);
        }

        $activeIndicators = $activeIndicators->count();

        $departments = Department::withCount('indicators');
        // Se o usuário nao for admin, mostrar apenas os indicadores do departamento dele
        if (!Auth::user()->is_admin) {
            $departments->where('id', Auth::user()->department_id);
        }

        $departments = $departments->get();

        // Dados para gráficos
        $indicatorsByDepartment = Indicator::selectRaw('department_id, count(*) as total')
            ->groupBy('department_id')
            ->with('department');

        // Se o usuário nao for admin, mostrar apenas os indicadores do departamento dele
        if (!Auth::user()->is_admin) {
            $indicatorsByDepartment->where('department_id', Auth::user()->department_id);
        }

        $indicatorsByDepartment = $indicatorsByDepartment->get();

        $indicatorsByType = Indicator::selectRaw('type, count(*) as total')
            ->groupBy('type');

        // Se o usuário nao for admin, mostrar apenas os indicadores do departamento dele
        if (!Auth::user()->is_admin) {
            $indicatorsByType->where('department_id', Auth::user()->department_id);
        }

        $indicatorsByType = $indicatorsByType->get();

        // Últimos valores registrados
        $latestValues = IndicatorValue::with('indicator.department')
            ->latest()
            ->take(10);

        // Se o usuário nao for admin, mostrar apenas os indicadores do departamento dele
        if (!Auth::user()->is_admin) {
            $latestValues->whereHas('indicator', function($query) {
                $query->where('department_id', Auth::user()->department_id);
            });
        }

        $latestValues = $latestValues->get();

        return view('app.index', [
            'title' => 'Dashboard',
            'indicators' => $indicators,
            'totalIndicators' => $totalIndicators,
            'activeIndicators' => $activeIndicators,
            'departments' => $departments,
            'indicatorsByDepartment' => $indicatorsByDepartment,
            'indicatorsByType' => $indicatorsByType,
            'latestValues' => $latestValues
        ]);
    }
}
