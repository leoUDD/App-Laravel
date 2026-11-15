<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConsultaRequest extends FormRequest
{
    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'mascota_id' => [
                'bail', 'required', 'integer',
                Rule::exists('mascotas', 'id')->where('user_id', $this->user()->id),
            ],
            'consulta_anterior_id' => [
                'bail', 'nullable', 'integer',
                Rule::exists('consultas', 'id')
                    ->where('user_id', $this->user()->id)
                    ->where('mascota_id', $this->integer('mascota_id', strict: true)),
            ],
            'pregunta' => ['required', 'string', 'max:1500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['mascota_id' => 'mascota', 'consulta_anterior_id' => 'consulta anterior'];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mascota_id.exists' => 'Selecciona una de tus mascotas registradas.',
            'consulta_anterior_id.exists' => 'Selecciona una consulta anterior de esta mascota.',
        ];
    }
}
