<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SesionRecuperacion extends Model
{
    use HasFactory;

    // Nombre real de la tabla
    protected $table = 'sesion_recuperacion';

    // PK no autoincremental
    protected $primaryKey = 'id_sesion';
    public $incrementing = false;
    protected $keyType = 'string';

    // Campos asignables
    protected $fillable = [
        'id_sesion',
        'id_plan',
        'idAsignatura',
        'fecha_sesion',
        'hora_inicio',
        'hora_fin',
        'aula',
        'tema',
        'horas_recuperadas',
        'estado_sesion'
    ];

    // Casts (opcional pero recomendado)
    protected $casts = [
        'fecha_sesion' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'horas_recuperadas' => 'decimal:2'
    ];

    /**
     * Relación: una sesión pertenece a un plan de recuperación
     */
    public function planRecuperacion()
    {
        return $this->belongsTo(
            PlanRecuperacion::class,
            'id_plan',
            'id_plan'
        );
    }

    /**
     * Relación: una sesión pertenece a una asignatura
     */
    public function asignatura()
    {
        return $this->belongsTo(
            Asignatura::class,
            'idAsignatura',
            'idAsignatura'
        );
    }
}
