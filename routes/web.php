<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ReceitaController;
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

//deixa o botão de login na inicial
Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

//Route::get('contas',[ContaController::class, 'showAll'])->name('contas.showAll')->middleware('auth');;
//Route::get('contas',[ContaController::class, 'showAll'])->name('contas.showAll');

Route::group(['middleware'=>['auth']], function(){
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //Contas
    Route::get('contas',[ContaController::class, 'showAll'])->name('contas.showAll');
    Route::get('contas/novo',[ContaController::class, 'new'])->name('contas.new');
    Route::post('contas/salvar',[ContaController::class, 'store'])->name('contas.store');

    Route::get('receitas',[ReceitaController::class, 'showAll'])->name('receitas.showAll');
    Route::get('receitas/novo',[ReceitaController::class, 'new'])->name('receitas.new');
    Route::post('receitas/salvar',[ReceitaController::class, 'store'])->name('receitas.store');
    Route::get('filter',[ReceitaController::class, 'filter'])->name('receitas.filter');
//gitbunda
    Route::get('categorias',[CategoriaController::class, 'showAll'])->name('categorias.showAll');
});
/*

Route::get('/cartoes', function () {
    return view('cartao');
});
*/
