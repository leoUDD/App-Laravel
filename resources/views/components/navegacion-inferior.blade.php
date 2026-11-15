{{--
    Barra de navegación inferior del prototipo.
    "Asistente" e "Historial" se activan solos cuando existan las rutas
    consultas.create y consultas.index (parte de Sebastián).
--}}
@php
    $secciones = [
        ['ruta' => 'mascotas.index', 'activa' => 'mascotas.*', 'texto' => 'Mis mascotas', 'icono' => 'huella-contorno'],
        ['ruta' => 'consultas.create', 'activa' => ['consultas.create', 'consultas.show'], 'texto' => 'Asistente', 'icono' => 'chispas', 'asistente' => true],
        ['ruta' => 'consultas.index', 'activa' => 'consultas.index', 'texto' => 'Historial', 'icono' => 'reloj'],
    ];
@endphp

<nav class="pc-navegacion" aria-label="Navegación principal">
    @foreach ($secciones as $seccion)
        @php
            $existe = Route::has($seccion['ruta']);
            $activa = $existe && request()->routeIs(...(array) $seccion['activa']);
            $clases = 'pc-navegacion__item'
                .($activa ? ' pc-navegacion__item--activo' : '')
                .(! empty($seccion['asistente']) ? ' pc-navegacion__item--asistente' : '');
        @endphp

        @if ($existe)
            <a href="{{ route($seccion['ruta']) }}" class="{{ $clases }}" @if ($activa) aria-current="page" @endif>
                <span class="pc-navegacion__icono"><x-icono :nombre="$seccion['icono']" :tamano="22" :grosor="1.9" /></span>
                {{ $seccion['texto'] }}
            </a>
        @else
            <span class="{{ $clases }} pc-navegacion__item--deshabilitado" title="Disponible pronto">
                <span class="pc-navegacion__icono"><x-icono :nombre="$seccion['icono']" :tamano="22" :grosor="1.9" /></span>
                {{ $seccion['texto'] }}
            </span>
        @endif
    @endforeach

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="pc-navegacion__item">
            <span class="pc-navegacion__icono"><x-icono nombre="salir" :tamano="22" :grosor="1.9" /></span>
            Salir
        </button>
    </form>
</nav>
