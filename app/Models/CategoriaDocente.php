<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaDocente extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'categorias_docente';

    // Primary key personalizada
    protected $primaryKey = 'idCategori_docente';

    // Tipo de la PK (CHAR)
    protected $keyType = 'string';

    // Indica que NO es autoincremental
    public $incrementing = false;

    // Campos asignables
    protected $fillable = [
        'idCategori_docente',
        'nombre',
    ];
}