@extends('layouts.app')
@section('title', 'Açai Life - Checkout de Vendas')
@section('content')
    <style>
        .comprovante {
            width: 300px;
            margin: 0 auto;
            background: #FAFAD2;
            border: 1px solid #ccc;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            color: #000;
        }

        .logo {
            text-align: center;
        }

        .logo img {
            width: 50px;
        }

        .title {
            font-weight: bold;
            text-align: center;
            margin-top: 5px;
        }

        .info {
            font-size: 12px;
            margin: 10px 0;
        }

        .produtos {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }

        .produtos th,
        .produtos td {
            text-align: left;
            padding: 2px 0;
        }

        .total {
            margin-top: 10px;
            font-size: 13px;
        }

        .agradecimento {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .comprovante {
                width: 58mm;
                box-shadow: none;
                border: none;
                margin: 0 auto;
                page-break-after: always;
            }
        }
    </style>

    <h2 class="mt-4 mb-2">
        <i class="bi bi-cash-coin"></i> Checkout de Vendas
    </h2>
    <hr style="border: 1px solid #fff;" class="mb-3">

    <div class="center">
        <div class="center_vendas col-md-12">
            <form id="formVenda" class="container-fluid formVenda col-md-12 g-2" action="{{ route('vendas.store') }}"
                method="POST">

                @csrf

                <div class="row g-2">
                    <div class="form-group col-md-3">
                        <label for="nome">Cliente (opcional)</label>
                        <select name="cliente_id" class="form-control">
                            <option disabled selected value="">Selecione um cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="usuario_id" value="{{ auth()->user()->id }}">

                    <div class="form-group col-md-6">
                        <label for="descricao">Descrição (opcional)</label>
                        <input type="text" name="descricao" class="form-control" placeholder="Descrição para registro">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="forma_pagamento">Forma de pagamento</label>
                        <select name="forma_pagamento" class="form-control" required>
                            <option disabled selected value="">Selecione uma forma de pagamento</option>
                            <option value="Pix">Pix</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Debito">Cartão de Débito</option>
                            <option value="Credito">Cartão de Crédito</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="status">Status da venda</label>
                        <select name="status" class="form-control" required>
                            <option disable selected value="">Selecione um status</option>
                            <option value="pendente">Pendente</option>
                            <option value="pago">Pago</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="desconto">Desconto (R$)</label>
                        <input type="number" step="0.01" min="0" name="desconto" id="descontoInput"
                            class="form-control" value="0.00">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="observacao">Observação (opcional)</label>
                        <input type="text" name="observacao" class="form-control"
                            placeholder="Ex: sem paçoca, e sem leite em pó...">
                    </div>


                    <div id="produtosContainer" class="col-md-12">
                        <div class="row mb-2 produtoRow">
                            <div class="col-md-5">
                                <label>Selecione o produto</label>
                                <select name="produtos[0][produto_id]" class="form-control produtoSelect" required>
                                    <option selected disabled value="">Selecione o produto</option>
                                    @foreach ($produtos as $produto)
                                        <option value="{{ $produto->id }}" data-preco="{{ $produto->preco_venda }}"
                                            data-unidade="{{ $produto->unidade }}">
                                            {{ $produto->nome_produto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Quantidade</label>
                                <input type="number" name="produtos[0][quantidade]"
                                    class="form-control quantidadeProdutoInput" min="0" step="1"
                                    placeholder="0">
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-success addProduto"><i
                                        class="bi bi-plus"></i></button>
                                <button type="button" class="btn btn-danger removeProduto"><i
                                        class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>



                </div>
            </form>
            <div class="center_buttons" style="display: flex;gap: 10px;">
                    <button class="btn  pesquisa" id="verRegistrosButton" style="width: 100%;" data-toggle="modal"
                        data-target="#modalCadastrarDespesa">
                        Visualizar Registros
                    </button>
                    <button type="button" class="btn pesquisa" id="registroButton"
                        style="background-color: #051D40;gap: 10px;" data-toggle="modal"
                        data-target="#modalRegistrarCompra" onmouseover="this.style.backgroundColor='#0a2b5c';"
                        onmouseout="this.style.backgroundColor='#051D40';">
                        Registrar Venda
                    </button>
                </div>

            </div>
        </div>
        <div class="overlay" style="display: none;">
            <div class="col-md-12 box_comprovante" style="">
                <div class="comprovante">
                    <div class="logo">
                        <img src="{{ asset('images/acai_nova_log.png') }}" alt="Açaí Life Logo">
                    </div>
                    <div class="title">Açaí Life</div>
                    <div class="info" style="text-align: center;">R. João Alves Berenguer, 63 - Monjope, Igarassu - PE
                    </div>

                    <div class="info">
                        Data: {{ date('d/m/Y') }}<br>
                        Pedido: {{ $proximoId }}<br>
                        Atendente: {{ auth()->user()->nome_usuario }}
                    </div>

                    <table class="produtos">
                        <thead>
                            <tr>
                                <th>Qtd</th>
                                <th>Produto</th>
                                <th>Vlr</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Itens adicionados via JS -->
                        </tbody>
                    </table>

                    <div class="total">
                        Subtotal: R$ 0,00<br>
                        Desconto: R$ 0,00<br>
                        <strong>Forma de pagamento:</strong> <span id="formaPag">-</span><br>
                        <strong>Total:</strong> R$ 0,00
                    </div>

                    <div class="agradecimento">Obrigado por escolher <strong>Açaí Life!</strong></div>
                </div>
                <div class="center_buttons" style="display: flex;gap: 10px; width:90%;margin: 0 auto">
                    <button class="btn  pesquisa" id="voltarButton" style="width: 100%;" data-toggle="modal"
                        data-target="#modalCadastrarDespesa">
                        Voltar
                    </button>
                    <button type="submit" form="formVenda" class="btn pesquisa" id="addCompraButton"
                        style="background-color: #051D40;gap: 10px;" data-toggle="modal"
                        data-target="#modalRegistrarCompra" onmouseover="this.style.backgroundColor='#0a2b5c';"
                        onmouseout="this.style.backgroundColor='#051D40';">
                        Confirmar Venda
                    </button>
                </div>
            </div>
        </div>
@endsection

<script type="module">
    function atualizarResumo() {
        let subtotal = 0;
        let comprovanteBody = $('.comprovante .produtos tbody');
        comprovanteBody.empty();

        $('.produtoRow').each(function() {
            let select = $(this).find('.produtoSelect');
            let quantidadeInput = $(this).find('.quantidadeProdutoInput');
            let produtoNome = select.find("option:selected").text();
            let preco = parseFloat(select.find("option:selected").data('preco')) || 0;
            let quantidade = parseFloat(quantidadeInput.val()) || 0;

            if (select.val() && quantidade > 0) {
                let totalProduto = preco * quantidade;
                subtotal += totalProduto;

                comprovanteBody.append(`
                    <tr>
                        <td>${quantidade}</td>
                        <td>${produtoNome}</td>
                        <td>${preco.toFixed(2)}</td>
                        <td>${totalProduto.toFixed(2)}</td>
                    </tr>
                `);
            }
        });

        let desconto = parseFloat($('#descontoInput').val()) || 0;
        let total = subtotal - desconto;

        $('.comprovante .total').html(`
            Subtotal: R$ ${subtotal.toFixed(2)}<br>
            Desconto: R$ ${desconto.toFixed(2)}<br>
            <strong>Forma de pagamento:</strong> <span id="formaPag">${$('select[name="forma_pagamento"]').val() || '-'}</span><br>
            <strong>Total:</strong> R$ ${total.toFixed(2)}
        `);
    }

    function atualizarNames() {
        $('#produtosContainer .produtoRow').each(function(index) {
            $(this).find('.produtoSelect').attr('name', `produtos[${index}][produto_id]`);
            $(this).find('.quantidadeProdutoInput').attr('name', `produtos[${index}][quantidade]`);
        });
    }

    $(document).on('change', '.produtoSelect, .quantidadeProdutoInput, #descontoInput', function() {
        atualizarResumo();
    });

    $(document).on('click', '.addProduto', function() {
        let clone = $(this).closest('.produtoRow').clone();
        clone.find('select').val('');
        clone.find('input').val('');
        $('#produtosContainer').append(clone);
        atualizarNames();
        atualizarResumo();
    });

    $(document).on('click', '.removeProduto', function() {
        if ($('#produtosContainer .produtoRow').length > 1) {
            $(this).closest('.produtoRow').remove();
            atualizarNames();
            atualizarResumo();
        }
    });

    $('select[name="forma_pagamento"]').on('change', function() {
        atualizarResumo();
    });

    $(document).ready(function() {
        atualizarNames();
        atualizarResumo();
    });

    $('#verRegistrosButton').on('click', function() {
        window.location.href = "{{ route('registroVendas') }}";
    });

    $('#registroButton').on('click', function() {
        $('.box_comprovante').css('visibility', 'visible');
        $('.overlay').fadeIn(200);
    });

    $('#voltarButton').on('click', function() {
        $('.box_comprovante').css('visibility', 'hidden');
        $('.overlay').fadeOut(200);
    });
</script>
