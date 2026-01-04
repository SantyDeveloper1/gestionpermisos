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
        'categoria_id',
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

    public function categoria()
    {
        return $this->belongsTo(CategoriaDocente::class, 'categoria_id', 'idCategori_docente');
    }

    public function contrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id', 'idTipo_contrato');
    }
}

