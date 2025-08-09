<?php

namespace App\Http\Controllers\ControleFinanceiro;

use App\Models\Despesa;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DespesaController
{
    public function index()
    {
        return view('controle_financeiro.controle_despesas.index');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nome' => 'required|string|max:255',
                'categoria' => 'nullable|string|max:255',
                'valor' => 'required|numeric|min:0',
                'data_lancamento' => 'required|date',
                'status' => 'required|in:pendente,pago,cancelado',
                'observacao' => 'nullable|string',
            ]);

            $data['usuario_id'] = session('usuario_id');

            Despesa::create($data);

            if ($data['status'] === 'pago') {
                try {
                    $this->registrarSaidaCaixa($data['valor'], "Despesa: {$data['nome']}");
                } catch (\Exception $e) {
                    $data['status'] = 'pendente';
                    Despesa::where('nome', $data['nome'])
                        ->where('usuario_id', $data['usuario_id'])
                        ->orderByDesc('id')
                        ->first()
                        ->update(['status' => 'pendente']);

                    return redirect()->back()->with('erro', 'Nenhum caixa aberto para registrar a movimentação.');

                    Log::error('Erro ao registrar saída no caixa: ' . $e->getMessage(), [
                        'exception' => $e,
                    ]);
                }
            }

            return redirect()->back()->with('mensagem', 'Despesa criada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar despesa: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            // return response()->json(['erro' => 'Erro ao criar despesa.'], 500);
            return redirect()->back()->with('erro', 'Erro ao criar despesa.');
        }
    }

    function create()
    {
        return view('controle_financeiro.controle_despesas.create');
    }

    function edit($id)
    {
        $despesa = Despesa::find($id);

        if (!$despesa) {
            return redirect()->back()->with('erro', 'Despesa não encontrada.');
        }

        return view('controle_financeiro.controle_despesas.edit', compact('despesa'));
    }

    public function update(Request $request, $id)
    {
        try {
            $despesa = Despesa::where('id', $id)->where('usuario_id', session('usuario_id'))->firstOrFail();

            $data = $request->validate([
                'nome' => 'sometimes|required|string|max:255',
                'categoria' => 'nullable|string|max:255',
                'valor' => 'sometimes|required|numeric|min:0',
                'data_lancamento' => 'sometimes|required|date',
                'status' => 'sometimes|required|in:pendente,pago,cancelado',
                'observacao' => 'nullable|string',
            ]);

            // Se a despesa mudou para "pago" e antes não estava paga, registra saída
            if (isset($data['status']) && $data['status'] === 'pago' && $despesa->status !== 'pago') {
                try {
                    $this->registrarSaidaCaixa($data['valor'] ?? $despesa->valor, "Despesa: {$despesa->nome}");
                } catch (\Exception $e) {
                    return redirect()->back()->with('erro', 'Nenhum caixa aberto para registrar a movimentação.');
                    Log::error('Erro ao registrar saída no caixa: ' . $e->getMessage(), [
                        'exception' => $e,
                    ]);
                }
            }

            $despesa->update($data);

            return redirect()->back()->with('mensagem', 'Despesa atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar despesa: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return redirect()->back()->with('erro', 'Erro ao atualizar despesa.');
        }
    }

    public function destroy($id)
    {
        try {
            $despesa = Despesa::where('id', $id)->where('usuario_id', session('usuario_id'))->firstOrFail();

            $despesa->delete();

            return redirect()->back()->with('mensagem', 'Despesa deletada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao deletar despesa: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return redirect()->back()->with('erro', 'Erro ao deletar despesa.');
        }
    }

    private function registrarSaidaCaixa($valor, $descricao)
    {
        $caixa = Caixa::where('status', 'aberto')->first();

        if (!$caixa) {
            throw new \Exception('Nenhum caixa aberto para registrar a movimentação.');
        }

        CaixaMovimentacao::create([
            'caixa_id' => $caixa->id,
            'tipo' => 'saida',
            'descricao' => $descricao,
            'valor' => $valor,
            'usuario_id' => session('usuario_id'),
        ]);

        $caixa->total_saidas += $valor;
        $caixa->saldo_final -= $valor;
        $caixa->save();
    }

    public function consultaDados(Request $request)
    {
        try {
            $despesas = Despesa::all();

            return response()->json($despesas);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar dados das despesas: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'error' => 'Erro ao consultar dados das despesas.'
            ], 500);
        }
    }
}
