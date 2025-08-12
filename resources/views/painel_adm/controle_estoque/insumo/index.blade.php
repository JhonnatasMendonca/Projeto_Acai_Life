<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('insumos.store') }}" method="POST">
    @csrf
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome_insumo">Nome</label>
            <input type="text" name="nome_insumo" class="form-control" required placeholder="Nome do insumo">
        </div>
        <div class="form-group col-md-6">
            <label for="categoria_insumo">Categoria</label>
            <select name="categoria_insumo" class="form-control" required>
                <option disable selected value="">Selecione uma categoria</option>
                <option value="Salgados">Salgados</option>
                <option value="Doces">Doces</option>
                <option value="Açai">Açai</option>
                <option value="Descartaveis">Descartáveis</option>
                <option value="Outros">Outros</option>
            </select>
        </div>
        <div class="form-group col-md-12">
            <label for="descricao_insumo">Descrição (opcional)</label>
            <input type="text" name="descricao_insumo" class="form-control" placeholder="Descreva o insumo">
        </div>
        <div class="form-group col-md-4">
            <label for="preco_custo">Preço de custo (R$)</label>
            <input type="number" name="preco_custo" class="form-control" required placeholder="0.00" min="0"
                step="0.01">
        </div>
        <div class="form-group col-md-4">
            <label for="estoque_insumo">Estoque</label>
            <input id="inputEstoque" type="number" name="estoque_insumo" class="form-control" required placeholder="0"
                step="0.01" min="0">
        </div>
        
        <div class="form-group col-md-4">
            <label for="alerta_estoque">Alerta de estoque</label>
            <input id="inputAlertaEstoque" type="number" name="alerta_estoque" class="form-control" required placeholder="0"
                step="1" min="0">
        </div>

        <div class="form-group col-md-4">
            <label for="unidade_medida">Unidade de medida</label>
            <select id="unidadeMedida" name="unidade_medida" class="form-control" required>
                <option disable value="">Selecione a medida</option>
                <option value="Unidade">Unidade (Un)</option>
                <option value="Quilo">Quilo (Kg)</option>
            </select>
        </div>

        <div style="display: none;" class="form-group col-md-4 peso">
            <label for="peso_total">Peso total (kg)</label>
            <input id="peso_total" type="number" name="peso_total" class="form-control" placeholder="0.00"
                step="0.01" min="0">
        </div>

    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="submit" class="btn background_sucess">Cadastrar</button>
    </div>
</form>

<script type="module">
    $(document).ready(function() {

        $('.background_cancel').on('click', function() {
            $('#modalCadastrarInsumo').modal('hide');
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
                $(this).val(valor.toFixed(2)); // mantém o ponto decimal
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
                // Mantém o valor com até duas casas decimais
                $(this).val(num.toString().replace('.', ','));
            } else if (valor === '') {
                $(this).val('');
            } else {
                // Remove caracteres inválidos
                $(this).val(valor.replace(/[^0-9.,]/g, ''));
            }
        });
    });
</script>
