<?php

namespace App\Http\Controllers\ControleFinanceiro;

use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use App\Models\Insumo;
use App\Models\Produto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CaixaController
{
    // Listar todos os caixas
    public function index()
    {
        $insumos = Insumo::all();
        $produtos = Produto::all();

        $userId = session('usuario_id');
        $caixas = Caixa::with('movimentacoes')->orderBy('data', 'desc')->get();
        return view('controle_financeiro.fluxo_caixa.index', compact('caixas', 'userId', 'insumos', 'produtos'));
    }

    // Abrir um novo caixa
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'valor_abertura' => 'required|numeric|min:0',
                'observacao' => 'nullable|string',
            ]);

            $caixaAberto = Caixa::where('status', 'aberto')->first();

            if ($caixaAberto) {
                return redirect()->back()->with('erro', 'Já existe um caixa aberto.');
            }

            Caixa::create([
                'data' => now()->toDateString(),
                'hora_abertura' => Carbon::now()->format('H:i:s'),
                'valor_abertura' => $data['valor_abertura'],
                'total_entradas' => 0,
                'total_saidas' => 0,
                'saldo_final' => $data['valor_abertura'],
                'status' => 'aberto',
                'usuario_abertura_id' => session('usuario_id'),
                'observacao' => $data['observacao'] ?? null,
            ]);

            return redirect()->back()->with('mensagem', 'Caixa aberto com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao abrir caixa: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return redirect()->back()->with('erro', 'Erro ao abrir caixa.');
        }
    }

    // Mostrar detalhes do caixa
    // public function show($id)
    // {
    //     $caixa = Caixa::with('movimentacoes')->find($id);

    //     if (!$caixa) {
    //         return response()->json(['message' => 'Caixa não encontrado.'], 404);
    //     }

    //     return response()->json($caixa);
    // }

    // Registrar movimentação no caixa
    public function movimentar(Request $request, $id)
    {
        $caixa = Caixa::find($id);

        if (!$caixa || $caixa->status !== 'aberto') {
            return response()->json(['message' => 'Caixa não encontrado ou não está aberto.'], 400);
        }

        $data = $request->validate([
            'tipo' => 'required|in:entrada,saida',
            'descricao' => 'required|string',
            'valor' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            CaixaMovimentacao::create([
                'caixa_id' => $caixa->id,
                'tipo' => $data['tipo'],
                'descricao' => $data['descricao'],
                'valor' => $data['valor'],
                'usuario_id' => session('usuario_id'),
            ]);

            if ($data['tipo'] === 'entrada') {
                $caixa->total_entradas += $data['valor'];
                $caixa->saldo_final += $data['valor'];
            } else {
                $caixa->total_saidas += $data['valor'];
                $caixa->saldo_final -= $data['valor'];
            }

            $caixa->save();

            DB::commit();

            return response()->json([
                'message' => 'Movimentação registrada com sucesso!',
                'caixa' => $caixa->fresh('movimentacoes'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro na movimentação.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Fechar caixa
    public function fechar($id)
    {
        try {
            $caixa = Caixa::find($id);

            if (!$caixa || $caixa->status !== 'aberto') {
                return redirect()->back()->with('erro', 'Caixa não encontrado ou já está fechado.');
            }

            $caixa->update([
                'hora_fechamento' => Carbon::now()->format('H:i:s'),
                'status' => 'fechado',
                'usuario_fechamento_id' => session('usuario_id'),
            ]);

            return redirect()->back()->with('mensagem', 'Caixa fechado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao fechar caixa: ' . $e->getMessage(), [
                'exception' => $e,
                'caixa_id' => $id,
            ]);
            return redirect()->back()->with('erro', 'Erro ao fechar caixa.');
        }
    }

    // Excluir caixa (uso cuidadoso, pode impactar histórico financeiro)
    public function destroy($id)
    {
        $caixa = Caixa::find($id);

        if (!$caixa) {
            return response()->json(['message' => 'Caixa não encontrado.'], 404);
        }

        $caixa->delete();

        return response()->json(['message' => 'Caixa removido com sucesso.']);
    }

    public function consultaDados(Request $request)
    {
        try {
            $caixas = Caixa::orderBy('id', 'desc')->get();

            return response()->json($caixas);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar dados do caixa: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'error' => 'Erro ao consultar dados do caixa.'
            ], 500);
        }
    }
}
