@extends('plantillas.app')

@section('titulo', "Editar a {$mascota->nombre}")

@section('contenido')
    <div class="pc-pagina">
        <header class="pc-encabezado pc-encabezado--formulario">
            <a href="{{ route('mascotas.show', $mascota) }}" class="pc-boton-icono" aria-label="Volver al perfil de {{ $mascota->nombre }}">
                <x-icono nombre="volver" :tamano="20" :grosor="2.2" />
            </a>
            <div class="pc-encabezado__texto">
                <h1>Editar a {{ $mascota->nombre }}</h1>
                <p class="pc-encabezado__descripcion">Mantén sus datos al día para recibir mejores respuestas</p>
            </div>
        </header>

        <form method="POST" action="{{ route('mascotas.update', $mascota) }}" class="pc-formulario" novalidate>
            @method('PUT')
            @include('mascotas._formulario')

            <button type="submit" class="pc-boton pc-boton--primario">Guardar cambios</button>
            <a href="{{ route('mascotas.show', $mascota) }}" class="pc-boton pc-boton--secundario">Cancelar</a>
        </form>
    </div>
@endsection
