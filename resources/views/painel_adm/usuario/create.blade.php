<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('usuarios.store') }}" method="POST">
    @csrf
    <div class="row g-2">

        <div class="form-group col-md-4">
            <label for="nome">Nome</label>
            <input type="text" name="nome_usuario" class="form-control" required placeholder="Nome do usuário">
        </div>

        <div class="form-group col-md-4">
            <label for="login">Login</label>
            <input type="text" name="login_usuario" class="form-control"  placeholder="Login do usuário">
        </div>  

        <div class="form-group col-md-4">
            <label for="senha">Senha</label>
            <input type="password" name="senha_usuario" class="form-control"  placeholder="Senha do usuário">
        </div>

        <div class="form-group col-md-6">
            <label for="perfil">Perfil</label>
            <select name="perfil_id" class="form-control">
                @foreach ($perfis as $perfil)
                    <option value="{{ $perfil->id }}">{{ $perfil->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-2">
            <label for="status">Status</label>
            <select name="status_usuario" class="form-control">
                <option value="1">Ativo</option>
                <option value="0">Inativo</option>
            </select>
        </div>

    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="submit" class="btn background_sucess">Cadastrar</button>
    </div>
</form>