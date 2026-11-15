<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultaRequest;
use App\Models\Consulta;
use App\Services\IA\GroqService;
use App\Services\IA\ServicioIaNoDisponible;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConsultaController extends Controller
{
    private const MAXIMO_CONSULTAS_PREVIAS = 6;

    public function create(Request $request): View
    {
        $mascotas = $request->user()->mascotas()->orderBy('nombre')->get();
        $mascotaSeleccionada = $mascotas->firstWhere('id', $request->integer('mascota'))?->id
            ?? $mascotas->first()?->id;

        return view('consultas.create', compact('mascotas', 'mascotaSeleccionada'));
    }

    public function store(ConsultaRequest $request, GroqService $groq): RedirectResponse
    {
        $usuario = $request->user();
        $datos = $request->validated();
        $mascota = $usuario->mascotas()->findOrFail($datos['mascota_id']);
        $anterior = isset($datos['consulta_anterior_id'])
            ? $usuario->consultas()->where('mascota_id', $mascota->id)->findOrFail($datos['consulta_anterior_id'])
            : null;
        $historial = $anterior === null ? [] : $this->consultasPrevias($anterior)
            ->map(fn (Consulta $consulta): array => ['pregunta' => $consulta->pregunta, 'respuesta' => $consulta->respuesta])
            ->all();

        try {
            $respuesta = $groq->consultar($datos['pregunta'], $mascota, $historial);
        } catch (ServicioIaNoDisponible $error) {
            $destino = $anterior === null
                ? redirect()->route('consultas.create', ['mascota' => $mascota->id])
                : redirect()->route('consultas.show', $anterior);

            return $destino->withInput($datos)->with('error', $error->mensajeParaUsuario());
        }

        $consulta = DB::transaction(function () use ($usuario, $mascota, $anterior, $datos, $respuesta): Consulta {
            // La mascota pudo eliminarse mientras Groq preparaba la respuesta.
            $mascotaId = $usuario->mascotas()->whereKey($mascota->id)->sharedLock()->value('id');

            return $usuario->consultas()->create([
                'mascota_id' => $mascotaId,
                'mascota_nombre' => $mascota->nombre,
                'consulta_anterior_id' => $anterior?->id,
                'consulta_raiz_id' => $anterior?->consulta_raiz_id ?? $anterior?->id,
                'pregunta' => $datos['pregunta'],
                'respuesta' => $respuesta->respuesta,
                'nivel_riesgo' => $respuesta->nivelRiesgo,
                'requiere_veterinario' => $respuesta->requiereVeterinario,
            ]);
        });

        return redirect()->route('consultas.show', $consulta);
    }

    public function show(Request $request, string $consulta): View
    {
        $seleccionada = $this->buscarConsulta($request, $consulta);
        $raizId = $seleccionada->consulta_raiz_id ?? $seleccionada->id;

        $mensajes = $request->user()->consultas()
            ->where(fn (Builder $query) => $query->whereKey($raizId)->orWhere('consulta_raiz_id', $raizId))
            ->oldest('id')
            ->get();

        return view('consultas.show', [
            'consulta' => $seleccionada,
            'mensajes' => $mensajes,
        ]);
    }

    public function favorita(Request $request, string $consulta): RedirectResponse
    {
        $mensaje = $this->buscarConsulta($request, $consulta);
        $request->validate(['favorita' => ['required', 'boolean']]);

        $mensaje->favorita = $request->boolean('favorita');
        $mensaje->save();

        return back();
    }

    public function index(Request $request): View
    {
        $usuario = $request->user();
        $mascotas = $usuario->mascotas()->orderBy('nombre')->get(['id', 'nombre']);
        $mascotaSeleccionada = $mascotas->firstWhere('id', $request->integer('mascota'))?->id;
        $buscar = $request->query('buscar');
        $busqueda = is_string($buscar) ? Str::limit(trim($buscar), 120, '') : '';
        $soloFavoritas = $request->boolean('favoritas');
        $favorita = 'MAX(CASE WHEN favorita THEN 1 ELSE 0 END)';

        $resumen = $usuario->consultas()->getQuery()
            ->selectRaw('COALESCE(consulta_raiz_id, id) AS raiz_id, MAX(id) AS ultima_id, COUNT(*) AS intercambios')
            ->selectRaw("{$favorita} AS tiene_favoritas")
            ->selectRaw("MAX(CASE WHEN nivel_riesgo = 'alto' THEN 1 ELSE 0 END) AS tiene_alerta")
            ->groupByRaw('COALESCE(consulta_raiz_id, id)');

        if ($mascotaSeleccionada !== null) {
            $resumen->where('mascota_id', $mascotaSeleccionada);
        }

        if ($busqueda !== '') {
            $resumen->havingRaw('MAX(CASE WHEN LOWER(pregunta) LIKE LOWER(?) THEN 1 ELSE 0 END) = 1', ['%'.$busqueda.'%']);
        }

        if ($soloFavoritas) {
            $resumen->havingRaw("{$favorita} = 1");
        }

        $consultas = Consulta::query()
            ->fromSub($resumen, 'hilos')
            ->join('consultas as inicial', 'inicial.id', '=', 'hilos.raiz_id')
            ->join('consultas as ultima', 'ultima.id', '=', 'hilos.ultima_id')
            ->select([
                'ultima.id', 'ultima.pregunta', 'ultima.mascota_nombre', 'ultima.created_at',
                'inicial.pregunta as pregunta_inicial', 'hilos.intercambios', 'hilos.tiene_alerta', 'hilos.tiene_favoritas',
            ])
            ->orderByDesc('ultima.id')
            ->paginate(12)
            ->appends([
                'mascota' => $mascotaSeleccionada,
                'buscar' => $busqueda === '' ? null : $busqueda,
                'favoritas' => $soloFavoritas ? 1 : null,
            ]);

        return view('consultas.index', compact('mascotas', 'mascotaSeleccionada', 'busqueda', 'soloFavoritas', 'consultas'));
    }

    private function buscarConsulta(Request $request, string $consulta): Consulta
    {
        $id = filter_var($consulta, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        abort_if($id === false, 404);

        return $request->user()->consultas()->findOrFail($id);
    }

    /**
     * @return Collection<int, Consulta>
     */
    private function consultasPrevias(Consulta $consulta): Collection
    {
        $historial = collect([$consulta]);
        $consultasDeLaMascota = Consulta::query()
            ->where('user_id', $consulta->user_id)
            ->where('mascota_id', $consulta->mascota_id);

        while ($consulta->consulta_anterior_id !== null && $historial->count() < self::MAXIMO_CONSULTAS_PREVIAS) {
            $anterior = (clone $consultasDeLaMascota)->find($consulta->consulta_anterior_id);

            if ($anterior === null) {
                break;
            }

            $historial->prepend($anterior);
            $consulta = $anterior;
        }

        return $historial;
    }
}
