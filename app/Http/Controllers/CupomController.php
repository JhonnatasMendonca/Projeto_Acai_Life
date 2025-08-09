<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CupomController extends Controller
{
    public function show($id)
    {
        // Busca dados do cupom (exemplo fixo)
        $cupom = [
            'id' => $id,
            'itens' => [
                ['nome' => 'Produto A', 'preco' => 10.00],
                ['nome' => 'Produto B', 'preco' => 5.00],
            ]
        ];

        return view('vendas.cupom', compact('cupom'));
    }

    public function download($id)
    {
        // Gera o ESC/POS (simples)
        $conteudo = "\x1B\x40"; // Init
        $conteudo .= "**** Cupom Fiscal ****\n";
        $conteudo .= "Produto A ........ R$ 10,00\n";
        $conteudo .= "Produto B ........ R$ 5,00\n";
        $conteudo .= "Total ............ R$ 15,00\n";
        $conteudo .= "\n\n\n";
        $conteudo .= "\x1D\x56\x00"; // Cut

        return Response::make($conteudo, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="cupom.txt"',
        ]);
    }
}