@extends('plantillas.app')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <div class="card tarjeta-autenticacion mx-auto shadow-sm border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <div class="icono-mascota">🐾</div>
                <h1 class="h4 mt-2 mb-1">Iniciar sesión</h1>
                <p class="text-muted small mb-0">Resuelve tus dudas sobre el cuidado de tu perro o gato.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" id="recordarme" name="recordarme" value="1" class="form-check-input">
                    <label for="recordarme" class="form-check-label">Mantener la sesión iniciada</label>
                </div>

                <button type="submit" class="btn btn-petcare w-100">Ingresar</button>
            </form>

            <p class="text-center small mt-3 mb-0">
                ¿No tienes cuenta? <a href="{{ route('registro') }}">Regístrate</a>
            </p>
        </div>
    </div>
@endsection
