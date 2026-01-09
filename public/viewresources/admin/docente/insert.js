'use strict';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(() => {

    $('#frmDocenteInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
        live: 'enabled',
        trigger: null,
        fields: {

            user_id: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Seleccione un docente.</b>'
                    }
                }
            },

            grado_id: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Seleccione un grado académico.</b>'
                    }
                }
            },

            tipo_contrato_id: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Seleccione un tipo de contrato.</b>'
                    }
                }
            },

            codigo_unamba: {
                validators: {
                    stringLength: {
                        max: 15,
                        message: '<b style="color:red;">Máximo 15 caracteres.</b>'
                    }
                }
            }

        }
    });
});

function sendFrmDocenteInsert() {

    let fv = $('#frmDocenteInsert').data('formValidation');
    fv.validate();

    if (!fv.isValid()) {
        new PNotify({
            title: 'Formulario incompleto',
            text: 'Complete la información requerida.',
            type: 'error'
        });
        return;
    }

    swal({
        title: 'Confirmar operación',
        text: '¿Desea registrar al docente?',
        icon: 'warning',
        buttons: ['Cancelar', 'Sí, registrar']
    }).then((proceed) => {

        if (!proceed) return;

        $.ajax({
            url: $('#frmDocenteInsert').attr('action'),
            type: 'POST',
            data: $('#frmDocenteInsert').serialize(),
            success: function (resp) {

                new PNotify({
                    title: 'Correcto',
                    text: resp.message,
                    type: 'success'
                });

                // Agregar fila dinámicamente si viene el item
                if (resp.item) {
                    agregarFilaDocente(resp.item);
                }

                // limpiar select2
                $('#user_id').val(null).trigger('change');

                $('#frmDocenteInsert')[0].reset();
                fv.resetForm();

                $('#modalAgregarDocente').modal('hide');
            },
            error: function (xhr) {

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.values(errors).forEach(err => {
                        new PNotify({
                            title: 'Error',
                            text: err[0],
                            type: 'error'
                        });
                    });
                } else if (xhr.status === 500) {
                    new PNotify({
                        title: 'Error del servidor',
                        text: 'Ocurrió un error interno. Por favor, revise los logs o contacte al administrador.',
                        type: 'error'
                    });
                    console.error('Error 500:', xhr.responseText);
                } else {
                    new PNotify({
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.',
                        type: 'error'
                    });
                }
            }
        });
    });
}

/* =====================================================
   AGREGAR FILA DINÁMICA A LA TABLA
===================================================== */
function agregarFilaDocente(item) {
    if (!item) return;

    // Obtener la instancia de DataTables
    const table = $('#example1').DataTable();

    // Preparar badges
    const badgeEstado = item.status === 'active' 
        ? '<span class="badge badge-success">Activo</span>' 
        : '<span class="badge badge-danger">Inactivo</span>';

    // Preparar botones de acción
    const botonesAccion = `
        <button class="btn btn-sm btn-warning" 
                onclick="showEditDocente('${item.idDocente}')" 
                data-toggle="modal" data-target="#editDocenteModal"
                ${item.status === 'inactive' ? 'disabled' : ''}>
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-info" 
                onclick="toggleEstadoDocente('${item.idDocente}', '${item.nombre}')">
            ${item.status === 'active' 
                ? '<i class="fas fa-toggle-on"></i>' 
                : '<i class="fas fa-toggle-off"></i>'}
        </button>
        <button class="btn btn-sm btn-danger" 
                onclick="deleteDocente('${item.idDocente}')"
                ${item.status === 'inactive' ? 'disabled' : ''}>
            <i class="fas fa-trash"></i>
        </button>
    `;

    // Agregar la fila usando la API de DataTables
    const rowNode = table.row.add([
        '1', // N° - será 1 porque es el más reciente
        item.dni,
        item.nombre,
        item.correo,
        item.telefono,
        `<span class="badge badge-primary">${item.grado}</span>`,
        `<span class="badge badge-warning">${item.condicion}</span>`,
        badgeEstado,
        item.fecha,
        botonesAccion
    ]).draw(false).node();

    // Agregar el ID y clase a la fila
    $(rowNode).attr('id', `docRow${item.idDocente}`);
    $(rowNode).addClass('text-center');

    // Mover la fila recién agregada al principio de la tabla
    const $row = $(rowNode);
    const $tbody = $('#example1 tbody');
    $row.prependTo($tbody);

    // Actualizar los números de fila (1, 2, 3, ...)
    $tbody.find('tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}
