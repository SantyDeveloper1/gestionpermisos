@extends('admin.template.layout')
@section('titleGeneral', 'Asignar Roles')
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
                <!-- Botón Agregar rol -->
                <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#modalCrearUsuario">
                    <i class="fas fa-user-plus"></i> Asignar Rol
                </button>
            </div>
        </div>

        <div class="card card-borde-doc">
            <div class="card-body">
                <table id="tablaExample3" class="table table-bordered table-striped">
                    <thead class="thead-custom">
                        <tr class="text-center">
                            <th class="all">N°</th>
                            <th>Usuario</th>
                            <th>Tipo</th>
                            <th class="all">Acciones</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </section>

    <!-- =========================================
                            MODAL ASIGNAR ROL
                        ========================================= -->
    <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalAsignarRolLabel" aria-hidden="true">

        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content shadow">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAsignarRolLabel">
                        <i class="fas fa-user-tag mr-1"></i> Asignar Rol a Usuario
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- FORM -->
                <form id="frmAsignarRol" action="{{ route('usuarios.asignar_roles.store') }}" method="POST"
                    autocomplete="off">
                    @csrf

                    <div class="modal-body">

                        <!-- USUARIO -->
                        <div class="form-group">
                            <label class="font-weight-bold">
                                <i class="fas fa-user"></i> Usuario
                            </label>

                            <select name="user_id" id="user_id" class="form-control select2">
                                <option value="">Seleccione usuario</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}">
                                        {{ $u->name }} {{ $u->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ROL -->
                        <div class="form-group">
                            <label class="font-weight-bold">
                                <i class="fas fa-id-badge"></i> Rol
                            </label>

                            <select name="role_id" id="role_id" class="form-control">
                                <option value="">Seleccione rol</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}">
                                        {{ ucfirst($r->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>

                        <button type="button" class="btn btn-success" onclick="sendFrmAsignarRol()">
                            <i class="fas fa-save"></i> Asignar Rol
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

            // ===============================
            // SELECT2 DENTRO DEL MODAL
            // ===============================
            $('#modalCrearUsuario').on('shown.bs.modal', function () {
                // Destruir la inicialización global del layout
                if ($('#user_id').hasClass('select2-hidden-accessible')) {
                    $('#user_id').select2('destroy');
                }

                // Reinicializar con configuración correcta para modal
                $('#user_id').select2({
                    dropdownParent: $('#modalCrearUsuario'),
                    width: '100%',
                    placeholder: 'Seleccione un usuario',
                    allowClear: true,
                    minimumResultsForSearch: 0, // Siempre mostrar el buscador
                    language: {
                        noResults: function () {
                            return "No se encontraron resultados";
                        },
                        searching: function () {
                            return "Buscando...";
                        }
                    }
                });
            });

            // Limpiar Select2 al cerrar el modal
            $('#modalCrearUsuario').on('hidden.bs.modal', function () {
                $('#user_id').val('').trigger('change');
                $('#role_id').val('');
            });

            // ===============================
            // DATATABLE ROLES ASIGNADOS
            // ===============================
            $('#tablaExample3').DataTable({
                processing: true,
                serverSide: false,
                destroy: true,
                responsive: true,
                autoWidth: false,

                ajax: {
                    url: "{{ route('usuarios.asignar_roles.listar') }}", // 👈 ruta correcta
                    type: "GET",
                    dataSrc: "data",
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                },

                columns: [
                    { title: "N°", className: "text-center", width: "5%" },
                    { title: "Usuario", className: "text-center" },
                    { title: "Rol", className: "text-center" },
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

    </script>
    <script src="{{ asset('viewresources/admin/asignar_roles/insert.js?v=23122025') }}"></script>
    <script src="{{ asset('viewresources/admin/asignar_roles/delete.js?v=23122025') }}"></script>
@endsection
</script>