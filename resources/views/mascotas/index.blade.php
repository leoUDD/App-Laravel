@extends('plantillas.app')

@section('titulo', 'Mis mascotas')

@section('contenido')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Mis mascotas</h1>
        <a href="{{ route('mascotas.create') }}" class="btn btn-petcare">
            <i class="bi bi-plus-lg"></i> Registrar mascota
        </a>
    </div>

    @if ($mascotas->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="icono-mascota mb-2">🐾</div>
                <h2 class="h5">Aún no tienes mascotas registradas</h2>
                <p class="text-muted">Registra a tu perro o gato para recibir orientación personalizada.</p>
                <a href="{{ route('mascotas.create') }}" class="btn btn-petcare">Registrar mi primera mascota</a>
            </div>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
            @foreach ($mascotas as $mascota)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm tarjeta-mascota">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="icono-mascota">{{ $mascota->icono() }}</span>
                                <div>
                                    <h2 class="h5 mb-0">{{ $mascota->nombre }}</h2>
                                    <span class="text-muted small">{{ $mascota->especieTexto() }} · {{ $mascota->raza }}</span>
                                </div>
                            </div>
                            <ul class="list-inline small text-muted mb-0">
                                <li class="list-inline-item"><i class="bi bi-calendar3"></i> {{ $mascota->edad }} {{ $mascota->edad === 1 ? 'año' : 'años' }}</li>
                                <li class="list-inline-item"><i class="bi bi-speedometer2"></i> {{ $mascota->peso }} kg</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex gap-2 pb-3">
                            <a href="{{ route('mascotas.show', $mascota) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                            <a href="{{ route('mascotas.edit', $mascota) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form method="POST" action="{{ route('mascotas.destroy', $mascota) }}" class="ms-auto"
                                  data-confirmacion="¿Seguro que quieres eliminar a {{ $mascota->nombre }}? Esta acción no se puede deshacer."
                                  onsubmit="return confirm(this.dataset.confirmacion);">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Eliminar a {{ $mascota->nombre }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
