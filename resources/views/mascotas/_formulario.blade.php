@csrf

<div class="mb-3">
    <label for="nombre" class="form-label">Nombre</label>
    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $mascota->nombre) }}"
           class="form-control @error('nombre') is-invalid @enderror" required maxlength="60" autofocus>
    @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <span class="form-label d-block">Especie</span>
    @foreach (\App\Models\Mascota::ESPECIES as $valor => $texto)
        <div class="form-check form-check-inline">
            <input type="radio" id="especie_{{ $valor }}" name="especie" value="{{ $valor }}"
                   class="form-check-input @error('especie') is-invalid @enderror"
                   @checked(old('especie', $mascota->especie) === $valor) required>
            <label for="especie_{{ $valor }}" class="form-check-label">{{ $texto }}</label>
        </div>
    @endforeach
    @error('especie')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="raza" class="form-label">Raza</label>
    <input type="text" id="raza" name="raza" value="{{ old('raza', $mascota->raza) }}"
           class="form-control @error('raza') is-invalid @enderror" required maxlength="60" placeholder="Ej: Labrador, Siamés o Mestizo">
    @error('raza')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-sm-6 mb-3">
        <label for="edad" class="form-label">Edad (años)</label>
        <input type="number" id="edad" name="edad" value="{{ old('edad', $mascota->edad) }}"
               class="form-control @error('edad') is-invalid @enderror" required min="0" max="30" step="1">
        @error('edad')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="form-text">Usa 0 si tiene menos de un año.</div>
        @enderror
    </div>

    <div class="col-sm-6 mb-3">
        <label for="peso" class="form-label">Peso (kg)</label>
        <input type="number" id="peso" name="peso" value="{{ old('peso', $mascota->peso) }}"
               class="form-control @error('peso') is-invalid @enderror" required min="0.1" max="150" step="0.01">
        @error('peso')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
