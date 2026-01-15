<?php

use App\Models\Ciclo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\Usuarios\UsuarioController;
use App\Http\Controllers\Admin\Docente\TipoContratoController;
use App\Http\Controllers\Admin\Docente\GradosAcademicosController;
use App\Http\Controllers\Admin\Docente\DocenteController;
use App\Http\Controllers\Admin\TipoPermisoController\TipoPermisoController;
use App\Http\Controllers\Admin\Permiso\PermisoController;
use App\Http\Controllers\Admin\PlanRecuperacion\PlanRecuperacionController;
use App\Http\Controllers\Admin\SesionRecuperacion\SesionRecuperacionController;
use App\Http\Controllers\Admin\EvidenciaRecuperacion\EvidenciaRecuperacionController;
use App\Http\Controllers\Admin\Academico\CicloController;
use App\Http\Controllers\Admin\Academico\AsignaturaController;
use App\Http\Controllers\Admin\Academico\SemestreAcademicoController;

use App\Http\Controllers\Docente\Permiso\PermisoController as DocentePermisoController;
use App\Http\Controllers\Docente\SesionRecuperacion\SesionRecuperacionController as DocenteSesionRecuperacionController;
use App\Http\Controllers\Docente\PlanRecuperacion\PlanRecuperacionController as DocentePlanRecuperacionController;
use App\Http\Controllers\Docente\EvidenciaRecuperacion\EvidenciaRecuperacionController as DocenteEvidenciaRecuperacionController;
use App\Http\Controllers\Docente\SeguimientoPermiso\SeguimientoPermisoController;
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
            Route::get('/usuarios/roles', [UsuarioController::class, 'actionUsuariosRoles']);
            Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');
            Route::get('/usuarios/asignar_roles', [UsuarioController::class, 'actionAsignarRoles'])->name('usuarios.asignar_roles');
            // GUARDAR (AJAX)
            Route::post('/usuarios/asignar_roles', [UsuarioController::class, 'storeAsignarRoles'])->name('usuarios.asignar_roles.store');
            Route::get('/usuarios/asignar_roles/listar', [UsuarioController::class, 'listarUsuariosRoles'])->name('usuarios.asignar_roles.listar');

            Route::post('usuarios/update/{idUsuario}', [UsuarioController::class, 'actionUpdate']);
            Route::post('usuarios/desactivar/{idUsuario}', [UsuarioController::class, 'actionDesactivar']);
            Route::post('usuarios/eliminar_rol/{idUsuario}', [UsuarioController::class, 'actionEliminarRol']);
            Route::get('usuarios/listar', [UsuarioController::class, 'listarUsuarios']);

            // Cambiar contraseña (disponible para todos los usuarios autenticados)
            Route::post('usuarios/password/update', [UsuarioController::class, 'actionUpdatePassword'])->name('usuarios.password.update');

            // Perfil de usuario
            Route::get('profile', [\App\Http\Controllers\Admin\Usuarios\ProfileController::class, 'index'])->name('admin.profile.index');
            Route::post('profile/update', [\App\Http\Controllers\Admin\Usuarios\ProfileController::class, 'update'])->name('admin.profile.update');



            // DOCENTE
            Route::get('/docente', [DocenteController::class, 'actionDocente'])->name('admin.docentes.index');

            Route::get('docente/show/{idDocente}', [DocenteController::class, 'actionShow']);
            Route::match(['get', 'post'], 'docente/insert', [DocenteController::class, 'actionInsert']);
            Route::post('docente/update/{idDocente}', [DocenteController::class, 'actionUpdate']);
            Route::put('docente/estado/{idDocente}', [DocenteController::class, 'actionEstado']);
            Route::delete('docente/delete/{idDocente}', [DocenteController::class, 'actionDelete']);

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
            Route::get('permiso', [PermisoController::class, 'actionPermiso'])->name('admin.permisos.index');
            Route::get('permiso/aprobados', function () {
                return redirect('/admin/permiso')->with('filter', 'APROBADO');
            })->name('admin.permisos.aprobados');
            Route::get('permiso/pendientes', function () {
                return redirect('/admin/permiso')->with('filter', 'SOLICITADO');
            })->name('admin.permisos.pendientes');
            Route::get('permiso/rechazados', function () {
                return redirect('/admin/permiso')->with('filter', 'RECHAZADO');
            })->name('admin.permisos.rechazados');
            Route::get('permiso/{id}', [PermisoController::class, 'actionShow'])->name('admin.permisos.show');
            Route::match(['get', 'post'], 'permiso/insert', [PermisoController::class, 'actionInsert'])->name('admin.permisos.create');
            Route::post('permiso/update/{idPermiso}', [PermisoController::class, 'actionUpdate'])->name('admin.permisos.edit');
            Route::post('permiso/enviar-email/{id}', [PermisoController::class, 'actionEnviarEmail'])->name('admin.permisos.enviar_email');
            Route::delete('permiso/delete/{idPermiso}', [PermisoController::class, 'actionDelete']);

            // CONFIGURACIÓN
            Route::get('configuracion', function () {
                return view('admin.configuracion.index');
            })->name('admin.configuracion.index');

            // PLAN DE RECUPERACIÓN
            Route::get('plan_recuperacion', [PlanRecuperacionController::class, 'actionPlanRecuperacion']);
            Route::get('plan_recuperacion/progreso/{idPermiso}', [PlanRecuperacionController::class, 'actionProgreso']);
            Route::get('plan_recuperacion/{id}', [PlanRecuperacionController::class, 'actionShow']);
            Route::match(['get', 'post'], 'plan_recuperacion/insert', [PlanRecuperacionController::class, 'actionInsert']);
            Route::post('plan_recuperacion/update/{idPlan_recuperacion}', [PlanRecuperacionController::class, 'actionUpdate']);
            Route::patch('plan_recuperacion/aprobar/{idPlan_recuperacion}', [PlanRecuperacionController::class, 'actionAprobar']);
            Route::post('plan_recuperacion/enviar-email/{id}', [PlanRecuperacionController::class, 'actionEnviarEmail'])->name('admin.plan_recuperacion.enviar_email');
            Route::delete('plan_recuperacion/delete/{idPlan_recuperacion}', [PlanRecuperacionController::class, 'actionDelete']);


            // SESION DE RECUPERACION
            Route::get('sesion_recuperacion', [SesionRecuperacionController::class, 'actionSesionRecuperacion']);
            Route::get('sesion_recuperacion/{id}', [SesionRecuperacionController::class, 'actionShow']);
            Route::match(['get', 'post'], 'sesion_recuperacion/insert', [SesionRecuperacionController::class, 'actionInsert']);
            Route::post('sesion_recuperacion/update/{idSesion_recuperacion}', [SesionRecuperacionController::class, 'actionUpdate']);
            Route::post('sesion_recuperacion/update-estado/{idSesion_recuperacion}', [SesionRecuperacionController::class, 'actionUpdateEstado']);
            Route::delete('sesion_recuperacion/delete/{idSesion_recuperacion}', [SesionRecuperacionController::class, 'actionDelete']);

            // EVIDENCIA DE RECUPERACION
            Route::get('evidencia_recuperacion', [EvidenciaRecuperacionController::class, 'actionEvidenciaRecuperacion']);
            Route::get('evidencia_recuperacion/ver/{id}', [EvidenciaRecuperacionController::class, 'actionVerEvidencia'])->name('evidencia.ver');
            Route::get('evidencia_recuperacion/{id}', [EvidenciaRecuperacionController::class, 'actionShow']);
            Route::post('evidencia_recuperacion/insert', [EvidenciaRecuperacionController::class, 'actionInsert'])->name('evidencia.store');
            Route::post('evidencia_recuperacion/update/{idEvidencia_recuperacion}', [EvidenciaRecuperacionController::class, 'actionUpdate']);
            Route::delete('evidencia_recuperacion/delete/{idEvidencia_recuperacion}', [EvidenciaRecuperacionController::class, 'actionDelete']);

            // CICLO
            Route::match(['get', 'post'], 'academico/ciclo/getall', [CicloController::class, 'actionGetall']);
            Route::post('academico/ciclo/update/{idCiclo}', [CicloController::class, 'actionUpdate']);
            Route::delete('academico/ciclo/delete/{idCiclo}', [CicloController::class, 'actionDelete']);

            // ASIGNATURA
            Route::match(['get', 'post'], 'academico/asignatura', [AsignaturaController::class, 'actionIndex']);
            Route::match(['get', 'post'], 'academico/asignatura/insert', [AsignaturaController::class, 'actionInsert']);
            Route::post('academico/asignatura/update/{idAsignatura}', [AsignaturaController::class, 'actionUpdate']);
            Route::put('academico/asignatura/estado/{idAsignatura}', [AsignaturaController::class, 'actionEstado']);
            Route::delete('academico/asignatura/delete/{idAsignatura}', [AsignaturaController::class, 'actionDelete']);
            Route::get('asignatura/buscar', [AsignaturaController::class, 'actionBuscar']);

            // SEMESTRE ACADEMICO
            Route::match(['get', 'post'], 'academico/semestre_academico/getall', [SemestreAcademicoController::class, 'actionGetall']);
            Route::post('academico/semestre_academico/update/{idSemestreAcademico}', [SemestreAcademicoController::class, 'actionUpdate']);
            Route::delete('academico/semestre_academico/delete/{idSemestreAcademico}', [SemestreAcademicoController::class, 'actionDelete']);
            Route::post('academico/semestre_academico/cambiar_estado', [SemestreAcademicoController::class, 'cambiarEstado']);
            Route::post('academico/semestre_academico/marcar_actual', [SemestreAcademicoController::class, 'marcarComoActual']);

            // REPORTES
            Route::get('reportes', [\App\Http\Controllers\Admin\Reporte\ReporteController::class, 'index'])->name('admin.reportes.index');

            Route::get('reportes/estadisticas', [\App\Http\Controllers\Admin\Reporte\ReporteController::class, 'estadisticas'])->name('admin.permisos.estadisticas');

            Route::get('reportes/pdf/semestre/{semestre_id}', [\App\Http\Controllers\Admin\Reporte\ReporteController::class, 'pdfSemestre'])->name('admin.reportes.pdf.semestre');

            Route::get('reportes/pdf/descargar/semestre/{semestre_id}', [\App\Http\Controllers\Admin\Reporte\ReporteController::class, 'descargarPdfSemestre'])->name('admin.reportes.pdf.descargar.semestre');

            Route::get('reportes/pdf/docente/{docente_id}', [\App\Http\Controllers\Admin\Reporte\ReporteController::class, 'pdfDocente'])->name('admin.reportes.pdf.docente');

            Route::get('reportes/pdf/descargar/docente/{docente_id}', [\App\Http\Controllers\Admin\Reporte\ReporteController::class, 'descargarPdfDocente'])->name('admin.reportes.pdf.descargar.docente');

        });


    // ================= DOCENTE =================
    Route::middleware('role:docente')
        ->prefix('docente')
        ->group(function () {

            Route::get('/', function () {
                return view('docente.index');
            });

            // SEGUIMIENTO DE PERMISO
            Route::get('seguimiento_permiso', [SeguimientoPermisoController::class, 'actionSeguimientoPermiso']);
            Route::get('seguimiento_permiso/get/{idPermiso}', [SeguimientoPermisoController::class, 'actionGetPermiso']);
            Route::post('seguimiento_permiso/update/{idPermiso}', [SeguimientoPermisoController::class, 'actionUpdate']);


            // PERMISO
            Route::get('permiso', [DocentePermisoController::class, 'actionPermiso']);
            Route::get('permiso/{id}', [DocentePermisoController::class, 'actionShow']);
            Route::match(['get', 'post'], 'permiso/insert', [DocentePermisoController::class, 'actionInsert']);
            Route::post('permiso/update/{idPermiso}', [DocentePermisoController::class, 'actionUpdate']);
            Route::delete('permiso/delete/{idPermiso}', [DocentePermisoController::class, 'actionDelete']);

            // PLAN DE RECUPERACIÓN
            Route::get('plan_recuperacion', [DocentePlanRecuperacionController::class, 'actionPlanRecuperacion']);
            Route::get('plan_recuperacion/progreso/{idPermiso}', [DocentePlanRecuperacionController::class, 'actionProgreso']);
            Route::get('plan_recuperacion/{id}', [DocentePlanRecuperacionController::class, 'actionShow']);
            Route::match(['get', 'post'], 'plan_recuperacion/insert', [DocentePlanRecuperacionController::class, 'actionInsert']);
            Route::post('plan_recuperacion/update/{idPlan_recuperacion}', [DocentePlanRecuperacionController::class, 'actionUpdate']);
            Route::patch('plan_recuperacion/aprobar/{idPlan_recuperacion}', [DocentePlanRecuperacionController::class, 'actionAprobar']);
            Route::delete('plan_recuperacion/delete/{idPlan_recuperacion}', [DocentePlanRecuperacionController::class, 'actionDelete']);

            // SESION DE RECUPERACION
            Route::get('sesion_recuperacion', [DocenteSesionRecuperacionController::class, 'actionSesionRecuperacion']);
            Route::get('sesion_recuperacion/{id}', [DocenteSesionRecuperacionController::class, 'actionShow']);
            Route::match(['get', 'post'], 'sesion_recuperacion/insert', [DocenteSesionRecuperacionController::class, 'actionInsert']);
            Route::post('sesion_recuperacion/update/{idSesion_recuperacion}', [DocenteSesionRecuperacionController::class, 'actionUpdate']);
            Route::post('sesion_recuperacion/update-estado/{idSesion_recuperacion}', [DocenteSesionRecuperacionController::class, 'actionUpdateEstado']);
            Route::delete('sesion_recuperacion/delete/{idSesion_recuperacion}', [DocenteSesionRecuperacionController::class, 'actionDelete']);

            // EVIDENCIA DE RECUPERACION
            Route::get('evidencia_recuperacion', [DocenteEvidenciaRecuperacionController::class, 'actionEvidenciaRecuperacion']);
            Route::get('evidencia_recuperacion/ver/{id}', [DocenteEvidenciaRecuperacionController::class, 'actionVerEvidencia'])->name('evidencia.ver');
            Route::get('evidencia_recuperacion/{id}', [DocenteEvidenciaRecuperacionController::class, 'actionShow']);
            Route::post('evidencia_recuperacion/insert', [DocenteEvidenciaRecuperacionController::class, 'actionInsert'])->name('evidencia.store');
            Route::post('evidencia_recuperacion/update/{idEvidencia_recuperacion}', [DocenteEvidenciaRecuperacionController::class, 'actionUpdate']);
            Route::delete('evidencia_recuperacion/delete/{idEvidencia_recuperacion}', [DocenteEvidenciaRecuperacionController::class, 'actionDelete']);
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
