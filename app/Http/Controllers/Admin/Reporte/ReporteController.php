<?php

namespace App\Http\Controllers\Admin\Reporte;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\Docente;
use App\Models\SemestreAcademico;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function pdfSemestre($semestre_id)
    {
        try {
            // Obtener el semestre
            $semestre = SemestreAcademico::findOrFail($semestre_id);

            // Obtener todos los permisos del semestre
            $permisos = Permiso::where('id_semestre_academico', $semestre_id)
                ->with(['docente.user', 'tipoPermiso'])
                ->orderBy('fecha_inicio', 'desc')
                ->get();

            // Generar PDF usando DomPDF
            $pdf = Pdf::loadView('admin.reporte.pdf.docente', compact('semestre', 'permisos'));

            // Configurar el PDF en formato A4 vertical
            $pdf->setPaper('A4', 'portrait');

            // Retornar el PDF para visualización en el navegador
            return $pdf->stream('reporte_semestre_' . $semestre->codigo_Academico . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al generar PDF de semestre: ' . $e->getMessage());
            abort(500, 'Error al generar el reporte PDF');
        }
    }

    public function descargarPdfSemestre($semestre_id)
    {
        try {
            // Obtener el semestre
            $semestre = SemestreAcademico::findOrFail($semestre_id);

            // Obtener todos los permisos del semestre
            $permisos = Permiso::where('id_semestre_academico', $semestre_id)
                ->with(['docente.user', 'tipoPermiso'])
                ->orderBy('fecha_inicio', 'desc')
                ->get();

            // Generar PDF usando DomPDF
            $pdf = Pdf::loadView('admin.reporte.pdf.docente', compact('semestre', 'permisos'));

            // Configurar el PDF en formato A4 vertical
            $pdf->setPaper('A4', 'portrait');

            // Descargar el PDF automáticamente
            return $pdf->download('reporte_semestre_' . $semestre->codigo_Academico . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al descargar PDF de semestre: ' . $e->getMessage());
            abort(500, 'Error al descargar el reporte PDF');
        }
    }

    public function pdfDocente($docente_id)
    {
        try {
            // Obtener el docente
            $docente = Docente::with('user')->findOrFail($docente_id);

            // Obtener el semestre opcional del query string
            $semestre_id = request()->get('semestre_id');
            $semestre = null;

            // Construir query de permisos
            $query = Permiso::where('id_docente', $docente_id)
                ->with(['tipoPermiso', 'semestreAcademico']);

            // Filtrar por semestre si se proporciona
            if ($semestre_id) {
                $semestre = SemestreAcademico::find($semestre_id);
                $query->where('id_semestre_academico', $semestre_id);
            }

            $permisos = $query->orderBy('fecha_inicio', 'desc')->get();

            // Generar PDF usando DomPDF
            $pdf = Pdf::loadView('admin.reporte.pdf.permiso_docente', compact('docente', 'permisos', 'semestre'));

            // Configurar el PDF en formato A4 vertical
            $pdf->setPaper('A4', 'portrait');

            // Retornar el PDF para visualización en el navegador
            return $pdf->stream('reporte_docente_' . $docente->user->last_name . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al generar PDF de docente: ' . $e->getMessage());
            abort(500, 'Error al generar el reporte PDF');
        }
    }

    public function descargarPdfDocente($docente_id)
    {
        try {
            // Obtener el docente
            $docente = Docente::with('user')->findOrFail($docente_id);

            // Obtener el semestre opcional del query string
            $semestre_id = request()->get('semestre_id');
            $semestre = null;

            // Construir query de permisos
            $query = Permiso::where('id_docente', $docente_id)
                ->with(['tipoPermiso', 'semestreAcademico']);

            // Filtrar por semestre si se proporciona
            if ($semestre_id) {
                $semestre = SemestreAcademico::find($semestre_id);
                $query->where('id_semestre_academico', $semestre_id);
            }

            $permisos = $query->orderBy('fecha_inicio', 'desc')->get();

            // Generar PDF usando DomPDF
            $pdf = Pdf::loadView('admin.reporte.pdf.permiso_docente', compact('docente', 'permisos', 'semestre'));

            // Configurar el PDF en formato A4 vertical
            $pdf->setPaper('A4', 'portrait');

            // Descargar el PDF automáticamente
            return $pdf->download('reporte_docente_' . $docente->user->last_name . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al descargar PDF de docente: ' . $e->getMessage());
            abort(500, 'Error al descargar el reporte PDF');
        }
    }
}