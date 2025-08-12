<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('clientes.store') }}" method="POST">
    @csrf
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome">Nome</label>
            <input type="text" name="nome" class="form-control" required placeholder="Nome do cliente">
        </div>

        <div class="form-group col-md-6">
            <label for="Sobrenome">Sobrenome</label>
            <input type="text" name="sobrenome" class="form-control"  placeholder="Sobrenome do cliente">
        </div>
        <div class="form-group col-md-5">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control"  placeholder="Email do cliente">
        </div>

        <div class="form-group col-md-4">
            <label for="telefone">Telefone</label>
            <input type="tel" name="telefone" class="form-control" placeholder="Telefone do cliente">
        </div>

        <div class="form-group col-md-3">
            <label for="cep">CEP</label>
            <input type="text" name="cep" class="form-control"  placeholder="CEP do cliente">
        </div>    
        <div class="form-group col-md-12">
            <label for="endereco">Endereço</label>
            <input type="text" name="endereco" class="form-control"  placeholder="Endereço do cliente">
        </div>

    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="submit" class="btn background_sucess">Cadastrar</button>
    </div>
</form>