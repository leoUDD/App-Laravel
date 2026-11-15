<?php

namespace App\Http\Requests;

use App\Models\EntradaBitacora;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntradaBitacoraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('mascota'));
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date_format:Y-m-d'],
            'tipo' => ['required', Rule::in(array_keys(EntradaBitacora::TIPOS))],
            'nota' => ['required', 'string', 'max:2000'],
        ];
    }
}
