<?php

namespace App\Http\Controllers\PainelAdm;

use App\Models\Permissao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissaoController
{
    // Listar todas as permissões
    public function index()
    {
        // return response()->json(Permissao::all());
        return view('painel_adm.permissao.index');
    }

    function create()
    {
        return view('painel_adm.permissao.create');
    }

    function edit($id)
    {
        $permissao = Permissao::find($id);

        if (!$permissao) {
            return redirect()->back()->with('erro', 'Permissão não encontrado.');
        }

        return view('painel_adm.permissao.edit', compact('permissao'));
    }

    // Cadastrar uma nova permissão
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|unique:permissoes',
                'descricao' => 'nullable|string',
            ]);

            Permissao::create([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
            ]);

            return redirect()->back()->with('mensagem', 'Permissão criada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar permissão: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()->with('erro', 'Erro ao criar permissão.');
        }
    }

    // Exibir uma permissão específica
    // public function show($id)
    // {
    //     $permissao = Permissao::find($id);

    //     if (!$permissao) {
    //         return response()->json(['message' => 'Permissão não encontrada.'], 404);
    //     }

    //     return response()->json($permissao);
    // }

    // Atualizar uma permissão
    public function update(Request $request, $id)
    {
        try {
            $permissao = Permissao::find($id);

            if (!$permissao) {
                return redirect()->back()->with('erro', 'Permissão não encontrada.');
            }

            $request->validate([
                'nome' => 'required|unique:permissoes,nome,' . $id,
                'descricao' => 'nullable|string',
            ]);

            $permissao->update([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
            ]);

            return redirect()->back()->with('mensagem', 'Permissão atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar permissão: ' . $e->getMessage(), [
                'exception' => $e,
                'permissao_id' => $id,
            ]);
            return redirect()->back()->with('erro', 'Erro ao atualizar permissão.');
        }
    }

    // Deletar uma permissão
    public function destroy($id)
    {
        try {
            $permissao = Permissao::find($id);

            if (!$permissao) {
                return redirect()->back()->with('erro', 'Permissão não encontrada.');
            }

            $permissao->delete();

            return redirect()->back()->with('mensagem', 'Permissão removida com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao remover permissão: ' . $e->getMessage(), [
                'exception' => $e,
                'permissao_id' => $id,
            ]);
            return redirect()->back()->with('erro', 'Erro ao remover permissão.');
        }
    }

    public function consultaDados(Request $request)
    {
        try {
            $permissoes = Permissao::all();

            return response()->json($permissoes);

        } catch (\Exception $e) {
            Log::error('Erro ao consultar dados da permissão: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'error' => 'Erro ao consultar dados da permissão.'
            ], 500);
        }
    }
}
