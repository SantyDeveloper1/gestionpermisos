<?php

namespace App\Http\Controllers\Admin\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaDocente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class CategoriaDocenteController extends Controller
{
    public function actionInsert(Request $request)
    {
        if ($request->isMethod('post')) {

            // =======================
            // VALIDACIÓN
            // =======================
            $validator = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required|string|max:100|unique:categorias_docente,nombre',
                ],
                [
                    'nombre.required' => 'Debe ingresar el nombre de la categoría docente.',
                    'nombre.unique'   => 'Esta categoría docente ya existe.',
                ]
            );

            if ($validator->fails()) {

                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()->all()
                    ], 422);
                }

                session()->flash('listMessage', $validator->errors()->all());
                session()->flash('typeMessage', 'error');

                return redirect('admin/docente/categoria_docente/insert');
            }

            // =======================
            // REGISTRO
            // =======================
            $cat = new CategoriaDocente();
            $cat->idCategori_docente = uniqid(); // PK CHAR(13)
            $cat->nombre = $request->nombre;
            $cat->save();

            // =======================
            // RESPUESTA AJAX
            // =======================
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id'     => $cat->idCategori_docente,
                        'nombre' => $cat->nombre,
                        'fecha'  => $cat->created_at->format('d/m/Y H:i'),
                        'numero' => CategoriaDocente::count(),
                    ]
                ]);
            }

            session()->flash('listMessage', ['Categoría docente registrada correctamente.']);
            session()->flash('typeMessage', 'success');

            return redirect('admin/docente/categorias/insert');
        }

        // =======================
        // LISTAR
        // =======================
        $listCategorias = CategoriaDocente::orderBy('created_at', 'DESC')->get();

        return view('admin.docente.categoria_docente.insert', compact('listCategorias'));
    }

    // =====================================================
    // MOSTRAR DATOS PARA MODAL (EDITAR)
    // =====================================================
    public function actionGetData($id)
    {
        $cat = CategoriaDocente::find($id);

        if (!$cat) {
            return response()->json(['status' => 'error', 'message' => 'No encontrado'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $cat
        ]);
    }

    // =====================================================
    // ACTUALIZAR CATEGORÍA DOCENTE
    // =====================================================
    public function actionUpdate(Request $request, $idCategori_docente)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nombre' => 'required|max:100|unique:categorias_docente,nombre,' . $idCategori_docente . ',idCategori_docente',
            ],
            [
                'nombre.required' => 'Debe ingresar el nombre de la categoría docente.',
                'nombre.unique'   => 'Este nombre ya está registrado.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $cat = CategoriaDocente::find($idCategori_docente);

        if (!$cat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $cat->nombre = $request->nombre;
        $cat->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Categoría docente actualizada correctamente.',
            'data' => [
                'id'     => $cat->idCategori_docente,
                'nombre' => $cat->nombre
            ]
        ]);
    }

    // =====================================================
    // ELIMINAR
    // =====================================================
    public function actionDelete($idCategori_docente)
    {
        $cat = CategoriaDocente::find($idCategori_docente);

        if (!$cat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Categoría no encontrada.'
            ]);
        }

        $cat->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Categoría docente eliminada correctamente.'
        ]);
    }
}
