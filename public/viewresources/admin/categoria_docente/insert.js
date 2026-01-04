'use strict';

// ================================
// CSRF GLOBAL
// ================================
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(() => {
    // ================================
    // VALIDACIÓN
    // ================================
    $('#frmCategoriaInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '.notValidate'],
        live: 'enabled',
        trigger: null,
        message: '<b style="color:#9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        fields: {
            nombre: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Debe ingresar el nombre de la categoría docente.</b>'
                    },
                    stringLength: {
                        max: 100,
                        message: '<b style="color:red;">Máximo 100 caracteres permitidos.</b>'
                    }
                }
            }
        }
    });

    $('#frmCategoriaInsert').on('status.field.fv', () => {
        $('#btnGuardarCategoria').prop('disabled', false);
    });
});

// ================================
// ENVÍO AJAX
// ================================
function sendFrmCategoriaInsert() {
    const form = $('#frmCategoriaInsert');
    const fv = form.data('formValidation');

    fv.validate();
    if (!fv.isValid()) {
        new PNotify({
            title: 'No se pudo proceder',
            text: 'Complete y corrija toda la información del formulario.',
            type: 'error'
        });
        return;
    }

    swal({
        title: 'Confirmar operación',
        text: '¿Registrar nueva categoría docente?',
        icon: 'warning',
        buttons: ['Cancelar', 'Sí, registrar']
    }).then(ok => {
        if (!ok) return;

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',

            success: function(res) {
                if (res.status === "success") {
                    const tabla = $("#tablaExample2").DataTable();

                    const botones = `
                        <button class="btn btn-sm btn-warning"
                            onclick="showEditCategoria('${res.data.id}')">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn btn-danger btn-sm"
                            onclick="deleteCategoria('${res.data.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;

                    const row = tabla.row.add([
                        res.data.numero,
                        res.data.nombre,
                        res.data.fecha,
                        botones
                    ]).draw(false).node();

                    $(row).attr('id', 'categoriaRow' + res.data.id);
                    $(row).find('td:eq(0)').addClass('text-center');
                    $(row).find('td:eq(1)').addClass('tdNombreCategoria');
                    $(row).find('td:eq(2)').addClass('text-center');
                    $(row).find('td:eq(3)').addClass('text-center');

                    // Reordenar numeración
                    tabla.rows().every(function(i) {
                        this.cell(i, 0).data(i + 1);
                    });

                    new PNotify({
                        title: 'Éxito',
                        text: 'Categoría registrada correctamente.',
                        type: 'success'
                    });

                    form[0].reset();
                    fv.resetForm(true);
                }
            },

            error: function(xhr) {
                if (xhr.status === 422) {
                    const errs = xhr.responseJSON.errors;
                    new PNotify({
                        title: 'Validación',
                        text: Object.values(errs).join('<br>'),
                        type: 'error'
                    });
                } else {
                    new PNotify({
                        title: 'Error',
                        text: 'Ocurrió un problema inesperado.',
                        type: 'error'
                    });
                }
            }
        });
    });
}
