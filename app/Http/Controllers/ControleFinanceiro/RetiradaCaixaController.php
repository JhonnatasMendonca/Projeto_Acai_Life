<?php

namespace App\Http\Controllers\ControleFinanceiro;

use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use App\Models\RetiradaCaixa;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RetiradaCaixaController
{
    // public function index()
    // {
    //     $retiradas = RetiradaCaixa::with(['usuario', 'caixa'])
    //         ->orderBy('data_retirada', 'desc')
    //         ->get();

    //     return response()->json($retiradas);
    // }

    // public function create()
    // {
    //     $userId = session('usuario_id');
    //     return view('controle_financeiro.fluxo_caixa.retirada_caixa', compact('userId'));
    // }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'usuario_id' => 'required|exists:usuarios,id',
                'senha' => 'required|string',
                'valor' => 'required|numeric|min:0.01',
                'descricao' => 'nullable|string',
            ]);

            // Verificar usuário e senha
            $usuario = Usuario::findOrFail($request->usuario_id);

            // Atenção: use o campo correto da senha no seu model. Aqui assumi 'senha_usuario'.
            if (!Hash::check($request->senha, $usuario->senha_usuario)) {
                Log::warning('Senha incorreta para retirada de caixa', [
                    'usuario_id' => $request->usuario_id
                ]);
                return redirect()->back()->with('erro', 'Senha incorreta.');
            }

            // Verificar se existe caixa aberto
            $caixa = Caixa::where('status', 'aberto')->first();

            if (!$caixa) {
                Log::warning('Tentativa de retirada sem caixa aberto', [
                    'usuario_id' => $request->usuario_id
                ]);
                return redirect()->back()->with('erro', 'Nenhum caixa aberto para realizar retirada.');
            }

            DB::beginTransaction();

            try {
                // Registrar retirada
                $retirada = RetiradaCaixa::create([
                    'caixa_id' => $caixa->id,
                    'usuario_id' => $usuario->id,
                    'valor' => $request->valor,
                    'descricao' => $request->descricao,
                    'data_retirada' => now(),
                ]);

                // Criar movimentação no caixa (saída)
                CaixaMovimentacao::create([
                    'caixa_id' => $caixa->id,
                    'tipo' => 'saida',
                    'descricao' => 'Retirada de caixa: ' . ($request->descricao ?? 'Sem descrição'),
                    'valor' => $request->valor,
                    'usuario_id' => $usuario->id,
                ]);

                // Atualizar caixa
                $caixa->total_saidas += $request->valor;
                $caixa->saldo_final -= $request->valor;
                $caixa->save();

                DB::commit();

                Log::info('Retirada realizada com sucesso', [
                    'retirada_id' => $retirada->id,
                    'usuario_id' => $usuario->id,
                    'valor' => $request->valor
                ]);

                return redirect()->back()->with('mensagem', 'Retirada realizada com sucesso!');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erro ao realizar retirada no banco', [
                    'error' => $e->getMessage(),
                    'usuario_id' => $usuario->id
                ]);
                return redirect()->back()->with('erro', 'Erro ao realizar retirada.');
            }
        } catch (\Exception $e) {
            Log::error('Erro geral ao processar retirada', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return redirect()->back()->with('erro', 'Erro ao processar retirada.');
        }
    }

    // public function show($id)
    // {
    //     $retirada = RetiradaCaixa::with(['usuario', 'caixa'])->find($id);

    //     if (!$retirada) {
    //         return response()->json(['message' => 'Retirada não encontrada.'], 404);
    //     }

    //     return response()->json($retirada);
    // }

    // public function destroy($id)
    // {
    //     $retirada = RetiradaCaixa::find($id);

    //     if (!$retirada) {
    //         return response()->json(['message' => 'Retirada não encontrada.'], 404);
    //     }

    //     $caixa = $retirada->caixa;

    //     DB::beginTransaction();

    //     try {
    //         // Estornar movimentação
    //         $caixa->total_saidas -= $retirada->valor;
    //         $caixa->saldo_final += $retirada->valor;
    //         $caixa->save();

    //         // Opcional: remover movimentação no caixa referente à retirada (não implementado aqui)

    //         $retirada->delete();

    //         DB::commit();

    //         return response()->json(['message' => 'Retirada excluída com sucesso.']);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Erro ao excluir retirada.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
