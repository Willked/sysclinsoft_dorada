<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignoVital extends Model
{
    protected $table = 'signos_vitales';

    protected $fillable = [
        'atencion_id',
        'medicion_en',
        'presion_sistolica',
        'presion_diastolica',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'saturacion_oxigeno',
        'temperatura',
        'glicemia',
        'fraccion_inspirada_oxigeno',
        'observaciones',
        'registrado_por_id',
    ];

    protected function casts(): array
    {
        return [
            'medicion_en' => 'datetime',
            'temperatura' => 'decimal:1',
        ];
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
