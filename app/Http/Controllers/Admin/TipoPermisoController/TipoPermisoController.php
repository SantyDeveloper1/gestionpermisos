<?php

namespace App\Http\Controllers\Admin\TipoPermisoController;

use App\Http\Controllers\Controller;
use App\Models\TipoPermiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoPermisoController extends Controller
{
    /**
     * Mostrar lista de tipos de permiso
     */
    public function actionTipoPermiso()
    {
        $listTipoPermisos = TipoPermiso::orderBy('created_at', 'DESC')->get();
        return view('admin.tipo_permiso.tipo_permiso', compact('listTipoPermisos'));
    }

    /**
     * Insertar nuevo tipo de permiso
     */
    public function actionInsert(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100|unique:tipo_permiso,nombre',
                'descripcion' => 'nullable|string',
                'requiere_recupero' => 'required|boolean',
                'con_goce_haber' => 'required|boolean',
                'requiere_documento' => 'nullable|boolean',
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'nombre.unique' => 'Este tipo de permiso ya existe.',
                'requiere_recupero.required' => 'Debe indicar si requiere recupero.',
                'con_goce_haber.required' => 'Debe indicar si es con goce de haber.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                $tipoPermiso = new TipoPermiso();
                $tipoPermiso->id_tipo_permiso = uniqid();
                $tipoPermiso->nombre = $request->nombre;
                $tipoPermiso->descripcion = $request->descripcion;
                $tipoPermiso->requiere_recupero = $request->requiere_recupero ?? false;
                $tipoPermiso->con_goce_haber = $request->con_goce_haber ?? false;
                $tipoPermiso->requiere_documento = $request->requiere_documento ?? false;
                $tipoPermiso->estado = 1;
                $tipoPermiso->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de permiso registrado correctamente.',
                    'data' => [
                        'id' => $tipoPermiso->id_tipo_permiso,
                        'nombre' => $tipoPermiso->nombre,
                        'descripcion' => $tipoPermiso->descripcion,
                        'requiere_recupero' => $tipoPermiso->requiere_recupero,
                        'con_goce_haber' => $tipoPermiso->con_goce_haber,
                        'requiere_documento' => $tipoPermiso->requiere_documento,
                        'fecha' => $tipoPermiso->created_at->format('d/m/Y')
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar: ' . $e->getMessage()
                ], 500);
            }
        }

        // Si es GET, mostrar vista (aunque no la usaremos)
        return redirect('admin/tipo_permiso');
    }

    /**
     * Actualizar tipo de permiso
     */
    public function actionUpdate(Request $request, $id_tipo_permiso)
    {
        try {
            $tipoPermiso = TipoPermiso::findOrFail($id_tipo_permiso);

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100|unique:tipo_permiso,nombre,' . $id_tipo_permiso . ',id_tipo_permiso',
                'descripcion' => 'nullable|string',
                'requiere_recupero' => 'nullable|boolean',
                'con_goce_haber' => 'nullable|boolean',
                'requiere_documento' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tipoPermiso->nombre = $request->nombre;
            if ($request->has('descripcion')) {
                $tipoPermiso->descripcion = $request->descripcion;
            }
            if ($request->has('requiere_recupero')) {
                $tipoPermiso->requiere_recupero = $request->requiere_recupero;
            }
            if ($request->has('con_goce_haber')) {
                $tipoPermiso->con_goce_haber = $request->con_goce_haber;
            }
            if ($request->has('requiere_documento')) {
                $tipoPermiso->requiere_documento = $request->requiere_documento;
            }

            $tipoPermiso->save();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de permiso actualizado correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar tipo de permiso
     */
    public function actionDelete($id_tipo_permiso)
    {
        try {
            $tipoPermiso = TipoPermiso::findOrFail($id_tipo_permiso);
            $tipoPermiso->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de permiso eliminado correctamente.'
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar este tipo de permiso porque tiene registros asociados.'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de permiso.'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}