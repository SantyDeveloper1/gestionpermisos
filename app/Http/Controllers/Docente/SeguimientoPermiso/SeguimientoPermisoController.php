<?php

namespace App\Http\Controllers\Docente\SeguimientoPermiso;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;

class SeguimientoPermisoController extends Controller
{
    public function actionSeguimientoPermiso()
    {
        $docente = auth()->user()->docente;

        // Debug temporal
        if (!$docente) {
            dd([
                'error' => 'El usuario no tiene un docente asociado',
                'user_id' => auth()->user()->id,
                'user_email' => auth()->user()->email
            ]);
        }

        // Obtener todos los permisos del docente
        $permisos = Permiso::where('id_docente', $docente->idDocente)
            ->with(['tipoPermiso', 'planRecuperacion', 'semestreAcademico'])
            ->orderBy('fecha_solicitud', 'desc')
            ->get();

        // Debug temporal - ver qué se está obteniendo
        \Log::info('Seguimiento Permiso Debug', [
            'id_docente' => $docente->idDocente,
            'total_permisos' => $permisos->count(),
            'permisos_ids' => $permisos->pluck('id_permiso')->toArray()
        ]);

        // Obtener el permiso más reciente para el timeline
        $permiso = $permisos->first();

        return view('docente.seguimiento_permiso.seguimiento_permiso', compact('permisos', 'permiso'));
    }

    public function actionGetPermiso($id)
    {
        try {
            // Obtener el docente autenticado
            $docente = auth()->user()->docente;

            if (!$docente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para realizar esta acción.'
                ], 403);
            }

            // Buscar el permiso con sus relaciones
            $permiso = Permiso::with(['tipoPermiso', 'planRecuperacion', 'semestreAcademico'])
                ->where('id_permiso', $id)
                ->first();

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado.'
                ], 404);
            }

            // SEGURIDAD: Verificar que el permiso pertenece al docente autenticado
            if ($permiso->id_docente !== $docente->idDocente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para ver este permiso.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'permiso' => [
                    'id_permiso' => $permiso->id_permiso,
                    'tipo_permiso' => $permiso->tipoPermiso->nombre ?? 'N/A',
                    'fecha_inicio' => $permiso->fecha_inicio ? $permiso->fecha_inicio->format('d/m/Y') : 'N/A',
                    'fecha_fin' => $permiso->fecha_fin ? $permiso->fecha_fin->format('d/m/Y') : 'N/A',
                    'dias_permiso' => $permiso->dias_permiso,
                    'estado_permiso' => $permiso->estado_permiso,
                    'updated_at' => $permiso->updated_at ? $permiso->updated_at->format('d/m/Y H:i') : 'N/A'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el permiso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function actionUpdate($id)
    {
        try {
            // Obtener el docente autenticado
            $docente = auth()->user()->docente;

            if (!$docente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para realizar esta acción.'
                ], 403);
            }

            // Buscar el permiso con su plan de recuperación
            $permiso = Permiso::with('planRecuperacion')->where('id_permiso', $id)->first();

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado.'
                ], 404);
            }

            // SEGURIDAD: Verificar que el permiso pertenece al docente autenticado
            if ($permiso->id_docente !== $docente->idDocente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para modificar este permiso.'
                ], 403);
            }

            // Validación: Si el permiso tiene un plan de recuperación, no se puede cambiar el estado a SOLICITADO
            $nuevoEstado = request()->input('estado_permiso');
            if ($permiso->planRecuperacion && $nuevoEstado === 'SOLICITADO') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este permiso tiene un plan de recuperación asociado. No se puede cambiar el estado a SOLICITADO.'
                ], 422);
            }

            // Validar los datos
            $validated = request()->validate([
                'estado_permiso' => 'required|in:SOLICITADO,APROBADO,RECHAZADO,EN_RECUPERACION,RECUPERADO,CERRADO',
                'fecha_resolucion' => 'nullable|date',
                'observacion' => 'nullable|string'
            ], [
                'estado_permiso.required' => 'El estado es requerido.',
                'estado_permiso.in' => 'El estado seleccionado no es válido.',
                'fecha_resolucion.date' => 'La fecha de resolución no es válida.'
            ]);

            // Actualizar el permiso
            $permiso->update($validated);

            // Cargar las relaciones para la respuesta
            $permiso->load(['docente', 'tipoPermiso']);

            return response()->json([
                'success' => true,
                'message' => 'Permiso actualizado correctamente.',
                'permiso' => [
                    'id_permiso' => $permiso->id_permiso,
                    'estado_permiso' => $permiso->estado_permiso,
                    'fecha_solicitud' => $permiso->fecha_solicitud,
                    'fecha_resolucion' => $permiso->fecha_resolucion,
                    'observacion' => $permiso->observacion
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el permiso: ' . $e->getMessage()
            ], 500);
        }
    }
}
