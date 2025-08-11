@extends('layouts.app')
@section('title', 'Açai Life - Controle de Despesas')
@section('content')

    <h2 class="mt-4 mb-2">
        <i class="bi bi-credit-card"></i> Controle de Despesas
    </h2>
    <hr style="border: 1px solid #fff;" class=" mb-3">
    <div class="box-inputs container-fluid  row">
        <div class="col-md-6 p-0" style="">
            <input type="search" class="form-control mt-2" placeholder="Pesquisar na tabela" id="searchInput" style="">
        </div>
        <div class="box-buttons-modals container-fluid  col-md-6 d-flex justify-content-between align-items-center" >
            <div></div>
            <button class="button-modal col-md-4 mt-2 btn pesquisa" id="addClienteButton" style="width: 100%;" data-toggle="modal"
                data-target="#modalCadastrarDespesa">
                <i class="bi bi-credit-card"></i> Registrar Despesa
            </button>

        </div>

    </div>

    <div id="myGrid" style="height: 60vh;" class="ag-theme-alpine mt-4"></div>




    <!-- Chamada do modal de cadastro de Despesa -->
    @include('partials.modal', [
        'id' => 'modalCadastrarDespesa',
        'title' => 'REGISTRAR DE DESPESA',
        'slot' => view('controle_financeiro.controle_despesas.create')->render(),
        'hideFooter' => true,
    ])


    </div>


    <!-- Modal de Edição de Cliente -->
    <div class="modal fade" id="modalEditarCliente" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Despesa</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="conteudoModalEditarCliente">
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
                url: "{{ route('consultaDadosDespesa') }}",
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
                { headerName: 'Id', field: 'id', maxWidth: 80 },
                { headerName: 'Nome', field: 'nome', minWidth: 80 },
                { 
                    headerName: 'Data do lançamento', 
                    field: 'data_lancamento', 
                    minWidth: 110,
                    valueFormatter: params => {
                        if (!params.value) return '';
                        const date = new Date(params.value);
                        if (isNaN(date)) return params.value;
                        return date.toLocaleDateString('pt-BR');
                    }
                },
                { headerName: 'Categoria', field: 'categoria', minWidth: 150 },
                { headerName: 'Valor', field: 'valor', minWidth: 200 },
                { headerName: 'Status', field: 'status', maxWidth: 110 },
                { headerName: 'Observação', field: 'observacao', minWidth: 200 },
                {
                    headerName: 'Ações',
                    field: 'acoes',
                    cellRenderer: function(params) {
                        const id = params.data.id;
                        const rota = `/despesas/${id}`;
                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        return `
                            <button class="btn btn-primary btn-sm btn-edit" data-id="${id}">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>
                            <form method="POST" action="${rota}" class="form-delete" style="display:inline;">
                                <input type="hidden" name="_token" value="${csrf}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-danger btn-sm btn-delete" data-id="${id}">
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
                    $.get('/despesas/' + id + '/edit', function(html) {
                        $('#conteudoModalEditarCliente').html(html);
                        $('#modalEditarCliente').modal('show');
                    });
                });

                $('#myGrid').on('click', '.btn-delete', function(e) {
                    e.preventDefault();
                    const confirmed = confirm('Tem certeza que deseja excluir esta despesa?');
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

