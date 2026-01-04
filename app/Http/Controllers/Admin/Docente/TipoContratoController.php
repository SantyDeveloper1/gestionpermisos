<?php

namespace App\Http\Controllers\Admin\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoContrato;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class TipoContratoController extends Controller
{
    public function actionInsert(Request $request)
    {
        if ($request->isMethod('post')) {

            // VALIDACIÓN
            $validator = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required|string|max:100|unique:tipos_contrato,nombre',
                ],
                [
                    'nombre.required' => 'Debe ingresar el nombre del tipo de contrato.',
                    'nombre.unique'   => 'Este tipo de contrato ya existe.',
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

                return redirect('admin/docente/tipo_contrato/insert');
            }

            // REGISTRO
            $cont = new TipoContrato();
            $cont->idTipo_contrato = uniqid(); 
            $cont->nombre = $request->nombre;
            $cont->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id'     => $cont->idTipo_contrato,
                        'nombre' => $cont->nombre,
                        'fecha'  => $cont->created_at->format('d/m/Y H:i'),
                        'numero' => TipoContrato::count(),
                    ]
                ]);
            }

            session()->flash('listMessage', ['Tipo de contrato registrado correctamente.']);
            session()->flash('typeMessage', 'success');

            return redirect('admin/docente/tipos_contrato/insert');
        }

        $listContratos = TipoContrato::orderBy('created_at', 'DESC')->get();

        return view('admin.docente.tipo_contrato.insert', compact('listContratos'));
    }
    
    public function actionUpdate(Request $request)
    {
        if ($request->isMethod('post')) {

            // VALIDACIÓN
            $validator = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required|string|max:100|unique:tipos_contrato,nombre,' . $request->idTipo_contrato . ',idTipo_contrato',
                ],
                [
                    'nombre.required' => 'Debe ingresar el nombre del tipo de contrato.',
                    'nombre.unique'   => 'Este tipo de contrato ya existe.',
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

                return redirect('admin/docente/tipo_contrato/update');
            }

            // REGISTRO
            $cont = TipoContrato::find($request->idTipo_contrato);
            $cont->nombre = $request->nombre;
            $cont->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id'     => $cont->idTipo_contrato,
                        'nombre' => $cont->nombre,
                        'fecha'  => $cont->updated_at->format('d/m/Y H:i'),
                        'numero' => TipoContrato::count(),
                    ]
                ]);
            }

            session()->flash('listMessage', ['Tipo de contrato actualizado correctamente.']);
            session()->flash('typeMessage', 'success');

            return redirect('admin/docente/tipos_contrato/update');
        }

        $listContratos = TipoContrato::orderBy('created_at', 'DESC')->get();

        return view('admin.docente.tipo_contrato.update', compact('listContratos'));
    }
    
    public function actionDelete($idTipo_contrato)
    {
        $cont = TipoContrato::find($idTipo_contrato);
        
        if (!$cont) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tipo de contrato no encontrado.'
            ], 404);
        }
        
        $cont->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tipo de contrato eliminado correctamente.'
        ]);
    }   
}