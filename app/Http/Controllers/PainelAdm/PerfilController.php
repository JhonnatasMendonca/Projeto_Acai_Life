<?php


namespace App\Http\Controllers\PainelAdm;

use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerfilController
{
    public function index()
    {
        // return Perfil::with('permissoes')->get();
        $permissoes = Permissao::all();
        return view('painel_adm.perfil.index')->with('permissoes', $permissoes);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'nome' => 'required|unique:perfis',
                'descricao' => 'nullable',
                'permissoes' => 'array',
                'permissoes.*' => 'exists:permissoes,id'
            ]);


            $perfil = Perfil::create([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
            ]);

            if ($request->has('permissoes')) {
                $perfil->permissoes()->sync($request->permissoes);
            }

            return redirect()->back()->with('mensagem', 'Perfil criado com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao criar perfil: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()->withInput()->with('erro', 'Erro ao criar perfil.');
        }
    }

    function create()
    {
        return view('painel_adm.perfil.create');
    }

    public function edit($id)
    {
        try {
            $perfil = Perfil::with('permissoes')->findOrFail($id);
            $permissoes = Permissao::all();
            return view('painel_adm.perfil.edit', compact('perfil', 'permissoes'));
        } catch (\Exception $e) {
            Log::error('Erro ao editar perfil: ' . $e->getMessage(), [
            'exception' => $e
            ]);
            return redirect()->back()->with('erro', 'Erro ao carregar dados do perfil.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $perfil = Perfil::findOrFail($id);

            $request->validate([
                'nome' => 'required|unique:perfis,nome,' . $id,
                'descricao' => 'nullable',
                'permissoes' => 'array',
                'permissoes.*' => 'exists:permissoes,id'
            ]);

            $perfil->update([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
            ]);

            $perfil->permissoes()->sync($request->permissoes ?? []);

            return redirect()->back()->with('mensagem', 'Perfil atualizado com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar perfil: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()->withInput()->with('erro', 'Erro ao atualizar perfil.');
        }
    }

    public function destroy($id)
    {
        try {
            $perfil = Perfil::findOrFail($id);
            $perfil->delete();
            return redirect()->back()->with('mensagem', 'Perfil excluído com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir perfil: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()->with('erro', 'Erro ao excluir perfil.');
        }
    }

    public function consultaDados(Request $request)
    {
        try {
            $perfil = Perfil::with('permissoes')->get();

            return response()->json($perfil);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar dados do perfil: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'error' => 'Erro ao consultar dados do perfil.'
            ], 500);
        }
    }
}
