<?php

namespace App\Http\Controllers\PainelAdm;

use App\Models\Perfil;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsuarioController
{
    // Listar todos os usuários - GET /usuarios
    public function index()
    {
        $perfis = Perfil::all();
        return view('painel_adm.usuario.index', compact('perfis'));
    }

    // Exibir formulário para criar usuário - GET /usuarios/create
    public function create()
    {
        return view('painel_adm.usuario.create');
    }

    // Armazenar um novo usuário - POST /usuarios
    public function store(Request $request)
    {
        try {
            Log::info('Iniciando criação de usuário', ['request' => $request->all()]);

            $request->validate([
                'nome_usuario' => 'required|max:45',
                'senha_usuario' => 'required|min:8|max:100',
                'login_usuario' => 'required|max:45|unique:usuarios,login_usuario',
                'status_usuario' => 'required|boolean',
                'perfil_id' => 'required|exists:perfis,id',
            ]);

            Log::info('Validação realizada com sucesso');

            $usuario = Usuario::create([
                'nome_usuario' => $request->input('nome_usuario'),
                'senha_usuario' => Hash::make($request->input('senha_usuario')),
                'login_usuario' => $request->input('login_usuario'),
                'status_usuario' => $request->input('status_usuario'),
                'perfil_id' => $request->input('perfil_id'),
            ]);

            Log::info('Usuário criado com sucesso', ['usuario_id' => $usuario->id]);

            return redirect()->back()->with('mensagem', 'Usuário cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erro ao cadastrar usuário.');
        }
    }

    // Exibir formulário para editar usuário - GET /usuarios/{id}/edit
    public function edit($id)
    {
        $usuario = Usuario::find($id);
        $perfis = Perfil::all();

        if (!$usuario) {
            return redirect()->back()->with('erro', 'Usuário não encontrado.');
        }

        return view('painel_adm.usuario.edit', compact('usuario', 'perfis'));
    }

    // Atualizar um usuário - PUT/PATCH /usuarios/{id}
    public function update(Request $request, $id)
    {
        try {
            Log::info('Iniciando atualização de usuário', ['usuario_id' => $id, 'request' => $request->all()]);

            $usuario = Usuario::find($id);

            if (!$usuario) {
                Log::warning('Usuário não encontrado', ['usuario_id' => $id]);
                return response()->json(['mensagem' => 'Usuário não encontrado.'], 404);
            }

            $request->validate([
                'nome_usuario' => 'sometimes|required|max:45',
                'login_usuario' => 'sometimes|required|max:45|unique:usuarios,login_usuario,' . $id,
                'status_usuario' => 'sometimes|required|boolean',
                'perfil_id' => 'sometimes|required|exists:perfis,id',
            ]);

            Log::info('Validação realizada com sucesso');

            $usuario->nome_usuario = $request->input('nome_usuario', $usuario->nome_usuario);
            $usuario->login_usuario = $request->input('login_usuario', $usuario->login_usuario);
            $usuario->status_usuario = $request->input('status_usuario', $usuario->status_usuario);
            $usuario->perfil_id = $request->input('perfil_id', $usuario->perfil_id);

            $usuario->save();

            Log::info('Usuário atualizado com sucesso', ['usuario_id' => $usuario->id]);

            return redirect()->back()->with('mensagem', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erro ao atualizar usuário.');
        }
    }

    // Deletar um usuário - DELETE /usuarios/{id}
    public function destroy($id)
    {
        try {
            Log::info('Iniciando exclusão de usuário', ['usuario_id' => $id]);

            $usuario = Usuario::find($id);

            if (!$usuario) {
                Log::warning('Usuário não encontrado', ['usuario_id' => $id]);
                return response()->json(['mensagem' => 'Usuário não encontrado.'], 404);
            }

            $usuario->delete();

            Log::info('Usuário deletado com sucesso', ['usuario_id' => $id]);

            return redirect()->back()->with('mensagem', 'Usuário deletado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao deletar usuário', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erro ao deletar usuário.');
        }
    }

    public function atualizarSenha(Request $request)
    {
        // dd($request->all());
        Log::info('Iniciando atualização de senha', ['request' => $request->all()]);
        try {
            $request->validate([
                'login_usuario' => 'required|string',
                'senha_usuario' => 'required|min:8|max:100',
            ]);

            Log::info('Validação realizada com sucesso');

            $usuario = Usuario::where('login_usuario', $request->login_usuario)->first();

            if (!$usuario) {
                Log::warning('Usuário não encontrado', ['login_usuario' => $request->login_usuario]);
                return redirect()->back()->with('error', 'Usuário não encontrado.');
            }

            $usuario->senha_usuario = Hash::make($request->input('senha_usuario'));
            $usuario->save();

            Log::info('Senha atualizada com sucesso', ['usuario_id' => $usuario->id]);

            return redirect()->route('login')->with('mensagem', 'Senha atualizada com sucesso. Por favor, faça login novamente.');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar senha', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao tentar atualizar a senha.');
        }
    }



    // Retornar todos os usuários em JSON - GET /usuarios/dados
    public function consultaDados(Request $request)
    {
        try {
            $usuarios = Usuario::with('perfil')->get();

            return response()->json($usuarios);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar dados do usuário: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'error' => 'Erro ao consultar dados do usuário.'
            ], 500);
        }
    }
}
