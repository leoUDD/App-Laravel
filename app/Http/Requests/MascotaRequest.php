<?php

namespace App\Http\Requests;

use App\Models\Mascota;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MascotaRequest extends FormRequest
{
    /**
     * La autorización (que la mascota sea del usuario) se revisa en el controlador con MascotaPolicy.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:60'],
            'especie' => ['required', Rule::in(array_keys(Mascota::ESPECIES))],
            'raza' => ['required', 'string', 'max:60'],
            'edad' => ['required', 'integer', 'between:0,30'],
            'peso' => ['required', 'numeric', 'between:0.1,150', 'decimal:0,2'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'quitar_foto' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Mensajes de error personalizados.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'foto.image' => 'La foto debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser JPG, PNG o WEBP.',
            'foto.max' => 'La foto no puede pesar más de 4 MB.',
            'foto.uploaded' => 'No se pudo subir la foto. Prueba con una imagen de menos de 4 MB.',
        ];
    }
}
