@extends('template.layout')
@section('titleGeneral', 'Insertar Ciclo')
@section('sectionGeneral')
    <style>
        .card-borde-ciclos {
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
    </style>
    <section class="content">
        <div class="container-fluid">
            <!-- FORMULARIO -->
            <div class="card card-primary card-borde-ciclos">
                <div class="card-header">
                    <h3 class="card-title">Registrar Ciclo</h3>
                </div>
                <div class="card-body">
                    <form id="frmCicloInsert" action="{{ url('admin/academico/ciclo/getall') }}" method="post"
                        onsubmit="event.preventDefault(); sendFrmCicloInsert();">
                        @csrf
                        <div class="row">
                            <!-- Nombre del Ciclo -->
                            <div class="col-md-6">
                                <label>Nombre del Ciclo:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-layer-group"></i>
                                        </div>
                                    </div>
                                    <input type="text" name="NombreCiclo" class="form-control"
                                        placeholder="Ej: PRIMER SEMESTRE" required>
                                </div>
                            </div>
                            <!-- Número del Ciclo (Romano) -->
                            <div class="col-md-4">
                                <label>Número (Romano):</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-list-ol"></i>
                                        </div>
                                    </div>
                                    <input type="text" name="NumeroCiclo" class="form-control"
                                        placeholder="Ej: I, II, III, IV" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary" onclick="sendFrmCicloInsert();"
                                    id="btnGuardarCiclo">
                                    <i class="fa fa-save"></i> Registrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-4"></div>
            <!-- TABLA -->
            <div class="card card-borde-ciclos">
                <div class="card-body table-responsive">
                    <h4 class="mb-3">Lista de Ciclos</h4>
                    <table id="tablaExample2" class="table table-bordered table-striped">
                        <thead class="thead-custom">
                            <tr class="text-center">
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Número (Romano)</th>
                                <th class="none">Registrado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listCiclos as $ciclo)
                                <tr id="cicloRow{{ $ciclo->IdCiclo }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="tdNombreCiclo">{{ $ciclo->NombreCiclo }}</td>
                                    <td class="tdNumeroCiclo text-center">{{ $ciclo->NumeroCiclo }}</td>
                                    <td class="text-center">{{ $ciclo->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" onclick="showEditCiclo('{{ $ciclo->IdCiclo }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="deleteCiclo({{ json_encode($ciclo->IdCiclo) }});">
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

    <!-- Modal Editar -->
    <div class="modal fade" id="editCicloModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg" style="border-radius: 12px;">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%); border-radius: 12px 12px 0 0;">
                    <h4 class="modal-title">Editar Ciclo</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: #f5fbff;">
                    <form id="editCicloForm">
                        <!-- Nombre -->
                        <div class="form-group">
                            <label for="txtNombreCiclo">Nombre del Ciclo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                </div>
                                <input type="text" class="form-control" id="txtNombreCiclo" name="txtNombreCiclo">
                            </div>
                        </div>
                        <!-- Número (Romano) -->
                        <div class="form-group">
                            <label for="txtNumeroCiclo">Número (Romano)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                </div>
                                <input type="text" class="form-control" id="txtNumeroCiclo" name="txtNumeroCiclo">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer" style="background:#eef7ff;border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" id="btnCancelarCiclo">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnActualizarCiclo">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/ciclo/getall.js?v=18122025') }}"></script>
    <script src="{{ asset('viewresources/admin/ciclo/update.js?v=18122025') }}"></script>
    <script src="{{ asset('viewresources/admin/ciclo/delete.js?v=18122025') }}"></script>
@endsection