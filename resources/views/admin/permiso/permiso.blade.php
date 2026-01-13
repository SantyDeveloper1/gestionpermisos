@extends('admin.template.layout')

@section('titleGeneral', 'Gestión de Permisos')

@section('sectionGeneral')

    <style>
        .card-borde {
            background: #ffffff;
            border-radius: 10px;
            border: 2px solid rgba(0, 139, 220, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .thead-custom th {
            background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .table-responsive {
            border-radius: 12px;
        }

        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .badge-solicitado {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            color: #d63031;
        }

        .badge-aprobado {
            background: linear-gradient(135deg, #a1ffce 0%, #faffd1 100%);
            color: #00b894;
        }

        .badge-rechazado {
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa8a8 100%);
            color: #d63031;
        }

        .badge-en_recuperacion {
            background: linear-gradient(135deg, #74b9ff 0%, #a29bfe 100%);
            color: #0984e3;
        }

        .badge-recuperado {
            background: linear-gradient(135deg, #55efc4 0%, #81ecec 100%);
            color: #00b894;
        }

        .badge-cerrado {
            background: linear-gradient(135deg, #636e72 0%, #b2bec3 100%);
            color: #2d3436;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
            border: none;
            border-radius: 6px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 139, 220, 0.3);
        }

        .btn-sm-custom {
            padding: 5px 10px;
            font-size: 0.875rem;
            border-radius: 4px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            margin: 0 2px;
        }

        .modal-header-gradient {
            background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .modal-content-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-control-custom {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control-custom:focus {
            border-color: #008BDC;
            box-shadow: 0 0 0 0.2rem rgba(0, 139, 220, 0.15);
        }

        .input-group-text-custom {
            background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
            color: white;
            border: none;
            border-radius: 6px 0 0 6px;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
            color: white;
            border-bottom: none;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0 !important;
        }

        .date-input-group {
            position: relative;
        }

        .date-input-group .form-control {
            padding-right: 40px;
        }

        .date-input-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #008BDC;
            pointer-events: none;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            height: 42px;
            padding: 6px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #008BDC;
        }

        .info-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #008BDC;
        }

        .info-card h6 {
            color: #2d3436;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .info-card p {
            color: #636e72;
            margin: 0;
            font-size: 0.9rem;
        }

        .timeline-container {
            position: relative;
            padding-left: 30px;
            margin: 20px 0;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #008BDC, #00A3E8);
        }

        .timeline-step {
            position: relative;
            margin-bottom: 20px;
            padding-left: 20px;
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #008BDC;
            border: 2px solid white;
            box-shadow: 0 0 0 3px rgba(0, 139, 220, 0.2);
        }

        .timeline-step.active::before {
            background: #00b894;
            box-shadow: 0 0 0 3px rgba(0, 184, 148, 0.2);
        }

        .timeline-step.completed::before {
            background: #00b894;
        }

        .timeline-content {
            background: white;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .btn-floating {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 139, 220, 0.3);
            font-size: 24px;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .btn-floating:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 139, 220, 0.4);
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 139, 220, 0.05);
        }

        .table-success {
            background-color: rgba(0, 184, 148, 0.3) !important;
            transition: background-color 0.3s ease;
        }

        .table tbody tr {
            transition: background-color 0.3s ease;
        }
    </style>

    <section class="content">
        <div class="container-fluid">

            <div class="text-right">
                <!-- Botón Agregar Permiso -->
                <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#nuevoPermisoModal">
                    <i class="fas fa-plus"></i> Agregar Permiso
                </button>
            </div>

            <!-- TABLA DE PERMISOS -->
            <div class="card card-borde">
                <div class="card-body table-responsive">

                    <h4 class="mb-3">
                        <i class="fas fa-list"></i> Permisos Registrados
                    </h4>

                    <table id="tablaExample2" class="table table-bordered table-striped">
                        <thead class="thead-custom">
                            <tr>
                                <th>N°</th>
                                <th>Docente</th>
                                <th>Tipo Permiso</th>
                                <th>Período</th>
                                <th>Duración</th>
                                <th>Plan de Recuperación</th>
                                <th>Estado</th>
                                <th class="none">Solicitud</th>
                                <th class="none">Creado</th>
                                <th class="none">Actualizado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($listPermisos as $permiso)
                                <tr id="permisoRow{{ $permiso->id_permiso }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $permiso->docente->user->last_name }}, {{ $permiso->docente->user->name }}</strong><br>
                                    </td>
                                    <td>{{ $permiso->tipoPermiso->nombre }}</td>
                                    <td class="text-center">
                                        <strong>{{ date('d/m/Y', strtotime($permiso->fecha_inicio)) }}</strong><br>
                                        <small class="text-muted">al</small><br>
                                        <strong>{{ date('d/m/Y', strtotime($permiso->fecha_fin)) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $permiso->dias_permiso }} días</span><br>
                                        <small class="text-muted">{{ $permiso->horas_afectadas }} horas</small>
                                    </td>
                                    <td class="text-center">
                                        @if($permiso->tipoPermiso->requiere_recupero)
                                            @if($permiso->planRecuperacion)
                                                @if($permiso->planRecuperacion->estado_plan == 'APROBADO')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Completada
                                                    </span>
                                                @elseif($permiso->planRecuperacion->estado_plan == 'PRESENTADO')
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-clock"></i> En Proceso
                                                    </span>
                                                @elseif($permiso->planRecuperacion->estado_plan == 'OBSERVADO')
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-exclamation-triangle"></i> Observado
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-file-alt"></i> Sin Plan
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-times-circle"></i> No Requiere
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-estado badge-{{ strtolower($permiso->estado_permiso) }}">
                                            {{ $permiso->estado_permiso }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <strong>Solicitado:</strong><br>
                                        {{ date('d/m/Y', strtotime($permiso->fecha_solicitud)) }}
                                        @if($permiso->fecha_resolucion)
                                            <br><strong>Resuelto:</strong><br>
                                            {{ date('d/m/Y', strtotime($permiso->fecha_resolucion)) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $permiso->created_at->format('d/m/Y') }}</strong><br>
                                        <small class="text-muted">{{ $permiso->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $permiso->updated_at->format('d/m/Y') }}</strong><br>
                                        <small class="text-muted">{{ $permiso->updated_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary btn-action"
                                            onclick="viewPermiso('{{ $permiso->id_permiso }}')" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <button class="btn btn-sm btn-warning btn-action"
                                            onclick="editPermiso('{{ $permiso->id_permiso }}')" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        @if($permiso->estado_permiso == 'SOLICITADO')
                                            <button class="btn btn-sm btn-success btn-action"
                                                onclick="aprobarPermiso('{{ $permiso->id_permiso }}')" title="Aprobar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif

                                        <button class="btn btn-sm btn-danger btn-action"
                                            onclick="deletePermiso('{{ $permiso->id_permiso }}')" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>
            </div>

        </div>
    </section>

    <!-- MODAL NUEVO PERMISO -->
    <div class="modal fade" id="nuevoPermisoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle mr-2"></i> Registrar Nuevo Permiso
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="frmPermisoInsert" action="{{ url('admin/permiso/insert') }}" method="POST"
                    onsubmit="event.preventDefault(); sendFrmPermisoInsert();">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row">
                            <!-- Docente -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Docente: <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text input-group-text-custom">
                                            <i class="fas fa-user-tie"></i>
                                        </span>
                                    </div>
                                    <select name="id_docente" class="form-control form-control-custom select2" required>
                                        <option value="">Seleccionar docente...</option>
                                        @foreach($docentes as $docente)
                                            <option value="{{ $docente->idDocente }}">
                                                {{ $docente->user->last_name }}, {{ $docente->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Tipo de Permiso -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Permiso: <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text input-group-text-custom">
                                            <i class="fas fa-tags"></i>
                                        </span>
                                    </div>
                                    <select name="id_tipo_permiso" class="form-control form-control-custom select2"
                                        required>
                                        <option value="">Seleccionar tipo...</option>
                                        @foreach($tipoPermisos as $tipo)
                                            <option value="{{ $tipo->id_tipo_permiso }}">
                                                {{ $tipo->nombre }}
                                                @if($tipo->con_goce_haber)
                                                    <span class="text-success"> (Con goce)</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fechas -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha Inicio: <span class="text-danger">*</span></label>
                                <div class="date-input-group">
                                    <input type="date" name="fecha_inicio" class="form-control form-control-custom"
                                        required>
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha Fin: <span class="text-danger">*</span></label>
                                <div class="date-input-group">
                                    <input type="date" name="fecha_fin" class="form-control form-control-custom" required>
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Días de Permiso: <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text input-group-text-custom">
                                            <i class="fas fa-calendar-day"></i>
                                        </span>
                                    </div>
                                    <input type="number" name="dias_permiso"
                                        class="form-control form-control-custom notValidate" min="1" max="365" required
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Horas Afectadas: <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text input-group-text-custom">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                    </div>
                                    <input type="number" name="horas_afectadas" class="form-control form-control-custom"
                                        step="0.5" min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Solicitud: <span class="text-danger">*</span></label>
                                <div class="date-input-group">
                                    <input type="date" name="fecha_solicitud" class="form-control form-control-custom"
                                        value="{{ date('Y-m-d') }}" required>
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                        @if($semestreActual)
                        <div class="row">
                            <!-- Semestre Académico Actual (solo lectura) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semestre Académico:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text input-group-text-custom">
                                            <i class="fas fa-calendar-check"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control form-control-custom" 
                                           value="{{ $semestreActual->codigo_Academico }} ({{ $semestreActual->anio_academico }})" 
                                           readonly>
                                    <input type="hidden" name="id_semestre_academico" value="{{ $semestreActual->IdSemestreAcademico }}">
                                </div>
                            </div>

                            <!-- Documento de sustento -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Documento de Sustento: <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text input-group-text-custom">
                                            <i class="fas fa-file-upload"></i>
                                        </span>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" name="documento_sustento" class="custom-file-input" 
                                               id="documentoSustento" required>
                                        <label class="custom-file-label" for="documentoSustento">Seleccionar archivo...</label>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Formatos permitidos: PDF, DOC, DOCX (Máx. 5MB)
                                </small>
                            </div>
                        </div>
                        @else
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    No hay un semestre académico activo. Por favor, active un semestre antes de registrar permisos.
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <!-- Motivo -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Motivo: <span class="text-danger">*</span></label>
                                <textarea name="motivo" class="form-control form-control-custom" rows="3"
                                    placeholder="Describa el motivo del permiso..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary-custom" id="btnGuardarPermiso">
                            <i class="fas fa-save mr-2"></i> Registrar Permiso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- MODAL DETALLE PERMISO -->
    <div class="modal fade" id="viewPermisoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content permiso-replica">

                <!-- HEADER -->
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-plane-departure text-primary mr-2"></i>
                        Detalle del Permiso
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body pt-3">

                    <!-- DOCENTE -->
                    <div class="docente-box">
                        <div class="avatar-circle">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="ml-3">
                            <h6 class="mb-0 font-weight-bold" id="viewDocenteNombre">
                                Jorge Pérez Castillo
                            </h6>
                            <small class="text-muted" id="viewDocenteInfo">
                                Matemáticas
                            </small><br>
                            <span class="badge badge-primary badge-pill mt-1" id="viewDocenteTipo">
                                Nombrado
                            </span>
                        </div>
                    </div>

                    <!-- INFO -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <small class="label">Tipo de Permiso</small>
                            <p class="value">
                                <span id="viewTipoPermiso">Comisión de Servicio</span>
                            </p>

                            <small class="label">Fecha Inicio</small>
                            <p class="value" id="viewFechaInicio">
                                31 de enero, 2024
                            </p>
                        </div>

                        <div class="col-md-6">
                            <small class="label">Días Solicitados</small>
                            <p class="value">
                                <strong id="viewDiasSolicitados">3</strong> días
                            </p>

                            <small class="label">Fecha Fin</small>
                            <p class="value" id="viewFechaFin">
                                02 de febrero, 2024
                            </p>
                        </div>
                    </div>

                    <!-- MOTIVO -->
                    <div class="motivo-card mt-3">
                        <small class="label">Motivo</small>
                        <p id="viewMotivo" class="mb-0">
                            Representación institucional en evento académico
                        </p>
                    </div>

                    <!-- ESTADOS -->
                    <div class="estado-box mt-4">
                        <div>
                            <small class="label">Estado del Permiso</small><br>
                            <span class="badge badge-success badge-pill px-3 py-2" id="viewEstado">
                                ✔ Aprobado
                            </span>
                        </div>
                        <div>
                            <small class="label">Estado de Recuperación</small><br>
                            <span class="badge badge-danger badge-pill px-3 py-2" id="viewEstadoRecuperacion">
                                Sin Plan
                            </span>
                        </div>
                    </div>

                    <!-- BOTÓN -->
                    <button class="btn btn-primary btn-block btn-lg mt-4" id="btnRegistrarPlan">
                        <i class="fas fa-file-alt mr-2"></i>
                        Registrar Plan de Recuperación
                    </button>

                </div>
            </div>
        </div>
    </div>



<style>
    .permiso-replica {
        border-radius: 16px;
        padding: 5px;
    }

    .docente-box {
        display: flex;
        align-items: center;
        background: #f8fafc;
        padding: 14px;
        border-radius: 12px;
    }

    .avatar-circle {
        width: 48px;
        height: 48px;
        background: #e5edff;
        color: #2563eb;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .label {
        font-size: 13px;
        color: #6b7280;
    }

    .value {
        font-size: 15px;
        font-weight: 500;
        color: #111827;
    }

    .motivo-card {
        background: #f9fafb;
        padding: 12px;
        border-radius: 10px;
    }

    .estado-box {
        display: flex;
        justify-content: space-between;
        background: #f8fafc;
        padding: 14px;
        border-radius: 12px;
    }

    .btn-lg {
        border-radius: 14px;
        font-weight: 600;
    }


</style>

    <!-- MODAL EDITAR ESTADO PERMISO -->
    <div class="modal fade" id="editPermisoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content shadow-lg border-0">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i> Editar Estado Permiso
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="frmPermisoEdit" onsubmit="event.preventDefault(); updatePermiso();">
                    @csrf
                    <input type="hidden" id="editIdPermiso" name="id_permiso">

                    <div class="modal-body">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">

                                <label class="font-weight-bold">Estado del Permiso</label>

                                <select name="estado_permiso" id="editEstadoPermiso"
                                    class="form-control custom-select" required>
                                    <option value="SOLICITADO">🟡 SOLICITADO</option>
                                    <option value="APROBADO">🟢 APROBADO</option>
                                    <option value="RECHAZADO">🔴 RECHAZADO</option>
                                    <option value="EN_RECUPERACION">🔵 EN RECUPERACIÓN</option>
                                    <option value="RECUPERADO">🟣 RECUPERADO</option>
                                    <option value="CERRADO">⚫ CERRADO</option>
                                </select>

                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Guardar cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/permiso/insert.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/permiso/detalle.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/permiso/update.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/permiso/delete.js?v=' . time()) }}"></script>
    
    <script>
        // Actualizar label del input file cuando se selecciona un archivo
        $(document).ready(function() {
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@endsection