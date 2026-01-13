<?php
namespace App\Http\Controllers\Admin\Usuarios;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class UsuarioController extends Controller
{
    public function actionAdminIndex()
    {
        // Total de permisos
        $totalPermisos = \App\Models\Permiso::count();

        // Permisos por estado
        $permisosAprobados = \App\Models\Permiso::where('estado_permiso', 'APROBADO')->count();
        $permisosPendientes = \App\Models\Permiso::where('estado_permiso', 'SOLICITADO')->count();
        $permisosRechazados = \App\Models\Permiso::where('estado_permiso', 'RECHAZADO')->count();

        // Permisos recientes (últimos 10)
        $permisosRecientes = \App\Models\Permiso::with(['docente.user', 'tipoPermiso'])
            ->orderBy('fecha_solicitud', 'desc')
            ->take(10)
            ->get();

        // Permisos de hoy
        $permisosHoy = \App\Models\Permiso::whereDate('fecha_inicio', \Carbon\Carbon::today())->count();

        // Permisos de esta semana
        $permisosSemana = \App\Models\Permiso::whereBetween('fecha_inicio', [
            \Carbon\Carbon::now()->startOfWeek(),
            \Carbon\Carbon::now()->endOfWeek()
        ])->count();

        // Permisos de este mes
        $permisosMes = \App\Models\Permiso::whereMonth('fecha_inicio', \Carbon\Carbon::now()->month)
            ->whereYear('fecha_inicio', \Carbon\Carbon::now()->year)
            ->count();

        // Eventos para el calendario
        $eventosCalendario = \App\Models\Permiso::with(['docente.user', 'tipoPermiso'])
            ->get()
            ->map(function ($permiso) {
                $color = match ($permiso->estado_permiso) {
                    'APROBADO' => '#28a745',
                    'SOLICITADO' => '#ffc107',
                    'RECHAZADO' => '#dc3545',
                    'EN_RECUPERACION' => '#17a2b8',
                    'RECUPERADO' => '#6c757d',
                    'CERRADO' => '#343a40',
                    default => '#007bff'
                };

                return [
                    'id' => $permiso->id_permiso,
                    'title' => ($permiso->docente->nombre ?? 'N/A') . ' - ' . ($permiso->tipoPermiso->nombre ?? 'N/A'),
                    'start' => \Carbon\Carbon::parse($permiso->fecha_inicio)->format('Y-m-d'),
                    'end' => $permiso->fecha_fin ? \Carbon\Carbon::parse($permiso->fecha_fin)->format('Y-m-d') : \Carbon\Carbon::parse($permiso->fecha_inicio)->format('Y-m-d'),
                    'backgroundColor' => $color,
                    'borderColor' => $color
                ];
            });

        return view('admin.index', compact(
            'totalPermisos',
            'permisosAprobados',
            'permisosPendientes',
            'permisosRechazados',
            'permisosRecientes',
            'permisosHoy',
            'permisosSemana',
            'permisosMes',
            'eventosCalendario'
        ));
    }

    public function actionIndex()
    {
        $usuarios = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })
            ->where('id', '!=', auth()->id()) // Excluir usuario autenticado
            ->where('status', 'active') // Solo usuarios activos
            ->with('roles')
            ->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function actionUsuariosRoles()
    {
        $usuarios = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })
            ->where('id', '!=', auth()->id()) // Excluir usuario autenticado
            ->where('status', 'active') // Solo usuarios activos
            ->with('roles')
            ->get();

        return view('admin.usuarios.usuarios_roles', compact('usuarios'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:150', 'unique:users,email'],
            'document_type' => ['required', Rule::in(['DNI', 'PASAPORTE', 'CE'])],
            'document_number' => ['required', 'string', 'min:8', 'max:20', 'unique:users,document_number'],
            'phone' => ['required', 'regex:/^[0-9]{9}$/'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('usuarios', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'image' => $imagePath,
            'password' => Hash::make($request->document_number),
            'status' => 'active',
        ]);

        // Devolver datos completos en JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Usuario creado correctamente. La contraseña inicial es su número de documento.',
            'data' => $user->load('roles') // incluir roles para JS
        ]);
    }

    public function actionAsignarRoles()
    {
        $usuarios = User::where('id', '!=', auth()->id())
            ->with('roles')
            ->get();
        $roles = Role::all();

        return view('admin.usuarios.asignar_roles', compact('usuarios', 'roles'));
    }

    public function storeAsignarRoles(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        // ❌ VALIDACIÓN LARAVEL
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::withCount('roles')->findOrFail($request->user_id);

        // 🚫 SOLO UN ROL
        if ($user->roles_count >= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Este usuario ya tiene un rol asignado.'
            ], 422);
        }

        // ✅ ASIGNAR ROL
        $user->roles()->attach($request->role_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Rol asignado correctamente.'
        ]);
    }

    public function listarUsuariosRoles()
    {
        $usuarios = User::whereHas('roles')
            ->where('id', '!=', auth()->id())
            ->with('roles')
            ->get();

        return response()->json([
            'data' => $usuarios->map(function ($u, $i) {
                return [
                    $i + 1,
                    $u->name . ' ' . $u->last_name,
                    $u->roles->pluck('name')->map(function ($r) {
                        return ucfirst($r);
                    })->implode(', '),
                    '<button class="btn btn-sm btn-danger" onclick="deleteRol(' . $u->id . ')">
                        <i class="fas fa-trash"></i>
                    </button>'
                ];
            })
        ]);
    }

    public function actionUpdate(Request $request, $idUsuario)
    {
        try {
            $usuario = User::findOrFail($idUsuario);

            // Validación solo de los campos editables
            $request->validate([
                'name' => ['required', 'string', 'max:100'],
                'last_name' => ['nullable', 'string', 'max:100'],
                'phone' => ['nullable', 'regex:/^[0-9]{9}$/'],
                'gender' => ['nullable', Rule::in(['male', 'female', 'other'])]
            ]);

            // Actualizar solo los campos permitidos
            $usuario->update([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'gender' => $request->gender,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Usuario actualizado correctamente.',
                'data' => $usuario
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Usuario no encontrado.'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function actionDesactivar($idUsuario)
    {
        try {
            $usuario = User::with('roles')->findOrFail($idUsuario);

            // Validar que el usuario no tenga roles asignados
            if ($usuario->roles->count() > 0) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'No se puede desactivar este usuario porque tiene un rol asignado. Por favor, elimine primero el rol del usuario.'
                ], 422);
            }

            // Cambiar estado a inactivo
            $usuario->update([
                'status' => 'inactive'
            ]);

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Usuario desactivado correctamente.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Usuario no encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Error al desactivar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function actionEliminarRol($idUsuario)
    {
        try {
            $usuario = User::with('roles')->findOrFail($idUsuario);

            // Verificar que el usuario tenga roles
            if ($usuario->roles->count() === 0) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Este usuario no tiene roles asignados.'
                ], 422);
            }

            // Verificar si el usuario tiene el rol de docente y tiene carga lectiva asignada
            $hasDocenteRole = $usuario->roles->contains('name', 'docente');

            if ($hasDocenteRole) {
                // Buscar si el usuario tiene un registro en la tabla docentes
                $docente = \DB::table('docentes')->where('user_id', $idUsuario)->first();

                if ($docente) {
                    // Verificar si tiene carga lectiva asignada
                    $tieneCargaLectiva = \DB::table('carga_lectiva')
                        ->where('IdDocente', $docente->idDocente)
                        ->exists();

                    if ($tieneCargaLectiva) {
                        return response()->json([
                            'success' => false,
                            'status' => 'error',
                            'message' => 'No se puede eliminar el rol de docente porque tiene carga lectiva asignada. Por favor, elimine primero las asignaciones de carga lectiva.'
                        ], 422);
                    }
                }
            }

            // Eliminar todos los roles del usuario
            $usuario->roles()->detach();

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Rol eliminado correctamente.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Usuario no encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Error al eliminar el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listarUsuarios()
    {
        $usuarios = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })
            ->where('id', '!=', auth()->id())
            ->where('status', 'active')
            ->with('roles')
            ->get();

        return response()->json([
            'data' => $usuarios->map(function ($u, $i) {
                // Género
                $gender = '-';
                if ($u->gender == 'male') {
                    $gender = '<span class="badge badge-info"><i class="fas fa-mars"></i> Masculino</span>';
                } elseif ($u->gender == 'female') {
                    $gender = '<span class="badge badge-pink"><i class="fas fa-venus"></i> Femenino</span>';
                } elseif ($u->gender == 'other') {
                    $gender = '<span class="badge badge-secondary"><i class="fas fa-genderless"></i> Otro</span>';
                }

                // Roles
                $roles = '';
                if ($u->roles->count() > 0) {
                    foreach ($u->roles as $rol) {
                        $badgeClass = 'badge-secondary';
                        if ($rol->name == 'admin')
                            $badgeClass = 'badge-danger';
                        elseif ($rol->name == 'docente')
                            $badgeClass = 'badge-primary';

                        $roles .= '<span class="badge ' . $badgeClass . '">' . ucfirst($rol->name) . '</span> ';
                    }
                } else {
                    $roles = '<span class="badge badge-warning">Sin rol</span>';
                }

                // Estado
                $status = $u->status === 'active'
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';

                // Botones
                $buttons = '
                    <button class="btn btn-sm btn-warning" onclick="showEditUsuario(\'' . $u->id . '\')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="' . $u->id . '" data-name="' . $u->name . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';

                return [
                    'DT_RowId' => 'usuarioRow' . $u->id, // ID para la fila
                    $i + 1,
                    $u->name . ($u->last_name ? ' ' . $u->last_name : ''),
                    $u->email,
                    $u->phone ?: '-',
                    $gender,
                    $roles,
                    $status,
                    $buttons
                ];
            })
        ]);
    }

}
