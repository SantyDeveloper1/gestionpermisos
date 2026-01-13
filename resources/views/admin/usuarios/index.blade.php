@extends('admin.template.layout')
@section('titleGeneral', 'Gestión de Usuarios')
@section('sectionGeneral')

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">
                <i class="fas fa-users"></i> Lista de Usuarios
            </h3>
            <button type="button" class="btn btn-success btn-sm float-right" data-toggle="modal"
                data-target="#modalCrearUsuario">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </button>
        </div>

        <div class="card-body">
            <table id="tablaExample" class="table table-bordered table-striped">
                <thead class="thead-custom">
                    <tr class="text-center">
                        <th class="all">N°</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Género</th>
                        <th>Roles</th>
                        <th class="all">Estado</th>
                        <th class="all">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <!-- =========================================
        MODAL CREAR USUARIO
    ========================================= -->
    <div class="modal fade" id="modalCrearUsuario" tabindex="-1" role="dialog" aria-labelledby="modalCrearUsuarioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <form id="frmUsuarioInsert" action="{{ route('usuarios.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalCrearUsuarioLabel">
                            <i class="fas fa-user-plus"></i> Crear Nuevo Usuario
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="row">
                            <!-- Nombres -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombres</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Ingrese los nombres">
                                </div>
                            </div>

                            <!-- Apellidos -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Apellidos</label>
                                    <input type="text" name="last_name" id="last_name" class="form-control"
                                        placeholder="Ingrese los apellidos">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="ejemplo@correo.com">
                                </div>
                            </div>

                            <!-- Tipo de Documento -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo de Documento</label>
                                    <select name="document_type" id="document_type" class="form-control">
                                        <option value="">Seleccione tipo de documento</option>
                                        <option value="DNI">DNI</option>
                                        <option value="PASAPORTE">Pasaporte</option>
                                        <option value="CE">Carné de Extranjería</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Número Documento -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Número de Documento</label>
                                    <input type="text" name="document_number" id="document_number" class="form-control"
                                        placeholder="Ingrese el número de documento">
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        placeholder="Ej: 987654321">
                                </div>
                            </div>

                            <!-- Género -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Género</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="">Seleccione el género</option>
                                        <option value="male">Masculino</option>
                                        <option value="female">Femenino</option>
                                        <option value="other">Otro</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Imagen -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Foto de Usuario</label>
                                    <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                                    <small class="text-muted">Formatos permitidos: JPG, PNG</small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="sendFrmUsuarioInsert()">
                            <i class="fas fa-save"></i> Guardar Usuario
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- =========================================
        MODAL EDITAR USUARIO
    ========================================= -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalEditarUsuarioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <form id="frmUsuarioUpdate">
                    @csrf

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalEditarUsuarioLabel">
                            <i class="fas fa-user-edit"></i> Editar Usuario
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" id="edit_user_id" name="user_id">

                        <div class="row">
                            <!-- Nombres -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombres</label>
                                    <input type="text" name="name" id="edit_name" class="form-control"
                                        placeholder="Ingrese los nombres">
                                </div>
                            </div>
                            <!-- Apellidos -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Apellidos</label>
                                    <input type="text" name="last_name" id="edit_last_name" class="form-control"
                                        placeholder="Ingrese los apellidos">
                                </div>
                            </div>
                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="text" name="phone" id="edit_phone" class="form-control"
                                        placeholder="Ej: 987654321">
                                </div>
                            </div>
                            <!-- Género -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Género</label>
                                    <select name="gender" id="edit_gender" class="form-control">
                                        <option value="">Seleccione el género</option>
                                        <option value="male">Masculino</option>
                                        <option value="female">Femenino</option>
                                        <option value="other">Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="btnActualizarUsuario">
                            <i class="fas fa-save"></i> Actualizar Usuario
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


@endsection

@section('js')
    <script>
        $(document).ready(function () {
            // Inicializar DataTable con AJAX
            $('#tablaExample').DataTable({
                processing: true,
                serverSide: false,
                destroy: true,
                responsive: true,
                autoWidth: false,

                ajax: {
                    url: "{{ url('admin/usuarios/listar') }}",
                    type: "GET",
                    dataSrc: "data",
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                },

                columns: [
                    { title: "N°", className: "text-center", width: "5%" },
                    { title: "Nombre Completo", className: "text-center" },
                    { title: "Email", className: "text-center" },
                    { title: "Teléfono", className: "text-center" },
                    { title: "Género", className: "text-center" },
                    { title: "Roles", className: "text-center" },
                    { title: "Estado", className: "text-center" },
                    {
                        title: "Acciones",
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        width: "10%"
                    }
                ],

                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron registros coincidentes",
                    emptyTable: "No hay datos disponibles en la tabla",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    },
                    aria: {
                        sortAscending: ": activar para ordenar la columna ascendente",
                        sortDescending: ": activar para ordenar la columna descendente"
                    }
                }
            });
        });
    </script>
    <script src="{{ asset('viewresources/admin/usuarios/insert.js?v=23122025') }}"></script>
    <script src="{{ asset('viewresources/admin/usuarios/update.js?v=23122025') }}"></script>
    <script src="{{ asset('viewresources/admin/usuarios/delete.js?v=23122025') }}"></script>
@endsection