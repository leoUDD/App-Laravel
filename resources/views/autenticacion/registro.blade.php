@extends('plantillas.app')

@section('titulo', 'Registrarse')

@section('contenido')
    <div class="card tarjeta-autenticacion mx-auto shadow-sm border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <div class="icono-mascota">🐾</div>
                <h1 class="h4 mt-2 mb-1">Crear cuenta</h1>
                <p class="text-muted small mb-0">Registra a tus mascotas y recibe orientación personalizada.</p>
            </div>

            <form method="POST" action="{{ route('registro') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text">Mínimo 8 caracteres.</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-petcare w-100">Crear cuenta</button>
            </form>

            <p class="text-center small mt-3 mb-0">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
            </p>
        </div>
    </div>
@endsection
