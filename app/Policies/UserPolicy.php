<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function before(User $user)
    {
        if ($user->is_admin) {
            return true;
        }
    }

    public function viewAny(User $user): Response
    {
        return $user->hasPermission('view_users')
            ? Response::allow()
            : Response::deny('Você não tem permissão para visualizar usuários.');
    }

    public function view(User $user, User $targetUser): Response
    {
        if (!$user->hasPermission('view_users')) {
            return Response::deny('Você não tem permissão para visualizar usuários.');
        }

        if ($user->department_id !== $targetUser->department_id) {
            return Response::deny('Você só pode visualizar usuários do seu próprio departamento.');
        }

        return Response::allow();
    }

    public function create(User $user): Response
    {
        return $user->hasPermission('create_users')
            ? Response::allow()
            : Response::deny('Você não tem permissão para criar novos usuários.');
    }

    public function update(User $user, User $targetUser): Response
    {
        if (!$user->hasPermission('edit_users')) {
            return Response::deny('Você não tem permissão para editar usuários.');
        }

        if ($user->department_id !== $targetUser->department_id) {
            return Response::deny('Você só pode editar usuários do seu próprio departamento.');
        }

        return Response::allow();
    }

    public function delete(User $user, User $targetUser): Response
    {
        if (!$user->hasPermission('delete_users')) {
            return Response::deny('Você não tem permissão para excluir usuários.');
        }

        if ($user->department_id !== $targetUser->department_id) {
            return Response::deny('Você só pode excluir usuários do seu próprio departamento.');
        }

        return Response::allow();
    }

    public function restore(User $user, User $targetUser): Response
    {
        return Response::deny('A restauração de usuários não está disponível.');
    }

    public function forceDelete(User $user, User $targetUser): Response
    {
        return Response::deny('A exclusão permanente de usuários não é permitida.');
    }
}
