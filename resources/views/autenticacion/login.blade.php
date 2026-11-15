@extends('plantillas.app')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <div class="pc-autenticacion">
        <x-marca />

        <header class="pc-encabezado pc-encabezado--formulario">
            <div class="pc-encabezado__texto">
                <h1>Iniciar sesión</h1>
                <p class="pc-encabezado__descripcion">Resuelve tus dudas sobre el cuidado de tu perro o gato.</p>
            </div>
        </header>

        <form method="POST" action="{{ route('login') }}" class="pc-formulario" novalidate>
            @csrf

            <div class="pc-campo">
                <label for="email" class="pc-campo__etiqueta">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="pc-campo__entrada @error('email') pc-campo__entrada--invalida @enderror"
                       placeholder="tucorreo@ejemplo.com" required autofocus autocomplete="email"
                       @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')
                    <p id="email-error" class="pc-campo__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="pc-campo">
                <label for="password" class="pc-campo__etiqueta">Contraseña</label>
                <input type="password" id="password" name="password"
                       class="pc-campo__entrada @error('password') pc-campo__entrada--invalida @enderror"
                       required autocomplete="current-password"
                       @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                @error('password')
                    <p id="password-error" class="pc-campo__error">{{ $message }}</p>
                @enderror
            </div>

            <label class="pc-casilla">
                <input type="checkbox" name="recordarme" value="1" @checked(old('recordarme'))>
                Mantener la sesión iniciada
            </label>

            <button type="submit" class="pc-boton pc-boton--primario">Ingresar</button>
        </form>

        <p class="pc-autenticacion__pie">
            ¿No tienes cuenta? <a href="{{ route('registro') }}">Crear cuenta</a>
        </p>
    </div>
@endsection
