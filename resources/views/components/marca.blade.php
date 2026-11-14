{{-- Logo de PetCare AI: huella + nombre + insignia "AI". --}}
<a href="{{ url('/') }}" {{ $attributes->merge(['class' => 'pc-marca']) }} aria-label="{{ config('app.name') }}, ir al inicio">
    <span class="pc-marca__logo"><x-icono nombre="huella" :tamano="24" /></span>
    <span class="pc-marca__nombre">PetCare</span>
    <span class="pc-marca__ia">AI</span>
</a>
