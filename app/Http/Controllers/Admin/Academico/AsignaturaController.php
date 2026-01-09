<?php
namespace App\Http\Controllers\Admin\Academico;
use App\Http\Controllers\Controller;
use App\Models\Asignatura;
use App\Models\Ciclo;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AsignaturaController extends Controller
{
	// 📌 Listar asignaturas
	public function actionIndex()
	{
		$listAsignaturas = Asignatura::with(['ciclo'])
			->get()
			->groupBy('IdCiclo'); // ← ESTA LÍNEA LO SOLUCIONA

		$ciclos = Ciclo::all();

		return view('admin.academico.asignatura.asignatura', compact('listAsignaturas', 'ciclos'));
	}



	public function actionInsert(Request $request, SessionManager $sessionManager)
	{
		if ($request->isMethod('post')) {

			$validator = Validator::make(
				$request->all(),
				[
					'codigo_asignatura' => 'required|string|max:20|unique:asignaturas,codigo_asignatura',
					'nom_asignatura' => 'required|string|max:120',
					'creditos' => 'required|integer|min:1',
					'horas_teoria' => 'required|integer|min:0',
					'horas_practica' => 'required|integer|min:0',
					'IdCiclo' => 'nullable|string',
					'tipo' => 'required|string',
				],
				[
					'codigo_asignatura.required' => 'Debe ingresar un código.',
					'codigo_asignatura.unique' => 'El código ya existe.',
					'nom_asignatura.required' => 'Debe ingresar el nombre de la asignatura.',
					'creditos.required' => 'Debe ingresar créditos.',
					'horas_teoria.required' => 'Debe ingresar horas de teoría.',
					'horas_practica.required' => 'Debe ingresar horas de práctica.',
					'tipo.required' => 'Debe seleccionar el tipo.',
				]
			);

			if ($validator->fails()) {
				$sessionManager->flash('listMessage', $validator->errors()->all());
				$sessionManager->flash('typeMessage', 'error');
				return redirect('admin/academico/asignatura/insert');
			}

			// Insertar asignatura
			$asig = new Asignatura();
			$asig->idAsignatura = uniqid();
			$asig->codigo_asignatura = $request->codigo_asignatura;
			$asig->nom_asignatura = $request->nom_asignatura;
			$asig->creditos = $request->creditos;
			$asig->horas_teoria = $request->horas_teoria;
			$asig->horas_practica = $request->horas_practica;
			$asig->IdCiclo = $request->IdCiclo;
			$asig->tipo = $request->tipo;
			$asig->estado = "Activo";
			$asig->save();

			$sessionManager->flash('listMessage', ['Asignatura registrada correctamente.']);
			$sessionManager->flash('typeMessage', 'success');

			return redirect('admin/academico/asignatura/');
		}

		// Obtener datos para los selectores
		$ciclos = Ciclo::all();

		return view('admin.academico.asignatura.insert', compact('ciclos'));
	}

	public function actionUpdate($idAsignatura, Request $request, SessionManager $sessionManager)
	{
		if (!$request->isMethod('post')) {
			return redirect()->back();
		}

		$validator = Validator::make(
			$request->all(),
			[
				'nom_asignatura' => 'required|string|max:120',
				'creditos' => 'required|integer|min:1',
				'horas_teoria' => 'required|integer|min:0',
				'horas_practica' => 'required|integer|min:0',
				'IdCiclo' => 'nullable|string',
				'tipo' => 'required|string|max:20',
			],
			[
				'nom_asignatura.required' => 'El nombre es obligatorio.',
				'creditos.required' => 'Los créditos son obligatorios.',
				'horas_teoria.required' => 'Horas teoría es obligatorio.',
				'horas_practica.required' => 'Horas práctica es obligatorio.',
				'tipo.required' => 'Debe seleccionar un tipo.',
			]
		);

		if ($validator->fails()) {
			if ($request->ajax()) {
				return response()->json([
					'success' => false,
					'errors' => $validator->errors()->all()
				], 422);
			}
		}

		$asig = Asignatura::findOrFail($idAsignatura);
		$asig->update([
			'nom_asignatura' => trim($request->nom_asignatura),
			'creditos' => (int) $request->creditos,
			'horas_teoria' => (int) $request->horas_teoria,
			'horas_practica' => (int) $request->horas_practica,
			'IdCiclo' => $request->IdCiclo,
			'tipo' => trim($request->tipo),
		]);

		if ($request->ajax()) {
			// Cargar la relación ciclo para obtener el nombre
			$asig->load('ciclo');

			return response()->json([
				'success' => true,
				'message' => 'Asignatura actualizada correctamente.',
				'nombreCiclo' => $asig->ciclo->NombreCiclo ?? 'N/A'
			]);
		}
	}

	public function actionEstado(Request $request, $idAsignatura)
	{
		$asig = Asignatura::find($idAsignatura);

		if (!$asig) {
			return response()->json([
				'success' => false,
				'message' => 'Asignatura no encontrada.'
			]);
		}

		// Si del front viene 1 = Activo, 0 = Inactivo
		$nuevoEstado = $request->estado == "1" ? "Activo" : "Inactivo";

		$asig->estado = $nuevoEstado;
		$asig->save();

		return response()->json([
			'success' => true,
			'estado' => $nuevoEstado
		]);
	}

	public function actionDelete($idAsignatura, SessionManager $sessionManager)
	{
		$asig = Asignatura::find($idAsignatura);

		if ($asig) {
			$asig->delete();

			if (request()->ajax()) {
				return response()->json(['status' => 'success']);
			}

			$sessionManager->flash('listMessage', ['Asignatura eliminada correctamente.']);
		}

		return redirect('admin/academico/asignatura/insert');
	}

	/**
	 * Buscar asignatura por código
	 */
	public function actionBuscar(Request $request)
	{
		try {
			$codigo = $request->input('codigo');

			if (!$codigo) {
				return response()->json([
					'success' => false,
					'message' => 'Debe proporcionar un código de asignatura'
				], 400);
			}

			$asignatura = Asignatura::where('codigo_asignatura', strtoupper($codigo))
				->where('estado', 'Activo')
				->first();

			if (!$asignatura) {
				return response()->json([
					'success' => false,
					'message' => 'No se encontró ninguna asignatura con el código: ' . $codigo
				], 404);
			}

			return response()->json([
				'success' => true,
				'asignatura' => [
					'idAsignatura' => $asignatura->idAsignatura,
					'codigo_asignatura' => $asignatura->codigo_asignatura,
					'nom_asignatura' => $asignatura->nom_asignatura,
					'creditos' => $asignatura->creditos,
					'horas_teoria' => $asignatura->horas_teoria,
					'horas_practica' => $asignatura->horas_practica
				]
			]);

		} catch (\Exception $e) {
			\Log::error('Error al buscar asignatura: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Error al buscar la asignatura'
			], 500);
		}
	}
}