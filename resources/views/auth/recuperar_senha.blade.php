@extends('layouts.app')
@section('title', 'Açai Life - Rcuperar Senha')
@section('content')
    <div class="div_canter">
        <img src="{{ asset('images/favicon.png') }}" alt="Açai Life Logo" class="img-fluid mx-auto d-block mt-5 mb-3"
            style="max-width: 200px;">
        <div style="width: 500px;" class="container mt-2 mb-5">
            <div class="position_center_row">
                <h5>ALTERAR SENHA</h5>
            </div>
            <form class="form_container" method="POST" action="{{ route('usuario.atualizarSenha') }}">
                @csrf
                {{-- @method('PATCH') --}}
                <div class="mb-3">
                    <label for="login_usuario" class="form-label label-black">Digite seu usuário</label>
                    <input type="text" class="form-control" id="login_usuario" name="login_usuario" required autofocus
                        placeholder="Login">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label label-black">Digite sua nova senha</label>
                    <input type="password" class="form-control" id="password" name="senha_usuario" required placeholder="Senha">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label label-black">Confirmar senha</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required placeholder="Senha">
                </div>

                <div class="d-flex justify-content-between">
                    <button id="voltarBtn" style="width: 40%;" type="button" class="btn btn-secondary">Cancelar</button>
                    <button style="width: 40%;" type="submit" class="btn btn-primary-interno">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script type="module">
        document.getElementById('voltarBtn').addEventListener('click', function() {
            window.location.href = "{{ route('login') }}";
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const senha = document.getElementById('password').value;

            const confirmar = document.getElementById('new_password').value;
            if (senha !== confirmar) {
                e.preventDefault();
                alert('As senhas não coincidem. Por favor, verifique.');
            }
        });
    </script>
@endsection
