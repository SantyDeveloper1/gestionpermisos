<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanRecuperacion extends Model
{
    // Nombre exacto de la tabla
    protected $table = 'plan_recuperacion';

    // Clave primaria
    protected $primaryKey = 'id_plan';
    public $incrementing = false;     // PK CHAR no incrementa
    protected $keyType = 'string';    // Importante para CHAR/VARCHAR

    // Campos asignables masivamente
    protected $fillable = [
        'id_plan',
        'id_permiso',
        'fecha_presentacion',
        'total_horas_recuperar',
        'estado_plan',
        'observacion'
    ];

    // Casts de tipos
    protected $casts = [
        'fecha_presentacion' => 'date',
        'total_horas_recuperar' => 'decimal:2',
    ];

    /**
     * Relación: Plan de Recuperación pertenece a un Permiso
     */
    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'id_permiso', 'id_permiso');
    }

    /**
     * Relación: Plan de Recuperación tiene muchas Sesiones de Recuperación
     */
    public function sesiones()
    {
        return $this->hasMany(SesionRecuperacion::class, 'id_plan', 'id_plan');
    }

    /**
     * Scope: planes por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado_plan', $estado);
    }

    /**
     * Estados permitidos
     */
    public static function estadosPermitidos(): array
    {
        return [
            'PRESENTADO',
            'APROBADO',
            'OBSERVADO'
        ];
    }
}
