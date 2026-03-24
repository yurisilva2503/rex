<?php

namespace App\Policies;

use App\Models\Analysis;
use App\Models\Department;
use App\Models\Indicator;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnalysisPolicy
{
     public function before(User $user)
    {
        if ($user->is_admin) {
            return true;
        }
    }

    public function viewAny(User $user): Response
    {
        return $user->hasPermission('view_analyses')
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar análises.');
    }

    public function view(User $user, Analysis $analysis): Response
    {
        if (!$user->hasPermission('view_analyses')) {
            return Response::deny('Você não tem permissão para visualizar análises.');
        }

        // Achando indicator

        $indicator = Indicator::where('id', $analysis->indicator_id)->first();

        if ($user->department_id !== $indicator->department_id) {
            return Response::deny('Você só pode visualizar análises do seu departamento.');
        }

        if ($analysis->indicator_id !== $indicator->id) {
            return Response::deny('Você só pode visualizar análises da indicador correspondente.');
        }

        return Response::allow();
    }

    public function create(User $user): Response
    {
        return $user->hasPermission('create_analyses') && $user->is_admin
            ? Response::allow()
            : Response::deny('Você não tem permissão para criar análises.');
    }

    public function update(User $user, Analysis $analysis): Response
    {
        if (!$user->hasPermission('edit_analyses')) {
            return Response::deny('Você não tem permissão para editar análises.');
        }

        // Achando indicator

        $indicator = Indicator::where('id', $analysis->indicator_id)->first();

        if ($user->department_id !== $indicator->department_id) {
            return Response::deny('Você só pode editar analises do seu departamento.');
        }

        if ($analysis->indicator_id !== $indicator->id) {
            return Response::deny(' Vocé não pode editar análises para esse indicador.');
        }

        return Response::allow();
    }

     public function delete(User $user, Analysis $analysis): Response
    {
        if (!$user->hasPermission('delete_analyses')) {
            return Response::deny(' Você não tem permissão para excluir análises.');
        }

        // Achando indicator

        $indicator = Indicator::where('id', $analysis->indicator_id)->first();

        if ($analysis->indicator_id !== $indicator->id) {
            return Response::deny(' Você não pode excluir análises para essa indicador.');
        }

        if ($user->department_id !== $indicator->department_id) {
            return Response::deny(' Você não pode excluir análises para indicadores fora do seu departamento.');
        }

        return Response::allow();
    }


    public function restore(User $user, Department $department): Response
    {
        return Response::deny('A restauração de análises não está disponível.');
    }

    public function forceDelete(User $user, Department $department): Response
    {
        return Response::deny('A exclusão permanente de análises não é permitida.');
    }
}
