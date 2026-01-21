<?php

namespace App\Http\Controllers\Docente\SeguimientoPlan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlanRecuperacion;
use App\Models\Permiso;
use App\Models\Docente;
use Illuminate\Support\Facades\Auth;

class SeguimientoPlanController extends Controller
{
    /**
     * Display the seguimiento plan view
     */
    public function actionSeguimientoPlan()
    {
        return view('docente.seguimiento_plan.seguimiento_plan');
    }

    /**
     * Get all recovery plans for the authenticated teacher
     */
    public function getPlanes()
    {
        try {
            // Obtener el docente autenticado
            $user = Auth::user();
            $docente = Docente::where('user_id', $user->id)->first();

            if (!$docente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Docente no encontrado'
                ], 404);
            }

            // Obtener permisos aprobados del docente
            $permisos = Permiso::where('id_docente', $docente->idDocente)
                ->where('estado_permiso', 'APROBADO')
                ->with(['tipoPermiso', 'semestreAcademico'])
                ->get();

            // Obtener planes de recuperación de esos permisos
            $planesData = [];

            foreach ($permisos as $permiso) {
                $plan = PlanRecuperacion::where('id_permiso', $permiso->id_permiso)
                    ->with([
                        'sesiones' => function ($query) {
                            $query->with(['asignatura', 'evidencias']);
                        },
                        'permiso.tipoPermiso'
                    ])
                    ->first();

                if ($plan) {
                    // Calcular estadísticas del plan
                    $sesiones = $plan->sesiones;
                    $totalSesiones = $sesiones->count();

                    $sesionesRealizadas = $sesiones->where('estado_sesion', 'REALIZADA')->count();
                    $sesionesProgramadas = $sesiones->where('estado_sesion', 'PROGRAMADA')->count();
                    $sesionesReprogramadas = $sesiones->where('estado_sesion', 'REPROGRAMADA')->count();
                    $sesionesCanceladas = $sesiones->where('estado_sesion', 'CANCELADA')->count();

                    // Contar evidencias por tipo
                    $evidencias = $sesiones->flatMap->evidencias;
                    $evidenciasActa = $evidencias->where('tipo_evidencia', 'ACTA')->count();
                    $evidenciasAsistencia = $evidencias->where('tipo_evidencia', 'ASISTENCIA')->count();
                    $evidenciasCaptura = $evidencias->where('tipo_evidencia', 'CAPTURA')->count();
                    $evidenciasOtro = $evidencias->where('tipo_evidencia', 'OTRO')->count();

                    // Preparar datos de sesiones
                    $sesionesData = $sesiones->map(function ($sesion) {
                        $evidenciasSesion = $sesion->evidencias;

                        return [
                            'id_sesion' => $sesion->id_sesion,
                            'fecha_sesion' => $sesion->fecha_sesion ? $sesion->fecha_sesion->format('d/m/Y') : 'N/A',
                            'fecha_original' => $sesion->fecha_sesion ? $sesion->fecha_sesion->format('Y-m-d') : null,
                            'hora_inicio' => $sesion->hora_inicio ? \Carbon\Carbon::parse($sesion->hora_inicio)->format('H:i') : 'N/A',
                            'hora_fin' => $sesion->hora_fin ? \Carbon\Carbon::parse($sesion->hora_fin)->format('H:i') : 'N/A',
                            'aula' => $sesion->aula ?? 'Por definir',
                            'tema' => $sesion->tema ?? 'Sin tema especificado',
                            'asignatura' => $sesion->asignatura?->nom_asignatura ?? 'Sin asignatura',
                            'estado_sesion' => $sesion->estado_sesion,
                            'horas_recuperadas' => $sesion->horas_recuperadas ?? 0,
                            'tiene_evidencia' => $evidenciasSesion->count() > 0,
                            'evidencias' => $evidenciasSesion->map(function ($ev) {
                                return [
                                    'tipo' => $ev->tipo_evidencia,
                                    'archivo' => $ev->archivo,
                                    'descripcion' => $ev->descripcion,
                                    'fecha_subida' => $ev->fecha_subida
                                ];
                            })
                        ];
                    });

                    // Buscar próxima sesión programada
                    $proximaSesion = $sesiones
                        ->where('estado_sesion', 'PROGRAMADA')
                        ->sortBy('fecha_sesion')
                        ->first();

                    $planesData[] = [
                        'id_plan' => $plan->id_plan,
                        'id_permiso' => $plan->id_permiso,
                        'nombre' => 'Plan #' . substr($plan->id_plan, -3) . ' - ' .
                            ($plan->permiso->tipoPermiso->nombre ?? 'N/A'),
                        'estado_plan' => $plan->estado_plan,
                        'fecha_presentacion' => $plan->fecha_presentacion->format('d/m/Y'),
                        'total_horas_recuperar' => $plan->total_horas_recuperar,
                        'observacion' => $plan->observacion,

                        // Estadísticas de sesiones
                        'total_sesiones' => $totalSesiones,
                        'sesiones_realizadas' => $sesionesRealizadas,
                        'sesiones_programadas' => $sesionesProgramadas,
                        'sesiones_reprogramadas' => $sesionesReprogramadas,
                        'sesiones_canceladas' => $sesionesCanceladas,

                        // Estadísticas de evidencias
                        'total_evidencias' => $evidencias->count(),
                        'evidencias_acta' => $evidenciasActa,
                        'evidencias_asistencia' => $evidenciasAsistencia,
                        'evidencias_captura' => $evidenciasCaptura,
                        'evidencias_otro' => $evidenciasOtro,

                        // Próxima sesión
                        'proxima_sesion' => $proximaSesion ? [
                            'fecha' => $proximaSesion->fecha_sesion->format('d/m/Y'),
                            'tema' => $proximaSesion->tema
                        ] : null,

                        // Datos del permiso
                        'permiso' => [
                            'tipo' => $plan->permiso->tipoPermiso->nombre ?? 'N/A',
                            'fecha_inicio' => $plan->permiso->fecha_inicio,
                            'fecha_fin' => $plan->permiso->fecha_fin,
                        ],

                        // Sesiones detalladas
                        'sesiones' => $sesionesData
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'planes' => $planesData,
                'docente' => [
                    'nombre' => $docente->nombre,
                    'id' => $docente->codigo_unamba ?? $docente->idDocente,
                    'departamento' => 'Departamento Académico'
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en getPlanes: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los planes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a specific plan
     */
    public function getPlanDetalle($idPlan)
    {
        try {
            $user = Auth::user();
            $docente = Docente::where('user_id', $user->id)->first();

            if (!$docente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Docente no encontrado'
                ], 404);
            }

            $plan = PlanRecuperacion::where('id_plan', $idPlan)
                ->with([
                    'sesiones' => function ($query) {
                        $query->with(['asignatura', 'evidencias']);
                    },
                    'permiso' => function ($query) {
                        $query->with('tipoPermiso');
                    }
                ])
                ->first();

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan no encontrado'
                ], 404);
            }

            // Verificar que el plan pertenece al docente
            if ($plan->permiso->id_docente != $docente->idDocente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'plan' => $plan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el plan: ' . $e->getMessage()
            ], 500);
        }
    }
}
