<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Acompanante extends Model
{
    protected $table = 'acompanantes';

    protected $fillable = [
        'paciente_id',
        'nombre',
        'parentesco',
        'tipo_documento_id',
        'numero_documento',
        'telefono',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class);
    }
}
