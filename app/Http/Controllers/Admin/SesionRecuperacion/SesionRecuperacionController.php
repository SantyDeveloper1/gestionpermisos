<?php
namespace App\Http\Controllers\Admin\SesionRecuperacion;
use App\Http\Controllers\Controller;
use App\Models\PlanRecuperacion;
use App\Models\SesionRecuperacion;
use App\Models\Asignatura;

class SesionRecuperacionController extends Controller
{
    public function actionSesionRecuperacion(\Illuminate\Http\Request $request)
    {
        // Obtener el filtro de plan si existe
        $planIdFilter = $request->get('plan_id');

        // Obtener planes de recuperación activos (APROBADO o PRESENTADO)
        $planesActivos = PlanRecuperacion::with(['permiso.docente', 'permiso.tipoPermiso'])
            ->whereIn('estado_plan', ['APROBADO', 'PRESENTADO'])
            ->orderBy('fecha_presentacion', 'desc')
            ->get();

        // Obtener todos los planes para el filtro
        $planes = PlanRecuperacion::with(['permiso.docente'])
            ->orderBy('fecha_presentacion', 'desc')
            ->get();

        // Obtener sesiones de ejecución con filtro opcional
        $sesionesQuery = SesionRecuperacion::with(['planRecuperacion.permiso.docente', 'planRecuperacion.permiso.tipoPermiso', 'asignatura']);

        if ($planIdFilter) {
            $sesionesQuery->where('id_plan', $planIdFilter);
        }

        $sesionesEjecucion = $sesionesQuery->orderBy('fecha_sesion', 'desc')->get();

        // Sesiones activas (EN_PROGRESO o PROGRAMADA)
        $sesionesActivas = SesionRecuperacion::whereIn('estado_sesion', ['PROGRAMADA', 'REALIZADA'])
            ->get();

        // Horas recuperadas hoy
        $horasRecuperadasHoy = SesionRecuperacion::where('fecha_sesion', date('Y-m-d'))
            ->where('estado_sesion', 'REALIZADA')
            ->sum('horas_recuperadas');

        // Cursos (vacío por ahora, necesitarás crear el modelo Curso)
        $cursos = collect([]);

        // Obtener asignaturas activas ordenadas por código
        $asignaturas = Asignatura::where('estado', 'Activo')
            ->orderBy('codigo_asignatura')
            ->get();

        // Obtener plan seleccionado si existe el filtro
        $planSeleccionado = null;
        $estadisticasPlan = null;

        if ($planIdFilter) {
            $planSeleccionado = PlanRecuperacion::with(['permiso.docente.user', 'permiso.tipoPermiso'])
                ->find($planIdFilter);

            if ($planSeleccionado) {
                // Calcular estadísticas dinámicas del plan
                $totalHoras = $planSeleccionado->total_horas_recuperar ?? 0;

                // Horas completadas (sesiones VALIDADAS)
                $horasCompletadas = SesionRecuperacion::where('id_plan', $planIdFilter)
                    ->where('estado_sesion', 'VALIDADA')
                    ->sum('horas_recuperadas') ?? 0;

                // Horas realizadas pero no validadas
                $horasRealizadas = SesionRecuperacion::where('id_plan', $planIdFilter)
                    ->where('estado_sesion', 'REALIZADA')
                    ->sum('horas_recuperadas') ?? 0;

                // Total de horas ejecutadas (completadas + realizadas)
                $horasEjecutadas = $horasCompletadas + $horasRealizadas;

                // Horas pendientes
                $horasPendientes = max(0, $totalHoras - $horasEjecutadas);

                // Porcentajes
                $porcentajeCompletado = $totalHoras > 0 ? round(($horasCompletadas / $totalHoras) * 100, 1) : 0;
                $porcentajePendiente = $totalHoras > 0 ? round(($horasPendientes / $totalHoras) * 100, 1) : 0;

                // Contar sesiones activas del plan
                $sesionesActivasPlan = SesionRecuperacion::where('id_plan', $planIdFilter)
                    ->whereIn('estado_sesion', ['PROGRAMADA', 'REALIZADA'])
                    ->count();

                // Porcentaje de sesiones (basado en estimación de 2 horas por sesión)
                $sesionesEstimadas = $totalHoras > 0 ? ceil($totalHoras / 2) : 1;
                $porcentajeSesiones = $sesionesEstimadas > 0 ? round(($sesionesActivasPlan / $sesionesEstimadas) * 100, 1) : 0;

                $estadisticasPlan = [
                    'total_horas' => $totalHoras,
                    'horas_completadas' => $horasCompletadas,
                    'horas_realizadas' => $horasRealizadas,
                    'horas_ejecutadas' => $horasEjecutadas,
                    'horas_pendientes' => $horasPendientes,
                    'porcentaje_completado' => $porcentajeCompletado,
                    'porcentaje_pendiente' => $porcentajePendiente,
                    'sesiones_activas' => $sesionesActivasPlan,
                    'sesiones_estimadas' => $sesionesEstimadas,
                    'porcentaje_sesiones' => $porcentajeSesiones,
                ];
            }
        }

        return view('admin/sesion_recuperacion/sesion_recuperacion', compact(
            'sesionesActivas',
            'horasRecuperadasHoy',
            'planes',
            'sesionesEjecucion',
            'planesActivos',
            'cursos',
            'planSeleccionado',
            'asignaturas',
            'estadisticasPlan'
        ));
    }

    /**
     * Handle the insert action for a new sesion de recuperación
     */
    public function actionInsert(\Illuminate\Http\Request $request)
    {
        try {
            // Validar los datos
            $validated = $request->validate([
                'id_plan_recuperacion' => 'required|exists:plan_recuperacion,id_plan',
                'idAsignatura' => 'required|exists:asignaturas,idAsignatura',
                'fecha_sesion' => 'required|date',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'aula' => 'nullable|string|max:50',
                'tema' => 'required|string|max:500',
                'horas_recuperadas' => 'required|numeric|min:0.5|max:8',
                'estado_sesion' => 'required|in:PROGRAMADA,REALIZADA,VALIDADA'
            ], [
                'id_plan_recuperacion.required' => 'Debe seleccionar un plan de recuperación.',
                'id_plan_recuperacion.exists' => 'El plan seleccionado no existe.',
                'idAsignatura.required' => 'Debe seleccionar una asignatura.',
                'idAsignatura.exists' => 'La asignatura seleccionada no existe.',
                'fecha_sesion.required' => 'La fecha de sesión es requerida.',
                'fecha_sesion.date' => 'La fecha de sesión no es válida.',
                'hora_inicio.required' => 'La hora de inicio es requerida.',
                'hora_inicio.date_format' => 'El formato de hora de inicio no es válido (HH:MM).',
                'hora_fin.required' => 'La hora de fin es requerida.',
                'hora_fin.date_format' => 'El formato de hora de fin no es válido (HH:MM).',
                'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
                'aula.max' => 'El aula no puede exceder 50 caracteres.',
                'tema.required' => 'El tema de la sesión es requerido.',
                'tema.max' => 'El tema no puede exceder 500 caracteres.',
                'horas_recuperadas.required' => 'Las horas a recuperar son requeridas.',
                'horas_recuperadas.min' => 'Las horas mínimas son 0.5.',
                'horas_recuperadas.max' => 'Las horas máximas son 8.',
                'estado_sesion.required' => 'El estado de sesión es requerido.',
                'estado_sesion.in' => 'El estado de sesión seleccionado no es válido.'
            ]);

            // Obtener el plan de recuperación para validaciones
            $plan = PlanRecuperacion::with('permiso')->findOrFail($validated['id_plan_recuperacion']);

            // Validar que la fecha de sesión no sea anterior a la fecha actual
            $fechaActual = date('Y-m-d');
            if ($validated['fecha_sesion'] < $fechaActual) {
                $fechaActualFormateada = date('d/m/Y', strtotime($fechaActual));
                return response()->json([
                    'success' => false,
                    'message' => "La fecha de sesión no puede ser anterior a la fecha actual ({$fechaActualFormateada}). Las sesiones de recuperación deben programarse desde hoy en adelante."
                ], 422);
            }

            // Generar ID único para la sesión (SES-YYYY-XXXX)
            $year = date('Y');
            $lastSesion = SesionRecuperacion::where('id_sesion', 'like', "SES-{$year}-%")
                ->orderBy('id_sesion', 'desc')
                ->first();

            if ($lastSesion) {
                $lastNumber = intval(substr($lastSesion->id_sesion, -4));
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }

            $id_sesion = "SES-{$year}-{$newNumber}";

            // Crear la sesión de recuperación
            $sesion = SesionRecuperacion::create([
                'id_sesion' => $id_sesion,
                'id_plan' => $validated['id_plan_recuperacion'],
                'idAsignatura' => $validated['idAsignatura'],
                'fecha_sesion' => $validated['fecha_sesion'],
                'hora_inicio' => $validated['hora_inicio'],
                'hora_fin' => $validated['hora_fin'],
                'aula' => $validated['aula'],
                'horas_recuperadas' => $validated['horas_recuperadas'],
                'estado_sesion' => $validated['estado_sesion']
            ]);


            // Cargar relaciones necesarias
            $sesion->load(['planRecuperacion.permiso.docente.user', 'asignatura']);

            return response()->json([
                'success' => true,
                'message' => 'Sesión de recuperación registrada exitosamente',
                'sesion' => [
                    'id_sesion' => $sesion->id_sesion,
                    'id_plan' => $sesion->id_plan,
                    'fecha_sesion' => $sesion->fecha_sesion,
                    'hora_inicio' => $sesion->hora_inicio,
                    'hora_fin' => $sesion->hora_fin,
                    'modalidad' => $sesion->modalidad,
                    'tipo_sesion' => $sesion->tipo_sesion,
                    'asignatura' => $sesion->asignatura,
                    'semestre' => $sesion->semestre,
                    'aula' => $sesion->aula,
                    'horas_recuperadas' => $sesion->horas_recuperadas,
                    'estado_sesion' => $sesion->estado_sesion,
                    'created_at' => $sesion->created_at->toISOString(),
                    'plan_horas_totales' => $sesion->planRecuperacion->total_horas_recuperar,
                    'docente_apellidos' => $sesion->planRecuperacion->permiso->docente->user->last_name,
                    'docente_nombres' => $sesion->planRecuperacion->permiso->docente->user->name,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al crear sesión de recuperación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show details of a specific sesion de recuperación
     */
    public function actionShow($id_sesion)
    {
        try {
            $sesion = SesionRecuperacion::with(['planRecuperacion.permiso.docente', 'planRecuperacion.permiso.tipoPermiso'])
                ->findOrFail($id_sesion);

            return response()->json([
                'success' => true,
                'sesion' => $sesion
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión de recuperación no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error al obtener sesión: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la sesión'
            ], 500);
        }
    }

    /**
     * Handle the update action for an existing sesion de recuperación
     */
    public function actionUpdate(\Illuminate\Http\Request $request, $id_sesion)
    {
        try {
            $sesion = SesionRecuperacion::findOrFail($id_sesion);

            $validated = $request->validate([
                'idAsignatura' => 'required|exists:asignaturas,idAsignatura',
                'fecha_sesion' => 'required|date',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'aula' => 'nullable|string|max:50',
                'tema' => 'required|string|max:500',
                'horas_recuperadas' => 'required|numeric|min:0.5|max:8',
                'estado_sesion' => 'required|in:PROGRAMADA,REALIZADA,VALIDADA'
            ]);

            // Cargar el plan para validaciones
            $sesion->load('planRecuperacion.permiso');

            // Validar que la fecha de sesión no sea anterior a la fecha actual
            $fechaActual = date('Y-m-d');
            if ($validated['fecha_sesion'] < $fechaActual) {
                $fechaActualFormateada = date('d/m/Y', strtotime($fechaActual));
                return response()->json([
                    'success' => false,
                    'message' => "La fecha de sesión no puede ser anterior a la fecha actual ({$fechaActualFormateada}). Las sesiones de recuperación deben programarse desde hoy en adelante."
                ], 422);
            }

            $sesion->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sesión actualizada exitosamente',
                'sesion' => $sesion->load(['planRecuperacion.permiso.docente'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar sesión: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la sesión'
            ], 500);
        }
    }

    /**
     * Handle the delete action for a sesion de recuperación
     */
    public function actionDelete($id_sesion)
    {
        try {
            $sesion = SesionRecuperacion::findOrFail($id_sesion);
            $sesion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión eliminada exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error al eliminar sesión: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la sesión'
            ], 500);
        }
    }

    /**
     * Update the estado_sesion of a specific session
     */
    public function actionUpdateEstado(\Illuminate\Http\Request $request, $id_sesion)
    {
        try {
            $sesion = SesionRecuperacion::findOrFail($id_sesion);

            $validated = $request->validate([
                'estado_sesion' => 'required|in:PROGRAMADA,REALIZADA,VALIDADA,CANCELADA',
                'comentario' => 'nullable|string|max:500'
            ]);

            // Validación: No permitir cambio a REALIZADA sin evidencia
            if ($validated['estado_sesion'] === 'REALIZADA') {
                // Verificar si la sesión tiene al menos una evidencia
                $evidenceCount = \DB::table('evidencia_recuperacion')
                    ->where('id_sesion', $id_sesion)
                    ->count();

                if ($evidenceCount === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede cambiar el estado a REALIZADA sin evidencia. Por favor, suba al menos una evidencia antes de marcar la sesión como realizada.'
                    ], 422);
                }
            }

            // CANCELADA puede establecerse sin evidencia (sin validación adicional)
            // VALIDADA y PROGRAMADA también pueden establecerse libremente

            // Actualizar el estado
            $sesion->estado_sesion = $validated['estado_sesion'];
            $sesion->save();

            \Log::info("Estado de sesión {$id_sesion} actualizado manualmente a {$validated['estado_sesion']}");

            return response()->json([
                'success' => true,
                'message' => 'Estado de la sesión actualizado exitosamente',
                'sesion' => $sesion->load(['planRecuperacion.permiso.docente', 'asignatura'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión no encontrada'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar estado de sesión: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
}