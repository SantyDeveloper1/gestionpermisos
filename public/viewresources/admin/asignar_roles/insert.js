'use strict';

$(document).ready(function () {

    // ================================
    // FORM VALIDATION
    // ================================
    $('#frmAsignarRol').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '.notValidate'],
        live: 'enabled',
        trigger: null,
        message: '<b style="color:#9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        fields: {

            user_id: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Debe seleccionar un usuario.</b>'
                    }
                }
            },

            role_id: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Debe seleccionar un rol.</b>'
                    }
                }
            }
        }
    });

});
/**
 * =================================
 * ENVIAR FORMULARIO AJAX
 * =================================
 */
function sendFrmAsignarRol() {
    const form = $('#frmAsignarRol');
    const fv   = form.data('formValidation');
    if (!fv) {
        console.error('❌ FormValidation no inicializado');
        return;
    }
    fv.validate();
    if (!fv.isValid()) {
        new PNotify({
            title: 'Formulario incompleto',
            text: 'Seleccione un usuario y un rol.',
            type: 'error'
        });
        return;
    }
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                new PNotify({
                    title: 'Éxito',
                    text: res.message,
                    type: 'success'
                });
                form[0].reset();
                fv.resetForm(true);
                form.find('.select2').val(null).trigger('change');
                $('#modalCrearUsuario').modal('hide');
                // RECARGAR TABLA
                if ($.fn.DataTable.isDataTable('#tablaExample3')) {
                    $('#tablaExample3').DataTable().ajax.reload(null, false);
                }
            }
        },

        error: function (xhr) {
            let mensaje = 'Ocurrió un error inesperado.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.errors) {
                    mensaje = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join('<br>');
                }
                else if (xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
            }
            new PNotify({
                title: 'Error',
                text: mensaje,
                type: 'error'
            });
        }
    });
}
