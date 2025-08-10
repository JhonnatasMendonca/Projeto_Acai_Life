<?php

namespace App\Http\Controllers\PainelAdm;

use App\Models\Insumo;
use Illuminate\Http\Request;
use App\Models\Produto;
use Illuminate\Support\Facades\Log;

class ProdutoController
{
    // Listar todos os produtos com insumos
    public function index()
    {
        return response()->json(Produto::with('insumos')->get());
    }

    // Cadastrar produto com insumos (AJUSTADO)
    public function store(Request $request)
    {
        try {
            // Regras de validação dinâmicas
            // Se não vier 'usa_insumo' no request, define como false
            if (!$request->has('usa_insumo')) {
                $request->merge(['usa_insumo' => false]);
                $request->merge(['estoque_inicial' => 0]);
                $request->merge(['quantidades' => [1]]);
            }

            $rules = [
                'nome_produto' => 'required|max:45',
                'categoria' => 'required|max:45',
                'descricao' => 'nullable|max:255',
                'preco_venda' => 'required|numeric|min:0',
                'preco_custo' => 'required|numeric|min:0',
                'usa_insumo' => 'required|boolean',
            ];

            if ($request->boolean('usa_insumo')) {
                $rules['insumos'] = 'required|array|min:1'
                ;
                $rules['gramaturas'] = 'required|array|min:1';
                $rules['estoque_inicial'] = 'nullable|integer|min:0';
                $rules['alerta_estoque'] = 'nullable|integer|min:0';
                $rules['quantidades'] = 'nullable|array|min:1';
            } else {
                $rules['estoque_inicial'] = 'required|integer|min:0';
                $rules['alerta_estoque'] = 'required|integer|min:0';
            }

            $request->validate($rules);

            Log::info('Iniciando cadastro de produto', ['request' => $request->all()]);

            $produto = Produto::create([
                'nome_produto' => $request->input('nome_produto'),
                'categoria' => $request->input('categoria'),
                'descricao' => $request->input('descricao', null),
                'preco_venda' => $request->input('preco_venda'),
                'preco_custo' => $request->input('preco_custo'),
                'estoque_inicial' => $request->input('estoque_inicial'),
                'alerta_estoque' => $request->input('alerta_estoque'),
                'usa_insumo' => $request->boolean('usa_insumo'),
            ]);

            if ($request->boolean('usa_insumo')) {
                $insumos = $request->input('insumos');
                $quantidades = $request->input('quantidades');
                $gramaturas = $request->input('gramaturas');

                for ($i = 0; $i < count($insumos); $i++) {
                    $produto->insumos()->attach($insumos[$i], [
                        'quantidade' => $quantidades[$i],
                        'gramatura' => $gramaturas[$i],
                        'unidade_medida' => $request->input("unidades_medida.$i", 'g')
                    ]);
                }
            }

            Log::info('Produto cadastrado com sucesso', ['produto_id' => $produto->id]);
            return redirect()->back()->with('mensagem', 'Produto cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar produto', [
                'error' => $e->getMessage(),
                // 'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()->with('erro', 'Erro ao cadastrar produto');
        }
    }

    public function edit($id)
    {
        $produto = Produto::with('insumos')->find($id);

        if (!$produto) {
            return response()->json(['error' => 'Produto não encontrado.'], 404);
        }

        $insumos = Insumo::all();

        return view('painel_adm.controle_estoque.produto.edit', compact('produto', 'insumos'));
    }

    // Editar produto com insumos
    public function update(Request $request, $id)
    {
        try {
            // Regras de validação dinâmicas
            if (!$request->has('usa_insumo')) {
                $request->merge(['usa_insumo' => false]);
                // $request->merge(['estoque_inicial' => 0]);
                $request->merge(['quantidades' => [1]]);
            }

            $rules = [
                'nome_produto' => 'sometimes|required|max:45',
                'categoria' => 'sometimes|required|max:45',
                'descricao' => 'nullable|max:255',
                'preco_venda' => 'sometimes|required|numeric|min:0',
                'preco_custo' => 'sometimes|required|numeric|min:0',
                'usa_insumo' => 'sometimes|required|boolean',
            ];

            if ($request->boolean('usa_insumo')) {
                $rules['insumos'] = 'sometimes|required|array|min:1';
                $rules['gramaturas'] = 'sometimes|required|array|min:1';
                $rules['estoque_inicial'] = 'nullable|integer|min:0';
                $rules['alerta_estoque'] = 'nullable|integer|min:0';
                $rules['quantidades'] = 'nullable|array|min:1';
            } else {
                $rules['estoque_inicial'] = 'sometimes|required|integer|min:0';
                $rules['alerta_estoque'] = 'sometimes|required|integer|min:0';
            }

            $request->validate($rules);

            Log::info('Iniciando atualização de produto', ['produto_id' => $id, 'request' => $request->all()]);

            $produto = Produto::find($id);

            if (!$produto) {
                return response()->json(['mensagem' => 'Produto não encontrado.'], 404);
            }

            $produto->update($request->only([
                'nome_produto',
                'categoria',
                'descricao',
                'preco_venda',
                'preco_custo',
                'estoque_inicial',
                'alerta_estoque',
                'usa_insumo',
            ]));

            if ($request->boolean('usa_insumo')) {
                $insumos = $request->input('insumos');
                $quantidades = $request->input('quantidades');
                $gramaturas = $request->input('gramaturas');

                $syncData = [];
                for ($i = 0; $i < count($insumos); $i++) {
                    $syncData[$insumos[$i]] = [
                        'quantidade' => $quantidades[$i],
                        'gramatura' => $gramaturas[$i],
                        'unidade_medida' => $request->input("unidades_medida.$i", 'g')
                    ];
                }
                $produto->insumos()->sync($syncData);
            } else {
                $produto->insumos()->detach();
            }

            Log::info('Produto atualizado com sucesso', ['produto_id' => $produto->id]);
            return redirect()->back()->with('mensagem', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produto', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return redirect()->back()->with('erro', 'Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    // Deletar produto
    public function destroy($id)
    {
        $produto = Produto::find($id);

        if (!$produto) {
            return redirect()->back()->with('erro', 'Produto não encontrado.');
        }

        // Define o produto como inativo
        $produto->ativo = false;
        $produto->save();

        return redirect()->back()->with('mensagem', 'Produto excluído com sucesso!');
    }
}
