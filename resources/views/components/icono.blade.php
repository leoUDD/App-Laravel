{{--
    Íconos del prototipo de PetCare AI. Toman el color del texto (currentColor).
    Uso: <x-icono nombre="volver" :tamano="20" />
--}}
@props(['nombre', 'tamano' => 24, 'grosor' => 2])

@php
    $relleno = [
        'chispa' => '<path d="M12 2l2 6 6 2-6 2-2 6-2-6-6-2 6-2z"/>',
        'chispas' => '<path d="M12 2l2 6 6 2-6 2-2 6-2-6-6-2 6-2z"/><path d="M19 15l.8 2.2 2.2.8-2.2.8L19 21l-.8-2.2-2.2-.8 2.2-.8z"/>',
        'huella' => '<circle cx="6.5" cy="9" r="2"/><circle cx="10.5" cy="5.5" r="2"/><circle cx="15.5" cy="6.3" r="2"/><circle cx="18.6" cy="10.8" r="2"/><path d="M7.6 17.2c0-2.8 2.2-5.4 4.8-5.4s4.8 2.6 4.8 5.4c0 1.8-1.4 2.7-2.8 2.4-1-.2-1.4-.6-2-.6s-1 .4-2 .6c-1.4.3-2.8-.6-2.8-2.4z"/>',
        'corazon' => '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>',
    ];

    $trazo = [
        'chispa-contorno' => '<path d="M12 3l1.8 4.7 4.7 1.8-4.7 1.8L12 16l-1.8-4.7-4.7-1.8 4.7-1.8z"/>',
        'escudo' => '<path d="M12 3l7 3v5c0 5-3 8.5-7 10-4-1.5-7-5-7-10V6z"/>',
        'mas' => '<path d="M12 5v14M5 12h14"/>',
        'enviar' => '<path d="M4 12 20 4l-6 16-3-7z"/>',
        'volver' => '<path d="M15 6l-6 6 6 6"/>',
        'flecha' => '<path d="M9 6l6 6-6 6"/>',
        'check' => '<path d="M5 12l5 5 9-10"/>',
        'camara' => '<path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>',
        'reloj' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'hogar' => '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z"/>',
        'huella-contorno' => '<circle cx="6.5" cy="9" r="1.8"/><circle cx="10.5" cy="5.5" r="1.8"/><circle cx="15.5" cy="6.3" r="1.8"/><circle cx="18.6" cy="10.8" r="1.8"/><path d="M7.6 17.2c0-2.8 2.2-5.4 4.8-5.4s4.8 2.6 4.8 5.4c0 1.8-1.4 2.7-2.8 2.4-1-.2-1.4-.6-2-.6s-1 .4-2 .6c-1.4.3-2.8-.6-2.8-2.4z"/>',
        'lapiz' => '<path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M13.5 6.5l4 4"/>',
        'basurero' => '<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/><path d="M10 11v6M14 11v6"/>',
        'salir' => '<path d="M15 4h4v16h-4"/><path d="M10 8l-4 4 4 4M6 12h10"/>',
        'alerta' => '<path d="M12 3 2 20h20z"/><path d="M12 10v4M12 17h.01"/>',
        'exito' => '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>',
    ];

    $esRelleno = array_key_exists($nombre, $relleno);
    $contenido = $esRelleno ? $relleno[$nombre] : ($trazo[$nombre] ?? '');
@endphp

<svg {{ $attributes->merge([
    'width' => $tamano,
    'height' => $tamano,
    'viewBox' => '0 0 24 24',
    'aria-hidden' => 'true',
    'focusable' => 'false',
]) }}
    @if ($esRelleno)
        fill="currentColor"
    @else
        fill="none" stroke="currentColor" stroke-width="{{ $grosor }}" stroke-linecap="round" stroke-linejoin="round"
    @endif
>{!! $contenido !!}</svg>
