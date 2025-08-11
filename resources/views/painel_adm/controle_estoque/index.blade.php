@extends('layouts.app')
@section('title', 'Açai Life - Controle de Estoque')
@section('content')

    <h2 class="mt-4 mb-2">
        <i class="bi bi-box-seam"></i> Controle de Estoque
    </h2>
    <hr style="border: 1px solid #fff;" class=" mb-3">
    <div class="box-inputs container-fluid  row">
        <div class="col-md-6 p-0 search-test" style="">
            <input type="search" class="form-control mt-2" placeholder="Pesquisar na tabela" id="searchInput" style="width: 100%;">
        </div>
        <div class="box-buttons-modals container-fluid  col-md-6 d-flex justify-content-between align-items-center" >
            <button class="button-modal col-md-4 mb-2 mt-2 btn pesquisa teste1" id="addProductButton" style="width: 100%; margin-right: 10px;" data-toggle="modal"
                data-target="#modalCadastrarProduto" style="color: #592f6c">
                <i class="bi bi-box test-salvar"></i> Cadastrar Produto
            </button>

            <button class="button-modal col-md-4 mb-2 mt-2 btn pesquisa teste1" id="addInsumoButton" style="width: 100%; margin-right: 10px;" data-toggle="modal" gap: 10px;" data-toggle="modal"
                data-target="#modalCadastrarInsumo">
                <i class="bi bi-tags"></i> Cadastrar Insumo
            </button>

            <button class="button-modal col-md-4 mb-2 mt-2 btn pesquisa teste1" id="addCompraButton" style="width: 100%; margin-right: 10px;" data-toggle="modal" background-color: #051D40;gap: 10px;"
                data-toggle="modal" data-target="#modalRegistrarCompra" onmouseover="this.style.backgroundColor='#0a2b5c';"
                onmouseout="this.style.backgroundColor='#051D40';">
                <i class="bi bi-cart"></i> Registrar Compra
            </button>

        </div>

    </div>

    <div id="myGrid" style="height: 60vh;" class="ag-theme-alpine mt-4"></div>


    <!-- Chamada do modal de cadastro de insumo -->
    @include('partials.modal', [
        'id' => 'modalCadastrarInsumo',
        'title' => 'CADASTRO DE INSUMOS',
        'slot' => view('painel_adm.controle_estoque.insumo.index')->render(),
        'hideFooter' => true,
    ])

    <!-- Chamada do modal de cadastro de produto -->
    @include('partials.modal', [
        'id' => 'modalCadastrarProduto',
        'title' => 'CADASTRO DE PRODUTOS',
        'slot' => view('painel_adm.controle_estoque.produto.index', compact('insumos'))->render(),
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


    <!-- Modal de Edição de Insumo -->
    <div class="modal fade" id="modalEditarInsumo" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Insumo</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="conteudoModalEditarInsumo">
                    <!-- Aqui entra o form via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Produto -->
    <div class="modal fade" id="modalEditarProduto" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Produto</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="conteudoModalEditarProduto">
                    <!-- Aqui entra o form via AJAX -->
                </div>
            </div>
        </div>
    </div>

@endsection

<script type="module">
    $(document).ready(function() {

        function atualizaDados() {
            $.ajax({
                url: "{{ route('consultaDadosEstoque') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    const produtos = data.produtos.map(p => ({
                        id: p.id,
                        tipo: 'Produto',
                        nome: p.nome_produto,
                        categoria: p.categoria,
                        descricao: p.descricao,
                        preco_venda: p.preco_venda,
                        preco_custo: p.preco_custo,
                        estoque: p.estoque_inicial,
                        unidade_medida: null,
                        peso_total: null
                    }));

                    const insumos = data.insumos.map(i => ({
                        id: i.id,
                        tipo: 'Insumo',
                        nome: i.nome_insumo,
                        categoria: i.categoria_insumo,
                        descricao: i.descricao_insumo,
                        preco_venda: null,
                        preco_custo: i.preco_custo,
                        estoque: i.estoque_insumo,
                        unidade_medida: i.unidade_medida,
                        peso_total: i.peso_total
                    }));

                    const dados = produtos.concat(insumos);

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
                    headerName: 'Tipo',
                    field: 'tipo',
                    maxWidth: 100,
                    pinned: 'left',
                    cellStyle: params => {
                        if (params.value === 'Insumo') {
                            return { color: '#fff', backgroundColor: '#28a745', fontWeight: 'bold' };
                        } else if (params.value === 'Produto') {
                            return { color: '#fff', backgroundColor: '#6f42c1', fontWeight: 'bold' };
                        }
                        return {};
                    }
                },
                {
                    headerName: 'Nome',
                    field: 'nome',
                    minWidth: 150,
                },
                {
                    headerName: 'Categoria',
                    field: 'categoria',
                    minWidth: 150,
                },
                {
                    headerName: 'Descrição',
                    field: 'descricao',
                    minWidth: 200,
                },
                {
                    headerName: 'Estoque',
                    field: 'estoque',
                    maxWidth: 110,
                    minWidth: 110,
                    valueFormatter: params => params.value ? `${params.value} und` : '',
                },
                {
                    headerName: 'Preço de Venda',
                    field: 'preco_venda',
                    minWidth: 140,
                    valueFormatter: params => params.value ? `R$ ${params.value}` : '',
                },
                {
                    headerName: 'Preço de Custo',
                    field: 'preco_custo',
                    minWidth: 140,
                    valueFormatter: params => params.value ? `R$ ${params.value}` : '',
                },
                {
                    headerName: 'Und. Medida',
                    field: 'unidade_medida',
                    minWidth: 150,
                },
                {
                    headerName: 'Peso Total (kg)',
                    field: 'peso_total',
                    minWidth: 120,
                    valueFormatter: params => params.value ? `${params.value} Kg` : '',
                },
                {
                    headerName: 'Ações',
                    field: 'acoes',
                    cellRenderer: function(params) {
                        const id = params.data.id;
                        const tipo = params.data.tipo; // Vem do backend: 'Produto' ou 'Insumo'
                        const rota = tipo === 'Produto' ? `/produtos/${id}` : `/insumos/${id}`;
                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content');

                        return `
                    <button class="btn btn-primary btn-sm btn-edit" data-id="${id}" data-tipo="${tipo}">
                        <i class="bi bi-pencil-square"></i> Editar
                    </button>
                    <form method="POST" action="${rota}" style="display:inline;">
                        <input type="hidden" name="_token" value="${csrf}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-sm btn-delete" data-id="${id}" data-tipo="${tipo}">
                            <i class="bi bi-trash"></i> Excluir
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

            icons: {
                menu: '<i class="fa fa-filter" style="width: 10px"/>',
            },

            suppressHeaderFocus: true,
            animateRows: true,
            paginationPageSize: 100,
            pagination: true,
            suppressColumnVirtualisation: true,
            suppressRowVirtualisation: true,
            enableCellTextSelection: true,
            rowHeight: 30,
            enableBrowserTooltips: true,

            onGridReady: function() {
                // Delegação para o container da grid
                $('#myGrid').on('click', '.btn-edit', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    const tipo = $(this).data('tipo');

                    if (tipo === 'Produto') {
                        $.get('/produtos/' + id + '/edit', function(html) {
                            $('#conteudoModalEditarProduto').html(html);
                            $('#modalEditarProduto').modal('show');
                        });
                    } else if (tipo === 'Insumo') {
                        $.get('/insumos/' + id + '/edit', function(html) {
                            $('#conteudoModalEditarInsumo').html(html);
                            $('#modalEditarInsumo').modal('show');
                        });
                    }
                });

                $('#myGrid').on('click', '.btn-delete', function(e) {
                    e.preventDefault();
                    const tipo = $(this).data('tipo');
                    const confirmMsg =
                        `Tem certeza que deseja excluir este ${tipo.toLowerCase()}?`;
                    if (confirm(confirmMsg)) {
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
