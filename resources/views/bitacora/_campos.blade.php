@php
    $nota = old('nota', $entrada?->nota ?? '');
    $fecha = old('fecha', $entrada?->fecha->toDateString() ?? today()->toDateString());
@endphp

<div class="pc-campo">
    <label class="pc-campo__etiqueta" for="fecha">Fecha</label>
    <input type="date" id="fecha" name="fecha" required
        value="{{ is_string($fecha) ? $fecha : '' }}"
        class="pc-campo__entrada @error('fecha') pc-campo__entrada--invalida @enderror"
        @error('fecha') aria-invalid="true" aria-describedby="error-fecha" @enderror>
    @error('fecha')
        <p class="pc-campo__error" id="error-fecha">{{ $message }}</p>
    @enderror
</div>

<div class="pc-campo">
    <label class="pc-campo__etiqueta" for="tipo">Tipo de entrada</label>
    <select id="tipo" name="tipo" required
        class="pc-campo__entrada @error('tipo') pc-campo__entrada--invalida @enderror"
        @error('tipo') aria-invalid="true" aria-describedby="error-tipo" @enderror>
        @foreach ($tipos as $valor => $nombre)
            <option value="{{ $valor }}" @selected(old('tipo', $entrada?->tipo ?? 'observacion') === $valor)>{{ $nombre }}</option>
        @endforeach
    </select>
    @error('tipo')
        <p class="pc-campo__error" id="error-tipo">{{ $message }}</p>
    @enderror
</div>

<div class="pc-campo">
    <label class="pc-campo__etiqueta" for="nota">Nota</label>
    <textarea id="nota" name="nota" rows="4" maxlength="2000" required
        class="pc-campo__entrada pc-consulta-pregunta @error('nota') pc-campo__entrada--invalida @enderror"
        @error('nota') aria-invalid="true" aria-describedby="error-nota" @enderror>{{ is_string($nota) ? $nota : '' }}</textarea>
    @error('nota')
        <p class="pc-campo__error" id="error-nota">{{ $message }}</p>
    @enderror
</div>
