<form class="container-fluid formModal col-md-12 g-2" action="{{ route('retiradaCaixa.store') }}" method="POST">
    @csrf
    <div class="row g-2">

        {{-- Usuário: pode ser oculto ou select --}}
        <input type="hidden" name="usuario_id" value="{{$userId}}"> 

        {{-- Senha --}}
        <div class="form-group col-md-4">
            <label for="senha">Senha</label>
            <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required>
        </div>

        {{-- Valor --}}
        <div class="form-group col-md-2">
            <label for="valor">Valor da Retirada</label>
            <input type="number" name="valor" class="form-control" placeholder="R$" step="0.01" min="0.01" required>
        </div>

        {{-- Descrição --}}
        <div class="form-group col-md-6">
            <label for="descricao">Descrição</label>
            <input type="text" name="descricao" class="form-control" placeholder="Descrição da retirada">
        </div>

    </div>

    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="submit" class="btn background_sucess">Registrar Retirada</button>
    </div>
</form>
