<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('insumos.update', $insumo->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome_insumo">Nome</label>
            <input type="text" name="nome_insumo" class="form-control" required placeholder="Nome do insumo"
                value="{{ $insumo->nome_insumo }}">
        </div>
        <div class="form-group col-md-6">
            <label for="categoria_insumo">Categoria</label>
            <select name="categoria_insumo" class="form-control" required>
                <option disabled {{ old('categoria_insumo', $insumo->categoria_insumo) == '' ? 'selected' : '' }}
                    value="">
                    Selecione uma categoria
                </option>
                <option value="Salgados"
                    {{ old('categoria_insumo', $insumo->categoria_insumo) == 'Salgados' ? 'selected' : '' }}>
                    Salgados
                </option>
                <option value="Doces"
                    {{ old('categoria_insumo', $insumo->categoria_insumo) == 'Doces' ? 'selected' : '' }}>
                    Doces
                </option>
                <option value="Açai"
                    {{ old('categoria_insumo', $insumo->categoria_insumo) == 'Açai' ? 'selected' : '' }}>
                    Açai
                </option>
            </select>
        </div>
        <div class="form-group col-md-12">
            <label for="descricao_insumo">Descrição (opcional)</label>
            <input type="text" name="descricao_insumo" class="form-control" placeholder="Descreva o insumo"
                value="{{ $insumo->descricao_insumo }}">
        </div>
        <div class="form-group col-md-4">
            <label for="preco_custo">Preço de custo (R$)</label>
            <input type="number" name="preco_custo" class="form-control" required placeholder="0.00" min="0"
                step="0.01" value="{{ $insumo->preco_custo }}">
        </div>
        <div class="form-group col-md-4">
            <label for="estoque_insumo">Estoque inicial</label>
            <input id="inputEstoque" type="number" name="estoque_insumo" class="form-control" required placeholder="0"
                step="0.01" min="0" value="{{ $insumo->estoque_insumo }}">
        </div>
        <div class="form-group col-md-4">
            <label for="unidade_medida">Unidade de medida</label>
            <select id="unidadeMedida" name="unidade_medida" class="form-control" required>
                <option disabled {{ old('unidade_medida', $insumo->unidade_medida) == '' ? 'selected' : '' }} value="">
                    Selecione a medida
                </option>
                <option value="Unidade" {{ old('unidade_medida', $insumo->unidade_medida) == 'Unidade' ? 'selected' : '' }}>
                    Unidade (Un)
                </option>
                <option value="Quilo" {{ old('unidade_medida', $insumo->unidade_medida) == 'Quilo' ? 'selected' : '' }}>
                    Quilo (Kg)
                </option>
            </select>
        </div>

        <div style="" class="form-group col-md-4 peso">
            <label for="peso_total">Peso total (kg)</label>
            <input id="peso_total" type="number" name="peso_total" class="form-control" placeholder="0.00"
                step="0.01" min="0" value="{{ $insumo->peso_total }}">
        </div>

    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        {{-- <button type="button" class="btn background_cancel">Cancelar</button> --}}
        <button type="submit" class="btn background_sucess">Atualizar</button>
    </div>
</form>

<script type="module">
    $(document).ready(function() {

        $('.background_cancel').on('click', function() {
            $(this).closest('.modal').modal('hide');
        });

        $('#unidadeMedida').on('change', function() {
            var valorSelecionado = $(this).val();
            if (valorSelecionado === 'Quilo') {
                $('.peso').show();
            } else {
                $('.peso').hide();
            }
        });

        // Formatar para 2 casas decimais ao sair do campo
        $('#peso_total').on('blur', function() {
            let valor = parseFloat($(this).val().replace(',', '.'));
            if (!isNaN(valor) && valor >= 0) {
                $(this).val(valor.toFixed(2).replace('.', ','));
            } else {
                $(this).val('');
            }
        });

        // Permitir vírgulas e pontos decimais no estoque
        $('#inputEstoque').on('input', function() {
            let valor = $(this).val();
            // Substitui vírgula por ponto para parseFloat
            valor = valor.replace(',', '.');
            let num = parseFloat(valor);
            if (!isNaN(num) && num >= 0) {
                // Mantém o valor digitado, mas limita a um ponto/virgula decimal
                $(this).val(valor.replace(/[^0-9.,]/g, ''));
            } else if (valor === '') {
                $(this).val('');
            } else {
                $(this).val('');
            }
        });

        // Formatar para 2 casas decimais ao sair do campo estoque
        $('#inputEstoque').on('blur', function() {
            let valor = $(this).val().replace(',', '.');
            let num = parseFloat(valor);
            if (!isNaN(num) && num >= 0) {
                $(this).val(num.toFixed(2).replace('.', ','));
            } else {
                $(this).val('');
            }
        });
    });
</script>
