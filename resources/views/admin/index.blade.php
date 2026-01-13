@extends('admin.template.layout')

@section('titleGeneral', 'Panel de Administración')

@section('sectionGeneral')

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $totalPermisos }}</h3>
                <p>Permisos Solicitados</p>
              </div>
              <div class="icon">
                <i class="fas fa-clipboard-list"></i>
              </div>
              <a href="{{ route('admin.permisos.index') }}" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ $permisosAprobados }}</h3>
                <p>Permisos Aprobados</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <a href="{{ route('admin.permisos.aprobados') }}" class="small-box-footer">Ver aprobados <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $permisosPendientes }}</h3>
                <p>Permisos Pendientes</p>
              </div>
              <div class="icon">
                <i class="fas fa-clock"></i>
              </div>
              <a href="{{ route('admin.permisos.pendientes') }}" class="small-box-footer">Revisar pendientes <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ $permisosRechazados }}</h3>
                <p>Permisos Rechazados</p>
              </div>
              <div class="icon">
                <i class="fas fa-times-circle"></i>
              </div>
              <a href="{{ route('admin.permisos.rechazados') }}" class="small-box-footer">Ver rechazados <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        
        <!-- Quick Actions Section -->
        <div class="row mt-4">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Acciones Rápidas</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3 col-sm-6 col-12">
                    <a href="{{ route('admin.permisos.create') }}" class="btn btn-primary btn-block">
                      <i class="fas fa-plus mr-2"></i> Nuevo Permiso
                    </a>
                  </div>
                  <div class="col-md-3 col-sm-6 col-12">
                    <a href="{{ route('admin.docentes.index') }}" class="btn btn-success btn-block">
                      <i class="fas fa-users mr-2"></i> Ver Docentes
                    </a>
                  </div>
                  <div class="col-md-3 col-sm-6 col-12">
                    <a href="{{ route('admin.reportes.index') }}" class="btn btn-info btn-block">
                      <i class="fas fa-chart-bar mr-2"></i> Reportes
                    </a>
                  </div>
                  <div class="col-md-3 col-sm-6 col-12">
                    <a href="{{ route('admin.configuracion.index') }}" class="btn btn-secondary btn-block">
                      <i class="fas fa-cog mr-2"></i> Configuración
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Recent Permissions Table -->
        <div class="row mt-4">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Permisos Recientes</h3>
                <div class="card-tools">
                  <span class="badge badge-primary">Últimos 10 registros</span>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Docente</th>
                      <th>Tipo</th>
                      <th>Fecha</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($permisosRecientes as $permiso)
                    <tr>
                      <td>{{ $permiso->docente->nombre ?? 'N/A' }}</td>
                      <td>
                        <span class="badge badge-info">
                          {{ $permiso->tipoPermiso->nombre ?? 'N/A' }}
                        </span>
                      </td>
                      <td>{{ \Carbon\Carbon::parse($permiso->fecha_inicio)->format('d/m/Y') }}</td>
                      <td>
                        @if($permiso->estado_permiso == 'APROBADO')
                          <span class="badge badge-success">Aprobado</span>
                        @elseif($permiso->estado_permiso == 'SOLICITADO')
                          <span class="badge badge-warning">Pendiente</span>
                        @else
                          <span class="badge badge-danger">Rechazado</span>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('admin.permisos.show', $permiso->id_permiso) }}" class="btn btn-sm btn-info">
                          <i class="fas fa-eye"></i>
                        </a>
                        @if($permiso->estado_permiso == 'SOLICITADO')
                          <a href="{{ route('admin.permisos.edit', $permiso->id_permiso) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                          </a>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="5" class="text-center">No hay permisos recientes</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center">
                <a href="{{ route('admin.permisos.index') }}" class="btn btn-sm btn-default">
                  Ver todos los permisos
                </a>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->
        
        <!-- Calendar Widget -->
        <div class="row mt-4">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">Calendario de Permisos</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-8">
                    <div id="calendar"></div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-box bg-gradient-info">
                      <span class="info-box-icon"><i class="fas fa-calendar-day"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Permisos Hoy</span>
                        <span class="info-box-number">{{ $permisosHoy }}</span>
                        <div class="progress">
                          <div class="progress-bar" style="width: {{ min(100, ($permisosHoy / max($totalPermisos, 1)) * 100) }}%"></div>
                        </div>
                        <span class="progress-description">
                          {{ $permisosHoy }} de {{ $totalPermisos }} totales
                        </span>
                      </div>
                    </div>
                    
                    <div class="info-box bg-gradient-success">
                      <span class="info-box-icon"><i class="fas fa-calendar-week"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Permisos Esta Semana</span>
                        <span class="info-box-number">{{ $permisosSemana }}</span>
                        <div class="progress">
                          <div class="progress-bar" style="width: {{ min(100, ($permisosSemana / max($totalPermisos, 1)) * 100) }}%"></div>
                        </div>
                        <span class="progress-description">
                          {{ $permisosSemana }} de {{ $totalPermisos }} totales
                        </span>
                      </div>
                    </div>
                    
                    <div class="info-box bg-gradient-warning">
                      <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Permisos Este Mes</span>
                        <span class="info-box-number">{{ $permisosMes }}</span>
                        <div class="progress">
                          <div class="progress-bar" style="width: {{ min(100, ($permisosMes / max($totalPermisos, 1)) * 100) }}%"></div>
                        </div>
                        <span class="progress-description">
                          {{ $permisosMes }} de {{ $totalPermisos }} totales
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
      </div><!-- /.container-fluid -->
    </section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/main.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('adminlte/plugins/fullcalendar/main.min.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'es',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: @json($eventosCalendario),
      eventClick: function(info) {
        window.location.href = '/admin/permisos/' + info.event.id;
      }
    });
    calendar.render();
  });
</script>
@endpush
