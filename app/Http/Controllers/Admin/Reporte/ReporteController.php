<?php

namespace App\Http\Controllers\Admin\Reporte;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\Docente;
use App\Models\SemestreAcademico;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        // Obtener todos los semestres
        $semestres = SemestreAcademico::orderBy('anio_academico', 'desc')
            ->orderBy('codigo_Academico', 'desc')
            ->get();

        // Obtener todos los docentes activos
        $docentes = Docente::where('estado', 1)
            ->with('user')
            ->get()
            ->sortBy(function ($docente) {
                return $docente->user->last_name . ' ' . $docente->user->name;
            });

        return view('admin.reporte.index', compact('semestres', 'docentes'));
    }

    public function estadisticas()
    {
        $totalPermisos = Permiso::count();
        $permisosActivos = Permiso::whereIn('estado_permiso', ['APROBADO', 'EN_RECUPERACION'])->count();
        $docentesConPermisos = Permiso::distinct('id_docente')->count('id_docente');
        $promedioDias = round(Permiso::avg('dias_permiso'), 1);

        return response()->json([
            'totalPermisos' => $totalPermisos,
            'permisosActivos' => $permisosActivos,
            'docentesConPermisos' => $docentesConPermisos,
            'promedioDias' => $promedioDias
        ]);
    }
}