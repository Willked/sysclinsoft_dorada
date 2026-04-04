<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
    protected $table = 'eps';

    protected $fillable = [
        'codigo',
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'email',
        'website',
        'logo',
        'descripcion',
        'contacto',
        'contacto_telefono',
        'contacto_email',
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
        return $query->where('activo', true)->orderBy('orden')->orderBy('nombre');
    }
}
