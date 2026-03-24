<?php

namespace App\Policies;

use App\Models\Indicator;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IndicatorPolicy
{
    public function before(User $user)
    {
        if ($user->is_admin) {
            return true;
        }
    }

    public function viewAny(User $user): Response
    {
        return $user->hasPermission('view_indicators')
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar indicadores.');
    }

    public function view(User $user, Indicator $indicator): Response
    {
        if (!$user->hasPermission('view_indicators')) {
            return Response::deny('Você não tem permissão para visualizar indicadores.');
        }

        if ($user->department_id !== $indicator->department_id) {
            return Response::deny('Você só pode visualizar indicadores do seu departamento.');
        }

        return Response::allow();
    }

    public function create(User $user): Response
    {
        return $user->hasPermission('create_indicators')
            ? Response::allow()
            : Response::deny('Você não tem permissão para criar indicadores.');
    }

    public function update(User $user, Indicator $indicator): Response
    {
        if (!$user->hasPermission('edit_indicators')) {
            return Response::deny('Você não tem permissão para editar indicadores.');
        }

        if ($user->department_id !== $indicator->department_id) {
            return Response::deny('Você só pode editar indicadores do seu departamento.');
        }

        return Response::allow();
    }

    public function delete(User $user, Indicator $indicator): Response
    {
        if (!$user->hasPermission('delete_indicators')) {
            return Response::deny('Você não tem permissão para excluir indicadores.');
        }

        if ($user->department_id !== $indicator->department_id) {
            return Response::deny('Você só pode excluir indicadores do seu departamento.');
        }

        return Response::allow();
    }

    public function restore(User $user, Indicator $indicator): Response
    {
        return Response::deny('A restauração de indicadores não está disponível.');
    }

    public function forceDelete(User $user, Indicator $indicator): Response
    {
        return Response::deny('A exclusão permanente de indicadores não é permitida.');
    }
}
