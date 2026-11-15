<?php

namespace App\Models;

use Database\Factories\MascotaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['nombre', 'especie', 'raza', 'edad', 'peso', 'foto'])]
class Mascota extends Model
{
    /** @use HasFactory<MascotaFactory> */
    use HasFactory;

    /**
     * Especies permitidas en el MVP (valor guardado => texto visible).
     *
     * @var array<string, string>
     */
    public const ESPECIES = [
        'perro' => 'Perro',
        'gato' => 'Gato',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'edad' => 'integer',
            'peso' => 'decimal:2',
        ];
    }

    /**
     * Dueño o cuidador de la mascota.
     *
     * @return BelongsTo<User, $this>
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<EntradaBitacora, $this>
     */
    public function entradas(): HasMany
    {
        return $this->hasMany(EntradaBitacora::class);
    }

    /**
     * Texto visible de la especie (por ejemplo, "Perro").
     */
    public function especieTexto(): string
    {
        return self::ESPECIES[$this->especie] ?? ucfirst($this->especie);
    }

    /**
     * URL pública de la foto, o null si la mascota no tiene foto.
     */
    public function urlFoto(): ?string
    {
        return $this->foto ? Storage::disk('public')->url($this->foto) : null;
    }

    /**
     * Emoji que representa a la especie.
     */
    public function icono(): string
    {
        return $this->especie === 'gato' ? '🐱' : '🐶';
    }
}
