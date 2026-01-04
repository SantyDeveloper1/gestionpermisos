@extends('template.layout')

@section('titleGeneral', 'Insertar Categoría Docente')

@section('sectionGeneral')

    <style>
        .card-borde-categorias {
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
            <div class="card card-primary card-borde-categorias">
                <div class="card-header">
                    <h3 class="card-title">Registrar Categoría Docente</h3>
                </div>

                <div class="card-body">

                    <form id="frmCategoriaInsert" action="{{ url('admin/docente/categoria-docente/insert') }}" method="post"
                        onsubmit="event.preventDefault(); sendFrmCategoriaInsert();">

                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre de la categoría:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-user-tie"></i></div>
                                        </div>
                                        <input type="text" name="nombre" class="form-control"
                                            placeholder="Ej: Auxiliar, Asociado, Principal" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-primary" onclick="sendFrmCategoriaInsert();"
                                    id="btnGuardarCategoria">
                                    <i class="fa fa-save"></i> Registrar
                                </button>
                            </div>

                        </div>

                    </form>

                </div>
            </div>

            <div class="mt-4"></div>

            <!-- TABLA -->
            <div class="card card-borde-categorias">
                <div class="card-body table-responsive">

                    <h4 class="mb-3">Lista de Categorías Docente</h4>

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
                            @foreach($listCategorias as $cat)
                                <tr id="categoriaRow{{ $cat->idCategori_docente }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="tdNombreCategoria">{{ $cat->nombre }}</td>
                                    <td class="text-center">{{ $cat->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning"
                                            onclick="showEditCategoria('{{ $cat->idCategori_docente }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-danger btn-sm"
                                            onclick="deleteCategoria({{ json_encode($cat->idCategori_docente) }});">
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
    <div class="modal fade" id="editCategoriaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg" style="border-radius: 12px;">

                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%); border-radius: 12px 12px 0 0;">
                    <h4 class="modal-title">Editar Categoría Docente</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="background-color: #f5fbff;">
                    <form id="editCategoriaForm">
                        <div class="form-group">
                            <label for="txtNombreCategoria">Nombre de la categoría</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                </div>
                                <input type="text" class="form-control" id="txtNombreCategoria" name="nombre">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer" style="background:#eef7ff;border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" id="btnCancelarCategoria">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnActualizarCategoria">
                        Guardar cambios
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/categoria_docente/insert.js?v=18122025') }}"></script>
    <script src="{{ asset('viewresources/admin/categoria_docente/delete.js?v=18122025') }}"></script>
    <script src="{{ asset('viewresources/admin/categoria_docente/update.js?v=18122025') }}"></script>
@endsection