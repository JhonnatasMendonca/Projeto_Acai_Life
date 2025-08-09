<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('clientes.update', $cliente->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome">Nome</label>
            <input type="text" name="nome" class="form-control" required placeholder="Nome do cliente" value="{{ $cliente->nome }}">
        </div>

        <div class="form-group col-md-6">
            <label for="Sobrenome">Sobrenome</label>
            <input type="text" name="sobrenome" class="form-control"  placeholder="Sobrenome do cliente" value="{{ $cliente->sobrenome }}">
        </div>
        <div class="form-group col-md-5">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control"  placeholder="Email do cliente" value="{{ $cliente->email }}">
        </div>

        <div class="form-group col-md-4">
            <label for="telefone">Telefone</label>
            <input type="tel" name="telefone" class="form-control" placeholder="Telefone do cliente" value="{{ $cliente->telefone }}">
        </div>

        <div class="form-group col-md-3">
            <label for="cep">CEP</label>
            <input type="text" name="cep" class="form-control"  placeholder="CEP do cliente" value="{{ $cliente->cep }}">
        </div>    
        <div class="form-group col-md-12">
            <label for="endereco">Endereço</label>
            <input type="text" name="endereco" class="form-control"  placeholder="Endereço do cliente" value="{{ $cliente->endereco }}">
        </div>

    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="button" class="btn background_cancel">Cancelar</button>
        <button type="submit" class="btn background_sucess">Cadastrar</button>
    </div>
</form>