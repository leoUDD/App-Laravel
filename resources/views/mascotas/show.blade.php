@extends('plantillas.app')

@section('titulo', $mascota->nombre)

@section('contenido')
    <a href="{{ route('mascotas.index') }}" class="btn btn-link px-0 mb-3">
        <i class="bi bi-arrow-left"></i> Volver a mis mascotas
    </a>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="icono-mascota">{{ $mascota->icono() }}</span>
                        <div>
                            <h1 class="h3 mb-0">{{ $mascota->nombre }}</h1>
                            <span class="text-muted">{{ $mascota->especieTexto() }}</span>
                        </div>
                    </div>

                    <dl class="row mb-4">
                        <dt class="col-5 text-muted fw-normal">Raza</dt>
                        <dd class="col-7">{{ $mascota->raza }}</dd>

                        <dt class="col-5 text-muted fw-normal">Edad</dt>
                        <dd class="col-7">{{ $mascota->edad }} {{ $mascota->edad === 1 ? 'año' : 'años' }}</dd>

                        <dt class="col-5 text-muted fw-normal">Peso</dt>
                        <dd class="col-7">{{ $mascota->peso }} kg</dd>

                        <dt class="col-5 text-muted fw-normal">Registrada el</dt>
                        <dd class="col-7">{{ $mascota->created_at->format('d-m-Y') }}</dd>
                    </dl>

                    {{-- Sebastián: aquí puede ir el botón "Consultar a la IA sobre esta mascota" --}}

                    <div class="d-flex gap-2">
                        <a href="{{ route('mascotas.edit', $mascota) }}" class="btn btn-petcare">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form method="POST" action="{{ route('mascotas.destroy', $mascota) }}"
                              data-confirmacion="¿Seguro que quieres eliminar a {{ $mascota->nombre }}? Esta acción no se puede deshacer."
                              onsubmit="return confirm(this.dataset.confirmacion);">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
