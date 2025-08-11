@extends('layouts.app')
@section('title', 'Açai Life - Cadastro de Clientes')
@section('content')

    <h2 class="mt-4 mb-2">
        <i class="bi bi-person cad-add"></i> Cadastro de Clientes
    </h2>
    <hr style="border: 1px solid #fff;" class=" mb-3">
    <div class="box-buttons" style="display: flex; justify-content: space-between; align-items: center; ">
        <div class="col-md-6 p-0" style="">
            <input type="search" class="form-control btn-search" placeholder="Pesquisar na tabela" id="searchInput" style="">
        </div>
        <div class="" style="display: flex;  align-items: center; gap: 10px;">
            <button class="btn pesquisa btn-cadastrar-cliente" id="addClienteButton" style="width: 100%;" data-toggle="modal"
                data-target="#modalCadastrarCliente" style="">
                <i class="bi bi-person-add"></i> Cadastrar Cliente
            </button>

        </div>

    </div>

    <div id="myGrid" style="height: 60vh;" class="ag-theme-alpine mt-4"></div>




    <!-- Chamada do modal de cadastro de cliente -->
    @include('partials.modal', [
        'id' => 'modalCadastrarCliente',
        'title' => 'CADASTRO DE CLIENTE',
        'slot' => view('painel_adm.cliente.create')->render(),
        'hideFooter' => true,
    ])


    </div>


    <!-- Modal de Edição de Cliente -->
    <div class="modal fade" id="modalEditarCliente" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Cliente</h5>
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
                url: "{{ route('consultaDadosCliente') }}",
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
                { headerName: 'Nome', field: 'nome', minWidth: 80 },
                { headerName: 'Sobrenome', field: 'sobrenome', minWidth: 150 },
                { headerName: 'Email', field: 'email', minWidth: 200 },
                { headerName: 'Telefone', field: 'telefone', minWidth: 110, minWidth: 110 },
                { headerName: 'CEP', field: 'cep', maxWidth: 110 },
                { headerName: 'Endereco', field: 'endereco', minWidth: 200 },
                {
                    headerName: 'Ações',
                    field: 'acoes',
                    cellRenderer: function(params) {
                        const id = params.data.cpf_usuario;
                        const rota = `/clientes/${id}`;
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
                    $.get('/clientes/' + id + '/edit', function(html) {
                        $('#conteudoModalEditarCliente').html(html);
                        $('#modalEditarCliente').modal('show');
                    });
                });

                $('#myGrid').on('click', '.btn-delete', function(e) {
                    e.preventDefault();
                    const confirmed = confirm('Tem certeza que deseja excluir este cliente?');
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

