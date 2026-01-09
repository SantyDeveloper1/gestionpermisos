<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciclo extends Model
{
    // Nombre de la tabla
    protected $table = 'ciclos';

    // PK personalizada CHAR(13)
    protected $primaryKey = 'IdCiclo';
    public $incrementing = false;     // No autoincrementa
    protected $keyType = 'string';    // Tipo string
    
    // Campos asignables
    protected $fillable = [
        'IdCiclo',
        'NombreCiclo',
        'NumeroCiclo'
    ];
}