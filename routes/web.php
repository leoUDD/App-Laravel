<?php

use App\Http\Controllers\Autenticacion\LoginController;
use App\Http\Controllers\Autenticacion\RegistroController;
use App\Http\Controllers\MascotaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('mascotas.index')
        : view('autenticacion.bienvenida');
})->name('bienvenida');

/*
|--------------------------------------------------------------------------
| Autenticación (Leandro)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/registro', [RegistroController::class, 'create'])->name('registro');
    Route::post('/registro', [RegistroController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Mascotas (Leandro)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::resource('mascotas', MascotaController::class);
});

/*
|--------------------------------------------------------------------------
| Consultas a la IA e historial (Sebastián)
|--------------------------------------------------------------------------
*/
