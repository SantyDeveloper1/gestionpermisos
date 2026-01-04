<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoAsignatura extends Model
{
    protected $table = 'grupo_asignaturas';
    protected $primaryKey = 'IdGrupoAsignatura';
    public $incrementing = false; // PK tipo CHAR, no autoincremental
    protected $keyType = 'string'; // Tipo string

    protected $fillable = [
        'IdGrupoAsignatura',
        'CodigoGrupo',
        'CantidadGrupo',
        'idAsignatura',
    ];

    // RELACIÓN: cada grupo pertenece a una asignatura
    public function asignatura()
    {
        return $this->belongsTo(
            Asignatura::class,
            'idAsignatura',
            'idAsignatura'
        );
    }

    // RELACIÓN: un grupo puede tener múltiples cargas lectivas
    public function cargaLectiva()
    {
        return $this->hasMany(
            CargaLectiva::class,
            'IdGrupoAsignatura',
            'IdGrupoAsignatura'
        );
    }

    // RELACIÓN: un grupo puede tener múltiples horarios
    public function horarios()
    {
        return $this->hasMany(
            Horario::class,
            'IdGrupoAsignatura',
            'IdGrupoAsignatura'
        );
    }

}
