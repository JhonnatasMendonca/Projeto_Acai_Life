<?php

namespace App\Http\Controllers\PainelAdm;

use Illuminate\Http\Request;
use App\Models\Insumo;
use Illuminate\Support\Facades\Log;

class InsumoController
{
    // Listar todos os insumos (index)
    public function index()
    {
        return response()->json(Insumo::all());
    }

    // Armazenar novo insumo (store)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nome_insumo' => 'required|max:45',
                'categoria_insumo' => 'required|max:45',
                'descricao_insumo' => 'nullable|max:255',
                'preco_custo' => 'required|numeric|min:0',
                'estoque_insumo' => 'required|integer|min:0',
                'unidade_medida' => 'required|max:10',
                'peso_total' => 'nullable|numeric|min:0',
            ]);

            Insumo::create([
                'nome_insumo' => $request->input('nome_insumo'),
                'categoria_insumo' => $request->input('categoria_insumo'),
                'descricao_insumo' => $request->input('descricao_insumo', null),
                'preco_custo' => $request->input('preco_custo'),
                'estoque_insumo' => $request->input('estoque_insumo'),
                'unidade_medida' => $request->input('unidade_medida'),
                'peso_total' => $request->input('peso_total', null),
            ]);

            return redirect()->back()->with('mensagem', 'Insumo adicionado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar insumo: ' . $e->getMessage());

            return redirect()->back()->with('erro', 'Ocorreu um erro ao adicionar o insumo. Tente novamente.');
        }
    }

    // Exibir insumo específico (show)
    public function show($id)
    {
        $insumo = Insumo::find($id);

        if (!$insumo) {
            return response()->json(['mensagem' => 'Insumo não encontrado.'], 404);
        }

        return response()->json($insumo);
    }

    public function edit($id)
    {
        $insumo = Insumo::find($id);

        if (!$insumo) {
            return redirect()->back()->with('erro', 'Insumo não encontrado.');
        }

        return view('painel_adm.controle_estoque.insumo.edit', compact('insumo'));
    }

    // Atualizar insumo existente (update)
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nome_insumo' => 'sometimes|required|max:45',
                'categoria_insumo' => 'sometimes|required|max:45',
                'descricao_insumo' => 'nullable|max:255',
                'preco_custo' => 'sometimes|required|numeric|min:0',
                'estoque_insumo' => 'sometimes|required|integer|min:0',
                'unidade_medida' => 'sometimes|required|max:10',
                'peso_total' => 'nullable|numeric|min:0',
            ]);

            $insumo = Insumo::find($id);

            if (!$insumo) {
                return redirect()->back()->with('erro', 'Insumo não encontrado.');
            }

            $insumo->update([
                'nome_insumo' => $request->input('nome_insumo', $insumo->nome_insumo),
                'categoria_insumo' => $request->input('categoria_insumo', $insumo->categoria_insumo),
                'descricao_insumo' => $request->input('descricao_insumo', $insumo->descricao_insumo),
                'preco_custo' => $request->input('preco_custo', $insumo->preco_custo),
                'estoque_insumo' => $request->input('estoque_insumo', $insumo->estoque_insumo),
                'unidade_medida' => $request->input('unidade_medida', $insumo->unidade_medida),
                'peso_total' => $request->input('peso_total', $insumo->peso_total),
            ]);

            return redirect()->back()->with('mensagem', 'Insumo atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar insumo: ' . $e->getMessage());

            return redirect()->back()->with('erro', 'Ocorreu um erro ao atualizar o insumo. Tente novamente.');
        }
    }

    // Deletar insumo (destroy)
    public function destroy($id)
    {
        $insumo = Insumo::find($id);

        if (!$insumo) {
            return redirect()->back()->with('erro', 'Insumo não encontrado.');
        }

        $insumo->delete();

        return redirect()->back()->with('mensagem', 'Insumo deletado com sucesso!');
    }
}
