<?php

namespace App\Http\Controllers\Autenticacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistroRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistroController extends Controller
{
    /**
     * Muestra el formulario de registro.
     */
    public function create(): View
    {
        return view('autenticacion.registro');
    }

    /**
     * Crea la cuenta e inicia sesión automáticamente.
     */
    public function store(RegistroRequest $request): RedirectResponse
    {
        $usuario = User::create($request->validated());

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('mascotas.index')
            ->with('exito', "¡Bienvenido/a a PetCare AI, {$usuario->name}! Registra tu primera mascota.");
    }
}
