<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('permissoes.update', $permissao->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome">Nome</label>
            <input type="text" name="nome" class="form-control" required placeholder="Nome da permissão" value="{{ $permissao->nome }}">
        </div>

        <div class="form-group col-md-6">
            <label for="descricao">Descrição</label>
            <input type="text" name="descricao" class="form-control"  placeholder="Descrição da permisssão" value="{{ $permissao->descricao }}">
        </div>
    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="submit" class="btn background_sucess">Atualizar</button>
    </div>
</form>