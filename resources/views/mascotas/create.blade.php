@extends('plantillas.app')

@section('titulo', 'Registrar mascota')

@section('contenido')
    <div class="pc-pagina">
        <header class="pc-encabezado pc-encabezado--formulario">
            <a href="{{ route('mascotas.index') }}" class="pc-boton-icono" aria-label="Volver a mis mascotas">
                <x-icono nombre="volver" :tamano="20" :grosor="2.2" />
            </a>
            <div class="pc-encabezado__texto">
                <h1>Registrar mascota</h1>
                <p class="pc-encabezado__descripcion">PetCare AI usará estos datos para personalizar sus respuestas</p>
            </div>
        </header>

        <form method="POST" action="{{ route('mascotas.store') }}" class="pc-formulario" novalidate>
            @include('mascotas._formulario')

            <button type="submit" class="pc-boton pc-boton--primario">Guardar perfil</button>
        </form>
    </div>
@endsection
