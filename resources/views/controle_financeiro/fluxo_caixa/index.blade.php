@extends('layouts.app')
@section('title', 'Açai Life - Fluxo de caixa')
@section('content')

    <h2 class="mt-4 mb-2">
        <i class="bi bi-cash"></i> Fluxo de caixa
    </h2>
    <hr style="border: 1px solid #fff;" class=" mb-3">
    <div class="" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="col-md-6 p-0" style="">
            <input type="search" class="form-control " placeholder="Pesquisar na tabela" id="searchInput" style="">
        </div>
        <div class="" style="display: flex;  align-items: center;gap: 10px;">
            <button class="btn pesquisa" id="addUsuarioButton" style="width: 100%; background-color:#0a6109;" data-toggle="modal"
                data-target="#modalCadastrarUsuario">
                <i class="bi bi-cash"></i> Abrir caixa
            </button>
            <button class="btn  pesquisa" id="addRetiradaButton" style="width: 100%; background-color:#e00b16;" data-toggle="modal"
                data-target="#modalCadastrarRetirada">
                <i class="bi bi-cash"></i> Retirada de caixa
            </button>
            <button class="btn pesquisa" id="addCompraButton" style="width: 100%; background-color: #051D40;gap: 10px;"
                data-toggle="modal" data-target="#modalRegistrarCompra" onmouseover="this.style.backgroundColor='#0a2b5c';"
                onmouseout="this.style.backgroundColor='#051D40';">
                <i class="bi bi-cart"></i> Registrar Compra
            </button>

        </div>

    </div>

    <div id="myGrid" style="height: 60vh;" class="ag-theme-alpine mt-4"></div>




    <!-- Chamada do modal de cadastro de cliente -->
    @include('partials.modal', [
        'id' => 'modalCadastrarUsuario',
        'title' => 'ABERTURA DE CAIXA',
        'slot' => view('controle_financeiro.fluxo_caixa.create')->render(),
        'hideFooter' => true,
    ])

    <!-- Chamada do modal de cadastro de cliente -->
    @include('partials.modal', [
        'id' => 'modalCadastrarRetirada',
        'title' => 'RETIRADA DE CAIXA',
        'slot' => view('controle_financeiro.fluxo_caixa.retirada_caixa', compact('userId'))->render(),
        'hideFooter' => true,
    ])

    <!-- Chamada do modal de registrar compras -->
    @include('partials.modal', [
        'id' => 'modalRegistrarCompra',
        'title' => 'REGISTRAR COMPRAS',
        'slot' => view(
            'painel_adm.controle_estoque.registrar_compra.index',
            compact('insumos', 'produtos'))->render(),
        'hideFooter' => true,
    ])


    </div>

@endsection

<script type="module">
    $(document).ready(function() {

        function atualizaDados() {
            $.ajax({
                url: "{{ route('consultaDadosCaixas') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    const dados = data;
                    const gridApi = agGrid.createGrid(document.querySelector('#myGrid'), gridOptions);
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
                // { headerName: 'CPF', field: 'cpf_usuario', minWidth: 120 },
                { headerName: 'Caixa', field: 'id', maxWidth: 100, pinned: 'left' },
                {
                    headerName: 'Data',
                    field: 'data',
                    minWidth: 110,
                    valueFormatter: function(params) {
                        if (!params.value) return '';
                        // Replace hyphens with slashes for correct parsing
                        const dateStr = params.value.replace(/-/g, '/');
                        const date = new Date(dateStr);
                        if (isNaN(date)) return params.value;
                        return date.toLocaleDateString('pt-BR');
                    }
                },
                { headerName: 'Hr. abertura', field: 'hora_abertura', minWidth: 150 },
                { headerName: 'Hr. fechamento', field: 'hora_fechamento', minWidth: 180 },
                { headerName: 'Vlr. abertura', field: 'valor_abertura', minWidth: 150 },
                { headerName: 'Vlr. entradas', field: 'total_entradas', minWidth: 150 },
                { headerName: 'Vlr. saídas', field: 'total_saidas', minWidth: 150 },
                { headerName: 'Saldo final', field: 'saldo_final', minWidth: 150 },
                { headerName: 'Status', field: 'status', minWidth: 100 },
                { headerName: 'Observação', field: 'observacao', minWidth: 300 },
                {
                    headerName: 'Ações',
                    field: 'acoes',
                    cellRenderer: function(params) {
                        const id = params.data.id;
                        const rota = `/caixas/${id}`;
                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        return `
                           
                            <form method="POST" action="${rota}/fechar" class="form-delete" style="display:inline;">
                                <input type="hidden" name="_token" value="${csrf}">
                                <input type="hidden" name="_method" value="POST">
                                <button type="submit" class="btn btn-danger btn-sm btn-delete" data-id="${id}">
                                    <i class="bi bi-cash"></i> Fechar caixa
                                </button>
                            </form>
                        `;
                    },
                    minWidth: 190,
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
            onGridReady: function() {
                // Delegação de evento para os botões dentro da grade
                $('#myGrid').on('click', '.btn-edit', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    $.get('/usuarios/' + id + '/edit', function(html) {
                        $('#conteudoModalEditarUsuario').html(html);
                        $('#modalEditarUsuario').modal('show');
                    });
                });

                $('#myGrid').on('click', '.btn-delete', function(e) {
                    e.preventDefault();
                    const confirmed = confirm('Tem certeza que deseja fechar este caixa?');
                    if (confirmed) {
                        $(this).closest('form').submit();
                    }
                });
            },
        };

        atualizaDados();

        var searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            var searchText = searchInput.value;
            gridApi.setGridOption('quickFilterText', searchText);
        });
    });
</script>

