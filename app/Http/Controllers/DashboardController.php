<?php

namespace App\Http\Controllers\DashboardController;

use App\Models\Venda;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardController
{

    public function index()
    {
        // Faturamento mensal (últimos 6 meses)
        $faturamentoPorMes = Venda::select(
            DB::raw("DATE_FORMAT(created_at, '%m/%Y') as mes"),
            DB::raw("SUM(total) as total")
        )
            ->groupBy('mes')
            ->orderByRaw("STR_TO_DATE(mes, '%m/%Y') ASC")
            ->take(6)
            ->get();

        // Vendas por categoria/produto
        $vendasPorCategoria = Produto::select(
            'categoria',
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('categoria')
            ->get();

        // Estoque atual por produto
        $estoqueDistribuicao = Produto::select('nome', 'estoque_inicial')->get();

        return view('home.index', [
            'faturamentoPorMes' => $faturamentoPorMes,
            'vendasPorCategoria' => $vendasPorCategoria,
            'estoqueDistribuicao' => $estoqueDistribuicao,
        ]);
    }
}
