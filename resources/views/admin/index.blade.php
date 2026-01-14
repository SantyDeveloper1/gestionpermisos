@extends('admin.template.layout')
@section('titleGeneral', 'Dashboard - Sistema de Permisos')
@section('sectionGeneral')

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Small boxes (Stat box) - Manteniendo el diseño anterior -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
          <div class="inner">
            <h3>{{ $totalPermisos }}</h3>
            <p>Permisos Totales</p>
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
            @php
              $porcentajeAprobados = $totalPermisos > 0 ? round(($permisosAprobados / $totalPermisos) * 100) : 0;
            @endphp
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
        <div class="small-box bg-gradient-info">
          <div class="inner">
            <h3>{{ $permisosHoy }}</h3>
            <p>Actividad Hoy</p>
          </div>
          <div class="icon">
            <i class="fas fa-calendar-day"></i>
          </div>
          <a href="{{ route('admin.permisos.index', ['fecha' => date('Y-m-d')]) }}" class="small-box-footer">Ver actividad <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
    </div>
    <!-- /.row -->

    <!-- Main Content Row -->
    <div class="row">
      <!-- Chart & Recent Activity -->
      <div class="col-xl-8 col-lg-7">

        <!-- Recent Permissions -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Permisos Recientes</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
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
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar-circle-sm bg-primary text-white mr-3">
                          {{ substr($permiso->docente->nombre ?? 'N/A', 0, 1) }}
                        </div>
                        <div>
                          <div class="font-weight-bold">{{ $permiso->docente->nombre ?? 'N/A' }}</div>
                          <div class="text-xs text-muted">{{ $permiso->docente->email ?? '' }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge badge-pill badge-info">
                        {{ $permiso->tipoPermiso->nombre ?? 'N/A' }}
                      </span>
                    </td>
                    <td>
                      <div class="text-xs">{{ \Carbon\Carbon::parse($permiso->fecha_inicio)->format('d/m/Y') }}</div>
                      <div class="text-xs text-muted">
                        {{ \Carbon\Carbon::parse($permiso->created_at)->diffForHumans() }}
                      </div>
                    </td>
                    <td>
                      @if($permiso->estado_permiso == 'APROBADO')
                        <span class="badge badge-success">
                          <i class="fas fa-check-circle mr-1"></i> Aprobado
                        </span>
                      @elseif($permiso->estado_permiso == 'SOLICITADO')
                        <span class="badge badge-warning">
                          <i class="fas fa-clock mr-1"></i> Pendiente
                        </span>
                      @else
                        <span class="badge badge-danger">
                          <i class="fas fa-times-circle mr-1"></i> Rechazado
                        </span>
                      @endif
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <a href="{{ route('admin.permisos.show', $permiso->id_permiso) }}" 
                           class="btn btn-sm btn-outline-info" title="Ver detalles">
                          <i class="fas fa-eye"></i>
                        </a>
                        @if($permiso->estado_permiso == 'SOLICITADO')
                          <a href="{{ route('admin.permisos.edit', $permiso->id_permiso) }}" 
                             class="btn btn-sm btn-outline-warning" title="Revisar">
                            <i class="fas fa-edit"></i>
                          </a>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#">
                            <i class="fas fa-file-pdf mr-2"></i> Generar PDF
                          </a>
                          <a class="dropdown-item" href="#">
                            <i class="fas fa-envelope mr-2"></i> Enviar recordatorio
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="5" class="text-center py-4">
                      <div class="text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No hay permisos recientes</p>
                      </div>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            <div class="text-center mt-3">
              <a href="{{ route('admin.permisos.index') }}" class="btn btn-primary">
                <i class="fas fa-list mr-2"></i> Ver todos los permisos
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-xl-4 col-lg-5">
        <!-- Quick Actions -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12 mb-3">
                <a href="{{ route('admin.permisos.create') }}" class="btn btn-primary btn-block btn-icon-split">
                  <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                  </span>
                  <span class="text">Nuevo Permiso</span>
                </a>
              </div>
              <div class="col-12 mb-3">
                <a href="{{ route('admin.permisos.pendientes') }}" class="btn btn-warning btn-block btn-icon-split">
                  <span class="icon text-white-50">
                    <i class="fas fa-clock"></i>
                  </span>
                  <span class="text">Revisar Pendientes</span>
                  @if($permisosPendientes > 0)
                    <span class="badge badge-light ml-2">{{ $permisosPendientes }}</span>
                  @endif
                </a>
              </div>
              <div class="col-12 mb-3">
                <a href="{{ route('admin.docentes.index') }}" class="btn btn-success btn-block btn-icon-split">
                  <span class="icon text-white-50">
                    <i class="fas fa-users"></i>
                  </span>
                  <span class="text">Gestión de Docentes</span>
                </a>
              </div>
              <div class="col-12 mb-3">
                <a href="{{ route('admin.reportes.index') }}" class="btn btn-info btn-block btn-icon-split">
                  <span class="icon text-white-50">
                    <i class="fas fa-chart-bar"></i>
                  </span>
                  <span class="text">Reportes y Estadísticas</span>
                </a>
              </div>
              <div class="col-12">
                <a href="{{ route('admin.configuracion.index') }}" class="btn btn-secondary btn-block btn-icon-split">
                  <span class="icon text-white-50">
                    <i class="fas fa-cog"></i>
                  </span>
                  <span class="text">Configuración</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Summary -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Resumen de Estadísticas</h6>
          </div>
          <div class="card-body">
            <div class="mb-4">
              <div class="small font-weight-bold text-gray-800 mb-1">
                Permisos Esta Semana
                <span class="float-right">{{ $permisosSemana }}</span>
              </div>
              <div class="progress">
                <div class="progress-bar bg-info" role="progressbar" 
                     style="width: {{ min(100, ($permisosSemana / max($totalPermisos, 1)) * 100) }}%"></div>
              </div>
            </div>
            
            <div class="mb-4">
              <div class="small font-weight-bold text-gray-800 mb-1">
                Permisos Este Mes
                <span class="float-right">{{ $permisosMes }}</span>
              </div>
              <div class="progress">
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: {{ min(100, ($permisosMes / max($totalPermisos, 1)) * 100) }}%"></div>
              </div>
            </div>
            
            <div class="mb-4">
              <div class="small font-weight-bold text-gray-800 mb-1">
                Tasa de Aprobación
                <span class="float-right">{{ $porcentajeAprobados }}%</span>
              </div>
              <div class="progress">
                <div class="progress-bar bg-primary" role="progressbar" 
                     style="width: {{ $porcentajeAprobados }}%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Calendar -->
        <div class="card shadow">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Calendario</h6>
          </div>
          <div class="card-body">
            <div id="mini-calendar"></div>
            <div class="mt-3">
              <h6 class="font-weight-bold text-gray-800">Próximos Eventos</h6>
              @forelse($eventosCalendario as $evento)
              <div class="card border-left-info shadow-sm my-2">
                <div class="card-body py-2">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase">
                        {{ $evento['title'] }}
                      </div>
                      <div class="text-xs text-muted">
                        {{ \Carbon\Carbon::parse($evento['start'])->format('d/m/Y') }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @empty
              <p class="text-muted text-center py-3">No hay eventos próximos</p>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('styles')
<style>
.avatar-circle-sm {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

#mini-calendar {
  max-width: 100%;
  margin: 0 auto;
}

.fc .fc-toolbar-title {
  font-size: 1.1em !important;
}

.fc .fc-button {
  padding: 0.25em 0.5em !important;
  font-size: 0.85em !important;
}

.btn-icon-split {
  position: relative;
  padding-left: 3.5rem;
}

.btn-icon-split .icon {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 3rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chart-area {
  position: relative;
  height: 20rem;
  width: 100%;
}

/* Estilo para las small-box del diseño original */
.small-box {
  border-radius: .25rem;
  box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
  position: relative;
  display: block;
  margin-bottom: 20px;
}

.small-box > .inner {
  padding: 10px;
}

.small-box h3 {
  font-size: 2.2rem;
  font-weight: bold;
  margin: 0 0 10px 0;
  white-space: nowrap;
  padding: 0;
}

.small-box p {
  font-size: 1rem;
  margin-bottom: 5px;
}

.small-box .icon {
  position: absolute;
  top: 15px;
  right: 15px;
  z-index: 0;
  font-size: 70px;
  color: rgba(0,0,0,0.15);
}

.small-box .small-box-footer {
  position: relative;
  text-align: center;
  padding: 8px 0;
  color: rgba(255,255,255,0.8);
  display: block;
  z-index: 10;
  background: rgba(0,0,0,0.1);
  text-decoration: none;
  border-radius: 0 0 .25rem .25rem;
}

.small-box .small-box-footer:hover {
  color: #fff;
  background: rgba(0,0,0,0.15);
}

.small-box .progress {
  background: rgba(0,0,0,0.2);
  margin: 5px -10px 5px -10px;
  height: 2px;
}

.small-box .progress .progress-bar {
  background-color: #fff;
}

.small-box.bg-gradient-info {
  background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
}
</style>
<link rel="stylesheet" href="{{ asset('adminlte/plugins/fullcalendar/main.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('adminlte/plugins/fullcalendar/main.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Mini Calendar
  var calendarEl = document.getElementById('mini-calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    headerToolbar: {
      left: 'title',
      right: 'prev,next'
    },
    height: 300,
    events: @json($eventosCalendario),
    eventClick: function(info) {
      window.location.href = '/admin/permisos/' + info.event.id;
    }
  });
  calendar.render();

  // Area Chart
  var ctx = document.getElementById("myAreaChart");
  if (ctx) {
    var myLineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        datasets: [{
          label: "Permisos",
          lineTension: 0.3,
          backgroundColor: "rgba(78, 115, 223, 0.05)",
          borderColor: "rgba(78, 115, 223, 1)",
          pointRadius: 3,
          pointBackgroundColor: "rgba(78, 115, 223, 1)",
          pointBorderColor: "rgba(78, 115, 223, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
          pointHoverBorderColor: "rgba(78, 115, 223, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          data: [65, 59, 80, 81, 56, 55, 40, 45, 60, 75, 65, 70],
        }],
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            ticks: {
              maxTicksLimit: 5,
              padding: 10,
            },
            grid: {
              color: "rgb(234, 236, 244)",
              drawBorder: false,
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  }
});
</script>
@endpush