<?php

namespace App\Policies;

use App\Models\ActionPlan;
use App\Models\Analysis;
use App\Models\Indicator;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActionPlanPolicy
{
    public function before(User $user)
    {
        if ($user->is_admin) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermission('view_action_plans')
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar planos de ações.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActionPlan $actionPlan): Response
    {
         if (!$user->hasPermission('view_action_plans')) {
            return Response::deny('Você não tem permissão para visualizar planos de ações.');
        }

        // Achando analise
        $analysis = Analysis::where('id', $actionPlan->analysis_id)->first();

        // Achando indicator
        $indicator = Indicator::where('id', $analysis->indicator_id)->first();

        if ($user->department_id !== $indicator->department_id) {
            return Response::deny('Você só pode visualizar planos de ações do seu departamento.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasPermission('create_action_plans')
            ? Response::allow()
            : Response::deny('Você não tem permissão para criar planos de ações.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActionPlan $actionPlan): Response
    {
        if (!$user->hasPermission('edit_action_plans')) {
            return Response::deny(' Você não tem permissão para editar planos de ações.');
        }

        // Achando indicator e analise

        $analysis = Analysis::where('id', $actionPlan->analysis_id)->first();
        $indicator = Indicator::where('id', $analysis->indicator_id)->first();

        if ($analysis->indicator_id !== $indicator->id) {
            return Response::deny(' Você não pode editar planos de ações para essa análise.');
        }

        if ($user->department_id !== $indicator->department_id) {
            return Response::deny(' Você não pode editar planos de ações para indicadores fora do seu departamento.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActionPlan $actionPlan): Response
    {
        // return $user->hasPermission('create_action_plans')
        //     ? Response::allow()
        //     : Response::deny('Você não tem permissão para criar planos de ações.');

        if (!$user->hasPermission('delete_action_plans')) {
            return Response::deny(' Você não tem permissão para excluir planos de ações.');
        }

        // Achando indicator e analise

        $analysis = Analysis::where('id', $actionPlan->analysis_id)->first();
        $indicator = Indicator::where('id', $analysis->indicator_id)->first();

        if ($analysis->indicator_id !== $indicator->id) {
            return Response::deny(' Você não pode excluir planos de ações para essa análise.');
        }

        if ($user->department_id !== $indicator->department_id) {
            return Response::deny(' Você não pode excluir planos de ações para indicadores fora do seu departamento.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ActionPlan $actionPlan): Response
    {
        return Response::deny('A restauração de planos de ação não está disponível.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ActionPlan $actionPlan): Response
    {
        return Response::deny('A exclusão permanente de indicadores não está disponível.');
    }
}
