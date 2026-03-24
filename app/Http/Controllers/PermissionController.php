<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function getUserPermissions($userId)
    {
        $user = User::with('permissions')->findOrFail($userId);

        // Busca todas as permissões disponíveis
        $allPermissions = Permission::orderBy('description')->get();

        // Mapeia as permissões que o usuário já possui
        $userPermissionIds = $user->permissions->pluck('id')->toArray();

        return response()->json([
            'all_permissions' => $allPermissions,
            'user_permissions' => $userPermissionIds,
            'user' => $user
        ]);
    }

    public function updateUserPermissions(Request $request, $userId)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ],
            [
                'permissions.*.exists' => 'Permissão inválida.',
            ]
        );

        $user = User::findOrFail($userId);

        // Impede que o usuário altere suas próprias permissões
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode alterar suas próprias permissões.'
            ], 403);
        }

        // Sincroniza as permissões (muitos-para-muitos)
        $user->permissions()->sync($request->permissions ?? []);

        // Se o usuário não for um admin e tiver alguma permissão de exclusão (usuario, departamento, plano de ação, analise ou indicator) ou de criação de departamento para ele, não pode registrar elas
        if (!$user->is_admin) {
            $user->permissions()->detach(Permission::where('name', 'like', '%delete%')->pluck('id')->toArray());
            $user->permissions()->detach(Permission::where('name', 'like', '%create_departments%')->pluck('id')->toArray());
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissões atualizadas com sucesso.'
        ]);
    }
}
