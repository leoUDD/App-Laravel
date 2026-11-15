@csrf

@php
    $especieElegida = old('especie', $mascota->especie ?? 'perro');
@endphp

<div class="pc-avatar-formulario" data-foto>
    <label for="foto" class="pc-avatar-formulario__marco @if ($mascota->foto) pc-avatar-formulario__marco--con-foto @endif" aria-hidden="true">
        <img src="{{ $mascota->urlFoto() }}" alt="" class="pc-avatar-formulario__imagen" data-foto-vista-previa>
        <x-ilustracion especie="perro" :tamano="78" class="pc-avatar-formulario__perro" />
        <x-ilustracion especie="gato" :tamano="74" class="pc-avatar-formulario__gato" />
        <span class="pc-avatar-formulario__camara">
            <x-icono nombre="camara" :tamano="18" />
        </span>
    </label>

    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
           class="visually-hidden" aria-describedby="foto-ayuda" data-foto-entrada
           @error('foto') aria-invalid="true" @enderror>
    <label for="foto" class="pc-avatar-formulario__boton">
        {{ $mascota->foto ? 'Cambiar fotografía' : 'Agregar fotografía' }}
    </label>

    @error('foto')
        <p id="foto-ayuda" class="pc-campo__error">{{ $message }}</p>
    @else
        <p id="foto-ayuda" class="pc-campo__ayuda">Opcional · JPG, PNG o WEBP, hasta 4 MB.</p>
    @enderror

    @if ($mascota->foto)
        <label class="pc-casilla pc-avatar-formulario__quitar">
            <input type="checkbox" name="quitar_foto" value="1" @checked(old('quitar_foto')) data-foto-quitar>
            Quitar la foto actual
        </label>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const contenedor = document.querySelector('[data-foto]');
        const marco = contenedor.querySelector('.pc-avatar-formulario__marco');
        const vistaPrevia = contenedor.querySelector('[data-foto-vista-previa]');
        const entrada = contenedor.querySelector('[data-foto-entrada]');
        const quitar = contenedor.querySelector('[data-foto-quitar]');
        const fotoGuardada = vistaPrevia.getAttribute('src');

        entrada.addEventListener('change', () => {
            const archivo = entrada.files[0];

            if (! archivo) {
                return;
            }

            vistaPrevia.src = URL.createObjectURL(archivo);
            marco.classList.add('pc-avatar-formulario__marco--con-foto');

            if (quitar) {
                quitar.checked = false;
            }
        });

        quitar?.addEventListener('change', () => {
            entrada.value = '';
            vistaPrevia.src = fotoGuardada;
            marco.classList.toggle('pc-avatar-formulario__marco--con-foto', ! quitar.checked);
        });
    });
</script>

<div class="pc-campo">
    <label for="nombre" class="pc-campo__etiqueta">Nombre</label>
    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $mascota->nombre) }}"
           class="pc-campo__entrada @error('nombre') pc-campo__entrada--invalida @enderror"
           placeholder="Ej: Rocco" required maxlength="60" autofocus
           @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror>
    @error('nombre')
        <p id="nombre-error" class="pc-campo__error">{{ $message }}</p>
    @enderror
</div>

<fieldset class="pc-campo m-0 p-0 border-0">
    <legend class="pc-campo__etiqueta mb-0 float-none">Especie</legend>
    <div class="pc-especies">
        @foreach (\App\Models\Mascota::ESPECIES as $valor => $texto)
            <label class="pc-especie">
                <input type="radio" name="especie" value="{{ $valor }}" @checked($especieElegida === $valor) required>
                <span><x-icono nombre="check" :tamano="18" :grosor="2.4" />{{ $texto }}</span>
            </label>
        @endforeach
    </div>
    @error('especie')
        <p class="pc-campo__error">{{ $message }}</p>
    @enderror
</fieldset>

<div class="pc-campo">
    <label for="raza" class="pc-campo__etiqueta">Raza</label>
    <input type="text" id="raza" name="raza" value="{{ old('raza', $mascota->raza) }}"
           class="pc-campo__entrada @error('raza') pc-campo__entrada--invalida @enderror"
           placeholder="Ej: Border Collie o Mestizo" required maxlength="60"
           @error('raza') aria-invalid="true" aria-describedby="raza-error" @enderror>
    @error('raza')
        <p id="raza-error" class="pc-campo__error">{{ $message }}</p>
    @enderror
</div>

<div class="pc-campo-doble">
    <div class="pc-campo">
        <label for="edad" class="pc-campo__etiqueta">Edad (años)</label>
        <input type="number" id="edad" name="edad" value="{{ old('edad', $mascota->edad) }}"
               class="pc-campo__entrada @error('edad') pc-campo__entrada--invalida @enderror"
               placeholder="Ej: 2" required min="0" max="30" step="1" aria-describedby="edad-ayuda"
               @error('edad') aria-invalid="true" @enderror>
        @error('edad')
            <p id="edad-ayuda" class="pc-campo__error">{{ $message }}</p>
        @else
            <p id="edad-ayuda" class="pc-campo__ayuda">Usa 0 si tiene menos de un año.</p>
        @enderror
    </div>

    <div class="pc-campo">
        <label for="peso" class="pc-campo__etiqueta">Peso (kg)</label>
        <input type="number" id="peso" name="peso" value="{{ old('peso', $mascota->peso) }}"
               class="pc-campo__entrada @error('peso') pc-campo__entrada--invalida @enderror"
               placeholder="Ej: 14,5" required min="0.1" max="150" step="0.01"
               @error('peso') aria-invalid="true" aria-describedby="peso-error" @enderror>
        @error('peso')
            <p id="peso-error" class="pc-campo__error">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="pc-nota">
    <x-icono nombre="chispa" :tamano="18" />
    <p>Con la raza, edad y peso, PetCare AI ajusta porciones, rutinas y alertas a tu mascota.</p>
</div>
