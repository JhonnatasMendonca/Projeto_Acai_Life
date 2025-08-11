@extends('layouts.app')
@section('title', 'Açai Life - Registros de vendas')
@section('content')

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> --}}

    <!-- Div oculta para comprovante PDF -->
    <div id="comprovanteContainer" style="display:none;">
        <div class="comprovante"
            style="width:300px;margin:0 auto;background:#FAFAD2;border:1px solid #ccc;padding:10px;color:#000;">
            <div class="logo" style="text-align:center;">
                <img id="comprovanteLogo" src="{{ asset('images/acai_nova_log.png') }}" alt="Açaí Life Logo"
                    style="width:50px;">
            </div>
            <div class="title" style="font-weight:bold;text-align:center;margin-top:5px;">Açaí Life</div>
            <div class="info" style="text-align:center;">R. João Alves Berenguer, 63 - Monjope, Igarassu - PE</div>
            <div class="info" id="comprovanteInfo"></div>
            <table class="produtos" style="width:100%;font-size:12px;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Qtd</th>
                        <th>Produto</th>
                        <th>Vlr</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="comprovanteProdutos"></tbody>
            </table>
            <div class="total" id="comprovanteTotal" style="margin-top:10px;font-size:13px;"></div>
            <div class="agradecimento" style="text-align:center;margin-top:10px;font-size:12px;">Obrigado por escolher
                <strong>Açaí Life!</strong></div>
        </div>
    </div>

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
            columnDefs: [{
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
                            `;
                            S
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

        // Função global para salvar comprovante em PDF 58mm
        window.salvarComprovantePDF = function() {
            const comprovante = document.querySelector('.comprovante');
            if (!comprovante) return;
            html2canvas(comprovante, {
                scale: 2
            }).then(function(canvas) {
                const imgData = canvas.toDataURL('image/png');
                // 58mm = ~164px em 72dpi, mas jsPDF usa mm, então:
                const pdf = new window.jspdf.jsPDF({
                    orientation: 'p',
                    unit: 'mm',
                    format: [58, canvas.height * 58 / canvas.width]
                });
                pdf.addImage(imgData, 'PNG', 0, 0, 58, (canvas.height * 58 / canvas.width));
                pdf.save('comprovante.pdf');
            });
        }

        // Função para preencher comprovante
        function preencherComprovante(venda) {
            // Info principal
            $('#comprovanteInfo').html(
                `Data: ${venda.created_at ? new Date(venda.created_at).toLocaleDateString('pt-BR') : ''}<br>` +
                `Pedido: ${venda.id}<br>` +
                `Atendente: ${venda.usuario?.nome_usuario || '-'}<br>`
            );
            // Produtos
            let produtosHtml = '';
            if (venda.itens && venda.itens.length) {
                venda.itens.forEach(function(item) {
                    produtosHtml += `<tr>
                        <td>${item.quantidade}</td>
                        <td>${item.produto?.nome_produto || '-'}</td>
                        <td>${parseFloat(item.preco_unitario).toFixed(2)}</td>
                        <td>${parseFloat(item.total_item).toFixed(2)}</td>
                    </tr>`;
                });
            }
            $('#comprovanteProdutos').html(produtosHtml);
            // Totais
            $('#comprovanteTotal').html(
                `Subtotal: R$ ${parseFloat(venda.subtotal).toFixed(2)}<br>` +
                `Desconto: R$ ${parseFloat(venda.desconto).toFixed(2)}<br>` +
                `<strong>Forma de pagamento:</strong> ${venda.forma_pagamento || '-'}<br>` +
                `<strong>Total:</strong> R$ ${parseFloat(venda.total).toFixed(2)}`
            );
        }

        // Lógica de imprimir comprovante ao aprovar venda
        $(document).on('submit', 'form[action*="atualizar-status"]', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.mensagem);
                        if (response.imprimir && response.venda) {
                            preencherComprovante(response.venda);
                            $('#comprovanteContainer').show();
                            setTimeout(function() {
                                if (window.salvarComprovantePDF) {
                                    window.salvarComprovantePDF();
                                }
                                setTimeout(function() {
                                    $('#comprovanteContainer').hide();
                                    location.reload();
                                }, 1000);
                            }, 500);
                        } else {
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    } else {
                        toastr.error(response.mensagem || 'Erro ao atualizar status!');
                    }
                },
                error: function(xhr) {
                    let msg = 'Erro ao atualizar status!';
                    if (xhr.responseJSON && xhr.responseJSON.mensagem) {
                        msg = xhr.responseJSON.mensagem;
                    }
                    toastr.error(msg);
                }
            });
        });

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
