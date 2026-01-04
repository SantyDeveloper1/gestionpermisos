<?php
namespace App\Http\Controllers\Admin\Permiso;
use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\TipoPermiso;
use App\Models\Permiso;

class PermisoController extends Controller
{
    public function actionPermiso()
    {
        // Obtener todos los docentes activos con su relación user
        $docentes = Docente::where('estado', 1)
            ->with('user')
            ->get()
            ->sortBy(function ($docente) {
                return $docente->user->last_name . ' ' . $docente->user->name;
            });

        // Obtener todos los tipos de permiso activos
        $tipoPermisos = TipoPermiso::where('estado', 1)
            ->orderBy('nombre', 'asc')
            ->get();

        // Obtener todos los permisos con sus relaciones
        $listPermisos = Permiso::with(['docente.user', 'tipoPermiso', 'planRecuperacion'])
            ->orderBy('fecha_solicitud', 'desc')
            ->get();

        return view('admin/permiso/permiso', compact('docentes', 'tipoPermisos', 'listPermisos'));
    }

    public function actionInsert()
    {
        try {
            // Validar los datos
            $validated = request()->validate([
                'id_docente' => 'required|exists:docentes,idDocente',
                'id_tipo_permiso' => 'required|exists:tipo_permiso,id_tipo_permiso',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'dias_permiso' => 'required|integer|min:1',
                'horas_afectadas' => 'required|numeric|min:0',
                'fecha_solicitud' => 'required|date',
                'motivo' => 'required|string|min:10',
                'observacion' => 'nullable|string'
            ], [
                'id_docente.required' => 'Debe seleccionar un docente.',
                'id_docente.exists' => 'El docente seleccionado no existe.',
                'id_tipo_permiso.required' => 'Debe seleccionar un tipo de permiso.',
                'id_tipo_permiso.exists' => 'El tipo de permiso seleccionado no existe.',
                'fecha_inicio.required' => 'La fecha de inicio es requerida.',
                'fecha_fin.required' => 'La fecha de fin es requerida.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
                'dias_permiso.required' => 'Los días de permiso son requeridos.',
                'dias_permiso.min' => 'Los días de permiso deben ser al menos 1.',
                'horas_afectadas.required' => 'Las horas afectadas son requeridas.',
                'horas_afectadas.min' => 'Las horas afectadas deben ser 0 o más.',
                'fecha_solicitud.required' => 'La fecha de solicitud es requerida.',
                'motivo.required' => 'El motivo es requerido.',
                'motivo.min' => 'El motivo debe tener al menos 10 caracteres.'
            ]);

            // Generar ID único para el permiso (formato: PER-YYYY-####)
            $year = date('Y');
            $lastPermiso = Permiso::where('id_permiso', 'like', "PER-{$year}-%")
                ->orderBy('id_permiso', 'desc')
                ->first();

            if ($lastPermiso) {
                $lastNumber = intval(substr($lastPermiso->id_permiso, -4));
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }

            $validated['id_permiso'] = "PER-{$year}-{$newNumber}";
            $validated['estado_permiso'] = 'SOLICITADO'; // Estado inicial

            // Crear el permiso
            $permiso = Permiso::create($validated);

            // Cargar las relaciones para la respuesta
            $permiso->load(['docente.user', 'tipoPermiso']);

            return response()->json([
                'success' => true,
                'message' => 'Permiso registrado correctamente.',
                'permiso' => [
                    'id_permiso' => $permiso->id_permiso,
                    'dias_permiso' => $permiso->dias_permiso,
                    'horas_afectadas' => $permiso->horas_afectadas,
                    'estado_permiso' => $permiso->estado_permiso,
                    'fecha_inicio' => $permiso->fecha_inicio,
                    'fecha_fin' => $permiso->fecha_fin,
                    'fecha_solicitud' => $permiso->fecha_solicitud,
                    'fecha_resolucion' => $permiso->fecha_resolucion,
                    'motivo' => $permiso->motivo,
                    'observacion' => $permiso->observacion,
                    'docente' => [
                        'idDocente' => $permiso->docente->idDocente,
                        'nombres' => $permiso->docente->user->name,
                        'appDocente' => $permiso->docente->user->last_name,
                        'apmDocente' => ''
                    ],
                    'tipo_permiso' => [
                        'id_tipo_permiso' => $permiso->tipoPermiso->id_tipo_permiso,
                        'nombre' => $permiso->tipoPermiso->nombre,
                        'requiere_recupero' => $permiso->tipoPermiso->requiere_recupero
                    ]
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
                'message' => 'Error al registrar el permiso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function actionShow($id)
    {
        try {
            // Buscar el permiso con sus relaciones
            $permiso = Permiso::with(['docente.user', 'tipoPermiso', 'planRecuperacion'])
                ->where('id_permiso', $id)
                ->first();

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado.'
                ], 404);
            }

            // Preparar datos del plan de recuperación
            $planData = null;
            if ($permiso->planRecuperacion) {
                $planData = [
                    'id_plan' => $permiso->planRecuperacion->id_plan,
                    'estado_plan' => $permiso->planRecuperacion->estado_plan,
                    'fecha_presentacion' => $permiso->planRecuperacion->fecha_presentacion,
                    'total_horas_recuperar' => $permiso->planRecuperacion->total_horas_recuperar,
                    'observacion' => $permiso->planRecuperacion->observacion
                ];
            }

            return response()->json([
                'success' => true,
                'permiso' => [
                    'id_permiso' => $permiso->id_permiso,
                    'dias_permiso' => $permiso->dias_permiso,
                    'horas_afectadas' => $permiso->horas_afectadas,
                    'estado_permiso' => $permiso->estado_permiso,
                    'fecha_inicio' => $permiso->fecha_inicio,
                    'fecha_fin' => $permiso->fecha_fin,
                    'fecha_solicitud' => $permiso->fecha_solicitud,
                    'fecha_resolucion' => $permiso->fecha_resolucion,
                    'motivo' => $permiso->motivo,
                    'observacion' => $permiso->observacion,
                    'docente' => [
                        'idDocente' => $permiso->docente->idDocente,
                        'nombres' => $permiso->docente->user->name,
                        'appDocente' => $permiso->docente->user->last_name,
                        'apmDocente' => '',
                        'numero_documento' => $permiso->docente->user->document_number ?? null
                    ],
                    'tipoPermiso' => [
                        'id_tipo_permiso' => $permiso->tipoPermiso->id_tipo_permiso,
                        'nombre' => $permiso->tipoPermiso->nombre,
                        'descripcion' => $permiso->tipoPermiso->descripcion ?? null,
                        'con_goce_haber' => $permiso->tipoPermiso->con_goce_haber,
                        'requiere_recupero' => $permiso->tipoPermiso->requiere_recupero
                    ],
                    'planRecuperacion' => $planData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles del permiso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function actionUpdate($id)
    {
        try {
            // Buscar el permiso
            $permiso = Permiso::where('id_permiso', $id)->first();

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado.'
                ], 404);
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
?>