<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PainelAdm\UsuarioController;
use App\Http\Controllers\PainelAdm\InsumoController;
use App\Http\Controllers\PainelAdm\ProdutoController;
use App\Http\Controllers\RegistrarCompraController;
use App\Http\Controllers\PainelAdm\ClienteController;
use App\Http\Controllers\Vendas\VendaController;
use App\Http\Controllers\ControleFinanceiro\DespesaController;
use App\Http\Controllers\ControleFinanceiro\CaixaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PainelAdm\ControleEstoqueController;
use App\Http\Controllers\PainelAdm\PerfilController;
use App\Http\Controllers\PainelAdm\PermissaoController;
use App\Http\Controllers\ControleFinanceiro\RetiradaCaixaController;
use App\Http\Controllers\CupomController;
use App\Http\Controllers\HomeController;

/**
 * Rotas públicas (sem autenticação)
 */
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::get('/recuperar-senha', [LoginController::class, 'esqueciSenha'])->name('recuperar-senha');
Route::post('/login', [LoginController::class, 'login'])->name('entrar');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('/usuario/atualizar-senha', [UsuarioController::class, 'atualizarSenha'])->name('usuario.atualizarSenha');

/**
 * Rotas protegidas por autenticação
 */
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/controle-estoque', [ControleEstoqueController::class, 'index'])->name('controleEstoque');

    Route::post('/consultaDadosEstoque', [ControleEstoqueController::class, 'consultaDados'])->name('consultaDadosEstoque');
    Route::post('/consultaDadosCliente', [ClienteController::class, 'consultaDados'])->name('consultaDadosCliente');
    Route::post('/consultaDadosUsuario', [UsuarioController::class, 'consultaDados'])->name('consultaDadosUsuario');
    Route::post('/consultaDadosPerfil', [PerfilController::class, 'consultaDados'])->name('consultaDadosPerfil');
    Route::post('/consultaDadosPermissao', [PermissaoController::class, 'consultaDados'])->name('consultaDadosPermissao');
    Route::post('/consultaDadosCaixas', [CaixaController::class, 'consultaDados'])->name('consultaDadosCaixas');
    Route::post('/consultaDadosDespesa', [DespesaController::class, 'consultaDados'])->name('consultaDadosDespesa');
    Route::post('/consultaDadosCompras', [RegistrarCompraController::class, 'consultaDados'])->name('consultaDadosCompras');
    Route::post('/consultaDadosVendas', [VendaController::class, 'consultaDados'])->name('consultaDadosVendas');

    Route::resource('usuarios', UsuarioController::class);
    Route::resource('insumos', InsumoController::class);
    Route::resource('produtos', ProdutoController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('perfis', PerfilController::class);
    Route::resource('permissoes', PermissaoController::class);
    Route::resource('compras', RegistrarCompraController::class);
    Route::resource('caixas', CaixaController::class);
    Route::resource('retiradaCaixa', RetiradaCaixaController::class);
    Route::resource('despesas', DespesaController::class);
    Route::resource('vendas', VendaController::class);

    Route::get('registros-de-vendas', [VendaController::class,'registrosVendas'])->name('registroVendas');
    Route::post('/vendas/{id}/atualizar-status', [VendaController::class, 'update'])->name('vendas.update.status');

    Route::post('caixas/{id}/movimentar', [CaixaController::class, 'movimentar'])->name('caixas.movimentar');
    Route::post('caixas/{id}/fechar', [CaixaController::class, 'fechar'])->name('caixas.fechar');

    Route::get('/cupom/{id}', [CupomController::class, 'show']);
    Route::get('/cupom/{id}/download', [CupomController::class, 'download']);

   
});
