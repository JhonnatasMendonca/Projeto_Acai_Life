<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('produtos.store') }}" method="POST">
    @csrf
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome_produto">Nome</label>
            <input type="text" name="nome_produto" class="form-control" required placeholder="Nome do produto">
        </div>
        <div class="form-group col-md-6">
            <label for="categoria">Categoria</label>
            <select name="categoria" class="form-control" required>
                <option disable selected value="">Selecione uma categoria</option>
                <option value="Salgados">Salgados</option>
                <option value="Doces">Doces</option>
                <option value="Açai">Açai</option>
            </select>
        </div>
        <div class="form-group col-md-12">
            <label for="descricao">Descrição (opcional)</label>
            <input type="text" name="descricao" class="form-control" placeholder="Descreva o produto">
        </div>
        <div class="form-group col-md-4">
            <label for="preco_venda">Preço de venda (R$)</label>
            <input type="number" name="preco_venda" class="form-control" required placeholder="0.00" min="0"
                step="0.01">
        </div>

        <div class="form-group col-md-4">
            <label for="preco_custo">Preço de custo (R$)</label>
            <input type="number" name="preco_custo" class="form-control" id="preco_custo" readonly>
        </div>

        <div class="form-group col-md-4">
            <label for="estoque_inicial">Estoque</label>
            <input id="inputEstoque" type="number" name="estoque_inicial" class="form-control" placeholder="0"
                step="1" min="0">
        </div>

        <div class="form-group col-md-4">
            <label for="alerta_estoque">Alerta de estoque</label>
            <input id="inputAlertaEstoque" type="number" name="alerta_estoque" class="form-control" required placeholder="0"
                step="1" min="0">
        </div>

        <div class="form-group col-md-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="usaInsumoCheckbox" name="usa_insumo" value="1"
                    checked>
                <label class="form-check-label" for="usaInsumoCheckbox">
                    Este produto leva insumo?
                </label>
            </div>
        </div>


        <div id="insumosContainer" class="col-md-12">
            <div class="row mb-2 insumoRow">
                <div class="col-md-3">
                    <label>Selecione o insumo</label>
                    <select name="insumos[]" class="form-control insumoSelect" >
                        <option selected disabled value="">Selecione o insumo</option>
                        @foreach ($insumos as $insumo)
                            <option value="{{ $insumo->id }}" data-unidade="{{ $insumo->unidade_medida }}"
                                data-preco="{{ $insumo->preco_custo }}">
                                {{ $insumo->nome_insumo }} ({{ $insumo->unidade_medida }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Quantidade</label>
                    <input type="number" name="quantidades[]" class="form-control quantidadeInput" min="0"
                        step="0.01" placeholder="0">
                </div>

                <div class="col-md-3">
                    <label>Gramatura (g)</label>
                    <input type="number" name="gramaturas[]" class="form-control gramaturaInput" min="0"
                        step="0.01" placeholder="0" style="display:none;">
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

        $('#usaInsumoCheckbox').on('change', function() {
            if ($(this).is(':checked')) {
                $('#insumosContainer').show();
                $('#preco_custo').prop('readonly', true);
                $('#usaInsumoCheckbox').val('1');
                $('#inputEstoque').val('0');
            } else {
                $('#preco_custo').prop('readonly', false);
                $('#insumosContainer').hide();
                $('#usaInsumoCheckbox').val('0');
            }
        });
        if (!$('#usaInsumoCheckbox').is(':checked')) {
            $('#insumosContainer').hide();
        }

        $('.background_cancel').on('click', function() {
            $(this).closest('.modal').modal('hide');
        });



        // Formatar para 2 casas decimais ao sair do campo
        $('#peso_total').on('blur', function() {
            let valor = parseFloat($(this).val());
            if (!isNaN(valor) && valor >= 0) {
                $(this).val(valor.toFixed(2));
            } else {
                $(this).val('');
            }
        });

        // $('#inputEstoque').on('input', function() {
        //     let valor = $(this).val();
        //     // Remove ponto e vírgula se houver
        //     if (valor.includes('.') || valor.includes(',')) {
        //         valor = parseInt(valor);
        //         if (isNaN(valor)) valor = '';
        //         $(this).val(valor);
        //     }
        // });

        $('#inputEstoque').on('input', function() {
            let valor = $(this).val().replace(',', '.'); // substitui vírgula por ponto
            if (valor && !/^(\d+(\.\d*)?)?$/.test(valor)) {
                valor = valor.slice(0, -1); // remove último caractere inválido
            }
            $(this).val(valor);
        });



        // Botão + para duplicar
        $(document).on('click', '.addInsumo', function() {
            let clone = $('.insumoRow').first().clone();
            clone.find('select').val('');
            clone.find('input').val('');
            $('#insumosContainer').append(clone);
        });

        // Botão 🗑️ para remover
        $(document).on('click', '.removeInsumo', function() {
            if ($('.insumoRow').length > 1) {
                $(this).closest('.insumoRow').remove();
                calcularPrecoCusto();
            }
        });

        // Ao mudar insumo, ajusta placeholder e step
        $(document).on('change', '.insumoSelect', function() {
            let unidade = $(this).find(':selected').data('unidade');
            let row = $(this).closest('.insumoRow');
            let inputQtd = row.find('.quantidadeInput');
            let inputGramatura = row.find('.gramaturaInput');

            if (unidade === 'Quilo') {
                inputQtd.attr('step', '0.01').attr('placeholder', 'Kg');
                inputGramatura.show().val(''); // mostra campo gramatura e limpa
                // inputQtd.hide(); // esconde o campo quantidade para evitar confusão
                inputQtd.val('1');
            } else {
                inputQtd.attr('step', '1').attr('placeholder', 'Unidades');
                inputQtd.show();
                inputGramatura.hide().val('');
            }

            calcularPrecoCusto();
        });

        $(document).on('input change', '.quantidadeInput, .insumoSelect, .gramaturaInput', function() {
            calcularPrecoCusto();
        });

        function calcularPrecoCusto() {
            let total = 0;

            $('.insumoRow').each(function() {
                let precoQuilo = parseFloat($(this).find('.insumoSelect option:selected').data(
                    'preco'));
                let unidade = $(this).find('.insumoSelect option:selected').data('unidade');
                let qtd = parseFloat($(this).find('.quantidadeInput').val());
                let gramatura = parseFloat($(this).find('.gramaturaInput').val());

                if (unidade === 'Quilo') {
                    if (!isNaN(precoQuilo) && !isNaN(gramatura)) {
                        // preço por grama = precoQuilo / 1000
                        total += (precoQuilo / 1000) * gramatura;
                    }
                } else {
                    if (!isNaN(precoQuilo) && !isNaN(qtd)) {
                        total += precoQuilo * qtd;
                    }
                }
            });

            $('input[name="preco_custo"]').val(total.toFixed(2));
        }

    });
</script>
