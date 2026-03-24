<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    public function before(User $user)
    {
        if ($user->is_admin) {
            return true;
        }
    }

    public function viewAny(User $user): Response
    {
        return $user->hasPermission('view_departments')
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar departamentos.');
    }

    public function view(User $user, Department $department): Response
    {
        if (!$user->hasPermission('view_departments')) {
            return Response::deny('Você não tem permissão para visualizar departamentos.');
        }

        if ($user->department_id !== $department->id) {
            return Response::deny('Você só pode visualizar o seu próprio departamento.');
        }

        return Response::allow();
    }

    public function create(User $user): Response
    {
        return $user->hasPermission('create_departments') && $user->is_admin
            ? Response::allow()
            : Response::deny('Você não tem permissão para criar departamentos.');
    }

    public function update(User $user, Department $department): Response
    {
        if (!$user->hasPermission('edit_departments')) {
            return Response::deny(' Vocé não tem permissão para editar departamentos.');
        }

        if ($user->department_id !== $department->id) {
            return Response::deny('Você só pode editar o seu próprio departamento.');
        }

        return Response::allow();
    }

    public function delete(User $user, Department $department): Response
    {
        return $user->hasPermission('delete_departments')
            ? Response::allow()
            : Response::deny('Você não tem permissão para excluir departamentos.');
    }

    public function restore(User $user, Department $department): Response
    {
        return Response::deny('A restauração de departamentos não está disponível.');
    }

    public function forceDelete(User $user, Department $department): Response
    {
        return Response::deny('A exclusão permanente de departamentos não é permitida.');
    }
}
