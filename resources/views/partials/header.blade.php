<nav class="navbar navbar-expand-md navbar-dark sticky-top" style="background-color: #051D40;">
    <a class="navbar-brand" href="{{ route('home') }}">
        <img src="{{ asset('images/acai_nova_log_2.png') }}" alt="Açai Life Logo" class="img-fluid mx-auto d-block"
            style="max-width: 50px; margin-left: 10px;">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNavDropdown" style="display: fkex; justify-content:space-between;">
            <ul class="navbar-nav">
                @can('visualizar inicio')
                    <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('home') }}">Inicio<span class="sr-only">(current)</span></a>
                    </li>
                @endcan
                @can(['visualizar painel administrativo','visualizar tela controle de estoque', 'visualizar tela cadastro de clientes', 'visualizar tela cadastro de usuarios', 'visualizar tela atribuição de perfil', 'visualizar tela cadastro de permissoes'])
                    <li class="nav-item dropdown {{ request()->routeIs(['controleEstoque', 'clientes.index', 'usuarios.index', 'perfis.index', 'permissoes.index']) ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Painel administrativo
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        @can('visualizar tela controle de estoque')
                            <a class="dropdown-item" href="{{ route('controleEstoque') }}">Controle de estoque</a>
                        @endcan
                        @can('visualizar tela cadastro de clientes')
                            <a class="dropdown-item" href="{{ route('clientes.index') }}">Cadastro de clientes</a>
                        @endcan
                        @can('visualizar tela cadastro de usuarios')
                            <a class="dropdown-item" href="{{ route('usuarios.index') }}">Cadastro de usuários</a>
                        @endcan
                        @can('visualizar tela atribuição de perfil')
                            <a class="dropdown-item" href="{{ route('perfis.index') }}">Atribuição de perfil</a>
                        @endcan
                        @can('visualizar tela cadastro de permissoes')
                            <a class="dropdown-item" href="{{ route('permissoes.index') }}">Cadastro de permissões</a>
                        @endcan
                    </div>
                </li>
                @endcan
                @can(['visualizar controle financeiro', 'visualizar tela fluxo de caixa', 'visualizar tela controle de despesas', 'visualizar tela registro de compras'])
                    <li class="nav-item dropdown {{ request()->routeIs(['caixas.index', 'despesas.index', 'compras.index']) ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Controle financeiro
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        @can('visualizar tela registro de compras')
                            <a class="dropdown-item" href="{{ route('compras.index') }}">Registros de compras</a>
                        @endcan
                        @can('visualizar tela controle de despesas')
                            <a class="dropdown-item" href="{{ route('despesas.index') }}">Controle de despesas</a>
                        @endcan
                        @can('visualizar tela fluxo de caixa')
                            <a class="dropdown-item" href="{{ route('caixas.index') }}">Fluxo de caixa</a>
                        @endcan
                    </div>
                </li>
                @endcan
                @can(['visualizar checkout de vendas'])
                    <li class="nav-item dropdown {{ request()->routeIs(['vendas.index', 'registroVendas']) ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Checkout de vendas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        @can('visualizar tela registro de vendas')
                            <a class="dropdown-item" href="{{ route('registroVendas') }}">Registros de vendas</a>
                        @endcan
                        @can('visualizar tela iniciar venda')
                            <a class="dropdown-item"  href="{{ route('vendas.index') }}"> Iniciar venda</a>
                        @endcan
                    </div>
                </li>
                @endcan
            </ul>
            <div>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                            style="color: #fff; display: flex; align-items: center;"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i style="font-size: 20px; margin-right:10px;" class="bi bi-person-circle"></i>{{ auth()->user()->nome_usuario}}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                            {{-- <a class="dropdown-item" href="{{ route('perfil') }}">Perfil</a> --}}
                            <a class="dropdown-item" href="{{ route('logout') }}">Sair</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
