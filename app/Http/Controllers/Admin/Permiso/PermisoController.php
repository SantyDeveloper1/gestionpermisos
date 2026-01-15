<?php
namespace App\Http\Controllers\Admin\Permiso;
use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\TipoPermiso;
use App\Models\Permiso;
use App\Models\SemestreAcademico;
use Illuminate\Support\Facades\Mail;
use App\Mail\CambioEstadoPermisoMail;

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

        // Obtener el semestre académico actual
        $semestreActual = SemestreAcademico::where('EsActualAcademico', 1)->first();

        return view('admin/permiso/permiso', compact('docentes', 'tipoPermisos', 'listPermisos', 'semestreActual'));
    }

    public function actionInsert()
    {
        try {
            // Validar los datos
            $validated = request()->validate([
                'id_docente' => 'required|exists:docentes,idDocente',
                'id_tipo_permiso' => 'required|exists:tipo_permiso,id_tipo_permiso',
                'id_semestre_academico' => 'required|exists:semestre_academico,IdSemestreAcademico',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'dias_permiso' => 'required|integer|min:1',
                'horas_afectadas' => 'required|numeric|min:0',
                'fecha_solicitud' => 'required|date',
                'motivo' => 'required|string|min:10',
                'documento_sustento' => 'required|file|mimes:pdf,doc,docx|max:5120' // 5MB máximo
            ], [
                'id_docente.required' => 'Debe seleccionar un docente.',
                'id_docente.exists' => 'El docente seleccionado no existe.',
                'id_tipo_permiso.required' => 'Debe seleccionar un tipo de permiso.',
                'id_tipo_permiso.exists' => 'El tipo de permiso seleccionado no existe.',
                'id_semestre_academico.required' => 'El semestre académico es requerido.',
                'id_semestre_academico.exists' => 'El semestre académico seleccionado no existe.',
                'fecha_inicio.required' => 'La fecha de inicio es requerida.',
                'fecha_fin.required' => 'La fecha de fin es requerida.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
                'dias_permiso.required' => 'Los días de permiso son requeridos.',
                'dias_permiso.min' => 'Los días de permiso deben ser al menos 1.',
                'horas_afectadas.required' => 'Las horas afectadas son requeridas.',
                'horas_afectadas.min' => 'Las horas afectadas deben ser 0 o más.',
                'fecha_solicitud.required' => 'La fecha de solicitud es requerida.',
                'motivo.required' => 'El motivo es requerido.',
                'motivo.min' => 'El motivo debe tener al menos 10 caracteres.',
                'documento_sustento.required' => 'El documento de sustento es requerido.',
                'documento_sustento.file' => 'Debe subir un archivo válido.',
                'documento_sustento.mimes' => 'El documento debe ser PDF, DOC o DOCX.',
                'documento_sustento.max' => 'El documento no debe superar los 5MB.'
            ]);

            // Manejar la subida del archivo
            $documentoPath = null;
            if (request()->hasFile('documento_sustento')) {
                $file = request()->file('documento_sustento');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Crear directorio si no existe
                $destinationPath = public_path('storage/permisos/documentos');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // Mover el archivo
                $file->move($destinationPath, $fileName);

                // Guardar la ruta relativa para la base de datos
                $documentoPath = 'storage/permisos/documentos/' . $fileName;
            }

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
            $validated['documento_sustento'] = $documentoPath; // Guardar la ruta del archivo

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
                    'documento_sustento' => $permiso->documento_sustento,
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
                    ],
                    'plan_recuperacion' => null // Nuevos permisos no tienen plan aún
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
                    'created_at' => $permiso->created_at ? $permiso->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $permiso->updated_at ? $permiso->updated_at->format('Y-m-d H:i:s') : null,
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
            // Buscar el permiso con su plan de recuperación
            $permiso = Permiso::with('planRecuperacion')->where('id_permiso', $id)->first();

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado.'
                ], 404);
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
                    'observacion' => $permiso->observacion,
                    'created_at' => $permiso->created_at ? $permiso->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $permiso->updated_at ? $permiso->updated_at->format('Y-m-d H:i:s') : null
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

    /**
     * Enviar correo electrónico de notificación de cambio de estado
     */
    public function actionEnviarEmail($id)
    {
        try {
            // Buscar el permiso con todas sus relaciones necesarias
            $permiso = Permiso::with(['docente.user', 'tipoPermiso', 'semestreAcademico'])
                ->where('id_permiso', $id)
                ->first();

            if (!$permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permiso no encontrado.'
                ], 404);
            }

            // Verificar que el docente tenga un email
            $emailDocente = $permiso->docente->user->email ?? null;

            if (!$emailDocente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El docente no tiene un correo electrónico registrado.'
                ], 422);
            }

            // Validar si el estado actual ya fue notificado
            if ($permiso->estado_notificado === $permiso->estado_permiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'El estado "' . $permiso->estado_permiso . '" ya fue notificado anteriormente al docente.'
                ], 422);
            }

            // Preparar los datos para el correo
            $nombreCompleto = $permiso->docente->user->last_name . ', ' . $permiso->docente->user->name;

            $fechaInicio = $permiso->fecha_inicio ? $permiso->fecha_inicio->format('d/m/Y') : '';
            $fechaFin = $permiso->fecha_fin ? $permiso->fecha_fin->format('d/m/Y') : '';
            $fechaPermiso = $fechaInicio . ' al ' . $fechaFin;

            $periodo = $permiso->semestreAcademico
                ? $permiso->semestreAcademico->codigo_Academico . ' - ' . $permiso->semestreAcademico->anio_academico
                : 'No especificado';

            $emailData = [
                'docente' => $nombreCompleto,
                'estado' => $permiso->estado_permiso,
                'tipoPermiso' => $permiso->tipoPermiso->nombre ?? 'No especificado',
                'fechaSolicitud' => $permiso->fecha_solicitud ? $permiso->fecha_solicitud->format('d/m/Y') : now()->format('d/m/Y'),
                'fechaPermiso' => $fechaPermiso,
                'periodo' => $periodo,
                'motivo' => $permiso->motivo ?? 'No especificado',
                'comentario' => $permiso->observacion ?? null,
                'validador' => 'Departamento Académico',
                'urlSistema' => url('/docente/permiso')
            ];

            // Enviar el correo
            Mail::to($emailDocente)->send(new CambioEstadoPermisoMail($emailData));

            // Actualizar el campo estado_notificado con el estado actual
            $permiso->update([
                'estado_notificado' => $permiso->estado_permiso
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo enviado exitosamente a ' . $emailDocente,
                'estado_notificado' => $permiso->estado_permiso
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>