@extends('plantillas.app')

@section('titulo', 'Editar entrada de la bitácora')

@section('contenido')
    <div class="pc-pagina">
        <header class="pc-encabezado pc-encabezado--formulario">
            <a href="{{ route('mascotas.bitacora.index', $mascota) }}" class="pc-boton-icono" aria-label="Volver a la bitácora">
                <x-icono nombre="volver" :tamano="20" />
            </a>
            <div class="pc-encabezado__texto">
                <h1>Editar entrada</h1>
                <p class="pc-encabezado__descripcion mb-0">{{ $mascota->nombre }}</p>
            </div>
        </header>

        <form method="POST" action="{{ route('mascotas.bitacora.update', [$mascota, $entrada]) }}" class="pc-formulario pc-tarjeta">
            @csrf
            @method('PATCH')
            @include('bitacora._campos')
            <button type="submit" class="pc-boton pc-boton--primario">Guardar cambios</button>
        </form>
    </div>
@endsection
