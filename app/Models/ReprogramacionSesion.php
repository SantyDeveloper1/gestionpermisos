<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReprogramacionSesion extends Model
{
    protected $table = 'reprogramacion_sesion';
    protected $primaryKey = 'id_reprogramacion';

    public $incrementing = false;   // 🔴 IMPORTANTE
    protected $keyType = 'string';  // 🔴 IMPORTANTE

    public $timestamps = false;

    protected $fillable = [
        'id_reprogramacion',
        'id_sesion',
        'fecha_anterior',
        'hora_inicio_anterior',
        'hora_fin_anterior',
        'aula_anterior',
        'fecha_nueva',
        'hora_inicio_nueva',
        'hora_fin_nueva',
        'aula_nueva',
        'motivo',
        'fecha_registro'
    ];

    protected $casts = [
        'fecha_anterior' => 'date',
        'fecha_nueva' => 'date',
        'hora_inicio_anterior' => 'datetime:H:i:s',
        'hora_fin_anterior' => 'datetime:H:i:s',
        'hora_inicio_nueva' => 'datetime:H:i:s',
        'hora_fin_nueva' => 'datetime:H:i:s',
        'fecha_registro' => 'datetime',
    ];

    /**
     * Relación con SesionRecuperacion
     */
    public function sesionRecuperacion()
    {
        return $this->belongsTo(SesionRecuperacion::class, 'id_sesion', 'id_sesion');
    }

    /**
     * Generar un ID único para la reprogramación
     * Formato: REP + timestamp (10 dígitos) = 13 caracteres
     */
    public static function generarId()
    {
        // Usar timestamp de 10 dígitos (segundos desde epoch)
        return 'REP' . time();
    }
}
