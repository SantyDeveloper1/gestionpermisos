<?php

namespace App\Http\Controllers\Admin\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GradoAcademico;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GradosAcademicosController extends Controller
{
    public function actionInsert(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required|string|max:100|unique:grados_academicos,nombre',
                ],
                [
                    'nombre.required' => 'Debe ingresar el nombre del grado académico.',
                    'nombre.unique'   => 'Este grado académico ya existe.',
                ]
            );

            if ($validator->fails()) {

                // SI ES AJAX → retornar JSON
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()->all()
                    ], 422);
                }

                session()->flash('listMessage', $validator->errors()->all());
                session()->flash('typeMessage', 'error');
                return redirect('admin/docente/grados-academicos/insert');
            }

            $grado = new GradoAcademico();
            $grado->idGrados_academicos = uniqid();
            $grado->nombre = $request->nombre;
            $grado->save();

            // 🔥 SI ES AJAX → devolver JSON con datos para la fila
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id'     => $grado->idGrados_academicos,
                        'nombre' => $grado->nombre,
                        'fecha'  => $grado->created_at->format('d/m/Y H:i'),
                        'numero' => GradoAcademico::count(), // o puedes mandar el loop->iteration
                    ]
                ]);
            }

            // Normal (sin AJAX)
            session()->flash('listMessage', ['Grado académico registrado correctamente.']);
            session()->flash('typeMessage', 'success');

            return redirect('admin/docente/grados-academicos/insert');
        }

        $listGrados = GradoAcademico::orderBy('created_at', 'DESC')->get();
        return view('admin.docente.grados_academicos.insert', compact('listGrados'));
    }

    public function actionUpdate($idGrado, Request $request)
{
    if (!$request->isMethod('post')) {
        return redirect()->back();
    }

    $validator = Validator::make($request->all(), [
        'nombre' => 'required|string|max:100',
    ], [
        'nombre.required' => 'El nombre del grado es obligatorio.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()->all()
        ], 422);
    }

    $grado = GradoAcademico::findOrFail($idGrado);

    $grado->update([
        'nombre' => trim($request->nombre)
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Grado académico actualizado correctamente.'
    ]);
}


    public function actionDelete($idGrados_academicos)
    {
        $grado = GradoAcademico::find($idGrados_academicos);

        if ($grado) {
            $grado->delete();

            if (request()->ajax()) {
                return response()->json(['status' => 'success']);
            }

            session()->flash('listMessage', ['Grado académico eliminado correctamente.']);
        }

        return redirect('admin/docente/grados-academicos');
    }
}
