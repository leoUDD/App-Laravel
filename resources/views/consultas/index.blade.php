@extends('plantillas.app')

@section('titulo', 'Historial')

@section('contenido')
    <div class="pc-pagina">
        <header class="pc-encabezado">
            <div class="pc-encabezado__texto">
                <h1>Historial</h1>
                <p class="pc-encabezado__descripcion mb-0">
                    {{ $consultas->total() }} {{ $consultas->total() === 1 ? 'conversación' : 'conversaciones' }}
                </p>
            </div>
            <a href="{{ route('consultas.create') }}" class="pc-boton-icono" aria-label="Nueva consulta">
                <x-icono nombre="mas" :tamano="22" />
            </a>
        </header>

        <form method="GET" action="{{ route('consultas.index') }}" class="pc-consulta-buscador" role="search">
            @if ($mascotaSeleccionada !== null)
                <input type="hidden" name="mascota" value="{{ $mascotaSeleccionada }}">
            @endif
            @if ($soloFavoritas)
                <input type="hidden" name="favoritas" value="1">
            @endif
            <label for="pc-buscar-consulta" class="visually-hidden">Buscar en el historial</label>
            <input id="pc-buscar-consulta" type="search" name="buscar" value="{{ $busqueda }}"
                maxlength="120" class="pc-campo__entrada" placeholder="Buscar consultas…">
            <button type="submit" class="pc-boton pc-boton--secundario pc-boton--compacto">Buscar</button>
        </form>

        @php
            $filtros = array_filter([
                'buscar' => $busqueda === '' ? null : $busqueda,
                'mascota' => $mascotaSeleccionada,
            ], fn ($valor) => $valor !== null);
        @endphp

        <nav class="pc-consulta-filtros" aria-label="Filtrar historial por favoritos">
            <a href="{{ route('consultas.index', $filtros) }}" @if (!$soloFavoritas) aria-current="page" @endif>Historial completo</a>
            <a href="{{ route('consultas.index', [...$filtros, 'favoritas' => 1]) }}"
                @if ($soloFavoritas) aria-current="page" @endif>Favoritos</a>
        </nav>

        @if ($mascotas->isNotEmpty())
            @php
                unset($filtros['mascota']);
                if ($soloFavoritas) {
                    $filtros['favoritas'] = 1;
                }
            @endphp
            <nav class="pc-consulta-filtros" aria-label="Filtrar historial por mascota">
                <a href="{{ route('consultas.index', $filtros) }}"
                    @if ($mascotaSeleccionada === null) aria-current="page" @endif>Todas</a>
                @foreach ($mascotas as $mascota)
                    <a href="{{ route('consultas.index', [...$filtros, 'mascota' => $mascota->id]) }}"
                        @if ($mascotaSeleccionada === $mascota->id) aria-current="page" @endif>{{ $mascota->nombre }}</a>
                @endforeach
            </nav>
        @endif

        @if ($consultas->isNotEmpty())
            <ul class="pc-consulta-lista list-unstyled mb-0">
                @foreach ($consultas as $consulta)
                    <li>
                        <a class="pc-tarjeta pc-consulta-item" href="{{ route('consultas.show', $consulta) }}">
                            <span class="pc-consulta-item__icono"><x-icono nombre="huella-contorno" :tamano="21" /></span>
                            <span class="pc-consulta-item__texto">
                                <strong>{{ $consulta->pregunta_inicial }}</strong>
                                @if ($consulta->intercambios > 1)
                                    <small>{{ $consulta->intercambios }} intercambios · {{ $consulta->pregunta }}</small>
                                @endif
                                <small>{{ $consulta->mascota_nombre }} · {{ $consulta->created_at->translatedFormat('j M Y · H:i') }}</small>
                                @if ($consulta->tiene_favoritas)
                                    <span class="pc-etiqueta">Favorita</span>
                                @endif
                                @if ($consulta->tiene_alerta)
                                    <span class="pc-etiqueta pc-consulta-riesgo">Riesgo alto</span>
                                @endif
                            </span>
                            <x-icono nombre="flecha" :tamano="18" />
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="pc-vacio">
                <x-icono nombre="reloj" :tamano="30" />
                @if ($soloFavoritas && $busqueda === '' && $mascotaSeleccionada === null)
                    <h2>Aún no hay respuestas favoritas</h2>
                    <a href="{{ route('consultas.index') }}" class="pc-boton pc-boton--secundario">Ver historial completo</a>
                @elseif ($consultas->total() > 0 || $busqueda !== '' || $mascotaSeleccionada !== null)
                    <h2>Sin resultados</h2>
                    <a href="{{ route('consultas.index') }}" class="pc-boton pc-boton--secundario">Limpiar filtros</a>
                @else
                    <h2>Aún no tienes consultas registradas</h2>
                    <a href="{{ route('consultas.create') }}" class="pc-boton pc-boton--primario">Hacer mi primera consulta</a>
                @endif
            </div>
        @endif

        @if ($consultas->hasPages())
            <nav class="pc-consulta-paginacion" aria-label="Páginas del historial">
                @if ($consultas->previousPageUrl())
                    <a class="pc-boton pc-boton--secundario pc-boton--compacto" href="{{ $consultas->previousPageUrl() }}">Anterior</a>
                @endif
                <span class="pc-texto-secundario small">Página {{ $consultas->currentPage() }} de {{ $consultas->lastPage() }}</span>
                @if ($consultas->nextPageUrl())
                    <a class="pc-boton pc-boton--secundario pc-boton--compacto" href="{{ $consultas->nextPageUrl() }}">Siguiente</a>
                @endif
            </nav>
        @endif
    </div>
@endsection
