<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntradaBitacoraRequest;
use App\Models\EntradaBitacora;
use App\Models\Mascota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BitacoraController extends Controller
{
    public function index(Mascota $mascota): View
    {
        Gate::authorize('view', $mascota);

        return view('bitacora.index', [
            'mascota' => $mascota,
            'entradas' => $mascota->entradas()->latest('fecha')->latest('id')->simplePaginate(12),
            'tipos' => EntradaBitacora::TIPOS,
        ]);
    }

    public function store(EntradaBitacoraRequest $request, Mascota $mascota): RedirectResponse
    {
        $mascota->entradas()->create($request->validated());

        return redirect()->route('mascotas.bitacora.index', $mascota)
            ->with('exito', 'Entrada guardada en la bitácora.');
    }

    public function edit(Mascota $mascota, EntradaBitacora $entrada): View
    {
        Gate::authorize('update', $mascota);

        return view('bitacora.edit', [
            'mascota' => $mascota,
            'entrada' => $entrada,
            'tipos' => EntradaBitacora::TIPOS,
        ]);
    }

    public function update(EntradaBitacoraRequest $request, Mascota $mascota, EntradaBitacora $entrada): RedirectResponse
    {
        $entrada->update($request->validated());

        return redirect()->route('mascotas.bitacora.index', $mascota)
            ->with('exito', 'Entrada actualizada.');
    }

    public function destroy(Mascota $mascota, EntradaBitacora $entrada): RedirectResponse
    {
        Gate::authorize('update', $mascota);

        $entrada->delete();

        return redirect()->route('mascotas.bitacora.index', $mascota)
            ->with('exito', 'Entrada eliminada.');
    }
}
