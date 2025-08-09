<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\Venda;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;

class HomeController
{
    public function index()
    {
        // Faturamento diário (últimos 7 dias)
        $faturamentoPorDia = Venda::select(
            DB::raw("DATE_FORMAT(created_at, '%d/%m') as dia"),
            DB::raw("SUM(total) as total")
        )
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->groupBy('dia')
            ->orderByRaw("STR_TO_DATE(dia, '%d/%m') ASC")
            ->get() ?? collect();

        // Vendas por categoria (agrupadas por produto)
        $vendasPorCategoria = Produto::select('categoria', DB::raw('COUNT(*) as total'))
            ->groupBy('categoria')
            ->get() ?? collect();

        // Estoque atual por produto
        $estoqueDistribuicao = Produto::select('nome_produto', 'estoque_inicial')->get() ?? collect();

        // Cards de destaque
        $totalVendasHoje = Venda::whereDate('created_at', today())
            ->where('status', 'pago')
            ->count() ?? 0;

        $mesAtual = now()->month;
        $anoAtual = now()->year;

        // Entradas no caixa
        $faturamentoMesAtual = Caixa::whereMonth('data', $mesAtual)
            ->whereYear('data', $anoAtual)
            ->sum('total_entradas') ?? 0;

        // Saídas no caixa
        $despesaMesAtual = Caixa::whereMonth('data', $mesAtual)
            ->whereYear('data', $anoAtual)
            ->sum('total_saidas') ?? 0;

        // Lucro = Entradas - Saídas
        $lucroMesAtual = $faturamentoMesAtual - $despesaMesAtual;

        // Produto mais vendido
        $produtoMaisVendido = DB::table('itens_venda')
            ->select('produto_id', DB::raw('SUM(quantidade) as total'))
            ->groupBy('produto_id')
            ->orderByDesc('total')
            ->first();

        $nomeProdutoMaisVendido = $produtoMaisVendido
            ? (Produto::find($produtoMaisVendido->produto_id)->nome_produto ?? 'N/A')
            : 'N/A';

        return view('home.index', [
            'faturamentoPorDia' => $faturamentoPorDia,
            'vendasPorCategoria' => $vendasPorCategoria,
            'estoqueDistribuicao' => $estoqueDistribuicao,
            'totalVendasHoje' => $totalVendasHoje,
            'faturamentoMesAtual' => $faturamentoMesAtual,
            'produtoMaisVendido' => $nomeProdutoMaisVendido,
            'lucroMesAtual' => $lucroMesAtual,
            'despesaMesAtual' => $despesaMesAtual
        ]);
    }
}
