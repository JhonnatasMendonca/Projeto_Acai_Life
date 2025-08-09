<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permissao;
use Carbon\Carbon;

class PermissaoSeeder extends Seeder
{
    public function run()
    {
    Permissao::insert([
        [
            'nome' => 'visualizar inicio',
            'descricao' => 'Permite visualizar pagina inicial do sistema',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar painel administrativo',
            'descricao' => 'Permite visualizar o menu com as opções administrativas',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar controle financeiro',
            'descricao' => 'Permite visualizar o menu com as opções de controle financeiro',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar checkout de vendas',
            'descricao' => 'Permite visualizar o menu com as opções de venda',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela controle de estoque',
            'descricao' => 'Permite visualizar a tela de controle de estoque',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela cadastro de clientes',
            'descricao' => 'Permite visualizar a tela de cadastro de clientes',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela cadastro de usuarios',
            'descricao' => 'Permite visualizar a tela de cadastro de usuários',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela atribuição de perfil',
            'descricao' => 'Permite visualizar a tela de atribuição de perfil',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela cadastro de permissoes',
            'descricao' => 'Permite visualizar a tela de cadastro de permissões',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela registro de compras',
            'descricao' => 'Permite visualizar a tela de registro de compras',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela controle de despesas',
            'descricao' => 'Permite visualizar a tela de controle de despesas',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela fluxo de caixa',
            'descricao' => 'Permite visualizar a tela de fluxo de caixa',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela registro de vendas',
            'descricao' => 'Permite visualizar a tela de registro de vendas',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'visualizar tela iniciar venda',
            'descricao' => 'Permite visualizar a tela para iniciar uma venda',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    }
}

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
