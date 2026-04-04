<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlasgowRegistro extends Model
{
    protected $table = 'glasgow_registros';

    protected $fillable = [
        'atencion_id',
        'medicion_en',
        'ocular',
        'verbal',
        'motor',
        'total',
        'registrado_por_id',
    ];

    protected function casts(): array
    {
        return [
            'medicion_en' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (GlasgowRegistro $registro): void {
            if ($registro->ocular !== null && $registro->verbal !== null && $registro->motor !== null) {
                $registro->total = (int) $registro->ocular + (int) $registro->verbal + (int) $registro->motor;
            } else {
                $registro->total = null;
            }
        });
    }

    public function atencion(): BelongsTo
    {
        return $this->belongsTo(Atencion::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por_id');
    }
}
