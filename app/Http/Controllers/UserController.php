<?php

namespace App\Http\Controllers;

use App\Mail\NewUserConfirmation;
use App\Models\Department;
use App\Models\Permission;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use Str;

class UserController extends Controller
{
    /**
     * Listar todos os usuários
     */

    public function list_all()
    {
        $this->authorize('viewAny', User::class);

        try {

            $users = User::query()
                ->with(['createdBy', 'updatedBy', 'department', 'permissions'])
                ->whereNotIn('id', [auth()->id()]);
            if (!auth()->user()->is_admin) {
                $users->where('department_id', auth()->user()->department_id)->where('is_admin', false);;
            }

            $users = $users->get();

            $users = $users->map(function ($user) {

                $currentUser = auth()->user();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'last_login' => $user->last_login,
                    'active' => $user->active,
                    'blocked_until' => $user->blocked_until,
                    'role' => $user->role,
                    'created_by' => $user->createdBy ?? null,
                    'created_at' => $user->created_at,
                    'updated_by' => $user->updatedBy ?? null,
                    'updated_at' => $user->updated_at,
                    'is_admin' => $user->is_admin,
                    'deleted_at' => $user->deleted_at,
                    'department' => $user->department,

                    'permissions' => [
                        'edit' => $currentUser->can('update', $user),
                        'delete' => $currentUser->can('delete', $user),
                    ]
                ];
            });

            return response()->json(['data' => $users]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Erro ao carregar usuários.'
            ], 500);

        }
    }


    public function list($id)
    {
        $this->authorize('viewAny', User::class);

        try {

            $users = User::query()
                ->with(['createdBy', 'updatedBy', 'department', 'permissions'])
                ->whereNotIn('id', [auth()->id()])
                ->where('department_id', $id);
                
            if (!auth()->user()->is_admin) {
                $users->where('department_id', auth()->user()->department_id)->where('is_admin', false);;
            }

            $users = $users->get();

            $users = $users->map(function ($user) {

                $currentUser = auth()->user();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'last_login' => $user->last_login,
                    'active' => $user->active,
                    'blocked_until' => $user->blocked_until,
                    'role' => $user->role,
                    'created_by' => $user->createdBy ?? null,
                    'created_at' => $user->created_at,
                    'updated_by' => $user->updatedBy ?? null,
                    'updated_at' => $user->updated_at,
                    'is_admin' => $user->is_admin,
                    'deleted_at' => $user->deleted_at,
                    'department' => $user->department,

                    'permissions' => [
                        'edit' => $currentUser->can('update', $user),
                        'delete' => $currentUser->can('delete', $user),
                    ]
                ];
            });

            return response()->json(['data' => $users]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Erro ao carregar usuários.'
            ], 500);

        }
    }
   public function index()
    {

        $this->authorize('viewAny', User::class);

        if (Auth::user()->is_admin) {
            $departments = Department::all();
        } else {
            $departments = Department::where('id', Auth::user()->department_id)->get();
        }

        $permissions = Permission::count();

        // Buscar usuários excluídos (soft delete)
        $deletedUsers = User::onlyTrashed()
            ->with(['department', 'createdBy', 'updatedBy'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('app.users.index', [
            'title' => 'Usuários',
            'departments' => $departments,
            'total_permissions' => $permissions,
            'deletedUsers' => $deletedUsers
        ]);
    }
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|min:3|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|min:3|max:255',
        ],
        [
            'name.required' => 'O nome do usuário é obrigatório',
            'name.min' => 'O nome do usuário deve ter pelo menos 3 caracteres',
            'name.max' => 'O nome do usuário deve ter no.maxcdn 255 caracteres',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'Digite um email válido',
            'email.min' => 'O email deve ter pelo menos 3 caracteres',
            'email.max' => 'O email deve ter no máximo 255 caracteres',
            'email.unique' => 'O email informado já foi cadastrado',
            'department_id.exists' => 'Departamento não encontrado',
            'role.required' => 'O cargo é obrigatório',
            'role.min' => 'O cargo deve ter pelo menos 3 caracteres',
            'role.max' => 'O cargo deve ter no máximo 255 caracteres',
        ]);

        $token = Str::random(32);

        $permissions = $request->boolean('is_admin')
            ? Permission::all()
            : Permission::where('name', 'like', '%view%')->get();


        if (
            $request->department_id != auth()->user()->department_id
            && !auth()->user()->is_admin
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode criar usuários de outros departamentos.'
            ], 403);
        }

        if (!Auth::user()->is_admin && $request->boolean('is_admin') == true) {
            return response()->json([
                'success' => false,
                'message' => 'Somente usuários com permissão de admin podem criar usuários com permissão de admin.'
            ], 403);
        };

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => null,
            'department_id' => $request->department_id,
            'is_admin' => $request->boolean('is_admin'),
            'active' => $request->boolean('active'),
            'created_by' => auth()->id(),
            'role' => $request->role,
            'token' => $token
        ]);

        $user->permissions()->sync($permissions->pluck('id'));

        $link = route('user.confirmation', ['token' => $token]);

        Mail::to($request->email)
            ->send(new NewUserConfirmation($request->email, $link));

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso.'
        ]);
    }
    public function edit($id)
    {
        $user = User::with('permissions')->findOrFail($id);

        $this->authorize('view', $user);

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        // Guarda estado antigo
        $wasAdmin = $user->is_admin;

        // Estado novo vindo do form
        $willBeAdmin = $request->boolean('is_admin');

        $rules = [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|min:3|max:255|unique:users,email,' . $id,
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|min:3|max:255',
        ];

        // Só valida senha se foi preenchida
        if ($request->filled('password')) {
            $rules['password'] = 'min:8|confirmed';
        }

        $messages = [
            'name.required' => 'O nome do usuário é obrigatório',
            'name.min' => 'O nome do usuário deve ter pelo menos 3 caracteres',
            'name.max' => 'O nome do usuário deve ter no máximo 255 caracteres',
            'email.required' => 'O e-mail é obrigatório',
            'email.email' => 'Digite um e-mail válido',
            'email.min' => 'O e-mail deve ter pelo menos 3 caracteres',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres',
            'email.unique' => 'Este e-mail já está cadastrado',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres',
            'password.confirmed' => 'A confirmação de senha não confere',
            'department_id.exists' => 'O departamento informado é inválido',
            'role.required' => 'A função é obrigatória',
            'role.min' => 'A função deve ter pelo menos 3 caracteres',
            'role.max' => 'A função deve ter no máximo 255 caracteres',
        ];

        $request->validate($rules, $messages);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'is_admin' => $willBeAdmin,
            'active' => $request->boolean('active'),
            'updated_by' => auth()->id(),
            'role' => $request->role,
        ];

        // Atualiza senha apenas se preenchida
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        /*
        |------------------------------------------------
        | Controle de permissões baseado no admin
        |------------------------------------------------
        */

        // Se virou admin → recebe todas permissões
        if ($willBeAdmin) {
            $user->permissions()->sync(
                Permission::pluck('id')
            );
        }

        // Se era admin e deixou de ser → fica só com view
        if ($wasAdmin && !$willBeAdmin) {
            $user->permissions()->sync(
                Permission::where('name', 'like', '%view%')->pluck('id')
            );
        }

        // Atualiza usuário
        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('delete', $user);

        // Impede que o usuário exclua a si mesmo
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode excluir seu próprio usuário.'
            ], 403);
        }

        // Se for exclusão permanente (force delete)
        if ($request->boolean('force')) {
            $user->forceDelete();
            $message = 'Usuário excluído permanentemente do sistema.';
        } else {
            $user->delete();
            $message = 'Usuário movido para lixeira.';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);


        // Impede que o usuário desative a si mesmo
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Você não pode alterar seu próprio status.'], 403);
        }

        $user->active = !$user->active;
        $user->updated_by = auth()->id();
        $user->save();

        return response()->json(['success' => true, 'active' => $user->active]);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('update', $user);

        $user->restore();

        return response()->json([
            'success' => true,
            'message' => 'Usuário restaurado com sucesso.'
        ]);
    }
}
