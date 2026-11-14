<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0E3B2C">
    <title>@yield('titulo', 'Inicio') · {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/petcare.css') }}">
</head>
<body class="pc-cuerpo">
    <div class="pc-app @auth pc-app--con-navegacion @endauth">
        @if (session('exito') || session('error'))
            <div class="pc-mensajes">
                @if (session('exito'))
                    <div class="alert alert-dismissible fade show pc-mensaje pc-mensaje--exito" role="status">
                        <x-icono nombre="exito" :tamano="20" />
                        {{ session('exito') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-dismissible fade show pc-mensaje pc-mensaje--error" role="alert">
                        <x-icono nombre="alerta" :tamano="20" />
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif
            </div>
        @endif

        <main>
            @yield('contenido')
        </main>

        @auth
            <x-navegacion-inferior />
        @endauth
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
