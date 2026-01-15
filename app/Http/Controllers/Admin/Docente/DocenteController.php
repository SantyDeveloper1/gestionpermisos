<?php

namespace App\Http\Controllers\Admin\Docente;
use App\Http\Controllers\Controller;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class DocenteController extends Controller
{
    public function actionDocente()
    {
        $listDocentes = Docente::with([
            'user:id,document_number,name,last_name,email,phone,status',
            'grado',
            'contrato'
        ])
            ->orderBy('created_at', 'DESC')
            ->get();

        $listGrados = \App\Models\GradoAcademico::all();
        $listContratos = \App\Models\TipoContrato::all();

        // 🔹 IDs de usuarios que ya son docentes
        $usuariosDocentes = Docente::pluck('user_id')->toArray();

        // 🔹 Usuarios con rol DOCENTE, activos y NO registrados aún
        $listUsuarios = User::where('status', 'active')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'DOCENTE');
            })
            ->whereNotIn('id', $usuariosDocentes)
            ->select('id', 'document_number', 'name', 'last_name')
            ->get();

        return view(
            'admin.docente.docente',
            compact(
                'listDocentes',
                'listGrados',
                'listContratos',
                'listUsuarios'
            )
        );
    }


    public function actionInsert(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'user_id' => 'required|exists:users,id|unique:docentes,user_id',
                    'codigo_unamba' => 'nullable|string|max:15|unique:docentes,codigo_unamba',
                    'grado_id' => 'required|exists:grados_academicos,idGrados_academicos',
                    'tipo_contrato_id' => 'required|exists:tipos_contrato,idTipo_contrato',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $nuevoDocente = Docente::create([
                'idDocente' => uniqid(),
                'user_id' => $request->user_id,
                'codigo_unamba' => $request->codigo_unamba,
                'grado_id' => $request->grado_id,
                'tipo_contrato_id' => $request->tipo_contrato_id,
                'estado' => 1,
            ]);

            // Cargar las relaciones
            $nuevoDocente->load([
                'user:id,document_number,name,last_name,email,phone,status',
                'grado',
                'contrato'
            ]);

            // Preparar datos para la respuesta
            $item = [
                'idDocente' => $nuevoDocente->idDocente,
                'dni' => $nuevoDocente->user->document_number ?? 'N/A',
                'nombre' => $nuevoDocente->user ? $nuevoDocente->user->name . ' ' . $nuevoDocente->user->last_name : 'Sin usuario',
                'correo' => $nuevoDocente->user->email ?? 'Sin usuario',
                'telefono' => $nuevoDocente->user->phone ?? 'Sin usuario',
                'grado' => $nuevoDocente->grado->nombre ?? 'N/A',
                'condicion' => $nuevoDocente->contrato->nombre ?? 'N/A',
                'status' => $nuevoDocente->user->status ?? 'inactive',
                'fecha' => $nuevoDocente->created_at ? $nuevoDocente->created_at->format('d/m/Y') : '—'
            ];

            return response()->json([
                'status' => true,
                'message' => 'Docente registrado correctamente',
                'item' => $item
            ]);
        }
    }

    /**
     * Obtener datos de un docente específico
     */
    public function actionShow($idDocente)
    {
        try {
            $docente = Docente::with([
                'user:id,document_number,name,last_name,email,phone,status',
                'grado',
                'contrato'
            ])->where('idDocente', $idDocente)->first();

            if (!$docente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Docente no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'docente' => $docente
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener docente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos del docente'
            ], 500);
        }
    }

    /**
     * Actualizar información del docente
     */
    public function actionUpdate(Request $request, $idDocente)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'dni' => 'required|string|max:20',
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'correo' => 'required|email|max:255',
                'telefono' => 'nullable|string|max:20',
                'grado_id' => 'required|exists:grados_academicos,idGrados_academicos',
                'tipo_contrato_id' => 'required|exists:tipos_contrato,idTipo_contrato'
            ],
            [
                'dni.required' => 'El DNI es obligatorio.',
                'nombre.required' => 'El nombre del docente es obligatorio.',
                'apellido.required' => 'El apellido del docente es obligatorio.',
                'correo.required' => 'El correo electrónico es obligatorio.',
                'correo.email' => 'El formato del correo electrónico no es válido.',
                'grado_id.required' => 'El grado académico es obligatorio.',
                'tipo_contrato_id.required' => 'El tipo de contrato es obligatorio.'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $docente = Docente::where('idDocente', $idDocente)->first();

            if (!$docente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Docente no encontrado en el sistema.'
                ], 404);
            }

            // Obtener el usuario asociado
            $user = User::find($docente->user_id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario asociado no encontrado.'
                ], 404);
            }

            // Validar que el correo no esté en uso por otro usuario
            $emailExists = User::where('email', $request->correo)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($emailExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El correo electrónico ya está registrado en el sistema por otro usuario.'
                ], 422);
            }

            // Validar que el DNI no esté en uso por otro usuario
            $dniExists = User::where('document_number', $request->dni)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($dniExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El DNI ya está registrado en el sistema por otro usuario.'
                ], 422);
            }

            // Actualizar datos del usuario
            $user->document_number = $request->dni;
            $user->name = $request->nombre;
            $user->last_name = $request->apellido;
            $user->email = $request->correo;
            $user->phone = $request->telefono;
            $user->save();

            // Actualizar datos del docente
            $docente->grado_id = $request->grado_id;
            $docente->tipo_contrato_id = $request->tipo_contrato_id;
            $docente->save();

            // Recargar las relaciones para obtener los nombres actualizados
            $docente->load(['grado', 'contrato']);

            return response()->json([
                'success' => true,
                'message' => 'Los datos del docente han sido actualizados exitosamente.',
                'docente' => [
                    'dni' => $user->document_number,
                    'nombre' => $user->name,
                    'apellido' => $user->last_name,
                    'correo' => $user->email,
                    'telefono' => $user->phone,
                    'grado' => $docente->grado->nombre ?? 'N/A',
                    'condicion' => $docente->contrato->nombre ?? 'N/A'
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al actualizar docente: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ha ocurrido un error al procesar la actualización. Por favor, intente nuevamente.'
            ], 500);
        }
    }

    /**
     * Actualizar estado del docente (activo/inactivo)
     */
    public function actionEstado(Request $request, $idDocente)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'estado' => 'required|in:0,1'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $docente = Docente::where('idDocente', $idDocente)->first();

            if (!$docente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Docente no encontrado'
                ], 404);
            }

            // Actualizar el estado del usuario asociado
            $user = User::find($docente->user_id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario asociado no encontrado'
                ], 404);
            }

            // Convertir 1/0 a 'active'/'inactive'
            $nuevoEstado = $request->estado == 1 ? 'active' : 'inactive';
            $user->status = $nuevoEstado;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'estado' => $nuevoEstado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar docente
     */
    public function actionDelete($idDocente)
    {
        try {
            $docente = Docente::where('idDocente', $idDocente)->first();

            if (!$docente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Docente no encontrado en el sistema.'
                ], 404);
            }

            // Verificar si el docente tiene cargas lectivas asignadas
            $tieneCargasAsignadas = \DB::table('carga_lectiva')
                ->where('IdDocente', $idDocente)
                ->exists();

            if ($tieneCargasAsignadas) {
                return response()->json([
                    'success' => false,
                    'message' => 'No es posible eliminar al docente debido a que tiene asignaciones de carga lectiva vigentes. Por favor, elimine o reasigne las cargas académicas antes de proceder.'
                ], 422);
            }

            // Verificar si tiene horarios asignados (a través de carga_lectiva -> grupo_asignaturas -> horarios)
            $tieneHorarios = \DB::table('horarios')
                ->join('carga_lectiva', 'horarios.IdGrupoAsignatura', '=', 'carga_lectiva.IdGrupoAsignatura')
                ->where('carga_lectiva.IdDocente', $idDocente)
                ->exists();

            if ($tieneHorarios) {
                return response()->json([
                    'success' => false,
                    'message' => 'No es posible eliminar al docente debido a que cuenta con horarios académicos asignados. Por favor, elimine los horarios correspondientes antes de proceder.'
                ], 422);
            }

            // Eliminar el docente
            $docente->delete();

            return response()->json([
                'success' => true,
                'message' => 'El docente ha sido eliminado exitosamente del sistema.'
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar errores de integridad referencial (foreign key constraints)
            if ($e->getCode() == '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'No es posible eliminar al docente debido a que mantiene vínculos con registros académicos en el sistema. Por favor, verifique y elimine primero las asignaciones relacionadas (cargas lectivas, horarios, evaluaciones, etc.).'
                ], 422);
            }

            // Otros errores de base de datos
            return response()->json([
                'success' => false,
                'message' => 'Ha ocurrido un error en la base de datos. Por favor, contacte al administrador del sistema.'
            ], 500);

        } catch (\Exception $e) {
            // Errores generales
            \Log::error('Error al eliminar docente: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ha ocurrido un error inesperado al procesar la solicitud. Por favor, intente nuevamente o contacte al soporte técnico.'
            ], 500);
        }
    }

}
