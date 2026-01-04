<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPermiso extends Model
{
    // Nombre exacto de la tabla
    protected $table = 'tipo_permiso';

    // Clave primaria personalizada
    protected $primaryKey = 'id_tipo_permiso';
    public $incrementing = false;
    protected $keyType = 'string';

    // Campos asignables masivamente
    protected $fillable = [
        'id_tipo_permiso',
        'nombre',
        'descripcion',
        'requiere_recupero',
        'con_goce_haber',
        'requiere_documento',
        'estado'
    ];

    // Casting de tipos
    protected $casts = [
        'requiere_recupero'  => 'boolean',
        'con_goce_haber'     => 'boolean',
        'requiere_documento'=> 'boolean',
        'estado'             => 'integer',
    ];

    /**
     * Scope: solo tipos de permiso activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }
}