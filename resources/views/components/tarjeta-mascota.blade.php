{{--
    Tarjeta de una mascota con sus datos y acciones (ver, editar, eliminar).
    Uso: <x-tarjeta-mascota :mascota="$mascota" />
    Con :enlazar="false" el encabezado no es un enlace (para la vista de detalle).
--}}
@props(['mascota', 'enlazar' => true, 'titulo' => 'h2'])

@php
    $edad = $mascota->edad.' '.($mascota->edad === 1 ? 'año' : 'años');
    $pesoDecimal = number_format((float) $mascota->peso, 2, ',', '');
    $peso = rtrim(rtrim($pesoDecimal, '0'), ',').' kg';
@endphp

<article {{ $attributes->merge(['class' => 'pc-tarjeta']) }} aria-label="{{ $mascota->nombre }}">
    @if ($enlazar)
        <a href="{{ route('mascotas.show', $mascota) }}" class="pc-mascota">
    @else
        <div class="pc-mascota">
    @endif
        <span class="pc-mascota__foto pc-mascota__foto--{{ $mascota->especie }}">
            @if ($mascota->urlFoto())
                <img src="{{ $mascota->urlFoto() }}" alt="Foto de {{ $mascota->nombre }}" class="pc-mascota__imagen">
            @else
                <x-ilustracion :especie="$mascota->especie" :tamano="54" />
            @endif
        </span>
        <span class="pc-mascota__datos">
            <span class="pc-mascota__nombre">
                <{{ $titulo }}>{{ $mascota->nombre }}</{{ $titulo }}>
                <span class="pc-etiqueta">{{ $mascota->especieTexto() }}</span>
            </span>
            <span class="pc-mascota__detalle d-block">{{ $mascota->raza }}</span>
        </span>
        @if ($enlazar)
            <x-icono nombre="flecha" :tamano="18" class="pc-mascota__flecha" />
        @endif
    @if ($enlazar)
        </a>
    @else
        </div>
    @endif

    <div class="pc-datos pc-datos--dos">
        <div class="pc-dato">
            <p class="pc-dato__nombre">Edad</p>
            <p class="pc-dato__valor">{{ $edad }}</p>
        </div>
        <div class="pc-dato">
            <p class="pc-dato__nombre">Peso</p>
            <p class="pc-dato__valor">{{ $peso }}</p>
        </div>
    </div>

    {{ $slot }}

    <div class="pc-acciones-mascota">
        <a href="{{ route('mascotas.edit', $mascota) }}" class="pc-accion">
            <x-icono nombre="lapiz" :tamano="16" />Editar
        </a>
        <form method="POST" action="{{ route('mascotas.destroy', $mascota) }}"
              data-confirmacion="¿Seguro que quieres eliminar a {{ $mascota->nombre }}? Esta acción no se puede deshacer."
              onsubmit="return confirm(this.dataset.confirmacion);">
            @csrf
            @method('DELETE')
            <button type="submit" class="pc-accion pc-accion--peligro">
                <x-icono nombre="basurero" :tamano="16" />Eliminar
            </button>
        </form>
    </div>
</article>
