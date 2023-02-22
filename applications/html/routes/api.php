<?php

use App\Http\Controllers\CepController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\PacienteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//CEP
Route::get('/cep/{cep}', [CepController::class, 'show']);

//Endereços
Route::get('/enderecos', [EnderecoController::class, 'index'])->name('enderecos.index');
Route::post('/enderecos', [EnderecoController::class, 'store'])->name('enderecos.store');
Route::get('/enderecos/{endereco}', [EnderecoController::class, 'show'])->name('enderecos.show');
Route::put('/enderecos/{endereco}', [EnderecoController::class, 'update'])->name('enderecos.update');
Route::delete('/enderecos/{endereco}', [EnderecoController::class, 'destroy'])->name('enderecos.destroy');

//Pacientes
Route::get('pacientes', [PacienteController::class, 'index'])->name('pacientes.index');
Route::get('pacientes/{id}', [PacienteController::class, 'show'])->name('pacientes.show');
Route::post('pacientes', [PacienteController::class, 'store'])->name('pacientes.store');
Route::put('/pacientes/{id}', [PacienteController::class, 'update'])->name('pacientes.update');
Route::delete('pacientes/{id}', [PacienteController::class, 'destroy'])->name('pacientes.destroy');
Route::post('/pacientes/import', [PacienteController::class, 'import'])->name('pacientes.import');
