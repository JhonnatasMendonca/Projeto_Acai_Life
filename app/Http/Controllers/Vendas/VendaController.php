<?php

namespace App\Http\Controllers\Vendas;

use App\Models\Venda;
use App\Models\Produto;
use App\Models\ItemVenda;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendaController
{
    public function index(Request $request)
    {
        $clientes = Cliente::all();
        $produtos = Produto::where('ativo', true)->get();
        $ultimoId = Venda::max('id') ?? 0;
        $proximoId = $ultimoId + 1;

        // return response()->json($vendas);
        return view('vendas.tela_vendas.index', compact('clientes', 'produtos', 'proximoId'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Iniciando registro de venda', ['request' => $request->all()]);

            $data = $request->validate([
                'cliente_id' => 'nullable|exists:cliente,id',
                'descricao' => 'nullable|string',
                'forma_pagamento' => 'required|string',
                'desconto' => 'nullable|numeric|min:0',
                'usuario_id' => 'required|exists:usuarios,id',
                'status' => 'nullable|string',
                'observacao' => 'nullable|string',
                'produtos' => 'required|array|min:1',
                'produtos.*.produto_id' => 'required|exists:produtos,id',
                'produtos.*.quantidade' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            $subtotal = 0;
            $itensVenda = [];

            foreach ($data['produtos'] as $item) {
                $produto = Produto::findOrFail($item['produto_id']);

                // Se o produto usa insumo, ignora o estoque do produto e faz validação/abatimento apenas nos insumos
                if ($data['status'] === 'pago' && !$produto->usa_insumo && $produto->estoque_inicial < $item['quantidade']) {
                    throw new \Exception("Estoque insuficiente para o produto: {$produto->nome}");
                }

                $totalItem = $produto->preco_venda * $item['quantidade'];
                $subtotal += $totalItem;

                $itensVenda[] = [
                    'produto_id' => $produto->id,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $produto->preco_venda,
                    'total_item' => $totalItem,
                    'produto_ref' => $produto,
                ];
            }

            $desconto = $data['desconto'] ?? 0;
            $total = $subtotal - $desconto;

            $venda = Venda::create([
                'cliente_id' => $data['cliente_id'] ?? null,
                'descricao' => $data['descricao'] ?? null,
                'subtotal' => $subtotal,
                'desconto' => $desconto,
                'total' => $total,
                'forma_pagamento' => $data['forma_pagamento'],
                'usuario_id' => $data['usuario_id'],
                'status' => $data['status'] ?? 'finalizada',
                'observacao' => $data['observacao'] ?? null,
            ]);

            foreach ($itensVenda as $item) {
                ItemVenda::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                    'total_item' => $item['total_item'],
                ]);

                if ($data['status'] === 'pago') {
                    $produto = $item['produto_ref'];
                    if ($produto->usa_insumo) {
                        foreach ($produto->insumos as $insumo) {
                            if ($insumo->unidade_medida === 'Unidade') {
                                $quantidade_usada = $insumo->pivot->quantidade * $item['quantidade'];
                                Log::info('Cálculo de insumo por unidade', [
                                    'produto_id' => $produto->id,
                                    'produto_nome' => $produto->nome_produto ?? $produto->nome,
                                    'insumo_id' => $insumo->id,
                                    'insumo_nome' => $insumo->nome_insumo,
                                    'quantidade_usada' => $quantidade_usada,
                                    'estoque_antes' => $insumo->estoque_insumo,
                                    'resultado' => $insumo->estoque_insumo >= $quantidade_usada ? 'sucesso' : 'erro',
                                    'mensagem' => $insumo->estoque_insumo >= $quantidade_usada ? 'Estoque suficiente para abatimento.' : 'Estoque insuficiente para abatimento.'
                                ]);
                                if ($insumo->estoque_insumo < $quantidade_usada) {
                                    throw new \Exception("Estoque insuficiente do insumo: {$insumo->nome}");
                                }
                                $insumo->estoque_insumo -= $quantidade_usada;
                                $insumo->save();
                                Log::info('Estoque de insumo por unidade atualizado com sucesso', [
                                    'insumo_id' => $insumo->id,
                                    'estoque_depois' => $insumo->estoque_insumo
                                ]);
                            } elseif ($insumo->unidade_medida === 'Quilo') {
                                $gramas_por_produto = $insumo->pivot->gramatura; // em gramas
                                $gramas_total = $gramas_por_produto * $item['quantidade'];
                                $peso_total_antes = $insumo->peso_total;
                                $peso_total = $insumo->peso_total - ($gramas_total / 1000); // peso_total em quilos
                                Log::info('Cálculo de insumo por quilo', [
                                    'produto_id' => $produto->id,
                                    'produto_nome' => $produto->nome_produto ?? $produto->nome,
                                    'insumo_id' => $insumo->id,
                                    'insumo_nome' => $insumo->nome,
                                    'gramas_por_produto' => $gramas_por_produto,
                                    'quantidade_vendida' => $item['quantidade'],
                                    'gramas_total' => $gramas_total,
                                    'peso_total_antes' => $peso_total_antes,
                                    'peso_total_calculado' => $peso_total,
                                    'estoque_insumo_antes' => $insumo->estoque_insumo
                                ]);
                                while ($peso_total < 0) {
                                    if ($insumo->estoque_insumo > 1) {
                                        $insumo->estoque_insumo -= 1;
                                        $peso_total += 1;
                                        Log::info('Consumo de 1 unidade de insumo quilo para repor peso_total', [
                                            'insumo_id' => $insumo->id,
                                            'estoque_insumo_atual' => $insumo->estoque_insumo,
                                            'peso_total_atual' => $peso_total
                                        ]);
                                    } else if ($insumo->estoque_insumo == 1 && $peso_total <= 0) {
                                        Log::error('Estoque insuficiente do insumo por quilo', [
                                            'insumo_id' => $insumo->id,
                                            'estoque_insumo' => $insumo->estoque_insumo,
                                            'peso_total' => $peso_total
                                        ]);
                                        throw new \Exception("Estoque insuficiente do insumo: {$insumo->nome_insumo}");
                                    } else {
                                        break;
                                    }
                                }
                                $insumo->peso_total = $peso_total;
                                $insumo->save();
                                Log::info('Estoque de insumo por quilo atualizado com sucesso', [
                                    'insumo_id' => $insumo->id,
                                    'peso_total_final' => $insumo->peso_total,
                                    'estoque_insumo_final' => $insumo->estoque_insumo
                                ]);
                            }
                        }
                    } else {
                        // Se não usa insumo, abate do estoque do próprio produto
                        Log::info('Cálculo de estoque do produto sem insumo', [
                            'produto_id' => $produto->id,
                            'produto_nome' => $produto->nome_produto ?? $produto->nome,
                            'estoque_antes' => $produto->estoque_inicial,
                            'quantidade_vendida' => $item['quantidade']
                        ]);
                        if ($produto->estoque_inicial < $item['quantidade']) {
                            Log::error('Estoque insuficiente do produto', [
                                'produto_id' => $produto->id,
                                'produto_nome' => $produto->nome_produto ?? $produto->nome,
                                'estoque_atual' => $produto->estoque_inicial,
                                'quantidade_vendida' => $item['quantidade']
                            ]);
                            throw new \Exception("Estoque insuficiente para o produto: {$produto->nome}");
                        }
                        $produto->estoque_inicial -= $item['quantidade'];
                        $produto->save();
                        Log::info('Estoque do produto atualizado com sucesso', [
                            'produto_id' => $produto->id,
                            'estoque_final' => $produto->estoque_inicial
                        ]);
                    }
                }
            }

            if ($data['status'] === 'pago') {
                $this->registrarEntradaCaixa($total, 'Venda ID: ' . $venda->id, $data['usuario_id']);
            }

            DB::commit();

            return redirect()->back()->with('mensagem', 'Venda registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao registrar venda', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'produtos' => isset($data['produtos']) ? $data['produtos'] : null,
                'explicacao' => 'Erro ao registrar venda: geralmente ocorre quando o estoque do produto ou insumo é insuficiente para a quantidade vendida. Verifique se o estoque_inicial do produto ou estoque_insumo/peso_total do insumo é suficiente para o cálculo da venda.'
            ]);
            return redirect()->back()->with('erro', 'Erro ao registrar a venda: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $venda = Venda::with('itens')->findOrFail($id);

            $data = $request->validate([
                'status' => 'required|in:pago,pendente,cancelado',
            ]);

            DB::beginTransaction();

            if ($venda->status === 'pago' && $data['status'] === 'pago') {
                // Já está pago, nada a fazer
                throw new \Exception('Venda já está com status pago.');
            }

            if ($data['status'] === 'cancelado' && $venda->status === 'pago') {
                // Se estava paga e foi cancelada, repõe o estoque
                foreach ($venda->itens as $item) {
                    $produto = Produto::findOrFail($item->produto_id);
                    $produto->estoque_inicial += $item->quantidade;
                    $produto->save();
                }
            }

            if ($data['status'] === 'pago' && $venda->status !== 'pago') {
                // Se estava pendente e foi paga, abate estoque e registra no caixa
                foreach ($venda->itens as $item) {
                    $produto = Produto::findOrFail($item->produto_id);
                    if ($produto->usa_insumo === 1) {
                        foreach ($produto->insumos as $insumo) {
                            if ($insumo->unidade_medida === 'Unidade') {
                                $quantidade_usada = $insumo->pivot->quantidade * $item->quantidade;
                                Log::info('Cálculo de insumo por unidade', [
                                    'produto_id' => $produto->id,
                                    'produto_nome' => $produto->nome_produto ?? $produto->nome,
                                    'insumo_id' => $insumo->id,
                                    'insumo_nome' => $insumo->nome,
                                    'quantidade_usada' => $quantidade_usada,
                                    'estoque_antes' => $insumo->estoque_insumo,
                                    'resultado' => $insumo->estoque_insumo >= $quantidade_usada ? 'sucesso' : 'erro',
                                    'mensagem' => $insumo->estoque_insumo >= $quantidade_usada ? 'Estoque suficiente para abatimento.' : 'Estoque insuficiente para abatimento.'
                                ]);
                                if ($insumo->estoque_insumo < $quantidade_usada) {
                                    throw new \Exception("Estoque insuficiente do insumo: {$insumo->nome}");
                                }
                                $insumo->estoque_insumo -= $quantidade_usada;
                                $insumo->save();
                                Log::info('Estoque de insumo por unidade atualizado com sucesso', [
                                    'insumo_id' => $insumo->id,
                                    'estoque_depois' => $insumo->estoque_insumo
                                ]);
                            } elseif ($insumo->unidade_medida === 'Quilo') {
                                $gramas_por_produto = $insumo->pivot->gramatura; // em gramas
                                $gramas_total = $gramas_por_produto * $item->quantidade;
                                $peso_total_antes = $insumo->peso_total;
                                $peso_total = $insumo->peso_total - ($gramas_total / 1000); // peso_total em quilos
                                Log::info('Cálculo de insumo por quilo', [
                                    'produto_id' => $produto->id,
                                    'produto_nome' => $produto->nome_produto ?? $produto->nome,
                                    'insumo_id' => $insumo->id,
                                    'insumo_nome' => $insumo->nome,
                                    'gramas_por_produto' => $gramas_por_produto,
                                    'quantidade_vendida' => $item->quantidade,
                                    'gramas_total' => $gramas_total,
                                    'peso_total_antes' => $peso_total_antes,
                                    'peso_total_calculado' => $peso_total,
                                    'estoque_insumo_antes' => $insumo->estoque_insumo
                                ]);
                                while ($peso_total < 0) {
                                    if ($insumo->estoque_insumo > 1) {
                                        $insumo->estoque_insumo -= 1;
                                        $peso_total += 1;
                                        Log::info('Consumo de 1 unidade de insumo quilo para repor peso_total', [
                                            'insumo_id' => $insumo->id,
                                            'estoque_insumo_atual' => $insumo->estoque_insumo,
                                            'peso_total_atual' => $peso_total
                                        ]);
                                    } else if ($insumo->estoque_insumo == 1 && $peso_total <= 0) {
                                        Log::error('Estoque insuficiente do insumo por quilo', [
                                            'insumo_id' => $insumo->id,
                                            'estoque_insumo' => $insumo->estoque_insumo,
                                            'peso_total' => $peso_total
                                        ]);
                                        throw new \Exception("Estoque insuficiente do insumo: {$insumo->nome}");
                                    } else {
                                        break;
                                    }
                                }
                                $insumo->peso_total = $peso_total;
                                $insumo->save();
                                Log::info('Estoque de insumo por quilo atualizado com sucesso', [
                                    'insumo_id' => $insumo->id,
                                    'peso_total_final' => $insumo->peso_total,
                                    'estoque_insumo_final' => $insumo->estoque_insumo
                                ]);
                            }
                        }
                    } else {
                        // Se não usa insumo, abate do estoque do próprio produto
                        Log::info('Cálculo de estoque do produto sem insumo', [
                            'produto_id' => $produto->id,
                            'produto_nome' => $produto->nome_produto ?? $produto->nome,
                            'estoque_antes' => $produto->estoque_inicial,
                            'quantidade_vendida' => $item->quantidade
                        ]);
                        if ($produto->estoque_inicial < $item->quantidade) {
                            Log::error('Estoque insuficiente do produto', [
                                'produto_id' => $produto->id,
                                'produto_nome' => $produto->nome_produto ?? $produto->nome,
                                'estoque_atual' => $produto->estoque_inicial,
                                'quantidade_vendida' => $item->quantidade
                            ]);
                            throw new \Exception("Estoque insuficiente para o produto: {$produto->nome}");
                        }
                        $produto->estoque_inicial -= $item->quantidade;
                        $produto->save();
                        Log::info('Estoque do produto atualizado com sucesso', [
                            'produto_id' => $produto->id,
                            'estoque_final' => $produto->estoque_inicial
                        ]);
                    }
                }
                $this->registrarEntradaCaixa($venda->total, 'Venda ID: ' . $venda->id, $venda->usuario_id);
            }

            // Atualiza o status da venda
            $venda->update([
                'status' => $data['status']
            ]);

            DB::commit();

            return redirect()->back()->with('mensagem', 'Status da venda atualizado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar status da venda', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'venda_id' => isset($venda) ? $venda->id : null,
                'produtos' => isset($venda) ? $venda->itens : null,
                'explicacao' => 'Erro ao atualizar status: geralmente ocorre quando o estoque do produto ou insumo é insuficiente para a quantidade vendida. Verifique se o estoque_inicial do produto ou estoque_insumo/peso_total do insumo é suficiente para o cálculo da venda.'
            ]);
            return redirect()->back()->with('erro', 'Erro ao atualizar o status da venda: ' . $e->getMessage());
        }
    }

    public function registrosVendas()
    {
        $query = Venda::with(['cliente', 'usuario', 'itens.produto']);


        $vendas = $query->orderBy('created_at', 'desc')->get();

        return view('vendas.tela_vendas.registro_vendas', compact('vendas'));
    }

    private function registrarEntradaCaixa(float $valor, string $descricao, int $usuarioId)
    {
        Log::info('Registrando entrada no caixa', [
            'valor' => $valor,
            'descricao' => $descricao,
            'usuario_id' => $usuarioId
        ]);

        $caixa = Caixa::where('status', 'aberto')->first();

        if (!$caixa) {
            Log::error('Nenhum caixa aberto para registrar movimentação.');
            throw new \Exception('Nenhum caixa aberto para registrar movimentação.');
            // return redirect()->back()->with('erro', 'Nenhum caixa aberto para registrar movimentação'); 
        }

        CaixaMovimentacao::create([
            'caixa_id' => $caixa->id,
            'tipo' => 'entrada',
            'descricao' => $descricao,
            'valor' => $valor,
            'usuario_id' => $usuarioId,
        ]);

        $caixa->total_entradas += $valor;
        $caixa->saldo_final += $valor;
        $caixa->save();

        Log::info('Entrada registrada no caixa com sucesso', [
            'caixa_id' => $caixa->id,
            'valor' => $valor
        ]);
    }

    public function consultaDados(Request $request)
    {
        try {
            // $vendas = Venda::orderBy('id', 'desc')->get();
            $query = Venda::with(['cliente', 'usuario', 'itens.produto']);

            $vendas = $query->orderBy('created_at', 'desc')->get();

            return response()->json($vendas);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar dados das vendas', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'explicacao' => 'Erro ao consultar dados das vendas: geralmente ocorre por falha de conexão ou erro de consulta no banco.'
            ]);
            return response()->json([
                'error' => 'Erro ao consultar dados das vendas.'
            ], 500);
        }
    }
}
