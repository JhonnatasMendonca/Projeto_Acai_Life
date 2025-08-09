<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-2">

        <div class="form-group col-md-4">
            <label for="nome">Nome</label>
            <input type="text" name="nome_usuario" class="form-control" required placeholder="Nome do usuário" value="{{ $usuario->nome_usuario }}">
        </div>

        <div class="form-group col-md-3">
            <label for="login">Login</label>
            <input type="text" name="login_usuario" class="form-control"  placeholder="Login do usuário" value="{{ $usuario->login_usuario }}">
        </div>  

        <div class="form-group col-md-3">
            <label for="perfil">Perfil</label>
            
            <select name="perfil_id" class="form-control">
                @foreach ($perfis as $perfil)
                    <option value="{{ $perfil->id }}" {{ $usuario->perfil_id == $perfil->id ? 'selected' : '' }}>
                        {{ $perfil->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-2">
            <label for="status">Status</label>
            <select name="status_usuario" class="form-control">
                <option value="1" {{ $usuario->status_usuario == 1 ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ $usuario->status_usuario == 0 ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="submit" class="btn background_sucess">Atualizar</button>
    </div>
</form>