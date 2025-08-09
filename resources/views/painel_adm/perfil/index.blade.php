@extends('layouts.app')
@section('title', 'Açai Life - Atribuição de Perfil')
@section('content')

    <h2 class="mt-4 mb-2">
        <i class="bi bi-person-gear"></i> Atribuição de Perfil
    </h2>
    <hr style="border: 1px solid #fff;" class=" mb-3">
    <div class="" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="col-md-6 p-0" style="">
            <input type="search" class="form-control " placeholder="Pesquisar na tabela" id="searchInput" style="">
        </div>
        <div class="" style="display: flex;  align-items: center;gap: 10px;">
            <button class="btn  pesquisa" id="addClienteButton" style="width: 100%;" data-toggle="modal"
                data-target="#modalCadastrarPerfil">
                <i class="bi bi-person-plus"></i> Cadastrar Perfil
            </button>

        </div>

    </div>
    {{-- {{ dd($permissoes) }} --}}

    <div id="myGrid" style="height: 60vh;" class="ag-theme-alpine mt-4"></div>




    <!-- Chamada do modal de cadastro de cliente -->
    @include('partials.modal', [
        'id' => 'modalCadastrarPerfil',
        'title' => 'CADASTRO DE PERFIL',
        'slot' => view('painel_adm.perfil.create', compact('permissoes'))->render(),
        'hideFooter' => true,
    ])


    </div>


    <!-- Modal de Edição de Cliente -->
    <div class="modal fade" id="modalEditarPerfil" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Perfil</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="conteudoModalEditarPerfil">
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
                url: "{{ route('consultaDadosPerfil') }}",
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
            columnDefs: [{
                    headerName: 'Id',
                    field: 'id',
                    maxWidth: 80
                },
                {
                    headerName: 'Nome',
                    field: 'nome',
                    minWidth: 80
                },
                {
                    headerName: 'Descrição',
                    field: 'descricao',
                    minWidth: 150
                },
                {
                    headerName: 'Permissões',
                    field: 'permissoes',
                    valueGetter: function(params) {
                        return Array.isArray(params.data.permissoes) ?
                            params.data.permissoes.map(p => p.nome).join(', ') :
                            '';
                    },
                    minWidth: 200,
                },
                {
                    headerName: 'Criado em',
                    field: 'created_at',
                    valueFormatter: function(params) {
                        return new Date(params.value).toLocaleDateString('pt-BR', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                        });
                    },
                    minWidth: 120,
                },
                {
                    headerName: 'Atualizado em',
                    field: 'updated_at',
                    valueFormatter: function(params) {
                        return new Date(params.value).toLocaleDateString('pt-BR', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                        });
                    },
                    minWidth: 120,
                },
                {
                    headerName: 'Ações',
                    field: 'acoes',
                    cellRenderer: function(params) {
                        const id = params.data.id;
                        const rota = `/perfis/${id}`;
                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content');

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
                    $.get('/perfis/' + id + '/edit', function(html) {
                        $('#conteudoModalEditarPerfil').html(html);
                        $('#modalEditarPerfil').modal('show');
                    });
                });

                $('#myGrid').on('click', '.btn-delete', function(e) {
                    e.preventDefault();
                    const confirmed = confirm('Tem certeza que deseja excluir este Perfil?');
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
