<?php

namespace App\Http\Controllers\Admin\PlanRecuperacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlanRecuperacion;
use App\Models\Permiso;

class PlanRecuperacionController extends Controller
{
    /**
     * Display the main plan de recuperación view
     */
    public function actionPlanRecuperacion()
    {
        // Obtener todos los planes de recuperación con sus relaciones
        $listPlanes = PlanRecuperacion::with(['permiso.docente.user', 'permiso.tipoPermiso'])
            ->orderBy('fecha_presentacion', 'desc')
            ->get();

        // Obtener permisos que requieren recuperación
        // Temporalmente mostramos todos los permisos aprobados con horas afectadas
        // para facilitar el debugging
        $permisosRecuperables = Permiso::with(['docente.user', 'tipoPermiso'])
            ->where('estado_permiso', 'APROBADO')
            ->where('horas_afectadas', '>', 0)
            // Comentado temporalmente para ver todos los permisos disponibles
            // ->whereDoesntHave('planRecuperacion')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        // Debug: Verificar si hay permisos
        \Log::info('Permisos recuperables encontrados: ' . $permisosRecuperables->count());

        return view('admin.plan_recuperacion.plan_recuperacion', compact('listPlanes', 'permisosRecuperables'));
    }

    /**
     * Handle the insert action for a new plan de recuperación
     */
    public function actionInsert(Request $request)
    {
        try {
            // Validar los datos
            $validated = $request->validate([
                'id_permiso' => 'required|exists:permiso,id_permiso',
                'fecha_presentacion' => 'required|date',
                'total_horas_recuperar' => 'required|numeric|min:0',
                'estado_plan' => 'required|in:PRESENTADO,APROBADO,OBSERVADO',
                'observacion' => 'nullable|string|max:500'
            ]);

            // Verificar que el permiso no tenga ya un plan de recuperación
            $planExistente = PlanRecuperacion::where('id_permiso', $validated['id_permiso'])->first();
            if ($planExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este permiso ya tiene un plan de recuperación registrado'
                ], 422);
            }

            // Generar ID único para el plan (formato: PLN-YYYY-####)
            $year = date('Y');
            $lastPlan = PlanRecuperacion::where('id_plan', 'like', "PLN-{$year}-%")
                ->orderBy('id_plan', 'desc')
                ->first();

            if ($lastPlan) {
                $lastNumber = intval(substr($lastPlan->id_plan, -4));
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }

            $validated['id_plan'] = "PLN-{$year}-{$newNumber}";

            // Crear el plan de recuperación
            $plan = PlanRecuperacion::create([
                'id_plan' => $validated['id_plan'],
                'id_permiso' => $validated['id_permiso'],
                'fecha_presentacion' => $validated['fecha_presentacion'],
                'total_horas_recuperar' => $validated['total_horas_recuperar'],
                'estado_plan' => $validated['estado_plan'],
                'observacion' => $validated['observacion']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan de recuperación creado exitosamente',
                'plan' => $plan->load(['permiso.docente', 'permiso.tipoPermiso'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al crear plan de recuperación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el plan de recuperación: ' . $e->getMessage()
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
}
