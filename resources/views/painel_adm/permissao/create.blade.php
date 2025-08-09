<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('permissoes.store') }}" method="POST">
    @csrf
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome">Nome</label>
            <input type="text" name="nome" class="form-control" required placeholder="Nome da permissão">
        </div>

        <div class="form-group col-md-6">
            <label for="descricao">Descrição</label>
            <input type="text" name="descricao" class="form-control"  placeholder="Descrição da permisssão">
        </div>
    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="button" class="btn background_cancel">Cancelar</button>
        <button type="submit" class="btn background_sucess">Cadastrar</button>
    </div>
</form>