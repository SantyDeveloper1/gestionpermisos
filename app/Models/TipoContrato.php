<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoContrato extends Model
{
    use HasFactory;

    protected $table = 'tipos_contrato';     // Nombre de la tabla

    protected $primaryKey = 'idTipo_contrato'; // PK

    public $incrementing = false;            // No es autoincremental
    protected $keyType = 'string';           // CHAR(13)

    protected $fillable = [
        'idTipo_contrato',
        'nombre',
    ];
}