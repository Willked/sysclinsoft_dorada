<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conductor extends Model
{
    protected $table = 'conductores';

    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'telefono',
        'numero_licencia',
        'categoria_licencia',
        'fecha_vencimiento_licencia',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_vencimiento_licencia' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function scopeActivosOrdenados(Builder $query): Builder
    {
        return $query
            ->selectRaw("id, trim(concat_ws(' ', primer_nombre, segundo_nombre, primer_apellido, segundo_apellido)) as nombre")
            ->where('activo', true)
            ->orderBy('primer_apellido')
            ->orderBy('primer_nombre');
    }
}
