@extends('plantillas.app')

@section('titulo', 'Crear cuenta')

@section('contenido')
    <div class="pc-autenticacion">
        <x-marca />

        <header class="pc-encabezado pc-encabezado--formulario">
            <div class="pc-encabezado__texto">
                <h1>Crear cuenta</h1>
                <p class="pc-encabezado__descripcion">Registra a tus mascotas y recibe orientación personalizada.</p>
            </div>
        </header>

        <form method="POST" action="{{ route('registro') }}" class="pc-formulario" novalidate>
            @csrf

            <div class="pc-campo">
                <label for="name" class="pc-campo__etiqueta">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       class="pc-campo__entrada @error('name') pc-campo__entrada--invalida @enderror"
                       placeholder="Ej: Camila" required autofocus autocomplete="name"
                       @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')
                    <p id="name-error" class="pc-campo__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="pc-campo">
                <label for="email" class="pc-campo__etiqueta">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="pc-campo__entrada @error('email') pc-campo__entrada--invalida @enderror"
                       placeholder="tucorreo@ejemplo.com" required autocomplete="email"
                       @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')
                    <p id="email-error" class="pc-campo__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="pc-campo">
                <label for="password" class="pc-campo__etiqueta">Contraseña</label>
                <input type="password" id="password" name="password"
                       class="pc-campo__entrada @error('password') pc-campo__entrada--invalida @enderror"
                       required autocomplete="new-password" aria-describedby="password-ayuda"
                       @error('password') aria-invalid="true" @enderror>
                @error('password')
                    <p id="password-ayuda" class="pc-campo__error">{{ $message }}</p>
                @else
                    <p id="password-ayuda" class="pc-campo__ayuda">Mínimo 8 caracteres.</p>
                @enderror
            </div>

            <div class="pc-campo">
                <label for="password_confirmation" class="pc-campo__etiqueta">Confirmar contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="pc-campo__entrada" required autocomplete="new-password">
            </div>

            <button type="submit" class="pc-boton pc-boton--primario">Crear cuenta</button>
        </form>

        <p class="pc-autenticacion__pie">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
        </p>
    </div>
@endsection
