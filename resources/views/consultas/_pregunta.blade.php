@php($preguntaAnterior = old('pregunta'))

<div class="pc-campo">
    <label class="pc-campo__etiqueta" for="pc-pregunta">{{ $etiqueta }}</label>
    <textarea id="pc-pregunta" name="pregunta" rows="3" maxlength="1500" required
        class="pc-campo__entrada pc-consulta-pregunta @error('pregunta') pc-campo__entrada--invalida @enderror"
        placeholder="{{ $placeholder }}"
        @error('pregunta') aria-invalid="true" aria-describedby="pc-error-pregunta" @enderror>{{ is_string($preguntaAnterior) ? $preguntaAnterior : '' }}</textarea>
    @error('pregunta')
        <p class="pc-campo__error" id="pc-error-pregunta">{{ $message }}</p>
    @enderror
</div>

<button type="submit" class="pc-boton pc-boton--primario">
    <x-icono nombre="enviar" :tamano="20" /> <span>{{ $boton }}</span>
</button>

<script>
    (() => {
        const formulario = document.getElementById('pc-formulario-consulta');
        const boton = formulario.querySelector('button[type="submit"]');
        const texto = boton.querySelector('span');
        const etiqueta = texto.textContent;

        formulario.addEventListener('submit', () => {
            boton.disabled = true;
            texto.textContent = 'Consultando…';
        });

        window.addEventListener('pageshow', (evento) => {
            if (evento.persisted) {
                boton.disabled = false;
                texto.textContent = etiqueta;
            }
        });
    })();
</script>
