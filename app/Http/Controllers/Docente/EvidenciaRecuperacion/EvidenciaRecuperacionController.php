<?php

namespace App\Http\Controllers\Docente\EvidenciaRecuperacion;

use App\Http\Controllers\Controller;
use App\Models\EvidenciaRecuperacion;
use App\Models\SesionRecuperacion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EvidenciaRecuperacionController extends Controller
{
    public function actionEvidenciaRecuperacion(Request $request)
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener el docente asociado al usuario autenticado
        $docente = \App\Models\Docente::where('user_id', $user->id)->first();

        if (!$docente) {
            abort(403, 'No se encontró un perfil de docente asociado a este usuario.');
        }

        // Obtener el filtro de sesión si existe
        $sesionIdFilter = $request->get('sesion_id');

        // Obtener evidencias SOLO de las sesiones del docente autenticado
        $evidenciasQuery = EvidenciaRecuperacion::with(['sesionRecuperacion.planRecuperacion.permiso.docente.user'])
            ->whereHas('sesionRecuperacion.planRecuperacion.permiso', function ($query) use ($docente) {
                $query->where('id_docente', $docente->idDocente);
            });

        if ($sesionIdFilter) {
            $evidenciasQuery->where('id_sesion', $sesionIdFilter);
        }

        $evidencias = $evidenciasQuery->paginate(10);

        // Obtener SOLO las sesiones del docente autenticado para el selector
        $sesiones = SesionRecuperacion::with(['planRecuperacion.permiso.docente.user'])
            ->whereHas('planRecuperacion.permiso', function ($query) use ($docente) {
                $query->where('id_docente', $docente->idDocente);
            })
            ->get();

        // Calcular estadísticas por tipo SOLO del docente autenticado
        $statsQuery = EvidenciaRecuperacion::query()
            ->whereHas('sesionRecuperacion.planRecuperacion.permiso', function ($query) use ($docente) {
                $query->where('id_docente', $docente->idDocente);
            });

        if ($sesionIdFilter) {
            $statsQuery->where('id_sesion', $sesionIdFilter);
        }

        $totalActas = (clone $statsQuery)->where('tipo_evidencia', 'ACTA')->count();
        $totalAsistencias = (clone $statsQuery)->where('tipo_evidencia', 'ASISTENCIA')->count();
        $totalCapturas = (clone $statsQuery)->where('tipo_evidencia', 'CAPTURA')->count();
        $totalOtros = (clone $statsQuery)->where('tipo_evidencia', 'OTRO')->count();

        // Obtener sesión seleccionada si existe el filtro
        $sesionSeleccionada = null;
        if ($sesionIdFilter) {
            $sesionSeleccionada = SesionRecuperacion::with(['planRecuperacion.permiso.docente.user'])
                ->whereHas('planRecuperacion.permiso', function ($query) use ($docente) {
                    $query->where('id_docente', $docente->idDocente);
                })
                ->find($sesionIdFilter);
        }

        return view('docente/evidencia/evidencia_recuperacion', compact(
            'evidencias',
            'sesiones',
            'totalActas',
            'totalAsistencias',
            'totalCapturas',
            'totalOtros',
            'sesionSeleccionada'
        ));
    }

    /**
     * Handle the insert action for new evidence
     */
    public function actionInsert(Request $request)
    {
        try {
            // Validar los datos
            $validated = $request->validate([
                'id_sesion' => 'required|exists:sesion_recuperacion,id_sesion',
                'tipo_evidencia' => 'required|in:ACTA,ASISTENCIA,CAPTURA,OTRO',
                'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240', // 10MB max
                'descripcion' => 'nullable|string|max:255'
            ]);

            // Generar ID único para la evidencia (EVI-YYYY-XXXX)
            $year = date('Y');
            $lastEvidencia = EvidenciaRecuperacion::where('id_evidencia', 'like', "EVI-{$year}-%")
                ->orderBy('id_evidencia', 'desc')
                ->first();

            if ($lastEvidencia) {
                $lastNumber = intval(substr($lastEvidencia->id_evidencia, -4));
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }

            $id_evidencia = "EVI-{$year}-{$newNumber}";

            // Manejar el archivo subido
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $fileName = $id_evidencia . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Guardar directamente en public/evidencias
                $destinationPath = public_path('storage/evidencias');

                // Crear la carpeta si no existe
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $fileName);
                $filePath = 'storage/evidencias/' . $fileName;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se recibió ningún archivo'
                ], 400);
            }

            // Crear la evidencia usando DB::table para manejar clave primaria compuesta
            \DB::table('evidencia_recuperacion')->insert([
                'id_evidencia' => $id_evidencia,
                'id_sesion' => $validated['id_sesion'],
                'tipo_evidencia' => $validated['tipo_evidencia'],
                'archivo' => $filePath,
                'descripcion' => $validated['descripcion'] ?? null,
                'fecha_subida' => \Carbon\Carbon::now('America/Lima')
            ]);

            // Obtener la evidencia recién creada
            $evidencia = \DB::table('evidencia_recuperacion')
                ->where('id_evidencia', $id_evidencia)
                ->where('id_sesion', $validated['id_sesion'])
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Evidencia registrada exitosamente',
                'evidencia' => $evidencia
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al crear evidencia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la evidencia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download evidence file
     */
    public function actionDownload($id_evidencia)
    {
        try {
            $evidencia = \DB::table('evidencia_recuperacion')
                ->where('id_evidencia', $id_evidencia)
                ->first();

            if (!$evidencia) {
                abort(404, 'Evidencia no encontrada');
            }

            $filePath = public_path($evidencia->archivo);

            if (!file_exists($filePath)) {
                abort(404, 'Archivo no encontrado');
            }

            return response()->download($filePath, basename($evidencia->archivo));

        } catch (\Exception $e) {
            \Log::error('Error al descargar evidencia: ' . $e->getMessage());
            abort(500, 'Error al descargar el archivo');
        }
    }

    /**
     * Show details of a specific evidence
     */
    public function actionShow($id_evidencia)
    {
        try {
            $evidencia = EvidenciaRecuperacion::with(['sesionRecuperacion.planRecuperacion.permiso.docente'])
                ->findOrFail($id_evidencia);

            return response()->json([
                'success' => true,
                'evidencia' => $evidencia
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Evidencia no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error al obtener evidencia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la evidencia'
            ], 500);
        }
    }

    /**
     * Display the evidence preview page as PDF
     */
    public function actionVerEvidencia($id_evidencia)
    {
        try {
            $evidencia = \DB::table('evidencia_recuperacion')
                ->where('id_evidencia', $id_evidencia)
                ->first();

            if (!$evidencia) {
                abort(404, 'Evidencia no encontrada');
            }

            // Cargar las relaciones manualmente
            $evidencia = EvidenciaRecuperacion::with([
                'sesionRecuperacion.planRecuperacion.permiso.docente.user'
            ])->where('id_evidencia', $id_evidencia)->first();

            // Generar PDF usando DomPDF
            $pdf = Pdf::loadView('admin/evidencia/ver/ver_evidencia', compact('evidencia'));

            // Configurar el PDF
            $pdf->setPaper('letter', 'portrait');

            // Retornar el PDF para visualización en el navegador
            return $pdf->stream('evidencia_' . $id_evidencia . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al mostrar evidencia: ' . $e->getMessage());
            abort(500, 'Error al cargar la evidencia');
        }
    }

    /**
     * Handle the update action for existing evidence
     */
    public function actionUpdate(Request $request, $id_evidencia)
    {
        try {
            $evidencia = EvidenciaRecuperacion::findOrFail($id_evidencia);

            $validated = $request->validate([
                'tipo_evidencia' => 'required|in:ACTA,ASISTENCIA,CAPTURA,OTRO',
                'descripcion' => 'nullable|string|max:255',
                'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240'
            ]);

            // Si hay un nuevo archivo, eliminar el anterior y subir el nuevo
            if ($request->hasFile('archivo')) {
                // Eliminar archivo anterior
                if ($evidencia->archivo && \Storage::disk('public')->exists($evidencia->archivo)) {
                    \Storage::disk('public')->delete($evidencia->archivo);
                }

                // Subir nuevo archivo
                $file = $request->file('archivo');
                $fileName = $evidencia->id_evidencia . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('evidencias', $fileName, 'public');
                $validated['archivo'] = $filePath;
            }

            $evidencia->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Evidencia actualizada exitosamente',
                'evidencia' => $evidencia->load(['sesionRecuperacion.planRecuperacion.permiso.docente'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Evidencia no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar evidencia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la evidencia'
            ], 500);
        }
    }

    /**
     * Handle the delete action for evidence
     */
    public function actionDelete($id_evidencia)
    {
        try {
            // Buscar la evidencia usando DB::table debido a la clave primaria compuesta
            $evidencia = \DB::table('evidencia_recuperacion')
                ->where('id_evidencia', $id_evidencia)
                ->first();

            if (!$evidencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Evidencia no encontrada'
                ], 404);
            }

            // Eliminar el archivo físico
            $filePath = public_path($evidencia->archivo);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Eliminar el registro de la base de datos
            \DB::table('evidencia_recuperacion')
                ->where('id_evidencia', $id_evidencia)
                ->where('id_sesion', $evidencia->id_sesion)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Evidencia eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar evidencia: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la evidencia: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>