<?php

/*
|--------------------------------------------------------------------------
| Mensajes de validación en español
|--------------------------------------------------------------------------
|
| Solo se incluyen las reglas que usa la aplicación. Las demás usan el
| mensaje en inglés de Laravel. Agreguen aquí las que necesiten.
|
*/

return [
    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'decimal' => 'El campo :attribute debe tener como máximo :decimal decimales.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'exists' => 'El valor seleccionado en :attribute no es válido.',
    'in' => 'El valor seleccionado en :attribute no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'lowercase' => 'El campo :attribute debe estar en minúsculas.',
    'max' => [
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'numeric' => 'El campo :attribute debe ser un número.',
    'password' => [
        'letters' => 'El campo :attribute debe contener al menos una letra.',
        'mixed' => 'El campo :attribute debe contener al menos una mayúscula y una minúscula.',
        'numbers' => 'El campo :attribute debe contener al menos un número.',
        'symbols' => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'La :attribute aparece en una filtración de datos. Elige otra.',
    ],
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser un texto.',
    'unique' => 'Este :attribute ya está registrado.',

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'nombre' => 'nombre',
        'especie' => 'especie',
        'raza' => 'raza',
        'edad' => 'edad',
        'peso' => 'peso',
    ],
];
