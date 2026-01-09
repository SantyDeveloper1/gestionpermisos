<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemestreAcademico extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'semestre_academico';

    // Primary Key personalizada
    protected $primaryKey = 'IdSemestreAcademico';

    // La clave NO es autoincremental
    public $incrementing = false;

    // Tipo de la clave primaria
    protected $keyType = 'string';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'IdSemestreAcademico',
        'codigo_Academico',        // Ej: "2025-I"
        'anio_academico',
        'FechaInicioAcademico',
        'FechaFinAcademico',
        'EstadoAcademico',
        'EsActualAcademico',
        'DescripcionAcademico',
    ];

    // Casts recomendados
    protected $casts = [
        'anio_academico'       => 'integer',
        'FechaInicioAcademico' => 'date',
        'FechaFinAcademico'    => 'date',
        'EsActualAcademico'    => 'boolean',
    ];
}