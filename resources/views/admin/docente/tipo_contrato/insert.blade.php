@extends('admin.template.layout')

@section('titleGeneral', 'Gestionar Tipos de Contrato')

@section('sectionGeneral')

<style>
    .card-borde-contrato {
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
        <div class="card card-primary card-borde-contrato">
            <div class="card-header">
                <h3 class="card-title">Registrar Tipo de Contrato</h3>
            </div>

            <div class="card-body">

                <form id="frmContratoInsert" action="{{ url('admin/docente/tipo_contrato/insert') }}" method="post"
                    onsubmit="event.preventDefault(); sendFrmContratoInsert();">

                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre del tipo de contrato:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-file-contract"></i></div>
                                    </div>
                                    <input type="text" name="nombre" class="form-control"
                                        placeholder="Ej: Nombrado, Contratado, CAS" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-primary" onclick="sendFrmContratoInsert();"
                                id="btnGuardarContrato">
                                <i class="fa fa-save"></i> Registrar
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>

        <div class="mt-4"></div>

        <!-- TABLA -->
        <div class="card card-borde-contrato">
            <div class="card-body table-responsive">

                <h4 class="mb-3">Lista de Tipos de Contrato</h4>

                <table id="tablaContratos" class="table table-bordered table-striped">
                    <thead class="thead-custom">
                        <tr class="text-center">
                            <th class="all">N°</th>
                            <th>Nombre</th>
                            <th class="none">Registrado</th>
                            <th class="all">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($listContratos as $cont)
                            <tr id="contratoRow{{ $cont->idTipo_contrato }}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="tdNombreContrato">{{ $cont->nombre }}</td>
                                <td class="text-center">{{ $cont->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning"
                                        onclick="showEditContrato('{{ $cont->idTipo_contrato }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm"
                                        onclick="deleteContrato('{{ $cont->idTipo_contrato }}')">
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
<div class="modal fade" id="editContratoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg" style="border-radius: 12px;">

            <div class="modal-header text-white"
                style="background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%); border-radius: 12px 12px 0 0;">
                <h4 class="modal-title">Editar Tipo de Contrato</h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body" style="background-color: #f5fbff;">
                <form id="editContratoForm">
                    <div class="form-group">
                        <label for="txtNombreContrato">Nombre del tipo de contrato</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-file-contract"></i></span>
                            </div>
                            <input type="text" class="form-control" id="txtNombreContrato" name="nombre">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="background:#eef7ff;border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" id="btnCancelarContrato">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnActualizarContrato">
                    Guardar cambios
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/tipo_contrato/insert.js?v=18122025') }}"></script>
    <script src="{{ asset('viewresources/admin/tipo_contrato/delete.js?v=18122025') }}"></script>
    <script src="{{ asset('viewresources/admin/tipo_contrato/update.js?v=18122025') }}"></script>
@endsection
