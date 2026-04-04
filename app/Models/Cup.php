<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Cup extends Model
{
    protected $table = 'cups';

    protected $fillable = [
        'codigo',
        'nombre',
        'orden',
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
        return $query->where('activo', true)->orderBy('orden')->orderBy('codigo');
    }
}
