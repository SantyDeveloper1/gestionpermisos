<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    // Nombre exacto de la tabla
    protected $table = 'permiso';

    // Clave primaria
    protected $primaryKey = 'id_permiso';
    public $incrementing = false;     // PK CHAR no incrementa
    protected $keyType = 'string';    // Importante para CHAR/VARCHAR

    // Campos asignables masivamente
    protected $fillable = [
        'id_permiso',
        'id_docente',
        'id_tipo_permiso',
        'id_semestre_academico',
        'fecha_inicio',
        'fecha_fin',
        'dias_permiso',
        'horas_afectadas',
        'estado_permiso',
        'motivo',
        'observacion',
        'documento_sustento',
        'fecha_solicitud',
        'fecha_resolucion',
        'estado_notificado'
    ];

    // Casts de tipos
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_solicitud' => 'date',
        'fecha_resolucion' => 'date',
        'dias_permiso' => 'integer',
        'horas_afectadas' => 'decimal:2',
    ];

    /**
     * Relación: Permiso pertenece a un Docente
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente');
    }

    /**
     * Relación: Permiso pertenece a un Tipo de Permiso
     */
    public function tipoPermiso()
    {
        return $this->belongsTo(TipoPermiso::class, 'id_tipo_permiso', 'id_tipo_permiso');
    }

    /**
     * Relación: Permiso pertenece a un Semestre Académico
     */
    public function semestreAcademico()
    {
        return $this->belongsTo(SemestreAcademico::class, 'id_semestre_academico', 'IdSemestreAcademico');
    }

    /**
     * Relación: Permiso tiene un Plan de Recuperación
     */
    public function planRecuperacion()
    {
        return $this->hasOne(PlanRecuperacion::class, 'id_permiso', 'id_permiso');
    }

    /**
     * Scope: permisos por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado_permiso', $estado);
    }

    /**
     * Scope: permisos activos (no cerrados)
     */
    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado_permiso', ['CERRADO', 'RECHAZADO']);
    }

    /**
     * Estados permitidos (control institucional)
     */
    public static function estadosPermitidos(): array
    {
        return [
            'SOLICITADO',
            'APROBADO',
            'RECHAZADO',
            'EN_RECUPERACION',
            'RECUPERADO',
            'CERRADO'
        ];
    }
}