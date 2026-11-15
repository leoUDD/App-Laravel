@extends('plantillas.app')

@section('titulo', 'Mis mascotas')

@section('contenido')
    <div class="pc-pagina">
        <header class="pc-encabezado">
            <div class="pc-encabezado__texto">
                <p class="pc-encabezado__fecha">{{ ucfirst(now()->locale('es')->translatedFormat('l, j \d\e F')) }}</p>
                <h1>Hola, {{ Str::before(auth()->user()->name, ' ') }}</h1>
            </div>
            <a href="{{ route('mascotas.create') }}" class="pc-boton-icono" aria-label="Registrar mascota">
                <x-icono nombre="mas" :tamano="22" :grosor="2.2" />
            </a>
        </header>

        <x-tarjeta-asistente :mascota="$mascotas->first()" />

        <section aria-labelledby="titulo-mascotas">
            <div class="pc-titulo-seccion">
                <h2 id="titulo-mascotas">Mis mascotas</h2>
                @if ($mascotas->isNotEmpty())
                    <span class="pc-texto-secundario small">{{ $mascotas->count() }} {{ $mascotas->count() === 1 ? 'registrada' : 'registradas' }}</span>
                @endif
            </div>

            @if ($mascotas->isEmpty())
                <div class="pc-vacio">
                    <div class="d-flex gap-2" aria-hidden="true">
                        <x-ilustracion especie="perro" :tamano="64" />
                        <x-ilustracion especie="gato" :tamano="58" />
                    </div>
                    <h2>Aún no tienes mascotas registradas</h2>
                    <p>Registra a tu perro o gato para recibir orientación personalizada.</p>
                    <a href="{{ route('mascotas.create') }}" class="pc-boton pc-boton--primario">Registrar mi primera mascota</a>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach ($mascotas as $mascota)
                        <x-tarjeta-mascota :mascota="$mascota" />
                    @endforeach

                    <a href="{{ route('mascotas.create') }}" class="pc-agregar">
                        <x-icono nombre="mas" :tamano="18" :grosor="2.4" />Agregar mascota
                    </a>
                </div>
            @endif
        </section>

        <p class="pc-aviso-legal">
            {{ config('app.name') }} entrega orientación general y no reemplaza la atención de un médico veterinario.
        </p>
    </div>
@endsection
