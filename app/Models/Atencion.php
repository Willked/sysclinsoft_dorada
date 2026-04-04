<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Atencion extends Model
{
    protected $table = 'atenciones';

    protected $fillable = [
        'paciente_id',
        'acompanante_id',
        'ambulancia_id',
        'conductor_id',
        'enfermero_id',
        'medico_id',
        'hora_llamada',
        'hora_despacho',
        'salida_base',
        'llegada_escena',
        'salida_escena',
        'llegada_destino',
        'cups_id',
        'tipo_servicio',
        'causa_externa_id',
        'institucion_origen',
        'institucion_destino',
        'departamento_id',
        'municipio_id',
        'eps_id',
        'autorizacion_eps',
        'tipo_usuario_id',
        'zona',
        'evaluacion_fisica',
        'comentario',
        'estado',
        'triage',
        'diagnostico_id',
        'signos_vitales_id',
    ];

    protected function casts(): array
    {
        return [
            'hora_llamada' => 'datetime',
            'hora_despacho' => 'datetime',
            'salida_base' => 'datetime',
            'llegada_escena' => 'datetime',
            'salida_escena' => 'datetime',
            'llegada_destino' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function acompanante(): BelongsTo
    {
        return $this->belongsTo(Acompanante::class);
    }

    public function ambulancia(): BelongsTo
    {
        return $this->belongsTo(Ambulancia::class);
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(Conductor::class);
    }

    public function enfermero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enfermero_id');
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function cup(): BelongsTo
    {
        return $this->belongsTo(Cup::class, 'cups_id');
    }

    public function causaExterna(): BelongsTo
    {
        return $this->belongsTo(CausaExterna::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    public function eps(): BelongsTo
    {
        return $this->belongsTo(Eps::class);
    }

    public function tipoUsuario(): BelongsTo
    {
        return $this->belongsTo(TipoUsuario::class);
    }

    public function signosVitales(): HasMany
    {
        return $this->hasMany(SignoVital::class);
    }

    /** Registro señalado explícitamente en atenciones.signos_vitales_id */
    public function signoVitalReferenciado(): BelongsTo
    {
        return $this->belongsTo(SignoVital::class, 'signos_vitales_id');
    }

    public function glasgowRegistros(): HasMany
    {
        return $this->hasMany(GlasgowRegistro::class);
    }

    public function notasClinicas(): HasMany
    {
        return $this->hasMany(NotaClinica::class);
    }
}
