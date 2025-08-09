<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            Log::info('Tentativa de login', ['login_usuario' => $request->login_usuario]);

            $request->validate([
                'login_usuario' => 'required|string',
                'senha_usuario' => 'required|string',
            ]);

            $usuario = Usuario::where('login_usuario', $request->login_usuario)->first();

            if (!$usuario || !Hash::check($request->senha_usuario, $usuario->senha_usuario)) {
                // Log::warning('Falha no login', ['login_usuario' => $request->login_usuario]);
                return redirect()->back()->withErrors(['login' => 'Usuário ou senha inválidos.']);
            }

            Log::info('Login realizado com sucesso', ['usuario_id' => $usuario->id]);

            // Se você usa session para guardar usuário, por exemplo:
            session(['usuario_id' => $usuario->id]); // usar id, não cpf

            // Se autenticar com o Auth manualmente, exemplo:
            Auth::login($usuario);

            return redirect()->route('home')->with('mensagem', 'Bem-vindo(a) '. $usuario->nome_usuario . '!');
        } catch (\Exception $e) {
            Log::error('Erro ao tentar login', [
                'login_usuario' => $request->login_usuario ?? null,
                'exception' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao tentar realizar o login.');
        }
    }


    public function logout()
    {
        // Lógica para deslogar o usuário, como limpar a sessão
        Auth::logout();

        // Redirecionar para a página de login
        return redirect()->route('login')->with('success', 'Logout realizado com sucesso!');
    }

    public function esqueciSenha()
    {
        // Lógica para exibir a página de recuperação de senha
        return view('auth.recuperar_senha');
    }
}
