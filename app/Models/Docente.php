<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Docente extends Model
{
    protected $table = 'docentes';

    protected $primaryKey = 'idDocente';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idDocente',
        'user_id',
        'codigo_unamba',
        'grado_id',
        'tipo_contrato_id',
        'estado',
    ];

    // 🔗 Docente pertenece a User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function grado()
    {
        return $this->belongsTo(GradoAcademico::class, 'grado_id', 'idGrados_academicos');
    }



    public function contrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id', 'idTipo_contrato');
    }

    // Accessor para obtener el nombre completo del docente desde User
    public function getNombreAttribute()
    {
        return $this->user ? $this->user->name . ' ' . $this->user->last_name : 'N/A';
    }
}

