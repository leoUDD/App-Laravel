@extends('plantillas.app')

@section('titulo', 'Asistente')

@section('contenido')
    <div class="pc-pagina">
        @include('consultas._encabezado', ['subtitulo' => 'Asistente de cuidado de mascotas'])

        @if ($mascotas->isEmpty())
            <div class="pc-vacio">
                <x-icono nombre="huella-contorno" :tamano="32" />
                <h2>Registra una mascota para consultar</h2>
                <p>El asistente usará su perfil para responder tu pregunta.</p>
                <a href="{{ route('mascotas.create') }}" class="pc-boton pc-boton--primario">Registrar mascota</a>
            </div>
        @else
            @php
                $mascotaElegida = filter_var(old('mascota_id', $mascotaSeleccionada), FILTER_VALIDATE_INT);
            @endphp

            <form method="POST" action="{{ route('consultas.store') }}" class="pc-formulario" id="pc-formulario-consulta">
                @csrf
                <fieldset>
                    <legend class="pc-campo__etiqueta">¿Sobre qué mascota quieres consultar?</legend>
                    <div class="pc-consulta-mascotas">
                        @foreach ($mascotas as $mascota)
                            <label class="pc-consulta-mascota">
                                <input type="radio" name="mascota_id" value="{{ $mascota->id }}"
                                    @checked($mascotaElegida === $mascota->id) required
                                    @error('mascota_id') aria-invalid="true" aria-describedby="pc-error-mascota" @enderror>
                                <span>
                                    <x-icono nombre="huella-contorno" :tamano="20" />
                                    <span><strong>{{ $mascota->nombre }}</strong><small>{{ $mascota->especieTexto() }} · {{ $mascota->raza }}</small></span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('mascota_id')
                        <p class="pc-campo__error mt-2" id="pc-error-mascota">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="pc-nota">
                    <x-icono nombre="chispas" :tamano="20" />
                    <p>Pregúntame sobre alimentación, comportamiento, cuidados o prevención.</p>
                </div>

                @include('consultas._pregunta', [
                    'etiqueta' => 'Escribe tu consulta',
                    'placeholder' => '¿Qué quieres saber sobre tu mascota?',
                    'boton' => 'Enviar consulta',
                ])
            </form>
        @endif
    </div>
@endsection
