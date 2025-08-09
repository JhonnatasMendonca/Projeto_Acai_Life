<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('perfis.update', $perfil->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome">Nome</label>
            <input type="text" name="nome" class="form-control" required placeholder="Nome do perfil" value="{{ $perfil->nome }}">
        </div>

        <div class="form-group col-md-6">
            <label for="descricao">Descrição</label>
            <input type="text" name="descricao" class="form-control" placeholder="Descrição do perfil" value="{{ $perfil->descricao }}">
        </div>

        <div id="permissaoContainer" class="col-md-12">
            <div class="row mb-2 permissaoRow">
                <div class="col-md-5">
                    <label>Selecione uma permissão</label>
                    <select name="permissoes[]" class="form-control permissaoSelect" required>
                        <option selected disabled value="">Selecione uma permissão</option>
                        @foreach ($permissoes as $permissao)
                            <option value="{{ $permissao->id }}">
                                {{ $permissao->nome }} ({{ $permissao->descricao }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-success addPermissao"><i class="bi bi-plus"></i></button>
                    <button type="button" class="btn btn-danger removePermissao"><i class="bi bi-trash"></i></button>
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
    $(document).on('click', '.addPermissao', function() {
        let clone = $('.permissaoRow').first().clone();
        clone.find('select').val('');
        $('#permissaoContainer').append(clone); 
    });

    $(document).on('click', '.removePermissao', function() {
        if ($('.permissaoRow').length > 1) {
            $(this).closest('.permissaoRow').remove();
        }
    });
</script>
