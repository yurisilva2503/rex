<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\Indicator;
use Illuminate\Http\Request;

class ActionPlanController extends Controller
{
    /**
     * Listar todos os valores de planos de ação
     */

    public function listActionPlans($id) {
        try {

            $this->authorize('viewAny', Indicator::class);
            
            return response()->json(['data' => ActionPlan::with(['analysis', 'createdBy', 'updatedBy'])->orderBy('deadline', 'desc')->where('analysis_id', $id)->get()]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar planos de ação: ' . $e->getMessage()
            ], 500);
        }
    }
}
