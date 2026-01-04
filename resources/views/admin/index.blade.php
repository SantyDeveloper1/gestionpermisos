@extends('template.layout')

@section('titleGeneral', 'Panel de Administración')

@section('sectionGeneral')

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0">� Panel de Administración</h2>
    </div>
    <div class="card-body">

        <h5 class="mb-3">👥 Lista de Usuarios y Roles</h5>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Roles Asignados</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }} {{ $usuario->last_name ?? '' }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @if($usuario->roles->count() > 0)
                                    @foreach($usuario->roles as $rol)
                                        <span class="badge bg-{{ $rol->name === 'admin' ? 'success' : 'primary' }}">
                                            {{ ucfirst($rol->name) }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">Sin rol</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $usuario->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($usuario->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No hay usuarios registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <hr>

        <h5 class="mb-3">📊 Resumen</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h3>{{ $usuarios->count() }}</h3>
                        <p class="mb-0">Total Usuarios</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h3>{{ $usuarios->filter(fn($u) => $u->roles->where('name', 'admin')->count() > 0)->count() }}</h3>
                        <p class="mb-0">Administradores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h3>{{ $usuarios->filter(fn($u) => $u->roles->where('name', 'docente')->count() > 0)->count() }}</h3>
                        <p class="mb-0">Docentes</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
