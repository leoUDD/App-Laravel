<?php

use App\Http\Controllers\Autenticacion\LoginController;
use App\Http\Controllers\Autenticacion\RegistroController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ConsultaController;
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
    Route::resource('mascotas.bitacora', BitacoraController::class)
        ->parameters(['bitacora' => 'entrada'])
        ->scoped(['entrada' => 'id'])
        ->where(['mascota' => '[0-9]+', 'entrada' => '[0-9]+'])
        ->only(['index', 'store', 'edit', 'update', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Consultas a la IA e historial (Sebastián)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/consultas', [ConsultaController::class, 'index'])->name('consultas.index');
    Route::get('/consultas/crear', [ConsultaController::class, 'create'])->name('consultas.create');
    Route::post('/consultas', [ConsultaController::class, 'store'])->name('consultas.store');
    Route::patch('/consultas/{consulta}/favorita', [ConsultaController::class, 'favorita'])
        ->whereNumber('consulta')->name('consultas.favorita');
    Route::get('/consultas/{consulta}', [ConsultaController::class, 'show'])->whereNumber('consulta')->name('consultas.show');
});
