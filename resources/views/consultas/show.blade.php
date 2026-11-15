@extends('plantillas.app')

@section('titulo', 'Respuesta')

@section('contenido')
    <div class="pc-pagina">
        @include('consultas._encabezado', ['subtitulo' => 'Respuesta para '.$consulta->mascota_nombre])

        @if ($consulta->consulta_anterior_id !== null)
            <a href="{{ route('consultas.show', $consulta->consulta_anterior_id) }}" class="pc-enlace">
                <x-icono nombre="volver" :tamano="16" /> Consulta anterior
            </a>
        @endif

        @foreach ($mensajes as $mensaje)
            <div class="pc-consulta-burbuja pc-consulta-burbuja--pregunta">
                <p>{{ $mensaje->pregunta }}</p>
                <time datetime="{{ $mensaje->created_at->toIso8601String() }}">
                    {{ $mensaje->created_at->translatedFormat('j M Y · H:i') }}
                </time>
            </div>

            @if ($mensaje->nivel_riesgo === 'alto')
                <section class="pc-consulta-alerta pc-consulta-alerta--alta" aria-labelledby="pc-titulo-alerta-{{ $mensaje->id }}">
                    <x-icono nombre="alerta" :tamano="22" />
                    <div>
                        <h2 id="pc-titulo-alerta-{{ $mensaje->id }}">Alerta de riesgo alto</h2>
                        <p>Contacta de inmediato a un médico veterinario.</p>
                    </div>
                </section>
            @elseif ($mensaje->requiere_veterinario)
                <section class="pc-consulta-alerta" aria-labelledby="pc-titulo-alerta-{{ $mensaje->id }}">
                    <x-icono nombre="escudo" :tamano="22" />
                    <div>
                        <h2 id="pc-titulo-alerta-{{ $mensaje->id }}">Se recomienda atención veterinaria</h2>
                        <p>Contacta a un médico veterinario para que evalúe a tu mascota.</p>
                    </div>
                </section>
            @endif

            <section class="pc-tarjeta pc-consulta-burbuja" aria-labelledby="pc-titulo-respuesta-{{ $mensaje->id }}">
                <h2 id="pc-titulo-respuesta-{{ $mensaje->id }}">Respuesta de {{ config('app.name') }}</h2>
                <p>{{ $mensaje->respuesta }}</p>
                <form method="POST" action="{{ route('consultas.favorita', $mensaje) }}" class="mt-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="favorita" value="{{ $mensaje->favorita ? 0 : 1 }}">
                    <button type="submit" class="pc-boton pc-boton--secundario pc-boton--compacto">
                        <x-icono nombre="corazon" :tamano="16" />
                        {{ $mensaje->favorita ? 'Quitar de favoritos' : 'Guardar en favoritos' }}
                    </button>
                </form>
            </section>
        @endforeach

        @if ($consulta->mascota_id !== null)
            <details class="pc-repregunta" @if (filter_var(old('consulta_anterior_id'), FILTER_VALIDATE_INT) === $consulta->id) open @endif>
                <summary class="pc-boton pc-boton--primario w-100">Continuar consulta</summary>
                <form method="POST" action="{{ route('consultas.store') }}" class="pc-formulario pc-tarjeta mt-3" id="pc-formulario-consulta">
                    @csrf
                    <input type="hidden" name="mascota_id" value="{{ $consulta->mascota_id }}">
                    <input type="hidden" name="consulta_anterior_id" value="{{ $consulta->id }}">

                    @foreach (['mascota_id', 'consulta_anterior_id'] as $campo)
                        @error($campo)
                            <p class="pc-campo__error" role="alert">{{ $message }}</p>
                        @enderror
                    @endforeach

                    @include('consultas._pregunta', [
                        'etiqueta' => 'Seguir conversando sobre '.$consulta->mascota_nombre,
                        'placeholder' => '¿Qué más quieres preguntar?',
                        'boton' => 'Enviar repregunta',
                    ])
                    <button type="button" class="pc-boton pc-boton--secundario" data-cerrar-repregunta>Cancelar</button>
                </form>
            </details>
            <script>
                (() => {
                    const panel = document.querySelector('.pc-repregunta');
                    const resumen = panel.querySelector('summary');

                    panel.addEventListener('toggle', () => {
                        if (panel.open) {
                            panel.querySelector('textarea').focus();
                        }
                    });

                    panel.querySelector('[data-cerrar-repregunta]').addEventListener('click', () => {
                        panel.open = false;
                        resumen.focus();
                    });
                })();
            </script>
        @endif

        <a href="{{ route('consultas.create', $consulta->mascota_id ? ['mascota' => $consulta->mascota_id] : []) }}"
            class="pc-boton {{ $consulta->mascota_id ? 'pc-boton--secundario' : 'pc-boton--primario' }}">Hacer otra consulta</a>

        <p class="pc-aviso-legal">{{ config('app.name') }} entrega orientación general y no reemplaza la evaluación de un médico veterinario.</p>
    </div>
@endsection
