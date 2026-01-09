<?php

namespace App\Http\Controllers\Admin\Academico;

use App\Http\Controllers\Controller;
use App\Models\Ciclo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CicloController extends Controller
{

    // ============================================
    // INSERT – REGISTRAR CICLO
    // ============================================
    public function actionGetAll(Request $request)
    {
        if ($request->isMethod('post')) {
            // POST → insertar ciclo
            $validator = Validator::make($request->all(), [
                'NombreCiclo' => 'required|string|max:100',
                'NumeroCiclo' => 'required|string|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            $ciclo = new Ciclo();
            $ciclo->IdCiclo = uniqid();
            $ciclo->NombreCiclo = $request->NombreCiclo;
            $ciclo->NumeroCiclo = strtoupper($request->NumeroCiclo);
            $ciclo->save();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $ciclo->IdCiclo,
                    'nombre' => $ciclo->NombreCiclo,
                    'numero' => $ciclo->NumeroCiclo,
                    'fecha' => $ciclo->created_at->format('d/m/Y H:i'),
                    'count' => Ciclo::count(),
                ]
            ]);
        }

        // GET → mostrar la vista con la lista de ciclos
        $listCiclos = Ciclo::orderBy('created_at', 'DESC')->get();
        return view('admin.academico.ciclo.getall', compact('listCiclos'));
    }

    public function actionUpdate(Request $request, $idCiclo)
    {
        // 1️⃣ Buscar el ciclo
        $ciclo = Ciclo::find($idCiclo);
        if (!$ciclo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ciclo no encontrado'
            ], 404);
        }
        // 2️⃣ Validar los datos
        $validator = Validator::make($request->all(), [
            'NombreCiclo' => 'required|string|max:100',
            'NumeroCiclo' => 'required|string|max:10', // Ej: I, II, III...
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->all()
            ], 422);
        }
        // 3️⃣ Actualizar campos
        $ciclo->NombreCiclo = $request->NombreCiclo;
        $ciclo->NumeroCiclo = strtoupper($request->NumeroCiclo); // Siempre en mayúsculas
        $ciclo->save();
        // 4️⃣ Respuesta JSON
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $ciclo->IdCiclo,
                'nombre' => $ciclo->NombreCiclo,
                'numero' => $ciclo->NumeroCiclo,
                'fecha' => $ciclo->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }
    public function actionDelete(Request $request, $idCiclo)
    {
        // 1️⃣ Buscar el ciclo
        $ciclo = Ciclo::find($idCiclo);
        if (!$ciclo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ciclo no encontrado'
            ], 404);
        }
        // 2️⃣ Eliminar el ciclo
        $ciclo->delete();
        // 3️⃣ Respuesta JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Ciclo eliminado correctamente'
        ]);
    }
}
