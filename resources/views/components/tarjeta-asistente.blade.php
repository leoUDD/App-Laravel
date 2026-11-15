{{--
    Tarjeta oscura que invita a consultar a la IA.
    Si todavía no existe la ruta consultas.create, se muestra como "Disponible pronto".
--}}
@props(['mascota' => null])

@php
    $disponible = Route::has('consultas.create');
    $titulo = $mascota ? "¿Tienes una duda sobre {$mascota->nombre}?" : '¿Tienes una duda sobre tu mascota?';
@endphp

<section class="pc-asistente" aria-label="Asistente PetCare AI">
    <div class="pc-asistente__circulo"></div>
    <x-icono nombre="chispas" :tamano="44" class="pc-asistente__chispas" />

    <span class="pc-asistente__etiqueta">Asistente PetCare AI</span>
    <h2>{{ $titulo }}</h2>
    <p>Respuestas personalizadas según su raza, edad y peso, y aviso de cuándo ir al veterinario.</p>

    @if ($disponible)
        <a href="{{ route('consultas.create', $mascota ? ['mascota' => $mascota] : []) }}" class="pc-asistente__campo">
            <span>Pregúntale algo a PetCare AI…</span>
            <span class="pc-asistente__enviar"><x-icono nombre="enviar" :tamano="20" :grosor="2.2" /></span>
        </a>
    @else
        <div class="pc-asistente__campo pc-asistente__campo--deshabilitado">
            <span>Las consultas a la IA estarán disponibles pronto</span>
            <span class="pc-asistente__enviar"><x-icono nombre="enviar" :tamano="20" :grosor="2.2" /></span>
        </div>
    @endif
</section>
