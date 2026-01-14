@extends('admin.template.layout')

@section('titleGeneral', 'Reportes de Permisos')

@section('sectionGeneral')
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h2><i class="fas fa-chart-bar mr-2"></i>Reportes de Permisos Docentes</h2>
                    <p class="text-muted">Genera reportes detallados de permisos por semestre y docente</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4" id="statsContainer" style="display: none;">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalPermisos">0</h3>
                            <p>Total Permisos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="permisosActivos">0</h3>
                            <p>Permisos Activos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="docentesConPermisos">0</h3>
                            <p>Docentes con Permisos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="promedioDias">0</h3>
                            <p>Promedio de Días</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <button class="btn btn-primary" onclick="showQuickStats()">
                        <i class="fas fa-chart-pie mr-2"></i>Ver Estadísticas
                    </button>
                </div>
            </div>

            <!-- Reports Grid -->
            <div class="row">
                <!-- Reporte por Semestre -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fas fa-calendar-check mr-2"></i>Reporte por Semestre</h3>
                        </div>
                        <div class="card-body">
                            <p>Genera un informe completo de todos los permisos otorgados durante un semestre específico.
                            </p>

                            <form id="frmReporteSemestre">
                                <div class="form-group">
                                    <label>Seleccionar Semestre</label>
                                    <select id="semestre_id" name="semestre_id" class="form-control" required>
                                        <option value="">-- Seleccione un semestre --</option>
                                        @foreach($semestres as $semestre)
                                            <option value="{{ $semestre->IdSemestreAcademico }}">
                                                {{ $semestre->codigo_Academico }} - {{ $semestre->anio_academico }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="btn-group btn-block">
                                    <div class="d-flex" style="gap: 10px;">
                                        <button type="button" class="btn btn-info" onclick="vistaPreviewPdfSemestre()">
                                            <i class="fas fa-eye"></i> Vista Previa
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="descargarPdfSemestre()">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Reporte por Docente -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Reporte por Docente</h3>
                        </div>
                        <div class="card-body">
                            <p>Genera un historial completo de permisos para un docente específico.</p>

                            <form id="frmReporteDocente">
                                <div class="form-group">
                                    <label>Seleccionar Docente</label>
                                    <select id="docente_id" name="docente_id" class="form-control" required>
                                        <option value="">-- Seleccione un docente --</option>
                                        @foreach($docentes as $docente)
                                            <option value="{{ $docente->idDocente }}">
                                                {{ $docente->user->last_name }}, {{ $docente->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Seleccionar Semestre (Opcional)</label>
                                    <select id="docente_semestre_id" name="semestre_id" class="form-control">
                                        <option value="">-- Todos los semestres --</option>
                                        @foreach($semestres as $semestre)
                                            <option value="{{ $semestre->IdSemestreAcademico }}">
                                                {{ $semestre->codigo_Academico }} - {{ $semestre->anio_academico }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="btn-group btn-block">
                                    <div class="d-flex" style="gap: 10px;">
                                        <button type="button" class="btn btn-info" onclick="vistaPreviewPdfDocente()">
                                            <i class="fas fa-eye"></i> Vista Previa
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="descargarPdfDocente()">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/reporte/permisos.js') }}"></script>
    <script src="{{ asset('viewresources/admin/reporte/permisoDocente.js') }}"></script>
    <script>
        // Mostrar estadísticas rápidas
        function showQuickStats() {
            const statsContainer = document.getElementById('statsContainer');
            if (statsContainer.style.display === 'none') {
                statsContainer.style.display = 'flex';
                loadStats();
            } else {
                statsContainer.style.display = 'none';
            }
        }

        // Cargar estadísticas
        async function loadStats() {
            try {
                const response = await fetch('{{ url("admin/reportes/estadisticas") }}');
                const data = await response.json();

                document.getElementById('totalPermisos').textContent = data.totalPermisos || 0;
                document.getElementById('permisosActivos').textContent = data.permisosActivos || 0;
                document.getElementById('docentesConPermisos').textContent = data.docentesConPermisos || 0;
                document.getElementById('promedioDias').textContent = data.promedioDias || 0;
            } catch (error) {
                console.error('Error cargando estadísticas:', error);
            }
        }
    </script>
@endsection