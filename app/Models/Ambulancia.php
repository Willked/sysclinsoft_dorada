<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ambulancia extends Model
{
    protected $table = 'ambulancias';

    protected $fillable = [
        'codigo',
        'placa',
        'descripcion',
        'clasificacion_servicio',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function scopeActivosOrdenados(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('codigo');
    }

    public function atenciones(): HasMany
    {
        return $this->hasMany(Atencion::class);
    }
}
