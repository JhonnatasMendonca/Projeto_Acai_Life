<?php

namespace App\Http\Controllers\PainelAdm;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produto;
use Illuminate\Support\Facades\Log;

class ControleEstoqueController extends Controller
{
    public function index()
    {
        $insumos = Insumo::all();
        $produtos = Produto::where('ativo', true)->get();

        return view('painel_adm.controle_estoque.index')->with('insumos', $insumos)
            ->with('produtos', $produtos);
    }

    public function consultaDados(Request $request)
    {
        try {
            $insumos = Insumo::all();
            $produtos = Produto::where('ativo', true)->get();

            return response()->json([
                'insumos' => $insumos,
                'produtos' => $produtos,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao consultar dados de estoque: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'error' => 'Erro ao consultar dados de estoque.'
            ], 500);
        }
    }
}
