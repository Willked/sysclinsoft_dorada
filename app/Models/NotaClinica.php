<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaClinica extends Model
{
    public const TIPO_MEDICO = 'medico';

    public const TIPO_ENFERMERIA = 'enfermeria';

    protected $table = 'notas_clinicas';

    protected $fillable = [
        'atencion_id',
        'usuario_id',
        'tipo_redactor',
        'contenido',
    ];

    public function atencion(): BelongsTo
    {
        return $this->belongsTo(Atencion::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
