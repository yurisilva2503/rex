<?php

namespace App\Http\Controllers;

use App\Mail\NewUserConfirmation;
use App\Mail\ResetUserPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use function Laravel\Prompts\pause;

class AuthController extends Controller
{

    public function login()
    {
        return view('home.login', [
            'title' => 'Login'
        ]);
    }

    public function loginForm(Request $request): RedirectResponse
    {
        // Validação do form
        $credentials = $request->validate(
            [
                'email' => ['required', 'email', 'min:3', 'max:255'],
                'password' => ['required', 'min:8', 'max:255']
            ],
            [
                'email.required' => 'Preencha o campo email',
                'email.email' => 'Preencha um email válido',
                'email.min' => 'O email precisa ter pelo menos 3 caracteres',
                'email.max' => 'O email pode ter no máximo 255 caracteres',
                'password.required' => 'Preencha o campo senha',
                'password.min' => 'A senha precisa ter pelo menos 8 caracteres',
                'password.max' => 'A senha pode ter no máximo 255 caracteres',

            ]
        );
        // Login tradicional do Laravel
        // Attempt pede quais são as credenciais. Nesse caso assumiria que tem um email e uma password

        // if (Auth::attempt($credentials)) { // Esse attempt vai verificar se o usuário existe e se a password é valida. Se o usuário existir e a password for valida, ele vai ser logado
        //     $request->session()->regenerate(); // Isso faria com que o token de sessão fosse atualizado
        // } else {
        //     return back()->withErrors([
        //         'email' => 'Email ou senha incorretos'
        //     ])->onlyInput('email');
        // }
        $user = User::where('email', '=', $credentials['email'])
            ->where('active', '=', true)
            ->where(function ($query) {
                $query->whereNull('blocked_until')
                    ->orWhere('blocked_until', '<=', now());
            })
            ->whereNotNull('email_verified_at')
            ->whereNull('deleted_at')
            ->first();

        // Verificar se o usuário existe

        if (!$user) {
            return back()->with([
                'invalid_login' => 'Email ou senha incorretos'
            ])->onlyInput('email');
        }

        // Verificar se a password é válida e pertence a ele

        if (!password_verify($credentials['password'], $user->password)) {
            return back()->with([
                'invalid_login' => 'Email ou senha incorretos'
            ])->onlyInput('email');
        }

        // Atualizar o último login (last_login)

        $user->last_login = Carbon::now();
        $user->blocked_until = null;
        $user->save();

        // Login
        $request->session()->regenerate();
        Auth::login($user);

        // Redirecionar para home
        // Se existir uma url que mandou a requisição pela rota, ele vai direto para ela após o login
        return redirect()->intended(route('home'));
    }

    public function register()
    {
        return view('home.register', [
            'title' => 'Registro'
        ]);
    }

    // public function registerForm(Request $request)
    // {
    //     $credentials = $request->validate(
    //         [
    //             'name' => ['required', 'min:3', 'max:255'],
    //             'email' => ['required', 'email', 'min:3', 'max:255', 'unique:users,email'], // Esse unique:users vai verificar se o email ja foi cadastrado no banco, então tem que referenciar o model User
    //             'password' => ['required', 'min:8', 'max:255'],
    //             'password_confirmation' => ['required', 'same:password'] // Esse same:password vai verificar se a password e a password_confirmation forem iguais
    //         ],
    //         [
    //             'name.required' => 'Preencha o campo nome',
    //             'name.min' => 'O nome precisa ter pelo menos 3 caracteres',
    //             'name.max' => 'O nome pode ter no.maxcdn 255 caracteres',
    //             'email.required' => 'Preencha o campo email',
    //             'email.email' => 'Preencha um email válido',
    //             'email.min' => 'O email precisa ter pelo menos 3 caracteres',
    //             'email.max' => 'O email pode ter no máximo 255 caracteres',
    //             'email.unique' => 'O email informado ja foi cadastrado',
    //             'password.required' => 'Preencha o campo senha',
    //             'password.min' => 'A senha precisa ter pelo menos 8 caracteres',
    //             'password.max' => 'A senha pode ter no máximo 255 caracteres',
    //             'password_confirmation.required' => 'Preencha o campo confirmar senha',
    //             'password_confirmation.same' => 'As senhas precisam ser iguais'

    //         ]
    //     );

    //     // Criar o usuário
    //     $user = new User();
    //     $user->name = $credentials['name'];
    //     $user->email = $credentials['email'];
    //     $user->role = 'user';
    //     $user->permissions = 'user';
    //     $user->password = password_hash($credentials['password'], PASSWORD_DEFAULT);
    //     $user->token = Str::random(32);

    //     // Gerar o link de confirmação
    //     $confirmationLink = route('newUserConfirmation', ['token' => $user->token]);
    //     $result = Mail::to($user->email)->send(new NewUserConfirmation($user->email, $confirmationLink));

    //     // Verificar se o email foi enviado
    //     if (!$result) {
    //         return back()->withInput()->with([
    //             'invalid_register' => 'Ocorreu um erro ao enviar o email de confirmação'
    //         ]);
    //     }

    //     // Salvar o usuário na base de dados
    //     $user->save();

    //     return view('auth.email_sent', [
    //         'email' => $user->email,
    //         'title' => 'Email enviado'
    //     ]);

    //     // Auth::login($user);
    //     // return redirect(route('home'));
    // }

    public function newUserConfirmation($token)
    {
        $user = User::where('token', $token)->where('email_verified_at', null)->first();

        if (!$user) {
            return abort(404);
        }

        return redirect(route('resetPassword', ['token' => $token]));
    }

    public function forgotPassword()
    {
        return view('home.forgot_password', [
            'title' => 'Esqueci minha senha'
        ]);
    }

    public function forgotPasswordForm(Request $request)
    {
        $request->validate(
            [
                'email' => ['required', 'email', 'min:3', 'max:255']
            ],
            [
                'email.required' => 'Preencha o campo email',
                'email.email' => 'Preencha um email válido',
                'email.min' => 'O email precisa ter pelo menos 3 caracteres',
                'email.max' => 'O email pode ter no.maxcdn 255 caracteres',
            ]

        );

        // Mensagem padrão para evitar falhas de segurança
        $msg = "Se o email existir, será enviado um <strong>link de redefinição de senha</strong> para o mesmo.";

        // Verificando se o email existe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            sleep(5);
            return back()->withInput()->with([
                'msg_forgot_password' => $msg
            ]);
        }

        // Gerando o token
        $user->token = Str::random(32);
        $user->save();

        // Gerando o link
        $link = route('resetPassword', ['token' => $user->token]);

        // Enviando o email
        $result = Mail::to($user->email)->send(new ResetUserPassword($user->email, $link));

        // Verificando se o email foi enviado
        if (!$result) {
            sleep(4);
            return back()->withInput()->with([
                'msg_forgot_password' => $msg
            ]);
        }

        // Redirecionando
        return back()->withInput()->with([
            'msg_forgot_password' => $msg
        ]);
    }

    public function resetPassword($token)
    {
        // Verificando se o token é do usuário mesmo
        $user = User::where('token', $token)->first();

        if (!$user) {
            return abort(404);
        }

        return view('home.reset_password', [
            'title' => 'Redefinir senha',
            'token' => $token,
            'email' => $user->email
        ]);
    }

    public function resetPasswordForm(Request $request)
    {
        $request->validate(
            [
                'email' => ['required', 'email', 'min:3', 'max:255'],
                'password' => ['required', 'min:6', 'max:255'],
                'password_confirm' => ['required', 'same:password'],
            ],
            [
                'email.required' => 'Preencha o campo email',
                'email.email' => 'Preencha um email válido',
                'email.min' => 'O email precisa ter pelo menos 3 caracteres',
                'email.max' => 'O email pode ter no.maxcdn 255 caracteres',
                'password.required' => 'Preencha o campo senha',
                'password.min' => 'A senha precisa ter pelo menos 6 caracteres',
                'password.max' => 'A senha pode ter no.maxcdn 255 caracteres',
                'password_confirm.required' => 'Preencha o campo confirmar senha',
                'password_confirm.same' => 'As senhas não conferem',
            ]
        );

        // Checando se o token pertence ao usuário
        $user = User::where('email', $request->email)->where('token', $request->token)->first();

        if (!$user) {
            return redirect(route('login'))->with([
                'invalid_login' => 'O token informado para mudança de senha não é válido. Por favor, tente novamente.'
            ]);
        }

        // Atualizando a senha
        $user->password = password_hash($request->password, PASSWORD_DEFAULT);
        $user->token = null;
        $user->updated_at = Carbon::now();
        $user->email_verified_at == null ? $user->email_verified_at = Carbon::now() : $user->email_verified_at;
        $user->last_login = Carbon::now();
        $user->save();

        // Logando usuário
        Auth::login($user);
        return redirect(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        // Logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('login'));
    }
}
