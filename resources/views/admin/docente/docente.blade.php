@extends('admin.template.layout')
@section('titleGeneral', 'Lista de Docentes')
@section('sectionGeneral')

    <style>
        .card-borde-doc {
            background: #ffffff;
            border-radius: 8px;
            border: 2px solid rgba(0, 139, 255, 0.3);
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

        .table {
            border-radius: 12px;
            overflow: hidden;
        }
    </style>

    <section class="content">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <!-- Botón Agregar Docente -->
                <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#modalAgregarDocente">
                   <i class="fas fa-user-pl|us"></i> Agregar Docente
                </button>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card card-borde-doc">
                        <div class="card-body">

                            <table id="example1" class="table table-bordered table-striped">
                                <thead class="thead-custom">
                                    <tr class="text-center">
                                        <th>N°</th>
                                        <th>DNI</th>
                                        <th>Nombres</th>
                                        <th class="none">Correo</th>
                                        <th class="none">Teléfono</th>
                                        <th>Grado Académico</th>
                                        <th class="none">Condición</th>
                                        <th>Estado</th>
                                        <th class="none">Registrado</th>
                                        <th class="all">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($listDocentes as $doc)
                                    <tr id="docRow{{ $doc->idDocente }}" class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $doc->user->document_number }}</td>
                                        <td>
                                            @if($doc->user)
                                                {{ $doc->user->name }} {{ $doc->user->last_name }}
                                            @else
                                                <span class="badge badge-danger">Sin usuario</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($doc->user)
                                                {{ $doc->user->email }}
                                            @else
                                                <span class="badge badge-danger">Sin usuario</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($doc->user)
                                                {{ $doc->user->phone }}
                                            @else
                                                <span class="badge badge-danger">Sin usuario</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $doc->grado->nombre ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">{{ $doc->contrato->nombre ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if ($doc->user->status == 'active')
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $doc->created_at?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"
                                                    onclick="showEditDocente('{{ $doc->idDocente }}')"
                                                    data-toggle="modal" data-target="#editDocenteModal"
                                                    {{ $doc->user->status == 'inactive' ? 'disabled' : '' }}>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-info"
                                                    onclick="toggleEstadoDocente('{{ $doc->idDocente }}', '{{ $doc->user->name }} {{ $doc->user->last_name }}')">
                                                @if ($doc->user->status == 'active')
                                                    <i class="fas fa-toggle-on"></i>
                                                @else
                                                    <i class="fas fa-toggle-off"></i>
                                                @endif
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="deleteDocente('{{ $doc->idDocente }}')"
                                                    {{ $doc->user->status == 'inactive' ? 'disabled' : '' }}>
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
            </div>
        </div>
    </section>

    <!-- MODAL AGREGAR DOCENTE -->
    <div class="modal fade" id="modalAgregarDocente" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Registrar Docente</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>

                <form id="frmDocenteInsert" method="POST" action="{{ url('admin/docente/insert') }}" novalidate>
                    @csrf


                    <div class="modal-body" style="background:#f5fbff;">

                        <!-- DATOS DEL USUARIO -->
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user"></i> Datos del Usuario
                        </h6>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nombre del Docente</label>
                                <select class="form-control select2" name="user_id" id="user_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($listUsuarios as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->document_number }} - {{ $user->name }} {{ $user->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                        <!-- DATOS ACADÉMICOS -->
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-graduation-cap"></i> Datos Académicos
                        </h6>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Código UNAMBA</label>
                                <input type="text" class="form-control" name="codigo_unamba" placeholder="Opcional">
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Grado Académico</label>
                                <select class="form-control" name="grado_id" required>
                                    <option disabled selected>Seleccione...</option>
                                    @foreach ($listGrados as $grado)
                                        <option value="{{ $grado->idGrados_academicos }}">
                                            {{ $grado->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Tipo de Contrato</label>
                                <select class="form-control" name="tipo_contrato_id" required>
                                    <option disabled selected>Seleccione...</option>
                                    @foreach ($listContratos as $tc)
                                        <option value="{{ $tc->idTipo_contrato }}">
                                            {{ $tc->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="sendFrmDocenteInsert();">
                            <i class="fas fa-save"></i> Registrar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- MODAL EDITAR DOCENTE -->
    <div class="modal fade" id="editDocenteModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header" style="background:#008bdc;color:white;">
                    <h4 class="modal-title">Editar Docente</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body" style="background:#f5fbff;">
                    <form id="editDocForm">

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>DNI</label>
                                <input type="text" class="form-control" id="txtDni" name="dni" readonly>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Nombres</label>
                                <input type="text" class="form-control" id="txtNombre" name="nombre">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Apellidos</label>
                                <input type="text" class="form-control" id="txtApellido" name="apellido">
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Correo</label>
                                <input type="email" class="form-control" id="txtCorreo" name="correo">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" id="txtTelefono" name="telefono">
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Grado Académico</label>
                                <select class="form-control" id="txtGrado" name="grado_id">
                                    <option value="">Seleccione...</option>
                                    @foreach ($listGrados as $grado)
                                        <option value="{{ $grado->idGrados_academicos }}">
                                            {{ $grado->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Tipo de Contrato</label>
                                <select class="form-control" id="txtCondicion" name="tipo_contrato_id">
                                    <option value="">Seleccione...</option>
                                    @foreach ($listContratos as $tc)
                                        <option value="{{ $tc->idTipo_contrato }}">
                                            {{ $tc->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="modal-footer" style="background:#eef7ff;">
                    <button class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" id="btnActualizarDoc">Guardar cambios</button>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL CAMBIAR ESTADO -->
    <div class="modal fade" id="estadoDocenteModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content shadow-lg">

                <div class="modal-header text-white" style="background:linear-gradient(90deg,#0066ff,#003399);">
                    <h5 class="modal-title">Cambiar Estado del Docente:
                        <span id="nombreDocEstado" class="text-warning font-weight-bold"></span>
                    </h5>
                    <button class="close text-white" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">

                    <p class="font-weight-bold mb-2">Seleccione el nuevo estado:</p>

                    <label class="mr-4">
                        <input type="radio" name="estadoDocente" value="1" id="radioDocActivo">
                        <span class="text-success font-weight-bold ml-2">Activo</span>
                    </label>

                    <label>
                        <input type="radio" name="estadoDocente" value="0" id="radioDocInactivo">
                        <span class="text-danger font-weight-bold ml-2">Inactivo</span>
                    </label>

                    <div id="estadoMensajeDoc" class="p-3 mt-3 rounded"
                        style="background:#e8f4fc;border-left:4px solid #007bff;">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span class="ml-2">Aquí aparecerá la descripción del estado.</span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" id="btnGuardarEstadoDoc">Guardar cambios</button>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar Select2 cuando se abre el modal
            $('#modalAgregarDocente').on('shown.bs.modal', function () {
                $('#user_id').select2({
                    theme: 'bootstrap4',
                    dropdownParent: $('#modalAgregarDocente'),
                    width: '100%',
                    placeholder: 'Seleccione un docente'
                });
            });
        });
    </script>
    <script src="{{ asset('viewresources/admin/docente/insert.js?=0512026') }}"></script>
    <script src="{{ asset('viewresources/admin/docente/update.js?=0512026') }}"></script>
    <script src="{{ asset('viewresources/admin/docente/delete.js?=0512026') }}"></script>
    <script src="{{ asset('viewresources/admin/docente/state.js?=0512026') }}"></script>

@endsection