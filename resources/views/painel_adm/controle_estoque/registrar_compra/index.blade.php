<form class="container-fluid formModal col-md-12 g-2" action="{{ route('compras.store') }}" method="POST">
    @csrf
    <div class="row g-2">
        <div class="form-group col-md-12">
            <label for="nome_fornecedor">Nome do fornecedor</label>
            <input type="text" name="nome_fornecedor" class="form-control" required placeholder="Nome do fornecedor">
        </div>

        <div class="form-group col-md-2">
            <label for="valor_total">Valor total (R$)</label>
            <input type="number" name="valor_total" class="form-control" required placeholder="0.00" min="0"
                step="0.01">
        </div>

        <div class="form-group col-md-4">
            <label for="data_compra">Data da compra</label>
            <input type="text" name="data_compra" class="form-control" required placeholder="dd/mm/aaaa"
                onfocus="(this.type='date')" onblur="if(this.value==''){this.type='text'}">
        </div>

        <div class="form-group col-md-6">
            <label for="forma_pagamento">Forma de pagamento</label>
            <select name="forma_pagamento" class="form-control" required>
                <option disabled selected value="">Selecione uma forma de pagamento</option>
                <option value="Pix">Pix</option>
                <option value="Dinheiro">Dinheiro</option>
                <option value="Debito">Cartão de Débito</option>
                <option value="Credito">Cartão de Crédito</option>
            </select>
        </div>

        

        <div id="produtosContainer" class="col-md-12">
            <div class="row mb-2 produtoRow">
                <div class="col-md-4">
                    <label>Selecione o produto</label>
                    <select name="produto[]" class="form-control produtoSelect">
                        <option selected disabled value="">Selecione o produto</option>
                        @foreach ($produtos as $produto)
                            <option value="{{ $produto->id }}" data-preco="{{ $produto->preco_custo }}"
                                data-unidade="{{ $produto->unidade }}">
                                {{ $produto->nome_produto }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Quantidade</label>
                    <input type="number" name="quantidades_produto[]" class="form-control quantidadeProdutoInput"
                        min="0" step="0.01" placeholder="0">
                </div>

                <div class="col-md-3">
                    <label>Preço</label>
                    <input type="number" name="preços_produto[]" class="form-control precoProdutoInput" min="0"
                        step="0.01" placeholder="0">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-success addProduto"><i class="bi bi-plus"></i></button>
                    <button type="button" class="btn btn-danger removeProduto"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        </div>

        <div id="insumosContainer" class="col-md-12">
            <div class="row mb-2 insumoRow">
                <div class="col-md-4">
                    <label>Selecione o insumo</label>
                    <select name="insumos[]" class="form-control insumoSelect">
                        <option selected disabled value="">Selecione o insumo</option>
                        @foreach ($insumos as $insumo)
                            <option value="{{ $insumo->id }}" data-preco="{{ $insumo->preco_custo }}"
                                data-unidade="{{ $insumo->unidade }}">
                                {{ $insumo->nome_insumo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Quantidade</label>
                    <input type="number" name="quantidades_insumo[]" class="form-control quantidadeInput"
                        min="0" step="0.01" placeholder="0">
                </div>

                <div class="col-md-3">
                    <label>Preço</label>
                    <input type="number" name="preços_insumo[]" class="form-control precoInput" min="0"
                        step="0.01" placeholder="0">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-success addInsumo"><i class="bi bi-plus"></i></button>
                    <button type="button" class="btn btn-danger removeInsumo"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="button" class="btn background_cancel">Cancelar</button>
        <button type="submit" class="btn background_sucess">Cadastrar</button>
    </div>
</form>

<script type="module">
    $(document).ready(function() {

        $('.background_cancel').on('click', function() {
            $('#modalRegistrarCompra').modal('hide');
        });

        // PRODUTOS - Botão +
        $(document).on('click', '.addInsumo', function() {
            let clone = $(this).closest('.insumoRow').clone();
            clone.find('select').val('');
            clone.find('input').val('');
            $(this).closest('#insumosContainer').append(clone);
        });

        // PRODUTOS - Botão 🗑️
        $(document).on('click', '.removeInsumo', function() {
            let container = $('#insumosContainer'); // fixo, 100% certo
            if (container.find('.insumoRow').length > 1) {
                $(this).closest('.insumoRow').remove();
                console.log('Insumos:', container.find('.insumoRow').length);
            }
        });
        
        $(document).on('change', '.insumoSelect', function() {
            let selected = $(this).find(':selected');
            let unidade = selected.data('unidade');
            let preco = selected.data('preco');
            let row = $(this).closest('.insumoRow');

            row.find('.precoInput').val(preco);

            let inputQtd = row.find('.quantidadeInput');
            if (unidade === 'Quilo') {
                inputQtd.attr('step', '0.01').attr('placeholder', 'Kg');
            } else {
                inputQtd.attr('step', '1').attr('placeholder', 'Unidades');
            }
        });

        // PRODUTOS - Botão +
        $(document).on('click', '.addProduto', function() {
            let clone = $(this).closest('.produtoRow').clone();
            clone.find('select').val('');
            clone.find('input').val('');
            $(this).closest('#produtosContainer').append(clone);
        });

        // PRODUTOS - Botão 🗑️
        $(document).on('click', '.removeProduto', function() {
            let container = $('#produtosContainer'); // fixo, 100% certo
            if (container.find('.produtoRow').length > 1) {
                $(this).closest('.produtoRow').remove();
                console.log('Produtos:', container.find('.produtoRow').length);
            }
        });

        $(document).on('change', '.produtoSelect', function() {
            let selected = $(this).find(':selected');
            let unidade = selected.data('unidade');
            let preco = selected.data('preco');
            let row = $(this).closest('.produtoRow');

            row.find('.precoProdutoInput').val(preco);

            let inputQtd = row.find('.quantidadeProdutoInput');
            if (unidade === 'Quilo') {
                inputQtd.attr('step', '0.01').attr('placeholder', 'Kg');
            } else {
                inputQtd.attr('step', '1').attr('placeholder', 'Unidades');
            }
        });

    });
</script>
