@extends('plantillas.app')

@section('titulo', 'Registrar mascota')

@section('contenido')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 mb-4">Registrar mascota</h1>

                    <form method="POST" action="{{ route('mascotas.store') }}" novalidate>
                        @include('mascotas._formulario')

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('mascotas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-petcare">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
