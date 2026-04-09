<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Cie10 extends Model
{
    protected $table = 'cie_10';

    protected $fillable = [
        'codigo',
        'descripcion',
        'capitulo',
        'orden',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'orden' => 'integer',
        ];
    }

    public function scopeActivosOrdenados(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('codigo');
    }
}
