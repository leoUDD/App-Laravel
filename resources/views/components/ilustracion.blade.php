{{--
    Ilustraciones de perro y gato del prototipo.
    Uso: <x-ilustracion especie="perro" :tamano="54" />
    Con "etiqueta" la imagen se anuncia a lectores de pantalla; sin ella es decorativa.
--}}
@props(['especie' => 'perro', 'tamano' => 54, 'etiqueta' => null])

<svg {{ $attributes->merge(['width' => $tamano, 'height' => $tamano, 'viewBox' => '0 0 100 100']) }}
    @if ($etiqueta) role="img" aria-label="{{ $etiqueta }}" @else aria-hidden="true" focusable="false" @endif>
    @if ($especie === 'gato')
        <path d="M20 16 L44 33 L24 50 Z" fill="#7C8B86"/>
        <path d="M80 16 L56 33 L76 50 Z" fill="#7C8B86"/>
        <path d="M25 25 L38 34 L27 44 Z" fill="#F2B8B0"/>
        <path d="M75 25 L62 34 L73 44 Z" fill="#F2B8B0"/>
        <ellipse cx="50" cy="57" rx="31" ry="27" fill="#9BA9A3"/>
        <ellipse cx="50" cy="67" rx="13" ry="9" fill="#EEF2F0"/>
        <ellipse cx="38" cy="52" rx="4.6" ry="5.2" fill="#3FA374"/>
        <ellipse cx="62" cy="52" rx="4.6" ry="5.2" fill="#3FA374"/>
        <ellipse cx="38" cy="52" rx="1.5" ry="4" fill="#1E2A25"/>
        <ellipse cx="62" cy="52" rx="1.5" ry="4" fill="#1E2A25"/>
        <path d="M46.5 61h7L50 65z" fill="#E58C84"/>
        <path d="M50 65v3M45 70c2.5 2 7.5 2 10 0" stroke="#1E2A25" stroke-width="1.8" fill="none" stroke-linecap="round"/>
        <path d="M31 63H15M31 67l-14 4M69 63h16M69 67l14 4" stroke="#5E6B66" stroke-width="1.4" stroke-linecap="round"/>
    @else
        <ellipse cx="22" cy="44" rx="13" ry="24" fill="#B9783F" transform="rotate(18 22 44)"/>
        <ellipse cx="78" cy="44" rx="13" ry="24" fill="#B9783F" transform="rotate(-18 78 44)"/>
        <circle cx="50" cy="52" r="30" fill="#E3A866"/>
        <ellipse cx="50" cy="67" rx="17" ry="13" fill="#F7E6CC"/>
        <circle cx="39" cy="48" r="4" fill="#1E2A25"/>
        <circle cx="61" cy="48" r="4" fill="#1E2A25"/>
        <circle cx="40.3" cy="46.7" r="1.3" fill="#fff"/>
        <circle cx="62.3" cy="46.7" r="1.3" fill="#fff"/>
        <ellipse cx="50" cy="60" rx="6" ry="4.5" fill="#1E2A25"/>
        <path d="M50 64.5v4M44 70c3 3 9 3 12 0" stroke="#1E2A25" stroke-width="2" fill="none" stroke-linecap="round"/>
        <ellipse cx="57" cy="76" rx="3.5" ry="4.5" fill="#E8848B"/>
    @endif
</svg>
