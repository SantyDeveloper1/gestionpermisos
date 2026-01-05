<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\Usuarios\UsuarioController;
use App\Http\Controllers\Admin\Docente\TipoContratoController;
use App\Http\Controllers\Admin\Docente\CategoriaDocenteController;
use App\Http\Controllers\Admin\Docente\GradosAcademicosController;
use App\Http\Controllers\Admin\Docente\DocenteController;
use App\Http\Controllers\Admin\TipoPermisoController\TipoPermisoController;
use App\Http\Controllers\Admin\Permiso\PermisoController;
use App\Http\Controllers\Admin\PlanRecuperacion\PlanRecuperacionController;
use App\Http\Controllers\Admin\SesionRecuperacion\SesionRecuperacionController;
use App\Http\Controllers\Admin\EvidenciaRecuperacion\EvidenciaRecuperacionController;
use Illuminate\Support\Facades\Hash;

Route::match(['get', 'post'], '/login', [LoginController::class, 'actionLogin'])
    ->name('login');

Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])
    ->name('logout');

// Ruta raíz - redirige según autenticación
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        // Redirigir según rol
        if ($user->roles()->where('name', 'docente')->exists()) {
            return redirect('/docente');
        }

        return redirect('/admin');
    }

    // Si no está autenticado, redirigir al login
    return redirect('/login');
});

Route::middleware('auth')->group(function () {

    // ================= ADMIN =================
    Route::middleware('role:admin')
        ->prefix('admin')
        ->group(function () {

            Route::get('/', [UsuarioController::class, 'actionAdminIndex']);
            Route::get('/usuarios', [UsuarioController::class, 'actionIndex']);
            Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');
            Route::get('/usuarios/asignar_roles', [UsuarioController::class, 'actionAsignarRoles'])->name('usuarios.asignar_roles');
            // GUARDAR (AJAX)
            Route::post('/usuarios/asignar_roles', [UsuarioController::class, 'storeAsignarRoles'])->name('usuarios.asignar_roles.store');
            Route::get('/usuarios/asignar_roles/listar', [UsuarioController::class, 'listarUsuariosRoles'])->name('usuarios.asignar_roles.listar');

            Route::post('usuarios/update/{idUsuario}', [UsuarioController::class, 'actionUpdate']);
            Route::post('usuarios/desactivar/{idUsuario}', [UsuarioController::class, 'actionDesactivar']);
            Route::post('usuarios/eliminar_rol/{idUsuario}', [UsuarioController::class, 'actionEliminarRol']);
            Route::get('usuarios/listar', [UsuarioController::class, 'listarUsuarios']);

            // DOCENTE
            Route::get('/docente', [DocenteController::class, 'actionDocente']);
            Route::get('docente/show/{idDocente}', [DocenteController::class, 'actionShow']);
            Route::match(['get', 'post'], 'docente/insert', [DocenteController::class, 'actionInsert']);
            Route::post('docente/update/{idDocente}', [DocenteController::class, 'actionUpdate']);
            Route::put('docente/estado/{idDocente}', [DocenteController::class, 'actionEstado']);
            Route::delete('docente/delete/{idDocente}', [DocenteController::class, 'actionDelete']);
            // CATEGORÍA DOCENTE
            Route::match(['get', 'post'], 'docente/categoria-docente/insert', [CategoriaDocenteController::class, 'actionInsert']);
            Route::post('docente/categoria-docente/update/{idCategori_docente}', [CategoriaDocenteController::class, 'actionUpdate']);
            Route::delete('docente/categoria-docente/delete/{idCategori_docente}', [CategoriaDocenteController::class, 'actionDelete']);

            // TIPO DE CONTRATO
            Route::match(['get', 'post'], 'docente/tipo_contrato/insert', [TipoContratoController::class, 'actionInsert']);
            Route::post('docente/tipo_contrato/update/{idTipo_contrato}', [TipoContratoController::class, 'actionUpdate']);
            Route::delete('docente/tipo_contrato/delete/{idTipo_contrato}', [TipoContratoController::class, 'actionDelete']);

            // GRADO ACADÉMICO
            Route::match(['get', 'post'], 'docente/grados-academicos/insert', [GradosAcademicosController::class, 'actionInsert']);
            Route::post('docente/grados-academicos/update/{idGrados_academicos}', [GradosAcademicosController::class, 'actionUpdate']);
            Route::delete('docente/grados-academicos/delete/{idGrados_academicos}', [GradosAcademicosController::class, 'actionDelete']);

            // TIPO DE PERMISO
            Route::get('tipo_permiso', [TipoPermisoController::class, 'actionTipoPermiso']);
            Route::match(['get', 'post'], 'tipo_permiso/insert', [TipoPermisoController::class, 'actionInsert']);
            Route::post('tipo_permiso/update/{id_tipo_permiso}', [TipoPermisoController::class, 'actionUpdate']);
            Route::delete('tipo_permiso/delete/{id_tipo_permiso}', [TipoPermisoController::class, 'actionDelete']);

            // PERMISO
            Route::get('permiso', [PermisoController::class, 'actionPermiso']);
            Route::get('permiso/{id}', [PermisoController::class, 'actionShow']);
            Route::match(['get', 'post'], 'permiso/insert', [PermisoController::class, 'actionInsert']);
            Route::post('permiso/update/{idPermiso}', [PermisoController::class, 'actionUpdate']);
            Route::delete('permiso/delete/{idPermiso}', [PermisoController::class, 'actionDelete']);

            // PLAN DE RECUPERACIÓN
            Route::get('plan_recuperacion', [PlanRecuperacionController::class, 'actionPlanRecuperacion']);
            Route::get('plan_recuperacion/{id}', [PlanRecuperacionController::class, 'actionShow']);
            Route::match(['get', 'post'], 'plan_recuperacion/insert', [PlanRecuperacionController::class, 'actionInsert']);
            Route::post('plan_recuperacion/update/{idPlan_recuperacion}', [PlanRecuperacionController::class, 'actionUpdate']);
            Route::patch('plan_recuperacion/aprobar/{idPlan_recuperacion}', [PlanRecuperacionController::class, 'actionAprobar']);
            Route::delete('plan_recuperacion/delete/{idPlan_recuperacion}', [PlanRecuperacionController::class, 'actionDelete']);


            // SESION DE RECUPERACION
            Route::get('sesion_recuperacion', [SesionRecuperacionController::class, 'actionSesionRecuperacion']);
            Route::get('sesion_recuperacion/{id}', [SesionRecuperacionController::class, 'actionShow']);
            Route::match(['get', 'post'], 'sesion_recuperacion/insert', [SesionRecuperacionController::class, 'actionInsert']);
            Route::post('sesion_recuperacion/update/{idSesion_recuperacion}', [SesionRecuperacionController::class, 'actionUpdate']);
            Route::delete('sesion_recuperacion/delete/{idSesion_recuperacion}', [SesionRecuperacionController::class, 'actionDelete']);

            // EVIDENCIA DE RECUPERACION
            Route::get('evidencia_recuperacion', [EvidenciaRecuperacionController::class, 'actionEvidenciaRecuperacion']);
            Route::get('evidencia_recuperacion/ver/{id}', [EvidenciaRecuperacionController::class, 'actionVerEvidencia'])->name('evidencia.ver');
            Route::get('evidencia_recuperacion/{id}', [EvidenciaRecuperacionController::class, 'actionShow']);
            Route::post('evidencia_recuperacion/insert', [EvidenciaRecuperacionController::class, 'actionInsert'])->name('evidencia.store');
            Route::post('evidencia_recuperacion/update/{idEvidencia_recuperacion}', [EvidenciaRecuperacionController::class, 'actionUpdate']);
            Route::delete('evidencia_recuperacion/delete/{idEvidencia_recuperacion}', [EvidenciaRecuperacionController::class, 'actionDelete']);


        });


    // ================= DOCENTE =================
    Route::middleware('role:docente')->group(function () {

        Route::get('/docente', function () {
            return view('docente.index');
        });

    });
});


Route::get('/test-rol', function () {
    if (!auth()->check()) {
        return ['error' => 'No estás autenticado. Por favor inicia sesión primero.'];
    }

    $user = auth()->user();
    return [
        'usuario' => $user->email,
        'nombre' => $user->name,
        'roles' => $user->roles->pluck('name'),
        'tiene_admin' => $user->hasRole('admin') ? 'SÍ' : 'NO',
        'tiene_docente' => $user->hasRole('docente') ? 'SÍ' : 'NO'
    ];
});



Route::get('/crear-usuario-test', function () {

    // Verificar si el usuario ya existe
    $user = User::where('email', 'ramirez@gmail.com')->first();

    if ($user) {
        return [
            'mensaje' => 'El usuario ya existe',
            'email' => $user->email
        ];
    }

    // Crear usuario
    $user = User::create([
        'name' => 'Dany',
        'last_name' => 'Ramirez',
        'email' => 'ramirez@gmail.com',
        'password' => Hash::make('123456'),
        'status' => 'active'
    ]);

    return [
        'mensaje' => 'Usuario creado correctamente',
        'email' => $user->email,
        'password' => '123456 (encriptada)',
    ];
});
