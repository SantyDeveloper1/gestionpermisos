@extends('admin.template.layout')

@section('titleGeneral', 'Gestionar Grados Académicos')

@section('sectionGeneral')

    <style>
        .card-borde-grados {
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
            <div class="card card-primary card-borde-grados">
                <div class="card-header">
                    <h3 class="card-title">Registrar Grado Académico</h3>
                </div>

                <div class="card-body">

                    <form id="frmGradoInsert" action="{{ url('admin/docente/grados-academicos/insert') }}" method="post"
                        onsubmit="event.preventDefault(); sendFrmGradoInsert();">

                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre del Grado:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-graduation-cap"></i></div>
                                        </div>
                                        <input type="text" name="nombre" class="form-control"
                                            placeholder="Ej: Ing., Mg., Dr., Lic." required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary" onclick="sendFrmGradoInsert();"
                                    id="btnGuardarGrado">
                                    <i class="fa fa-save"></i> Registrar
                                </button>
                            </div>

                        </div>


                    </form>

                </div>
            </div>

            <div class="mt-4"></div>

            <!-- TABLA -->
            <div class="card card-borde-grados">
                <div class="card-body table-responsive">

                    <h4 class="mb-3">Lista de Grados Académicos</h4>

                    <table id="tablaExample2" class="table table-bordered table-striped">
                        <thead class="thead-custom">
                            <tr class="text-center">
                                <th class="all">N°</th>
                                <th>Nombre</th>
                                <th class="none">Registrado</th>
                                <th class="all">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($listGrados as $grado)
                                <tr id="gradoRow{{ $grado->idGrados_academicos }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="tdNombreGrado">{{ $grado->nombre }}</td>
                                    <td class="text-center">{{ $grado->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning"
                                            onclick="showEditGrado('{{ $grado->idGrados_academicos }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-danger btn-sm"
                                            onclick="deleteGrado({{ json_encode($grado->idGrados_academicos) }});">
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

    <!-- Modal Editar Grado -->
    <div class="modal fade" id="editGradoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content shadow-lg" style="border-radius: 12px;">

                <!-- Header -->
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
                    border-top-left-radius: 12px;
                    border-top-right-radius: 12px;">
                    <h4 class="modal-title">Editar Grado Académico</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body" style="background-color: #f5fbff; padding: 20px;">
                    <form id="editGradoForm">
                        <div class="form-group">
                            <label for="txtNombreGrado">Nombre del grado</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                </div>
                                <input type="text" class="form-control" id="txtNombreGrado" name="nombre">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="modal-footer justify-content-between"
                    style="background-color: #eef7ff; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" id="btnCancelarGrado">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnActualizarGrado">
                        Guardar cambio  s
                    </button>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/grados_academicos/insert.js?v=18122025') }}"></script>
    <script src="{{ asset('viewresources/admin/grados_academicos/delete.js?v=18122025') }}"></script>
    <script src="{{ asset('viewresources/admin/grados_academicos/update.js?v=18122025') }}"></script>
@endsection