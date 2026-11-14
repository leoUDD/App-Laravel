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
        ];
    }
}
