<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenciaRecuperacion extends Model
{
    // Nombre de la tabla
    protected $table = 'evidencia_recuperacion';

    // No hay clave primaria única
    protected $primaryKey = null;
    public $incrementing = false;

    // No usamos timestamps de Laravel
    public $timestamps = false;

    // Campos asignables
    protected $fillable = [
        'id_evidencia',
        'id_sesion',
        'tipo_evidencia',
        'archivo',
        'descripcion',
        'fecha_subida'
    ];

    // Casts
    protected $casts = [
        'fecha_subida' => 'datetime',
    ];

    /**
     * Relación: Evidencia pertenece a una Sesión de Recuperación
     */
    public function sesionRecuperacion()
    {
        return $this->belongsTo(
            SesionRecuperacion::class,
            'id_sesion',
            'id_sesion'
        );
    }

    /**
     * Tipos de evidencia permitidos
     */
    public static function tiposPermitidos(): array
    {
        return [
            'ACTA',
            'ASISTENCIA',
            'CAPTURA',
            'OTRO'
        ];
    }

    /**
     * Scope: evidencias por sesión
     */
    public function scopePorSesion($query, $idSesion)
    {
        return $query->where('id_sesion', $idSesion);
    }
}
