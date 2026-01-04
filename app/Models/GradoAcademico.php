<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradoAcademico extends Model
{
    protected $table = 'grados_academicos';

    protected $primaryKey = 'idGrados_academicos';
    public $incrementing = false; // Porque no es INTEGER
    protected $keyType = 'string';

    protected $fillable = [
        'idGrados_academicos',
        'nombre',
    ];
}