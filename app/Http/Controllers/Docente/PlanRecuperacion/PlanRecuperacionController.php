<?php

namespace App\Http\Controllers\Docente\PlanRecuperacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PlanRecuperacion;
use App\Models\SesionRecuperacion;
use App\Models\Permiso;

class PlanRecuperacionController extends Controller
{
    /**
     * Display the main plan de recuperación view
     */
    public function actionPlanRecuperacion()
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener el docente asociado al usuario autenticado
        $docente = \App\Models\Docente::where('user_id', $user->id)->first();

        if (!$docente) {
            abort(403, 'No se encontró un perfil de docente asociado a este usuario.');
        }

        // Debug: Log del docente autenticado
        \Log::info('=== DEBUG PLAN RECUPERACION ===');
        \Log::info('Usuario autenticado: ' . $user->name . ' ' . $user->last_name . ' (ID: ' . $user->id . ')');
        \Log::info('Docente ID: ' . $docente->idDocente);

        // Obtener todos los planes de recuperación SOLO del docente autenticado
        $listPlanes = PlanRecuperacion::with(['permiso.docente.user', 'permiso.tipoPermiso'])
            ->whereHas('permiso', function ($query) use ($docente) {
                $query->where('id_docente', $docente->idDocente);
            })
            ->orderBy('fecha_presentacion', 'desc')
            ->get();

        // Debug: Log de planes encontrados
        \Log::info('Planes encontrados: ' . $listPlanes->count());
        foreach ($listPlanes as $plan) {
            \Log::info('Plan ID: ' . $plan->id_plan . ' - Docente: ' . $plan->permiso->docente->user->last_name . ', ' . $plan->permiso->docente->user->name . ' (ID Docente: ' . $plan->permiso->id_docente . ')');
        }

        // Obtener permisos que requieren recuperación SOLO del docente autenticado
        $permisosRecuperables = Permiso::with(['docente.user', 'tipoPermiso'])
            ->where('id_docente', $docente->idDocente)
            ->where('estado_permiso', 'APROBADO')
            ->where('horas_afectadas', '>', 0)
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        // Debug: Verificar si hay permisos
        \Log::info('Permisos recuperables encontrados para docente ' . $docente->idDocente . ': ' . $permisosRecuperables->count());

        return view('docente.plan_recuperacion.plan_recuperacion', compact('listPlanes', 'permisosRecuperables'));
    }

    /**
     * Handle the insert action for a new plan de recuperación with initial session
     */
    public function actionInsert(Request $request)
    {
        try {
            // Validar los datos del plan
            $validated = $request->validate([
                'id_permiso' => 'required|exists:permiso,id_permiso',
                'fecha_presentacion' => 'required|date',
                'total_horas_recuperar' => 'required|numeric|min:0',
                'estado_plan' => 'required|in:PRESENTADO,APROBADO,OBSERVADO',
                'observacion' => 'nullable|string|max:500',
                // Validar datos de la sesión
                'idAsignatura' => 'required|exists:asignaturas,idAsignatura',
                'fecha_sesion' => 'required|date',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'horas_recuperadas' => 'required|numeric|min:0.5|max:8',
                'estado_sesion' => 'required|in:PROGRAMADA,REALIZADA,CANCELADA',
                'aula' => 'nullable|string|max:50'
            ]);

            // Obtener el permiso para validaciones adicionales
            $permiso = Permiso::findOrFail($validated['id_permiso']);

            // Validar que la fecha de presentación no sea anterior a la fecha actual
            $fechaActual = date('Y-m-d');
            if ($validated['fecha_presentacion'] < $fechaActual) {
                $fechaActualFormateada = date('d/m/Y', strtotime($fechaActual));
                return response()->json([
                    'success' => false,
                    'message' => "La fecha de presentación del plan no puede ser anterior a la fecha actual ({$fechaActualFormateada}). El plan de recuperación debe presentarse desde hoy en adelante."
                ], 422);
            }

            // Validar que la fecha de sesión no sea anterior a la fecha actual
            if ($validated['fecha_sesion'] < $fechaActual) {
                $fechaActualFormateada = date('d/m/Y', strtotime($fechaActual));
                return response()->json([
                    'success' => false,
                    'message' => "La fecha de sesión no puede ser anterior a la fecha actual ({$fechaActualFormateada})"
                ], 422);
            }

            // Validar que las horas de la sesión no excedan el total del plan
            if ($validated['horas_recuperadas'] > $validated['total_horas_recuperar']) {
                return response()->json([
                    'success' => false,
                    'message' => "Las horas de la sesión ({$validated['horas_recuperadas']}) no pueden exceder el total del plan ({$validated['total_horas_recuperar']})"
                ], 422);
            }

            // Verificar si el permiso ya tiene un plan de recuperación
            $planExistente = PlanRecuperacion::where('id_permiso', $validated['id_permiso'])->first();
            if ($planExistente) {
                // Calcular horas ya recuperadas
                $horasRecuperadas = SesionRecuperacion::where('id_plan', $planExistente->id_plan)
                    ->where('estado_sesion', '!=', 'CANCELADA')
                    ->sum('horas_recuperadas');

                // Solo bloquear si las horas ya están completamente recuperadas
                if ($horasRecuperadas >= $planExistente->total_horas_recuperar) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este permiso ya tiene todas las horas recuperadas. No se pueden agregar más sesiones.'
                    ], 422);
                }

                // Si aún hay horas pendientes, permitir agregar la sesión al plan existente
                // En lugar de crear un nuevo plan, agregar la sesión al plan existente
                $idPlan = $planExistente->id_plan;
                $plan = $planExistente;
            } else {
                // Si no existe plan, crear uno nuevo
                $idPlan = null;
                $plan = null;
            }

            // Iniciar transacción para crear plan y sesión atómicamente
            DB::beginTransaction();

            try {
                $year = date('Y');

                // Solo crear un nuevo plan si no existe uno
                if (!$plan) {
                    // Generar ID único para el plan (formato: PLN-YYYY-####)
                    $lastPlan = PlanRecuperacion::where('id_plan', 'like', "PLN-{$year}-%")
                        ->orderBy('id_plan', 'desc')
                        ->first();

                    if ($lastPlan) {
                        $lastNumber = intval(substr($lastPlan->id_plan, -4));
                        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                    } else {
                        $newNumber = '0001';
                    }

                    $idPlan = "PLN-{$year}-{$newNumber}";

                    // Crear el plan de recuperación
                    $plan = PlanRecuperacion::create([
                        'id_plan' => $idPlan,
                        'id_permiso' => $validated['id_permiso'],
                        'fecha_presentacion' => $validated['fecha_presentacion'],
                        'total_horas_recuperar' => $validated['total_horas_recuperar'],
                        'estado_plan' => $validated['estado_plan'],
                        'observacion' => $validated['observacion'] ?? null
                    ]);
                } else {
                    // Usar el ID del plan existente
                    $idPlan = $plan->id_plan;
                }

                // Generar ID único para la sesión (formato: SES-YYYY-####)
                $lastSesion = SesionRecuperacion::where('id_sesion', 'like', "SES-{$year}-%")
                    ->orderBy('id_sesion', 'desc')
                    ->first();

                if ($lastSesion) {
                    $lastNumber = intval(substr($lastSesion->id_sesion, -4));
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $idSesion = "SES-{$year}-{$newNumber}";

                // Crear la sesión de recuperación
                $sesion = SesionRecuperacion::create([
                    'id_sesion' => $idSesion,
                    'id_plan' => $idPlan,
                    'idAsignatura' => $validated['idAsignatura'],
                    'fecha_sesion' => $validated['fecha_sesion'],
                    'hora_inicio' => $validated['hora_inicio'],
                    'hora_fin' => $validated['hora_fin'],
                    'horas_recuperadas' => $validated['horas_recuperadas'],
                    'estado_sesion' => $validated['estado_sesion'],
                    'aula' => $validated['aula'] ?? null
                ]);

                // Confirmar transacción
                DB::commit();

                // Cargar relaciones necesarias
                $plan->load(['permiso.docente.user', 'permiso.tipoPermiso']);

                return response()->json([
                    'success' => true,
                    'message' => 'Plan de recuperación y sesión inicial creados exitosamente',
                    'plan' => [
                        'id_plan' => $plan->id_plan,
                        'id_permiso' => $plan->id_permiso,
                        'fecha_presentacion' => $plan->fecha_presentacion,
                        'total_horas_recuperar' => $plan->total_horas_recuperar,
                        'estado_plan' => $plan->estado_plan,
                        'observacion' => $plan->observacion,
                        'tipo_permiso' => $plan->permiso->tipoPermiso->nombre,
                        'docente_nombre' => $plan->permiso->docente->user->name,
                        'docente_apellido' => $plan->permiso->docente->user->last_name,
                    ],
                    'sesion' => [
                        'id_sesion' => $sesion->id_sesion,
                        'fecha_sesion' => $sesion->fecha_sesion,
                        'horas_recuperadas' => $sesion->horas_recuperadas,
                        'estado_sesion' => $sesion->estado_sesion
                    ]
                ]);

            } catch (\Exception $e) {
                // Revertir transacción en caso de error
                DB::rollback();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al crear plan de recuperación y sesión: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el plan de recuperación y sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle the update action for an existing plan de recuperación
     */
    public function actionUpdate(Request $request, $idPlan_recuperacion)
    {
        try {
            // Buscar el plan
            $plan = PlanRecuperacion::findOrFail($idPlan_recuperacion);

            // Validar los datos
            $validated = $request->validate([
                'fecha_presentacion' => 'required|date',
                'total_horas_recuperar' => 'required|numeric|min:0',
                'estado_plan' => 'required|in:PRESENTADO,APROBADO,OBSERVADO',
                'observacion' => 'nullable|string|max:500'
            ]);

            // Actualizar el plan
            $plan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Plan de recuperación actualizado exitosamente',
                'plan' => $plan->load(['permiso.docente', 'permiso.tipoPermiso'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Plan de recuperación no encontrado'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar plan de recuperación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el plan de recuperación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show details of a specific plan de recuperación
     */
    public function actionShow($idPlan_recuperacion)
    {
        try {
            $plan = PlanRecuperacion::with(['permiso.docente.user', 'permiso.tipoPermiso'])
                ->findOrFail($idPlan_recuperacion);

            return response()->json([
                'success' => true,
                'id_plan' => $plan->id_plan,
                'fecha_presentacion' => $plan->fecha_presentacion,
                'total_horas_recuperar' => $plan->total_horas_recuperar,
                'estado_plan' => $plan->estado_plan,
                'observacion' => $plan->observacion,
                'permiso' => [
                    'id_permiso' => $plan->permiso->id_permiso,
                    'fecha_inicio' => $plan->permiso->fecha_inicio,
                    'fecha_fin' => $plan->permiso->fecha_fin,
                    'horas_afectadas' => $plan->permiso->horas_afectadas,
                    'docente' => [
                        'last_name' => $plan->permiso->docente->user->last_name,
                        'name' => $plan->permiso->docente->user->name,
                        'full_name' => $plan->permiso->docente->user->last_name . ', ' . $plan->permiso->docente->user->name
                    ],
                    'tipoPermiso' => [
                        'nombre' => $plan->permiso->tipoPermiso->nombre
                    ]
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Plan de recuperación no encontrado'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error al obtener plan de recuperación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el plan de recuperación'
            ], 500);
        }
    }

    /**
     * Approve a plan de recuperación
     */
    public function actionAprobar($idPlan_recuperacion)
    {
        try {
            $plan = PlanRecuperacion::findOrFail($idPlan_recuperacion);

            // Update status to APROBADO
            $plan->update([
                'estado_plan' => 'APROBADO'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan de recuperación aprobado exitosamente',
                'plan' => $plan->load(['permiso.docente.user', 'permiso.tipoPermiso'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Plan de recuperación no encontrado'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error al aprobar plan de recuperación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar el plan de recuperación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle the delete action for a plan de recuperación
     */
    public function actionDelete($idPlan_recuperacion)
    {
        try {
            $plan = PlanRecuperacion::findOrFail($idPlan_recuperacion);
            $plan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Plan de recuperación eliminado exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Plan de recuperación no encontrado'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error al eliminar plan de recuperación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el plan de recuperación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recovery progress for a specific permission
     */
    public function actionProgreso($idPermiso)
    {
        try {
            // Buscar el plan de recuperación asociado al permiso
            $plan = PlanRecuperacion::where('id_permiso', $idPermiso)->first();

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe un plan de recuperación para este permiso',
                    'horas_recuperadas' => 0
                ]);
            }

            // Obtener todas las sesiones del plan y sumar las horas recuperadas
            $horasRecuperadas = SesionRecuperacion::where('id_plan', $plan->id_plan)
                ->where('estado_sesion', '!=', 'CANCELADA') // No contar sesiones canceladas
                ->sum('horas_recuperadas');

            // Contar sesiones programadas
            $sesionesProgramadas = SesionRecuperacion::where('id_plan', $plan->id_plan)
                ->where('estado_sesion', 'PROGRAMADA')
                ->count();

            $horasProgramadas = SesionRecuperacion::where('id_plan', $plan->id_plan)
                ->where('estado_sesion', 'PROGRAMADA')
                ->sum('horas_recuperadas');

            return response()->json([
                'success' => true,
                'id_plan' => $plan->id_plan,
                'horas_totales' => $plan->total_horas_recuperar,
                'horas_recuperadas' => $horasRecuperadas,
                'horas_pendientes' => $plan->total_horas_recuperar - $horasRecuperadas,
                'sesiones_count' => SesionRecuperacion::where('id_plan', $plan->id_plan)
                    ->where('estado_sesion', '!=', 'CANCELADA')
                    ->count(),
                'sesiones_programadas' => $sesionesProgramadas,
                'horas_programadas' => $horasProgramadas
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener progreso de recuperación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el progreso de recuperación',
                'horas_recuperadas' => 0
            ], 500);
        }
    }
}
