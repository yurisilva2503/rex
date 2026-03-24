<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

    public function list_all()
    {
        $this->authorize('viewAny', Department::class);

        try {

            $query = Department::query()
                ->withCount('indicators')
                ->withCount('users')
                ->with(['createdBy', 'updatedBy', 'indicators']);

            if (!auth()->user()->is_admin) {
                $query->where('id', auth()->user()->department_id);
            }

            $departments = $query->get();

            $departments = $departments->map(function (Department $department) {

            $currentUser = auth()->user();

            return [
                'id' => $department->id,
                'description' => $department->description,
                'icon' => $department->icon,
                'name' => $department->name,
                'active' => $department->active,
                'users_count' => $department->users_count,
                'indicators_count' => $department->indicators_count,
                'created_by' => $department->createdBy,
                'created_at' => $department->created_at,
                'updated_by' => $department->updatedBy,
                'updated_at' => $department->updated_at,

                'permissions' => [
                    'create' => $currentUser->can('create', $department),
                    'edit' => $currentUser->can('update', $department),
                    'delete' => $currentUser->can('delete', $department),
                ]
            ];
        });

            return response()->json(['data' => $departments]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Erro ao carregar departamentos'
            ], 500);

        }
    }

    public function index()
    {
        return view('app.departments.index', [
            'title' => 'Departamentos',
        ]);
    }

    public function store(Request $request)
    {
        // Verificando permissão de criação
        $this->authorize('create', Department::class);

        $request->validate(
            [
                'name' => 'required|max:255|unique:departments,name',
                'description' => 'required|min:3|max:1000',
                'icon' => 'nullable|string',
            ],
            [
                'name.required' => 'O nome do departamento é obrigatório',
                'name.unique' => 'O nome do departamento já existe',
                'description.required' => 'A descrição é obrigatória',
                'description.min' => 'A descrição deve ter pelo menos 3 caracteres',
                'description.max' => 'A descrição deve ter no máximo 1000 caracteres',
            ]
        );

        // Criar o departamento
        Department::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
        // return redirect()->route('departments.index')->with('success', 'Departamento creado exitosamente.');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);

        // Verificando permissão de criação
        $this->authorize('update', $department);

        return response()->json($department);
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        // Verificando permissão de alteração
        $this->authorize('update', $department);

        $request->validate([
            'name' => 'required|max:255|unique:departments,name,' . $id,
            'description' => 'required|min:3|max:1000',
            'icon' => 'nullable|string',
            'active' => 'boolean',
        ], [
            'name.required' => 'O nome do departamento é obrigatório',
            'name.unique' => 'O nome do departamento já existe',
            'description.required' => 'A descrição é obrigatória',
            'description.min' => 'A descrição deve ter pelo menos 3 caracteres',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres',
        ]);

        // Atualizando o active de 1 e 0 para true e false, respectivamente
        $request->merge([
            'active' => $request->has('active') ? true : false,
        ]);

        $department->update([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'active' => $request->has('active') ? $request->active : $department->active,
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Departamento atualizado com sucesso.']);
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        // Verificando permissão de exclusão
        $this->authorize('delete', $department);

        $department->delete();

        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $department = Department::findOrFail($id);

        // Verificando permissão de alteração
        $this->authorize('update', $department);

        $department->active = !$department->active;
        $department->updated_by = auth()->id();
        $department->save();

        return response()->json(['success' => true, 'active' => $department->active]);
    }
}
