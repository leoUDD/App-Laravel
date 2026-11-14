<?php

namespace App\Http\Controllers;

use App\Http\Requests\MascotaRequest;
use App\Models\Mascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MascotaController extends Controller
{
    /**
     * Lista las mascotas del usuario autenticado.
     */
    public function index(Request $request): View
    {
        $mascotas = $request->user()->mascotas()->orderBy('nombre')->get();

        return view('mascotas.index', ['mascotas' => $mascotas]);
    }

    /**
     * Muestra el formulario para registrar una mascota.
     */
    public function create(): View
    {
        return view('mascotas.create', ['mascota' => new Mascota]);
    }

    /**
     * Guarda una nueva mascota del usuario autenticado.
     */
    public function store(MascotaRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except(['foto', 'quitar_foto']);

        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('mascotas', 'public');
        }

        $mascota = $request->user()->mascotas()->create($datos);

        return redirect()->route('mascotas.show', $mascota)
            ->with('exito', "{$mascota->nombre} fue registrado/a correctamente.");
    }

    /**
     * Muestra el perfil de una mascota.
     */
    public function show(Mascota $mascota): View
    {
        Gate::authorize('view', $mascota);

        return view('mascotas.show', ['mascota' => $mascota]);
    }

    /**
     * Muestra el formulario para editar una mascota.
     */
    public function edit(Mascota $mascota): View
    {
        Gate::authorize('update', $mascota);

        return view('mascotas.edit', ['mascota' => $mascota]);
    }

    /**
     * Actualiza los datos de una mascota.
     */
    public function update(MascotaRequest $request, Mascota $mascota): RedirectResponse
    {
        Gate::authorize('update', $mascota);

        $datos = $request->safe()->except(['foto', 'quitar_foto']);
        $fotoAnterior = $mascota->foto;

        if ($request->hasFile('foto')) {
            $datos['foto'] = $request->file('foto')->store('mascotas', 'public');
        } elseif ($request->boolean('quitar_foto')) {
            $datos['foto'] = null;
        }

        $mascota->update($datos);

        if ($fotoAnterior && $fotoAnterior !== $mascota->foto) {
            Storage::disk('public')->delete($fotoAnterior);
        }

        return redirect()->route('mascotas.show', $mascota)
            ->with('exito', 'Los datos de la mascota se actualizaron correctamente.');
    }

    /**
     * Elimina una mascota.
     */
    public function destroy(Mascota $mascota): RedirectResponse
    {
        Gate::authorize('delete', $mascota);

        $mascota->delete();

        if ($mascota->foto) {
            Storage::disk('public')->delete($mascota->foto);
        }

        return redirect()->route('mascotas.index')
            ->with('exito', "{$mascota->nombre} fue eliminado/a.");
    }
}
