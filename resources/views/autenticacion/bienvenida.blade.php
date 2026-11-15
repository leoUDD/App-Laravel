@extends('plantillas.app')

@section('titulo', 'Bienvenida')

@section('contenido')
    <div class="pc-bienvenida">
        <x-marca />

        <div class="pc-bienvenida__ilustracion">
            <div class="pc-circulo pc-circulo--grande"></div>
            <div class="pc-circulo pc-circulo--mediano"></div>
            <div class="pc-circulo pc-circulo--sombra"></div>
            <x-ilustracion especie="perro" :tamano="176" class="pc-bienvenida__perro" etiqueta="Ilustración de un perro" />
            <x-ilustracion especie="gato" :tamano="148" class="pc-bienvenida__gato" etiqueta="Ilustración de un gato" />
            <x-icono nombre="chispa" :tamano="40" class="pc-bienvenida__chispa-grande" />
            <x-icono nombre="chispa" :tamano="18" class="pc-bienvenida__chispa-chica" />
            <x-icono nombre="corazon" :tamano="30" class="pc-bienvenida__corazon" />
        </div>

        <h1>El bienestar de tu mascota comienza aquí</h1>
        <p class="pc-bienvenida__descripcion">
            Resuelve dudas sobre alimentación, comportamiento y cuidados con un asistente que conoce a tu perro o gato.
        </p>

        <div class="pc-beneficios">
            <span class="pc-beneficio"><x-icono nombre="chispa-contorno" :tamano="14" />IA personalizada</span>
            <span class="pc-beneficio"><x-icono nombre="escudo" :tamano="14" />Alertas de riesgo</span>
        </div>

        <div class="pc-bienvenida__acciones">
            <a href="{{ route('login') }}" class="pc-boton pc-boton--primario">Iniciar sesión</a>
            <a href="{{ route('registro') }}" class="pc-boton pc-boton--secundario">Crear cuenta</a>
            <p class="pc-aviso-legal">
                PetCare AI entrega orientación general y no reemplaza la atención de un médico veterinario.
            </p>
        </div>
    </div>
@endsection
