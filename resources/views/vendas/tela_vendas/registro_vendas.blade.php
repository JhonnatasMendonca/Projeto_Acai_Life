@extends('layouts.app')
@section('title', 'Açai Life - Registros de vendas')
@section('content')

    <h2 class="mt-4 mb-2">
        <i class="bi-card-list"></i> Registros de Vendas
    </h2>
    <hr style="border: 1px solid #fff;" class=" mb-3">
    <div class="" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="col-md-6 p-0" style="">
            <input type="search" class="form-control " placeholder="Pesquisar na tabela" id="searchInput" style="">
        </div>
        <div class="" style="display: flex;  align-items: center;gap: 10px;">
            <button class="btn  pesquisa" id="addProductButton" style="width: 100%;" data-toggle="modal"
                data-target="#modalCadastrarProduto">
                <i class="bi bi-plus"></i> Iniciar Venda
            </button>
        </div>

    </div>

    <div id="myGrid" style="height: 60vh;" class="ag-theme-alpine mt-4"></div>


@endsection

<script type="module">
    $(document).ready(function() {

        function atualizaDados() {
            $.ajax({
                url: "{{ route('consultaDadosVendas') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    const dados = data;
                    const gridApi = agGrid.createGrid(document.querySelector('#myGrid'),
                        gridOptions);
                    gridApi.setGridOption('rowData', dados);
                    window.gridApi = gridApi;
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao buscar dados:', error);
                }
            });
        }

        var gridOptions = {
            columnDefs: [
                {
                    headerName: 'Pedido',
                    field: 'id',
                    maxWidth: 100,
                    pinned: 'left'
                },
                {
                    headerName: 'Status',
                    field: 'status',
                    maxWidth: 120,
                    pinned: 'left',
                    cellRenderer: function(params) {
                        let color, text;
                        switch (params.value) {
                            case 'pago':
                                color = '#28a745'; // verde
                                text = 'Pago';
                                break;
                            case 'pendente':
                                color = '#ffc107'; // amarelo
                                text = 'Pendente';
                                break;
                            case 'cancelado':
                                color = '#dc3545'; // vermelho
                                text = 'Cancelado';
                                break;
                            default:
                                color = '#6c757d'; // cinza
                                text = params.value || 'Desconhecido';
                        }
                        return `<span style="font-weight:bold; color:${color};">${text}</span>`;
                    }
                },
                {
                    headerName: 'Data',
                    field: 'created_at',
                    minWidth: 110,
                    valueFormatter: function(params) {
                        if (!params.value) return '';

                        const date = new Date(params.value);
                        if (isNaN(date)) return params.value;

                        // Formata como DD/MM/AAAA
                        const dia = String(date.getDate()).padStart(2, '0');
                        const mes = String(date.getMonth() + 1).padStart(2, '0');
                        const ano = date.getFullYear();

                        return `${dia}/${mes}/${ano}`;
                    }
                },
                {
                    headerName: 'Cliente',
                    field: 'cliente.nome',
                    valueGetter: function(params) {
                        return params.data.cliente?.nome ?? 'Não informado';
                    },
                    minWidth: 150
                },
                {
                    headerName: 'Atendente',
                    field: 'usuario.nome_usuario',
                    valueGetter: function(params) {
                        return params.data.usuario?.nome_usuario ?? 'Não informado';
                    },
                    minWidth: 180
                },
                
                {
                    headerName: 'Subtotal',
                    field: 'subtotal',
                    minWidth: 150
                },
                {
                    headerName: 'Desconto',
                    field: 'desconto',
                    minWidth: 150
                },
                {
                    headerName: 'Total',
                    field: 'total',
                    minWidth: 150
                },
                {
                    headerName: 'Forma de Pagamento',
                    field: 'forma_pagamento',
                    minWidth: 150
                },
                {
                    headerName: 'Descrição',
                    field: 'descricao',
                    minWidth: 150
                },
                {
                    headerName: 'Observação',
                    field: 'observacao',
                    minWidth: 300
                },
                {
                    headerName: 'Alterar Status',
                    field: 'acoes',
                    cellRenderer: function(params) {
                        const id = params.data.id;
                        const status = params.data.status;
                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content');

                        if (status === 'pendente') {
                            return `
                                <form method="POST" action="/vendas/${id}/atualizar-status" style="display:inline; margin-right: 5px;">
                                    <input type="hidden" name="_token" value="${csrf}">
                                    <input type="hidden" name="status" value="pago">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle"></i> Aprovar
                                    </button>
                                </form>

                                <form method="POST" action="/vendas/${id}/atualizar-status" style="display:inline;">
                                    <input type="hidden" name="_token" value="${csrf}">
                                    <input type="hidden" name="status" value="cancelado">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                </form>
                            `;S
                        } else {
                            return `<span class="text-muted">Sem ações</span>`;
                        }
                    },

                    minWidth: 250,
                },
            ],
            defaultColDef: {
                flex: 1,
                sortable: true,
                filter: true,
                resizable: true,
                wrapHeaderText: true,
                autoHeaderHeight: true,
            },
            animateRows: true,
            paginationPageSize: 100,
            pagination: true,
            rowHeight: 30,
            enableBrowserTooltips: true,
        };

        atualizaDados();

        var searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            var searchText = searchInput.value;
            gridApi.setGridOption('quickFilterText', searchText);
        });

        $('#addProductButton').on('click', function() {
            window.location.href = "{{ route('vendas.index') }}";
        });
    });
</script>
