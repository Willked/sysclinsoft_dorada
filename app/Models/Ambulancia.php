<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
