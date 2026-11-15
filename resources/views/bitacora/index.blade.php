@extends('plantillas.app')

@section('titulo', 'Bitácora de '.$mascota->nombre)

@section('contenido')
    <div class="pc-pagina">
        <header class="pc-encabezado pc-encabezado--formulario">
            <a href="{{ route('mascotas.show', $mascota) }}" class="pc-boton-icono" aria-label="Volver al perfil">
                <x-icono nombre="volver" :tamano="20" />
            </a>
            <div class="pc-encabezado__texto">
                <h1>Bitácora de {{ $mascota->nombre }}</h1>
            </div>
        </header>

        <details @if ($errors->any()) open @endif>
            <summary class="pc-boton pc-boton--primario w-100">
                <x-icono nombre="mas" :tamano="18" /> Nueva entrada
            </summary>
            <form method="POST" action="{{ route('mascotas.bitacora.store', $mascota) }}" class="pc-formulario pc-tarjeta mt-3">
                @csrf
                @include('bitacora._campos', ['entrada' => null])
                <button type="submit" class="pc-boton pc-boton--primario">Guardar entrada</button>
            </form>
        </details>

        @forelse ($entradas as $entrada)
            <article class="pc-tarjeta">
                <header>
                    <h2 class="h6 mb-1">{{ $entrada->tipoTexto() }}</h2>
                    <time class="pc-texto-secundario small" datetime="{{ $entrada->fecha->toDateString() }}">
                        {{ $entrada->fecha->translatedFormat('j M Y') }}
                    </time>
                </header>

                <p class="pc-bitacora-nota">{{ $entrada->nota }}</p>

                <div class="pc-acciones-mascota">
                    <a class="pc-accion" href="{{ route('mascotas.bitacora.edit', [$mascota, $entrada]) }}">
                        <x-icono nombre="lapiz" :tamano="16" /> Editar
                    </a>
                    <form method="POST" action="{{ route('mascotas.bitacora.destroy', [$mascota, $entrada]) }}"
                        onsubmit="return confirm('¿Eliminar esta entrada de la bitácora?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="pc-accion pc-accion--peligro">
                            <x-icono nombre="basurero" :tamano="16" /> Eliminar
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="pc-vacio">
                <x-icono nombre="reloj" :tamano="30" />
                <h2>Aún no hay entradas</h2>
            </div>
        @endforelse

        @if ($entradas->hasPages())
            <nav class="pc-consulta-paginacion" aria-label="Páginas de la bitácora">
                @if ($entradas->previousPageUrl())
                    <a href="{{ $entradas->previousPageUrl() }}" class="pc-boton pc-boton--secundario pc-boton--compacto">Anterior</a>
                @endif
                @if ($entradas->nextPageUrl())
                    <a href="{{ $entradas->nextPageUrl() }}" class="pc-boton pc-boton--secundario pc-boton--compacto">Siguiente</a>
                @endif
            </nav>
        @endif
    </div>
@endsection
