@extends('layouts.app')
@section('title', 'Açai Life - Login')
@section('content')
    <div class="div_canter">
        <img src="{{ asset('images/favicon.PNG') }}" alt="Açai Life Logo" class="img-fluid mx-auto d-block mt-5 mb-3" style="max-width: 200px;">
        <div style="width: 400px;" class="container mt-2 mb-5 login-t" >
            <div class="position_center_row mb-3">
                <div class="line"></div>
                <h5>LOGIN</h5>
                <div class="line"></div>
            </div>
            <form class="form_container" method="POST" action="{{ route('entrar') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" class="form-control" id="cpf" name="login_usuario" required autofocus placeholder="Login">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="senha_usuario" required placeholder="Senha">
                </div>
                <div class="mb-3">
                    <a href="{{ route('recuperar-senha') }}" class="text-decoration-none esquci_senha">Esqueci minha senha</a>
                </div>
                <button type="submit" class="btn btn-primary-interno">Entrar</button>
            </form>
        </div>
    </div>
@endsection


