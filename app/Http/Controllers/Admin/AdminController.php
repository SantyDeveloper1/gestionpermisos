<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\Docente;
use App\Models\User;
use App\Models\PlanRecuperacion;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Total de permisos
        $totalPermisos = Permiso::count();

        // Permisos por estado
        $permisosAprobados = Permiso::where('estado_permiso', 'APROBADO')->count();
        $permisosPendientes = Permiso::where('estado_permiso', 'SOLICITADO')->count();
        $permisosRechazados = Permiso::where('estado_permiso', 'RECHAZADO')->count();

        // Permisos recientes (últimos 10)
        $permisosRecientes = Permiso::with(['docente.user', 'tipoPermiso'])
            ->orderBy('fecha_solicitud', 'desc')
            ->take(10)
            ->get();

        // Permisos de hoy
        $permisosHoy = Permiso::whereDate('fecha_inicio', Carbon::today())->count();

        // Permisos de esta semana
        $permisosSemana = Permiso::whereBetween('fecha_inicio', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        // Permisos de este mes
        $permisosMes = Permiso::whereMonth('fecha_inicio', Carbon::now()->month)
            ->whereYear('fecha_inicio', Carbon::now()->year)
            ->count();

        // Estadísticas adicionales
        $totalDocentes = Docente::count();
        $totalUsuarios = User::count();
        $totalPlanesRecuperacion = PlanRecuperacion::count();

        // Eventos para el calendario
        $eventosCalendario = Permiso::with(['docente.user', 'tipoPermiso'])
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
                    'start' => Carbon::parse($permiso->fecha_inicio)->format('Y-m-d'),
                    'end' => $permiso->fecha_fin ? Carbon::parse($permiso->fecha_fin)->format('Y-m-d') : Carbon::parse($permiso->fecha_inicio)->format('Y-m-d'),
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
            'totalDocentes',
            'totalUsuarios',
            'totalPlanesRecuperacion',
            'eventosCalendario'
        ));
    }
}
