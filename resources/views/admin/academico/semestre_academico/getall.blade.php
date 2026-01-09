@extends('template.layout')

@section('titleGeneral', 'Semestres Académicos')

@section('sectionGeneral')

    <style>
        .card-borde-semestres {
            background: #ffffff;
            border-radius: 8px;
            border: 2px solid rgba(0, 139, 220, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 0;
        }

        .thead-custom th {
            background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
            color: white;
            font-weight: 600;
            padding: 12px 10px;
            border-bottom: 3px solid #006fa8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-responsive {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
        }
    </style>

    <section class="content">

        <div class="container-fluid">

            <!-- FORMULARIO -->
            <div class="card card-primary card-borde-semestres">
                <div class="card-header">
                    <h3 class="card-title">Registrar Semestre Académico</h3>
                </div>

                <div class="card-body">

                    <form id="frmSemestreAcademicoInsert" action="{{ url('admin/academico/semestre_academico/getall') }}"
                        method="post" onsubmit="event.preventDefault(); sendFrmSemestreAcademicoInsert();">

                        @csrf

                        <div class="row">

                            <!-- CÓDIGO ACADÉMICO -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Código Académico:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-code-branch"></i>
                                            </div>
                                        </div>
                                        <input type="text" name="codigo_Academico" class="form-control"
                                            placeholder="Ej: 2025-I" required>
                                    </div>
                                </div>
                            </div>

                            <!-- AÑO -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Año:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                        </div>
                                        <input type="number" name="anio_academico" class="form-control" placeholder="2025"
                                            min="2000" max="2100" required>
                                    </div>
                                </div>
                            </div>

                            <!-- FECHA INICIO -->
                            <div class="col-md-3">
                                <label>Fecha Inicio:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-play-circle"></i>
                                        </div>
                                    </div>
                                    <input type="date" name="FechaInicioAcademico" class="form-control" required>
                                </div>
                            </div>

                            <!-- FECHA FIN -->
                            <div class="col-md-3">
                                <label>Fecha Fin:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-flag-checkered"></i>
                                        </div>
                                    </div>
                                    <input type="date" name="FechaFinAcademico" class="form-control" required>
                                </div>
                            </div>

                            <!-- DESCRIPCIÓN -->
                            <div class="col-md-4">
                                <label>Descripción:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-align-left"></i>
                                        </div>
                                    </div>
                                    <input type="text" name="DescripcionAcademico" class="form-control"
                                        placeholder="Opcional...">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary" onclick="sendFrmSemestreAcademicoInsert();"
                                    id="btnGuardarSemestre">
                                    <i class="fa fa-save"></i> Registrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-4"></div>

            <!-- TABLA -->
            <div class="card card-borde-semestres">
                <div class="card-body table-responsive">
                    <h4 class="mb-3">Lista de Semestres Académicos</h4>
                    <table id="tablaExample2" class="table table-bordered table-striped">
                        <thead class="thead-custom">
                            <tr class="text-center">
                                <th class="all">N°</th>
                                <th>Código</th>
                                <th class="all">Año</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Estado</th>
                                <th>Actual</th>
                                <th class="none">Registrado</th>
                                <th class="all">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listSemestres as $sem)
                                <tr id="semRow{{ $sem->IdSemestreAcademico }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $sem->codigo_Academico }}</td>
                                    <td class="text-center">{{ $sem->anio_academico }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($sem->FechaInicioAcademico)->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($sem->FechaFinAcademico)->format('d/m/Y') }}</td>
                                    {{-- ESTADO --}}
                                    <td class="text-center">
                                        @if($sem->EstadoAcademico === 'Planificado')
                                            <span class="badge badge-info px-3 py-2">Planificado</span>
                                        @elseif($sem->EstadoAcademico === 'Activo')
                                            <span class="badge badge-success px-3 py-2">Activo</span>
                                        @elseif($sem->EstadoAcademico === 'Cerrado')
                                            <span class="badge badge-danger px-3 py-2">Cerrado</span>
                                        @else
                                            <span class="badge badge-secondary px-3 py-2">—</span>
                                        @endif
                                    </td>
                                    {{-- ES ACTUAL --}}
                                    <td class="text-center">
                                        @if($sem->EsActualAcademico)
                                            <span class="badge badge-success px-3 py-2">
                                                <i class="fas fa-check-circle"></i> Actual
                                            </span>
                                        @else
                                            <span class="badge badge-secondary px-3 py-2">
                                                <i class="fas fa-times-circle"></i> No
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $sem->created_at->format('d/m/Y H:i') }}</td>
                                    {{-- ACCIONES --}}
                                    <td class="text-center accionesSemestre">
                                        {{-- EDITAR --}}
                                        <button class="btn btn-sm btn-warning"
                                            onclick="showEditSemestre('{{ $sem->IdSemestreAcademico }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        {{-- ELIMINAR --}}
                                        <button class="btn btn-danger btn-sm"
                                            onclick="deleteSemestre({{ json_encode($sem->IdSemestreAcademico) }});">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        {{-- CAMBIAR A ACTIVO --}}
                                        @if($sem->EstadoAcademico === 'Planificado')
                                            <button class="btn btn-primary btn-sm btnCambiarEstado"
                                                onclick="cambiarEstadoSemestre('{{ $sem->IdSemestreAcademico }}', 'Activo')">
                                                <i class="fas fa-play"></i> Activar
                                            </button>
                                        @endif
                                        {{-- CERRAR --}}
                                        @if($sem->EstadoAcademico === 'Activo')
                                            <button class="btn btn-danger btn-sm btnCambiarEstado"
                                                onclick="cambiarEstadoSemestre('{{ $sem->IdSemestreAcademico }}', 'Cerrado')">
                                                <i class="fas fa-lock"></i> Cerrar
                                            </button>
                                        @endif
                                        {{-- REABRIR --}}
                                        @if($sem->EstadoAcademico === 'Cerrado')
                                            <button class="btn btn-info btn-sm btnCambiarEstado"
                                                onclick="cambiarEstadoSemestre('{{ $sem->IdSemestreAcademico }}', 'Planificado')">
                                                <i class="fas fa-undo"></i> Reabrir
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL EDITAR -->
    <div class="modal fade" id="editSemestreModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg" style="border-radius: 12px;">

                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%); border-radius: 12px 12px 0 0;">
                    <h4 class="modal-title">Editar Semestre Académico</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="background-color: #f5fbff;">
                    <form id="editSemestreForm">

                        <input type="hidden" id="editIdSemestre">

                        <div class="row">

                            <div class="col-md-4">
                                <label>Código Académico</label>
                                <input type="text" class="form-control" id="editCodigo">
                            </div>

                            <div class="col-md-2">
                                <label>Año</label>
                                <input type="number" class="form-control" id="editAnio">
                            </div>

                            <div class="col-md-3">
                                <label>Fecha Inicio</label>
                                <input type="date" id="editInicio" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label>Fecha Fin</label>
                                <input type="date" id="editFin" class="form-control">
                            </div>

                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Descripción</label>
                                <input type="text" id="editDescripcion" class="form-control">
                            </div>

                        </div>

                    </form>
                </div>

                <div class="modal-footer" style="background:#eef7ff;border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" id="btnCancelarSemestre">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnActualizarSemestre">
                        Guardar cambios
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/semestre_academico/getall.js?v=24112025') }}"></script>
    <script src="{{ asset('viewresources/admin/semestre_academico/delete.js?v=24112025') }}"></script>
    <script src="{{ asset('viewresources/admin/semestre_academico/update.js?v=24112025') }}"></script>
    <script src="{{ asset('viewresources/admin/semestre_academico/cambiar_estado.js?v=24112025') }}"></script>
    <script src="{{ asset('viewresources/admin/semestre_academico/marcar_actual.js?v=24112025') }}"></script>
@endsection