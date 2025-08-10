@extends('layouts.app')
@section('title', 'Açai Life - Home')
@section('content')

    <style>
        .card-metric {
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            font-weight: bold;
        }

        .bg-purple {
            background: linear-gradient(135deg, #8e2de2, #4a00e0);
        }

        .bg-blue {
            background: linear-gradient(135deg, #007cf0, #00dfd8);
        }

        .bg-orange {
            background: linear-gradient(135deg, #f7971e, #ffd200);
        }

        h4 {
            font-size: 16px;
        }

        .grafico-container {
            /* background-color: #4e3bc2; */
            /* cor desejada */
            border-radius: 12px;
            padding: 20px;
            color: white;
        }

        .box-dashboard {
            background-color: #E9E9E9;
            padding: 2%;
            border-radius: 15px;
            color: #000 !important;
        }

        h5 {
            color: #000 !important;
        }

        .apexcharts-menu-item {
            color: #000 !important;
        }
    </style>


    

    <h2 class="mt-4 mb-2">
        <i class="bi-bar-chart"></i> Dashboard - Controle de Estoque e Vendas
    </h2>
    <hr style="border: 1px solid #fff;" class=" mb-3">
    @if(!empty($alertasEstoque) && count($alertasEstoque) > 0)

        <style>
            .alerta-estoque-piscando {
                border: 3px solid red;
                animation: borda-pisca 1s infinite;
                box-shadow: 0 0 10px red;
            }
            @keyframes borda-pisca {
                0% { border-color: red; }
                50% { border-color: #fff; }
                100% { border-color: red; }
            }
        </style>

        <div class="alert alert-warning alerta-estoque-piscando" style="font-size:16px; border-radius: 15px;">
            <strong class="text-danger">Atenção!</strong> Os itens abaixo estão com estoque baixo:<br>
            <ul style="margin-bottom:0;">
                @foreach($alertasEstoque as $alerta)
                    <li>
                        <b>{{ $alerta['tipo'] }}:</b> {{ $alerta['nome'] }} - <span style="color:red;">{{ $alerta['estoque'] }}</span> unidades
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container-fluid mt-2 box-dashboard">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card-metric bg-purple">
                    <h4>{{ $totalVendasHoje }} VENDAS HOJE</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-metric bg-blue">
                    <h4>R$ {{ number_format($faturamentoMesAtual, 2, ',', '.') }} FATURAMENTO MÊS</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-metric bg-orange">
                    <h4>R$ {{ number_format($despesaMesAtual, 2, ',', '.') }} DESPESA MÊS</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-metric bg-orange">
                    <h4>R$ {{ number_format($lucroMesAtual, 2, ',', '.') }} LUCRO BRUTO MÊS</h4>
                </div>
            </div>
            {{-- <div class="col-md-3">
                <div class="card-metric bg-orange">
                    <h4>{{ strtoupper($produtoMaisVendido) }} MAIS VENDIDO</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-metric bg-orange">
                    <h4>{{ strtoupper($produtoMaisVendido) }} MAIS VENDIDO</h4>
                </div>
            </div> --}}
        </div>

        <div class="row mt-2">
        </div>
        <div class="row mt-5" style="display: flex; justify-content:space-between;">
            <div class="grafico-container col-md-4">
                <h5 class=""><i class="bi bi-box-seam"></i> Distribuição de Estoque</h5>
                <div id="graficoDonut"></div>
            </div>
            <div class="grafico-container col-md-4">
                <h5 class=""><i class="bi	bi-graph-up"></i> Faturamento diário</h5>
                <div id="graficoLinha"></div>
            </div>

            <div class="grafico-container col-md-4">
                <h5 class=""><i class="bi bi-cash-coin"></i> Vendas por Categoria</h5>
                <div id="graficoBarra"></div>
            </div>
        </div>

    </div>

    <script type="module">
        const dias = @json($faturamentoPorDia->pluck('dia'));
        const totais = @json($faturamentoPorDia->pluck('total'));

        const categorias = @json($vendasPorCategoria->pluck('categoria'));
        const totalPorCategoria = @json($vendasPorCategoria->pluck('total'));

        const produtosEstoque = @json($estoqueDistribuicao->pluck('nome_produto'));
        const estoqueQtd = @json($estoqueDistribuicao->pluck('estoque_inicial'));

        // Linha - Faturamento diário
        const elLinha = document.querySelector("#graficoLinha");
        if (elLinha) {
            new ApexCharts(elLinha, {
                chart: {
                    type: 'line'
                },
                series: [{
                    name: 'R$',
                    data: totais
                }],
                xaxis: {
                    categories: dias
                },
                stroke: {
                    curve: 'smooth'
                },
                colors: ['#00dfd8'],
                tooltip: {
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Arial, sans-serif',
                        colors: ['#ffffff'] // cor do texto do tooltip
                    },
                    theme: 'dark' // 'light' ou 'dark'
                }
            }).render();
        }

        // Barra - Vendas por categoria
        const elBarra = document.querySelector("#graficoBarra");
        if (elBarra) {
            new ApexCharts(elBarra, {
                chart: {
                    type: 'bar'
                },
                series: [{
                    name: 'Vendas',
                    data: totalPorCategoria
                }],
                xaxis: {
                    categories: categorias
                },
                colors: ['#f7971e'],
                tooltip: {
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Arial, sans-serif',
                        colors: ['#ffffff'] // cor do texto do tooltip
                    },
                    theme: 'dark' // 'light' ou 'dark'
                }
            }).render();
        }

        // Donut - Estoque
        const elDonut = document.querySelector("#graficoDonut");
        if (elDonut) {
            new ApexCharts(elDonut, {
                chart: {
                    type: 'donut'
                },
                labels: produtosEstoque,
                series: estoqueQtd,
                colors: ['#4a00e0', '#00dfd8', '#f7971e', '#8e2de2'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            }).render();
        }
    </script>
@endsection
