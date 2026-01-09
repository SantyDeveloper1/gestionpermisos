<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'asignaturas';

    // Primary Key personalizada
    protected $primaryKey = 'idAsignatura';

    // La clave NO es autoincremental
    public $incrementing = false;

    // Tipo de la clave primaria
    protected $keyType = 'string';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'idAsignatura',
        'codigo_asignatura',
        'nom_asignatura',
        'creditos',
        'horas_teoria',
        'horas_practica',
        'IdCiclo',
        'tipo',
        'estado',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Una asignatura pertenece a un ciclo
    public function ciclo()
    {
        return $this->belongsTo(Ciclo::class, 'IdCiclo', 'IdCiclo');
    }

    // Una asignatura tiene muchas sesiones de recuperación
    public function sesionesRecuperacion()
    {
        return $this->hasMany(SesionRecuperacion::class, 'idAsignatura', 'idAsignatura');
    }

}