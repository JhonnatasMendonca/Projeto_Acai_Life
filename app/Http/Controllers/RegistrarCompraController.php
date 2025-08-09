<?php

namespace App\Http\Controllers;

use App\Models\RegistrarCompra;
use App\Models\ItensCompra;
use App\Models\Produto;
use App\Models\Insumo;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RegistrarCompraController
{
    public function index()
    {
        $insumos = Insumo::all();
        $produtos = Produto::all();
        // $compras = RegistrarCompra::with('itensCompra.insumo', 'itensCompra.produto')->get();
        return view('controle_financeiro.historico_compras.index', compact('insumos', 'produtos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_fornecedor' => 'required|string|max:255',
            'data_compra' => 'required|date',
            'forma_pagamento' => 'nullable|string|max:50',
            'valor_total' => 'required|numeric|min:0',

            'produto' => 'nullable|array',
            'produto.*' => 'nullable|exists:produtos,id',
            'quantidades_produto' => 'nullable|array',
            'quantidades_produto.*' => 'nullable|numeric|min:0.01',
            'preços_produto' => 'nullable|array',
            'preços_produto.*' => 'nullable|numeric|min:0',

            'insumos' => 'nullable|array',
            'insumos.*' => 'nullable|exists:insumos,id',
            'quantidades_insumo' => 'nullable|array',
            'quantidades_insumo.*' => 'nullable|numeric|min:0.01',
            'preços_insumo' => 'nullable|array',
            'preços_insumo.*' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $compra = RegistrarCompra::create([
                'nome_fornecedor' => $request->nome_fornecedor,
                'data_compra' => $request->data_compra,
                'forma_pagamento' => $request->forma_pagamento ?? null,
                'valor_total' => $request->valor_total,
            ]);

            if ($request->has('produto')) {
                foreach ($request->produto as $index => $produtoId) {
                    if ($produtoId) {
                        $quantidade = $request->quantidades_produto[$index];
                        $preco = $request->preços_produto[$index];

                        ItensCompra::create([
                            'registrar_compra_id' => $compra->id,
                            'produto_id' => $produtoId,
                            'insumo_id' => null,
                            'quantidade' => $quantidade,
                            'preco_custo' => $preco,
                            'subtotal' => $quantidade * $preco,
                        ]);

                        $produto = Produto::find($produtoId);
                        $produto->estoque_inicial += $quantidade;
                        $produto->preco_custo = $preco;
                        $produto->save();
                    }
                }
            }

            if ($request->has('insumos')) {
                foreach ($request->insumos as $index => $insumoId) {
                    if ($insumoId) {
                        $quantidade = $request->quantidades_insumo[$index];
                        $preco = $request->preços_insumo[$index];

                        ItensCompra::create([
                            'registrar_compra_id' => $compra->id,
                            'produto_id' => null,
                            'insumo_id' => $insumoId,
                            'quantidade' => $quantidade,
                            'preco_custo' => $preco,
                            'subtotal' => $quantidade * $preco,
                        ]);

                        $insumo = Insumo::find($insumoId);
                        $insumo->estoque_insumo += $quantidade;
                        $insumo->preco_custo = $preco;
                        $insumo->save();
                    }
                }
            }

            $this->registrarSaidaCaixa($request->valor_total, 'Compra registrada ID: ' . $compra->id);

            DB::commit();

            Log::info('Compra registrada com sucesso.', [
                'compra_id' => $compra->id,
                'fornecedor' => $compra->nome_fornecedor,
                'valor_total' => $compra->valor_total,
                'usuario_id' => optional(Auth::user())->id,
            ]);

            return redirect()->back()->with('mensagem', 'Compra registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar compra.', [
                'erro' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'usuario_id' => optional(Auth::user())->id,
            ]);

            return redirect()->back()->with('erro', 'Erro ao registrar compra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $compra = RegistrarCompra::with('itensCompra.insumo', 'itensCompra.produto')->find($id);

        if (!$compra) {
            return redirect()->back()->with('erro', 'Compra não encontrada.');
        }

        // return view('sua_view_detalhes_compra', compact('compra'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->back()->with('erro', 'Atualização de compra ainda não implementada.');
    }

    public function destroy($id)
    {
        $compra = RegistrarCompra::find($id);

        if (!$compra) {
            return redirect()->back()->with('erro', 'Compra não encontrada.');
        }

        $compra->delete();

        return redirect()->back()->with('mensagem', 'Compra deletada com sucesso!');
    }

    private function registrarSaidaCaixa($valor, $descricao)
    {
        $caixa = Caixa::where('status', 'aberto')->first();

        if (!$caixa) {
            throw new \Exception('Nenhum caixa aberto para registrar movimentação.');
        }

        CaixaMovimentacao::create([
            'caixa_id' => $caixa->id,
            'tipo' => 'saida',
            'descricao' => $descricao,
            'valor' => $valor,
            'usuario_id' => optional(Auth::user())->id,
        ]);

        $caixa->total_saidas += $valor;
        $caixa->saldo_final -= $valor;
        $caixa->save();
    }

    public function consultaDados(Request $request)
    {
        try {
            $compras = RegistrarCompra::all();

            return response()->json($compras);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar dados das compras: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'error' => 'Erro ao consultar dados das compras.'
            ], 500);
        }
    }
}
