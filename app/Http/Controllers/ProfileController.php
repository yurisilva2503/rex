<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('app.profile', [
            'title' => 'Profile'
        ]);
    }

    public function profileForm(Request $request)
    {
        $request->validate(
            [
                'name' => ['required', 'min:3', 'max:255'],

                // só valida senha atual se estiver tentando trocar a senha
                'current_password' => ['min:8', 'max:255'],

                // nova senha é opcional
                'new_password' => ['nullable', 'min:8', 'max:255', 'confirmed'],

                // confirmação só é obrigatória se nova senha existir
                'new_password_confirmation' => ['required_with:new_password', 'same:new_password'],
            ],
            [
                'name.required' => 'Preencha o campo nome',
                'name.min' => 'O nome precisa ter pelo menos 3 caracteres',
                'name.max' => 'O nome pode ter no.maxcdn 255 caracteres',
                'current_password.required' => 'Informe a senha atual para atualizar os dados',
                'current_password.min' => 'A senha precisa ter pelo menos 8 caracteres',
                'current_password.max' => 'A senha pode ter no.maxcdn 255 caracteres',
                'new_password.confirmed' => 'As novas senhas não conferem',
                'new_password.required' => 'Preencha o campo nova senha',
                'new_password.min' => 'A senha precisa ter pelo menos 8 caracteres',
                'new_password.max' => 'A senha pode ter no.maxcdn 255 caracteres',
                'new_password_confirmation.required_with' => 'Preencha o campo confirmar senha',
                'new_password_confirmation.same' => 'As novas senhas não conferem'
            ]
        );

        // Verificando se a senha do usuário atual esta correta
        $user = auth()->user();
        $userDb = User::where('email', $user->email)->first();
        if (!password_verify($request->current_password, $userDb->password)) {
            return back()->with([
                'invalid_update_profile' => 'Senha atual incorreta'
            ]);
        }

        // Atualizando os dados
        $user->name = $request->name;
        if ($request->new_password) {
            $user->password = password_hash($request->new_password, PASSWORD_DEFAULT);
        }
        $user->updated_at = Carbon::now();

        // Salvando
        $user->save();

        // Retornando
        return redirect()->route('profile')->with([
            'success_update_profile' => 'Dados atualizados com sucesso'
        ]);
    }


    public function deleteProfileForm(Request $request)
    {
        // Se a opção de deletar for ativada
        if ($request->delete) {

            // Buscando o usuário
            $user = Auth::user();
            $userDB = User::where('email', $user->email)->first();

            // Verificando se o usuário existe
            if (!$userDB) {
                return back()->with([
                    'invalid_delete_profile' => 'Usuário não encontrado'
                ]);
            }

            // Deletando com SOFT DELETE porque foi ativado la no model
            $userDB->delete();

            // Se eu quisesse forçar o hard delete, eu poderia usar o forceDelete
            // $userDB->forceDelete();

            return redirect(route('login'))->with([
                'success_message' => 'Usuário excluido com sucesso'
            ]);
        }

        return view('app.profile', [
            'title' => 'Profile'
        ]);
    }
}
