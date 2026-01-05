<?php
namespace App\Http\Controllers\Admin\SesionRecuperacion;
use App\Http\Controllers\Controller;
use App\Models\PlanRecuperacion;
use App\Models\SesionRecuperacion;

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
        $sesionesQuery = SesionRecuperacion::with(['planRecuperacion.permiso.docente', 'planRecuperacion.permiso.tipoPermiso']);

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

        // Obtener plan seleccionado si existe el filtro
        $planSeleccionado = null;
        if ($planIdFilter) {
            $planSeleccionado = PlanRecuperacion::with(['permiso.docente', 'permiso.tipoPermiso'])
                ->find($planIdFilter);
        }

        return view('admin/sesion_recuperacion/sesion_recuperacion', compact(
            'sesionesActivas',
            'horasRecuperadasHoy',
            'planes',
            'sesionesEjecucion',
            'planesActivos',
            'cursos',
            'planSeleccionado'
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
                'fecha_sesion' => 'required|date',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'modalidad' => 'required|in:PRESENCIAL,VIRTUAL,EXTRA',
                'tipo_sesion' => 'required|in:TEORIA,PRACTICA,EXAMEN',
                'asignatura' => 'required|string|max:100',
                'semestre' => 'required|in:PRIMERO,SEGUNDO,TERCERO,CUARTO,QUINTO,SEXTO,SEPTIMO,OCTAVO,NOVENO,DECIMO',
                'aula' => 'nullable|string|max:50',
                'horas_recuperadas' => 'required|numeric|min:0.5|max:8',
                'estado_sesion' => 'required|in:PROGRAMADA,REALIZADA,VALIDADA'
            ], [
                'id_plan_recuperacion.required' => 'Debe seleccionar un plan de recuperación.',
                'id_plan_recuperacion.exists' => 'El plan seleccionado no existe.',
                'fecha_sesion.required' => 'La fecha de sesión es requerida.',
                'fecha_sesion.date' => 'La fecha de sesión no es válida.',
                'hora_inicio.required' => 'La hora de inicio es requerida.',
                'hora_inicio.date_format' => 'El formato de hora de inicio no es válido (HH:MM).',
                'hora_fin.required' => 'La hora de fin es requerida.',
                'hora_fin.date_format' => 'El formato de hora de fin no es válido (HH:MM).',
                'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
                'modalidad.required' => 'La modalidad es requerida.',
                'modalidad.in' => 'La modalidad seleccionada no es válida.',
                'tipo_sesion.required' => 'El tipo de sesión es requerido.',
                'tipo_sesion.in' => 'El tipo de sesión seleccionado no es válido.',
                'asignatura.required' => 'La asignatura es requerida.',
                'asignatura.max' => 'La asignatura no puede exceder 100 caracteres.',
                'semestre.required' => 'El semestre es requerido.',
                'semestre.in' => 'El semestre seleccionado no es válido.',
                'aula.max' => 'El aula no puede exceder 50 caracteres.',
                'horas_recuperadas.required' => 'Las horas a recuperar son requeridas.',
                'horas_recuperadas.min' => 'Las horas mínimas son 0.5.',
                'horas_recuperadas.max' => 'Las horas máximas son 8.',
                'estado_sesion.required' => 'El estado de sesión es requerido.',
                'estado_sesion.in' => 'El estado de sesión seleccionado no es válido.'
            ]);

            // Obtener el plan de recuperación y su permiso asociado para validar la fecha
            $plan = PlanRecuperacion::with('permiso')->findOrFail($validated['id_plan_recuperacion']);

            // Validar que la fecha de sesión no sea anterior a la fecha fin del permiso
            if ($validated['fecha_sesion'] < $plan->permiso->fecha_fin) {
                $fechaFinFormateada = date('d/m/Y', strtotime($plan->permiso->fecha_fin));
                return response()->json([
                    'success' => false,
                    'message' => "La fecha de sesión no puede ser anterior a la fecha fin del permiso ({$fechaFinFormateada}). Las sesiones de recuperación deben programarse después de que finalice el período del permiso."
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
                'fecha_sesion' => $validated['fecha_sesion'],
                'hora_inicio' => $validated['hora_inicio'],
                'hora_fin' => $validated['hora_fin'],
                'modalidad' => $validated['modalidad'],
                'tipo_sesion' => $validated['tipo_sesion'],
                'asignatura' => $validated['asignatura'],
                'semestre' => $validated['semestre'],
                'aula' => $validated['aula'],
                'horas_recuperadas' => $validated['horas_recuperadas'],
                'estado_sesion' => $validated['estado_sesion']
            ]);

            // Cargar relaciones necesarias
            $sesion->load(['planRecuperacion.permiso.docente.user']);

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
                'fecha_sesion' => 'required|date',
                'modalidad' => 'required|in:PRESENCIAL,VIRTUAL,EXTRA',
                'curso' => 'required|string|max:100',
                'horas_recuperadas' => 'required|numeric|min:0.5|max:8',
                'estado_sesion' => 'required|in:PROGRAMADA,REALIZADA,VALIDADA'
            ]);

            // Cargar el plan y permiso asociado para validar la fecha
            $sesion->load('planRecuperacion.permiso');

            // Validar que la fecha de sesión no sea anterior a la fecha fin del permiso
            if ($validated['fecha_sesion'] < $sesion->planRecuperacion->permiso->fecha_fin) {
                $fechaFinFormateada = date('d/m/Y', strtotime($sesion->planRecuperacion->permiso->fecha_fin));
                return response()->json([
                    'success' => false,
                    'message' => "La fecha de sesión no puede ser anterior a la fecha fin del permiso ({$fechaFinFormateada}). Las sesiones de recuperación deben programarse después de que finalice el período del permiso."
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
}