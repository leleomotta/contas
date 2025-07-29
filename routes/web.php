<?php

use App\Http\Controllers\CartaoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\ReceitaController;
use App\Http\Controllers\RecorrenciaController;
use App\Http\Controllers\TransferenciaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContaController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//php artisan make:model XXXXX -mcr --api

//php artisan route:cache
//php artisan route:clear

//Instruções para poder colocar no ar:
//arrumar o .env => cp .env.example .env
//rodar o update do composer => composer update
//gerar a chave no .env => php artisan key:generate
//faz isso que eu não sei o porque => php artisan storage:link
//depois ela usa o migrate, mas eu já faço o banco

//php artisan config:cache

//php artisan optimize
//php artisan optimize:clear

//php artisan storage::link


//lista os comandos do artisan
//php artisan list

//deixa o botão de login na inicial
Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

//Route::get('contas',[ContaController::class, 'showAll'])->name('contas.showAll')->middleware('auth');;
//Route::get('contas',[ContaController::class, 'showAll'])->name('contas.showAll');

Route::group(['middleware'=>['auth']], function(){

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'showAll'])->name('home.showAll');

    //----------------- CARTÃO -----------------
    Route::get('cartoes',[CartaoController::class, 'showAll'])->name('cartoes.showAll');
    Route::get('cartoes/novo',[CartaoController::class, 'new'])->name('cartoes.new');
    Route::post('cartoes/salvar',[CartaoController::class, 'store'])->name('cartoes.store');
    //Route::delete('cartoes/apagar/{ID_Receita}',[CartaoController::class, 'destroy'])->name('cartoes.destroy');
    //Route::get('cartoes/editar/{ID_Receita}',[CartaoController::class, 'edit'])->name('cartoes.edit');
    //Route::put('cartoes/atualiza/{ID_Receita}',[CartaoController::class, 'update'])->name('cartoes.update');
    //----------------- CARTÃO -----------------

    //----------------- CATEGORIAS -----------------
    Route::get('categorias',[CategoriaController::class, 'showAll'])->name('categorias.showAll');
    Route::get('categorias/novo',[CategoriaController::class, 'new'])->name('categorias.new');
    Route::post('categorias/salvar',[CategoriaController::class, 'store'])->name('categorias.store');
    Route::delete('categorias/apagar/{ID_Categoria}',[CategoriaController::class, 'destroy'])->name('categorias.destroy');
    Route::get('categorias/editar/{ID_Categoria}',[CategoriaController::class, 'edit'])->name('categorias.edit');
    Route::put('categorias/atualiza/{ID_Categoria}',[CategoriaController::class, 'update'])->name('categorias.update');
    //----------------- CATEGORIAS -----------------

    //----------------- CONTAS -----------------
    Route::get('contas',[ContaController::class, 'showAll'])->name('contas.showAll');
    Route::get('contas/novo',[ContaController::class, 'new'])->name('contas.new');
    Route::post('contas/salvar',[ContaController::class, 'store'])->name('contas.store');
    Route::get('contas/editar/{ID_Conta}',[ContaController::class, 'edit'])->name('contas.edit');
    Route::put('contas/atualiza/{ID_Conta}',[ContaController::class, 'update'])->name('contas.update');
    //----------------- CONTAS -----------------

    //----------------- DESPESAS -----------------
    Route::get('despesas',[DespesaController::class, 'showAll'])->name('despesas.showAll');
    Route::get('despesas/novo',[DespesaController::class, 'new'])->name('despesas.new');
    Route::post('despesas/salvar',[DespesaController::class, 'store'])->name('despesas.store');
    Route::get('despesas/filter',[DespesaController::class, 'filter'])->name('despesas.filter');
    Route::delete('despesas/apagar/{ID_Despesa}',[DespesaController::class, 'destroy'])->name('despesas.destroy');
    Route::get('despesas/efetiva/{ID_Despesa}',[DespesaController::class, 'efetiva'])->name('despesas.efetiva');
    Route::get('despesas/editar/{ID_Despesa}',[DespesaController::class, 'edit'])->name('despesas.edit');
    Route::put('despesas/atualiza/{ID_Despesa}',[DespesaController::class, 'update'])->name('despesas.update');
    //----------------- DESPESAS -----------------

    Route::get('recorrencias/novo',[DespesaController::class, 'recorrencias_new'])->name('recorrencias.new');
    Route::post('recorrencias/salvar', [DespesaController::class, 'recorrencias_store'])->name('recorrencias.store');
    Route::get('recorrencias/gerar/{mes}/{ano}', [RecorrenciaController::class, 'gerarRecorrencias'])->name('recorrencias.gerar');



    //----------------- FATURA -----------------
    //FATURA PRA MIM É Cartão. Vai ficar no controle do CartaoController por pragmatismo
    Route::get('fatura',[CartaoController::class, 'fatura'])->name('cartoes.fatura');
    Route::get('fatura/despesa/novo',[CartaoController::class, 'new_despesa'])->name('cartoes.new_despesa');
    Route::post('fatura/despesa/salvar',[CartaoController::class, 'store_despesa'])->name('cartoes.store_despesa');
    Route::delete('fatura/despesas/apagar/{ID_Despesa}',[CartaoController::class, 'destroy_despesa'])->name('cartoes.destroy_despesa');
    Route::get('fatura/despesas/editar/{ID_Despesa}',[CartaoController::class, 'edit_despesa'])->name('cartoes.edit_despesa');
    Route::put('fatura/despesas/atualiza/{ID_Despesa}',[CartaoController::class, 'update_despesa'])->name('cartoes.update_despesa');
    Route::get('fatura/fechar',[CartaoController::class, 'fatura_fechar'])->name('cartoes.fatura_fechar');
    Route::get('fatura/reabrir',[CartaoController::class, 'fatura_reabrir'])->name('cartoes.fatura_reabrir');
    //----------------- FATURA -----------------

    //----------------- RECEITAS -----------------
    Route::get('receitas',[ReceitaController::class, 'showAll'])->name('receitas.showAll');
    Route::get('receitas/novo',[ReceitaController::class, 'new'])->name('receitas.new');
    Route::post('receitas/salvar',[ReceitaController::class, 'store'])->name('receitas.store');
    Route::get('receitas/filter',[ReceitaController::class, 'filter'])->name('receitas.filter');
    Route::delete('receitas/apagar/{ID_Receita}',[ReceitaController::class, 'destroy'])->name('receitas.destroy');
    Route::get('receitas/efetiva/{ID_Receita}',[ReceitaController::class, 'efetiva'])->name('receitas.efetiva');
    Route::get('receitas/editar/{ID_Receita}',[ReceitaController::class, 'edit'])->name('receitas.edit');
    Route::put('receitas/atualiza/{ID_Receita}',[ReceitaController::class, 'update'])->name('receitas.update');
    //----------------- RECEITAS -----------------

    //----------------- TRANSFERENCIAS -----------------
    Route::get('transferencias',[TransferenciaController::class, 'showAll'])->name('transferencias.showAll');
    Route::get('transferencias/novo',[TransferenciaController::class, 'new'])->name('transferencias.new');
    Route::post('transferencias/salvar',[TransferenciaController::class, 'store'])->name('transferencias.store');
    Route::delete('transferencias/apagar/{ID_Transferencia}',[TransferenciaController::class, 'destroy'])->name('transferencias.destroy');
    Route::get('transferencias/editar/{ID_Transferencia}',[TransferenciaController::class, 'edit'])->name('transferencias.edit');
    Route::put('transferencias/atualiza/{ID_Transferencia}',[TransferenciaController::class, 'update'])->name('transferencias.update');
    //----------------- TRANSFERENCIAS -----------------

});
/*

Route::get('/cartoes', function () {
    return view('cartao');
});
*/
