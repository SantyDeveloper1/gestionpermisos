@extends('template.layout')
@section('titleGeneral', 'Lista de asignaturas')
@section('sectionGeneral')

    <style>
        .card-borde-asig {
            background: #ffffff;
            border-radius: 8px;

            /* borde celeste suave */
            border: 2px solid rgba(0, 139, 255, 0.3);

            /* sombra suave */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);

            padding: 0;
        }

        /* Estilo moderno para thead */
        .thead-custom th {
            background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
            color: white;
            font-weight: 600;
            padding: 12px 10px;
            border-bottom: 3px solid #006fa8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .thead-custom tr {
            border-radius: 12px 12px 0 0;
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        /* Sombra elegante */
        .table-responsive {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
        }   
    </style>

    <section class="content">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ url('admin/academico/asignatura/insert') }}" class="btn btn-primary mr-2">
                    <i class="fas fa-plus"></i> Agregar asignatura
                </a>

                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalSubirArchivo">
                    <i class="fas fa-upload"></i> Subir Archivo
                </button>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    @foreach ($listAsignaturas as $idCiclo => $asignaturas)
                        <div class="card card-borde-asig mb-4">
                            <div class="card-header bg-light text-dark">
                                <h5 class="mb-0">
                                    Ciclo: {{ $asignaturas->first()->ciclo->NombreCiclo ?? $idCiclo }}
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered mb-0">
                                    <thead class="thead-custom">
                                        <tr class="text-center">
                                            <th>Nro.</th>
                                            <th>Código</th>
                                            <th>Asignatura</th>
                                            <th>Créditos</th>
                                            <th>HT</th>
                                            <th>HP</th>
                                            <th>Tipo</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($asignaturas as $asig)
                                            <tr id="asigRow{{ $asig->idAsignatura }}" 
                                                data-id-ciclo="{{ $asig->IdCiclo }}" 
                                                data-id-plan="{{ $asig->idPlanEstudio }}">
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ $asig->codigo_asignatura }}</td>
                                                <td class="tdNombre">{{ $asig->nom_asignatura }}</td>
                                                <td class="text-center tdCreditos">{{ $asig->creditos }}</td>
                                                <td class="text-center tdHT">{{ $asig->horas_teoria }}</td>
                                                <td class="text-center tdHP">{{ $asig->horas_practica }}</td>

                                                <td class="text-center tdTipo">
                                                    <span class="badge badge-info">{{ $asig->tipo }}</span>
                                                </td>

                                                <td class="text-center tdEstado">
                                                    @if ($asig->estado === 'Activo')
                                                        <span class="badge badge-success">Activo</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactivo</span>
                                                    @endif
                                                </td>

                                                <td class="text-center">

                                                    <button class="btn btn-sm btn-warning"
                                                        onclick="showEditAsignatura('{{ $asig->idAsignatura }}')"
                                                        data-toggle="modal" data-target="#editAsignaturaModal"
                                                        {{ $asig->estado == 'Inactivo' ? 'disabled' : '' }}>
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <button class="btn btn-info"
                                                        onclick="toggleEstadoAsignatura('{{ $asig->idAsignatura }}', '{{ $asig->nom_asignatura }}')">
                                                        @if ($asig->estado == 'Activo')
                                                            <i class="fas fa-toggle-on"></i>
                                                        @else
                                                            <i class="fas fa-toggle-off"></i>
                                                        @endif
                                                    </button>

                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="deleteAsignatura('{{ $asig->idAsignatura }}')"
                                                        {{ $asig->estado == 'Inactivo' ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

    </section>


    <!-- Modal Editar Asignatura -->
    <div class="modal fade" id="editAsignaturaModal" tabindex="-1" role="dialog" aria-labelledby="editAsignaturaModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="editAsignaturaModalLabel">Editar Asignatura</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <form id="editAsigForm">
                        <div class="row">
                            <!-- Nombre -->
                            <div class="col-md-6 form-group">
                                <label for="txtNomAsig">Nombre</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" id="txtNomAsig" name="nom_asignatura">
                                </div>
                            </div>

                            <!-- Créditos -->
                            <div class="col-md-6 form-group">
                                <label for="txtCreditos">Créditos</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                    </div>
                                    <input type="number" class="form-control" id="txtCreditos" name="creditos" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Horas Teoría -->
                            <div class="col-md-6 form-group">
                                <label for="txtHT">Horas Teoría</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                    <input type="number" class="form-control" id="txtHT" name="horas_teoria" min="0">
                                </div>
                            </div>

                            <!-- Horas Práctica -->
                            <div class="col-md-6 form-group">
                                <label for="txtHP">Horas Práctica</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-tools"></i>
                                        </div>
                                    </div>
                                    <input type="number" class="form-control" id="txtHP" name="horas_practica" min="0">
                                </div>
                            </div>
                        </div>

                        
                        <div class="row">
                            <!-- CICLO -->
                            <div class="col-md-6 form-group">
                                <label for="IdCiclo">Ciclo</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-layer-group"></i></div>
                                    </div>
                                    <select class="form-control" id="IdCiclo" name="IdCiclo">
                                        <option value="" disabled selected>Seleccione...</option>
                                        @foreach($ciclos as $c)
                                            <option value="{{ $c->IdCiclo }}">
                                                {{ $c->NombreCiclo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- TIPO ASIGNATURA -->
                            <div class="col-md-6 form-group">
                                <label for="tipo">Tipo de Asignatura</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-list-alt"></i>
                                        </div>
                                    </div>
                                    <select class="form-control" id="tipo" name="tipo">
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="GENERAL">General</option>
                                        <option value="ESPECIALIDAD">Especialidad</option>
                                        <option value="ESPECIFICO">Específico</option>
                                        <option value="ELECTIVO">Electivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnActualizarAsig">
                        Guardar cambios
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Estado Asignatura -->
    <div class="modal fade" id="estadoAsignaturaModal" tabindex="-1" role="dialog" aria-labelledby="estadoAsignaturaModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px;">

                <!-- Header con fondo degradado -->
                <div class="modal-header text-white"
                    style="background: linear-gradient(90deg, #0066ff, #003399); border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title" id="estadoAsignaturaModalLabel">
                        Cambiar Estado de la Asignatura
                        <span class="font-weight-bold text-warning" id="nombreAsignaturaEstado"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Cuerpo -->
                <div class="modal-body">

                    <p class="mb-2 font-weight-bold">Seleccione el nuevo estado:</p>

                    <!-- Radios personalizados -->
                    <div class="d-flex align-items-center mb-3">
                        <label class="mr-4 d-flex align-items-center" style="cursor: pointer;">
                            <input type="radio" name="estadoAsignatura" value="1" id="radioAsigActivo">
                            <span class="ml-2 text-success font-weight-bold">Activo</span>
                        </label>

                        <label class="d-flex align-items-center" style="cursor: pointer;">
                            <input type="radio" name="estadoAsignatura" value="0" id="radioAsigInactivo">
                            <span class="ml-2 text-danger font-weight-bold">Inactivo</span>
                        </label>
                    </div>

                    <!-- Caja de información dinámica -->
                    <div id="estadoMensajeAsig" class="p-3 rounded"
                        style="background-color: #e3f5ff; border-left: 4px solid #007bff;">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span class="ml-2">
                            Aquí aparecerá la descripción del estado seleccionado.
                        </span>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 py-2" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGuardarEstadoAsig" class="btn btn-primary px-4 py-2">Guardar Cambios</button>
                </div>

            </div>
        </div>
    </div>

    <style>
        input[type="radio"] {
            width: 18px;
            height: 18px;
        }

        #estadoMensajeAsig {
            background: #e8f4fc;
            /* celeste suave por defecto */
            color: #0c5460;
            /* azul oscuro legible */
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.4;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease-in-out;
        }

        #estadoMensajeAsig.success {
            background: #e6f9f0;
            /* verde suave */
            color: #155724;
            border-left: 4px solid #28a745;
        }

        #estadoMensajeAsig.danger {
            background: #fdecea;
            /* rojo suave */
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
    </style>

    <!-- #estilo para modal -->
    <style>
        /* HEADER */
        #editAsignaturaModal .modal-header {
            background: #008bdc;
            /* Celeste */
            color: white;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }

        /* BODY */
        #editAsignaturaModal .modal-body {
            background: #f5fbff;
            padding-top: 20px;
        }

        /* FOOTER */
        #editAsignaturaModal .modal-footer {
            background: #eef7ff;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        /* Inputs más bonitos */
        #editAsignaturaModal input,
        #editAsignaturaModal select {
            border: 1px solid rgba(0, 139, 220, 0.4);
        }

        #editAsignaturaModal .input-group-text {
            background: rgba(0, 139, 220, 0.2);
            color: #005f8f;
            font-weight: bold;
        }
    </style>
@endsection
@section('js')
    <script src="{{ asset('viewresources/admin/asignatura/update.js?=19122025') }}"></script>
    <script src="{{ asset('viewresources/admin/asignatura/delete.js?=19122025') }}"></script>
    <script src="{{ asset('viewresources/admin/asignatura/state.js?=19122025') }}"></script>
@endsection