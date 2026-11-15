@extends('plantillas.app')

@section('titulo', $mascota->nombre)

@section('contenido')
    <div class="pc-pagina">
        <header class="pc-encabezado pc-encabezado--formulario">
            <a href="{{ route('mascotas.index') }}" class="pc-boton-icono" aria-label="Volver a mis mascotas">
                <x-icono nombre="volver" :tamano="20" :grosor="2.2" />
            </a>
            <div class="pc-encabezado__texto">
                <h1>Perfil de {{ $mascota->nombre }}</h1>
                <p class="pc-encabezado__descripcion">Registrada el {{ $mascota->created_at->format('d-m-Y') }}</p>
            </div>
        </header>

        <x-tarjeta-mascota :mascota="$mascota" :enlazar="false" titulo="h2" />

        <a href="{{ route('mascotas.bitacora.index', $mascota) }}" class="pc-boton pc-boton--secundario">
            <x-icono nombre="reloj" :tamano="20" /> Abrir bitácora
        </a>

        <x-tarjeta-asistente :mascota="$mascota" />
    </div>
@endsection
