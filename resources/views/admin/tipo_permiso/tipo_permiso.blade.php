@extends('admin.template.layout')

@section('titleGeneral', 'Gestión de Tipos de Permiso')

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
    </style>

    <section class="content">
        <div class="container-fluid">

            {{-- FORMULARIO REGISTRO --}}
            <div class="card card-primary card-borde mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i> Registrar Tipo de Permiso
                    </h3>
                </div>

                <div class="card-body">
                    <form id="frmTipoPermisoInsert" onsubmit="event.preventDefault(); sendFrmTipoPermisoInsert();">

                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre del permiso</label>
                                    <input type="text" class="form-control" name="nombre"
                                        placeholder="Ej: Permiso por salud" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" class="form-control" name="descripcion"
                                        placeholder="Descripción breve del permiso">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="requiere_recupero"
                                        name="requiere_recupero">
                                    <label class="custom-control-label" for="requiere_recupero">
                                        Requiere recuperación
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="con_goce_haber"
                                        name="con_goce_haber">
                                    <label class="custom-control-label" for="con_goce_haber">
                                        Con goce de haber
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="requiere_documento"
                                        name="requiere_documento">
                                    <label class="custom-control-label" for="requiere_documento">
                                        Requiere documento
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary">
                                    <i class="fa fa-save"></i> Registrar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="card card-borde">
                <div class="card-body table-responsive">

                    <h4 class="mb-3">
                        <i class="fas fa-list"></i> Tipos de Permiso Registrados
                    </h4>

                    <table id="tablaExample2" class="table table-bordered table-striped">
                        <thead class="thead-custom">
                            <tr>
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Goce Haber</th>
                                <th>Recupero</th>
                                <th>Documento</th>
                                <th class="none">Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($listTipoPermisos as $tp)
                                                <tr id="row{{ $tp->id_tipo_permiso }}">
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $tp->nombre }}</td>

                                                    <td class="text-center">
                                                        {!! $tp->con_goce_haber
                                ? '<span class="badge badge-success">Sí</span>'
                                : '<span class="badge badge-secondary">No</span>' !!}
                                                    </td>

                                                    <td class="text-center">
                                                        {!! $tp->requiere_recupero
                                ? '<span class="badge badge-warning">Sí</span>'
                                : '<span class="badge badge-secondary">No</span>' !!}
                                                    </td>

                                                    <td class="text-center">
                                                        {!! $tp->requiere_documento
                                ? '<span class="badge badge-info">Sí</span>'
                                : '<span class="badge badge-secondary">No</span>' !!}
                                                    </td>

                                                    <td class="text-center">
                                                        {{ $tp->created_at->format('d/m/Y: H:i:s') }}
                                                    </td>

                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-warning"
                                                            onclick="editTipoPermiso('{{ $tp->id_tipo_permiso }}')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="deleteTipoPermiso('{{ $tp->id_tipo_permiso }}')">
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

    {{-- MODAL EDITAR --}}
    <div class="modal fade" id="editTipoPermisoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg" style="border-radius: 12px;">

                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%); border-radius: 12px 12px 0 0;">
                    <h4 class="modal-title">
                        <i class="fas fa-edit"></i> Editar Tipo de Permiso
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="background-color: #f5fbff;">
                    <form id="frmTipoPermisoEdit">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre del permiso <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="editNombre" name="nombre"
                                        placeholder="Ej: Permiso por salud" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" class="form-control" id="editDescripcion" name="descripcion"
                                        placeholder="Descripción breve del permiso">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="editRequiereRecupero"
                                        name="requiere_recupero">
                                    <label class="custom-control-label" for="editRequiereRecupero">
                                        Requiere recuperación
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="editConGoceHaber"
                                        name="con_goce_haber">
                                    <label class="custom-control-label" for="editConGoceHaber">
                                        Con goce de haber
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="editRequiereDocumento"
                                        name="requiere_documento">
                                    <label class="custom-control-label" for="editRequiereDocumento">
                                        Requiere documento
                                    </label>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="modal-footer" style="background:#eef7ff; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnActualizarTipoPermiso">
                        <i class="fa fa-save"></i> Guardar cambios
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/tipo_permiso/insert.js?v='.time()) }}"></script>
    <script src="{{ asset('viewresources/admin/tipo_permiso/update.js?v='.time()) }}"></script>
    <script src="{{ asset('viewresources/admin/tipo_permiso/delete.js?v='.time()) }}"></script>
@endsection