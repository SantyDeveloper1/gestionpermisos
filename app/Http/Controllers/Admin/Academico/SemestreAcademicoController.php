<?php

namespace App\Http\Controllers\Admin\Academico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SemestreAcademico;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SemestreAcademicoController extends Controller
{
    public function actionGetAll(Request $request)
    {
        if ($request->isMethod('post')) {

            // =======================
            // VALIDACIÓN
            // =======================
            $validator = Validator::make(
                $request->all(),
                [
                    'codigo_Academico' => 'required|string|max:30|unique:semestre_academico,codigo_Academico',
                    'anio_academico' => 'required|integer|min:2000|max:2100',
                    'FechaInicioAcademico' => 'required|date',
                    'FechaFinAcademico' => 'required|date|after_or_equal:FechaInicioAcademico',
                    'DescripcionAcademico' => 'nullable|string|max:255',
                ],
                [
                    'codigo_Academico.required' => 'Debe ingresar el código académico.',
                    'codigo_Academico.unique' => 'Este código académico ya existe.',
                    'anio_academico.required' => 'Debe ingresar el año académico.',
                    'FechaInicioAcademico.required' => 'Debe ingresar la fecha de inicio.',
                    'FechaFinAcademico.required' => 'Debe ingresar la fecha de fin.',
                    'FechaFinAcademico.after_or_equal' => 'La fecha de fin debe ser mayor o igual a la de inicio.',
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
                return redirect('admin/academico/semestre/getall');
            }

            // =======================
            // REGISTRO
            // =======================
            $sem = new SemestreAcademico();
            $sem->IdSemestreAcademico = uniqid(); // PK CHAR(13)

            $sem->codigo_Academico = $request->codigo_Academico;
            $sem->anio_academico = $request->anio_academico;
            $sem->FechaInicioAcademico = $request->FechaInicioAcademico;
            $sem->FechaFinAcademico = $request->FechaFinAcademico;

            $sem->EstadoAcademico = $request->EstadoAcademico ?? 'Planificado';
            $sem->EsActualAcademico = $request->EsActualAcademico ?? 0;
            $sem->DescripcionAcademico = $request->DescripcionAcademico;

            $sem->save();

            // =======================
            // RESPUESTA AJAX
            // =======================
            if ($request->ajax()) {

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'id' => $sem->IdSemestreAcademico,
                        'codigo' => $sem->codigo_Academico,
                        'anio' => $sem->anio_academico,
                        'inicio' => date('d/m/Y', strtotime($sem->FechaInicioAcademico)),
                        'fin' => date('d/m/Y', strtotime($sem->FechaFinAcademico)),
                        'estado' => $sem->EstadoAcademico,
                        'es_actual' => $sem->EsActualAcademico,
                        'fecha' => $sem->created_at->format('d/m/Y'),
                        'numero' => SemestreAcademico::count(),
                    ]
                ]);
            }

            session()->flash('listMessage', ['Semestre académico registrado correctamente.']);
            session()->flash('typeMessage', 'success');
            return redirect('admin/academico/semestre/getall');
        }

        // =======================
        // LISTAR
        // =======================
        $listSemestres = SemestreAcademico::orderBy('codigo_Academico', 'DESC')
            ->orderBy('FechaInicioAcademico', 'ASC')
            ->get();

        return view('admin.academico.semestre_academico.getAll', compact('listSemestres'));
    }

    public function actionUpdate(Request $request, $idSemestreAcademico)
    {
        if ($request->isMethod('post')) {

            // =======================
            // VALIDACIÓN
            // =======================
            $validator = Validator::make(
                $request->all(),
                [
                    'codigo_Academico' => 'required|string|max:30|unique:semestre_academico,codigo_Academico,' . $idSemestreAcademico . ',IdSemestreAcademico',
                    'anio_academico' => 'required|integer|min:2000|max:2100',
                    'FechaInicioAcademico' => 'required|date',
                    'FechaFinAcademico' => 'required|date|after_or_equal:FechaInicioAcademico',
                    'DescripcionAcademico' => 'nullable|string|max:255',
                ],
                [
                    'codigo_Academico.required' => 'Debe ingresar el código académico.',
                    'codigo_Academico.unique' => 'Este código académico ya existe.',
                    'anio_academico.required' => 'Debe ingresar el año académico.',
                    'FechaInicioAcademico.required' => 'Debe ingresar la fecha de inicio.',
                    'FechaFinAcademico.required' => 'Debe ingresar la fecha de fin.',
                    'FechaFinAcademico.after_or_equal' => 'La fecha de fin debe ser mayor o igual a la de inicio.',
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
                return redirect('admin/academico/semestre/getall');
            }

            // =======================
            // ACTUALIZACIÓN
            // =======================
            $sem = SemestreAcademico::findOrFail($idSemestreAcademico);

            $sem->codigo_Academico = $request->codigo_Academico;
            $sem->anio_academico = $request->anio_academico;
            $sem->FechaInicioAcademico = $request->FechaInicioAcademico;
            $sem->FechaFinAcademico = $request->FechaFinAcademico;

            // Solo actualizar estos campos si vienen en el request
            if ($request->has('EstadoAcademico')) {
                $sem->EstadoAcademico = $request->EstadoAcademico;
            }
            if ($request->has('EsActualAcademico')) {
                $sem->EsActualAcademico = $request->EsActualAcademico;
            }

            $sem->DescripcionAcademico = $request->DescripcionAcademico;

            $sem->save();

            // =======================
            // RESPUESTA AJAX
            // =======================
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semestre actualizado correctamente.',
                    'data' => [
                        'id' => $sem->IdSemestreAcademico,
                        'codigo' => $sem->codigo_Academico,
                        'anio' => $sem->anio_academico,
                        'inicio' => date('d/m/Y', strtotime($sem->FechaInicioAcademico)),
                        'fin' => date('d/m/Y', strtotime($sem->FechaFinAcademico)),
                        'estado' => $sem->EstadoAcademico,
                        'es_actual' => $sem->EsActualAcademico,
                        'FechaInicioAcademico' => $sem->FechaInicioAcademico,
                        'FechaFinAcademico' => $sem->FechaFinAcademico,
                    ]
                ]);
            }

            session()->flash('listMessage', ['Semestre académico actualizado correctamente.']);
            session()->flash('typeMessage', 'success');
            return redirect('admin/academico/semestre/getall');
        }
    }

    /**
     * Cambiar estado del semestre
     */
    public function cambiarEstado(Request $request)
    {
        $sem = SemestreAcademico::findOrFail($request->id);

        $nuevoEstado = $request->estado;

        // Actualizar estado
        $sem->EstadoAcademico = $nuevoEstado;

        // Si pasa a ACTIVO → marcar como ACTUAL automáticamente
        if ($nuevoEstado === 'Activo') {

            // Desmarcar todos los demás
            SemestreAcademico::where('EsActualAcademico', 1)
                ->update(['EsActualAcademico' => 0]);

            // Marcar este como actual
            $sem->EsActualAcademico = 1;
        }

        $sem->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente.',
            'data' => [
                'EstadoAcademico' => $sem->EstadoAcademico,
                'EsActualAcademico' => $sem->EsActualAcademico
            ]
        ]);
    }


    /**
     * Marcar semestre como actual
     */
    public function marcarComoActual(Request $request)
    {
        try {
            $id = $request->id;

            $nuevoActual = SemestreAcademico::findOrFail($id);

            // VALIDACIÓN profesional
            if ($nuevoActual->EstadoAcademico === 'Cerrado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede marcar como actual un semestre cerrado.'
                ], 422);
            }

            DB::beginTransaction();

            // Semestre que era actual antes
            $previo = SemestreAcademico::where('EsActualAcademico', 1)->first();

            // Poner todos en 0
            SemestreAcademico::query()->update(['EsActualAcademico' => 0]);

            // Marcar este como actual
            $nuevoActual->EsActualAcademico = 1;
            $nuevoActual->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Semestre marcado como actual.',
                'data' => [
                    'actual_id' => $nuevoActual->IdSemestreAcademico,
                    'previo_id' => $previo ? $previo->IdSemestreAcademico : null
                ]
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como actual.'
            ], 500);
        }
    }

    public function actionDelete(Request $request)
    {
        if ($request->isMethod('delete')) {
            $sem = SemestreAcademico::find($request->idSemestreAcademico);
            if ($sem) {
                $sem->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Semestre académico eliminado correctamente.'
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Semestre académico no encontrado.'
            ]);
        }
    }

}