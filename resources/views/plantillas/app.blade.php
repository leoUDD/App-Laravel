<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Inicio') · {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/petcare.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark navbar-petcare shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ auth()->check() ? route('mascotas.index') : route('login') }}">
                <i class="bi bi-heart-pulse"></i> {{ config('app.name') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                    aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                @auth
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link @if (request()->routeIs('mascotas.*')) active @endif" href="{{ route('mascotas.index') }}">
                                <i class="bi bi-list-ul"></i> Mis mascotas
                            </a>
                        </li>
                        {{-- Sebastián: agregar aquí los enlaces de "Consultar a la IA" e "Historial" --}}
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item d-flex align-items-center text-white-50 me-md-3">
                            <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                @else
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link @if (request()->routeIs('login')) active @endif" href="{{ route('login') }}">Iniciar sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (request()->routeIs('registro')) active @endif" href="{{ route('registro') }}">Registrarse</a>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('exito'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('exito') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @yield('contenido')
    </main>

    <footer class="text-center text-muted small py-3">
        {{ config('app.name') }} entrega orientación general y no reemplaza la atención de un médico veterinario.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
