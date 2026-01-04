'use strict';

$(() => {
    // ================================
    // INICIALIZAR FORMVALIDATION
    // ================================
    $('#frmUsuarioInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '.notValidate'],
        live: 'enabled',
        trigger: null,
        message: '<b style="color:#9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        fields: {
            name: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">Los nombres son obligatorios.</b>' },
                    stringLength: { max: 100, message: '<b style="color:red;">Máximo 100 caracteres permitidos.</b>' }
                }
            },
            last_name: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">Los apellidos son obligatorios.</b>' },
                    stringLength: { max: 100, message: '<b style="color:red;">Máximo 100 caracteres permitidos.</b>' }
                }
            },
            email: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">El email es obligatorio.</b>' },
                    emailAddress: { message: '<b style="color:red;">Ingrese un email válido.</b>' }
                }
            },
            document_type: {
                validators: { notEmpty: { message: '<b style="color:red;">Seleccione el tipo de documento.</b>' } }
            },
            document_number: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">El número de documento es obligatorio.</b>' },
                    stringLength: { min: 8, max: 20, message: '<b style="color:red;">Documento inválido.</b>' }
                }
            },
            phone: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">El teléfono es obligatorio.</b>' },
                    regexp: { regexp: /^[0-9]{9}$/, message: '<b style="color:red;">Ingrese un teléfono válido de 9 dígitos.</b>' }
                }
            },
            gender: {
                validators: { notEmpty: { message: '<b style="color:red;">Seleccione el género.</b>' } }
            }
        }
    });
});
$(document).ready(function() {
    $('#document_type').on('change', function() {
        const tipo = $(this).val();
        const inputDoc = $('#document_number');

        switch(tipo) {
            case 'DNI':
                inputDoc.attr('placeholder', 'Ingrese su DNI (8 dígitos)');
                break;
            case 'PASAPORTE':
                inputDoc.attr('placeholder', 'Ingrese su Pasaporte');
                break;
            case 'CE':
                inputDoc.attr('placeholder', 'Ingrese su Carné de Extranjería');
                break;
            default:
                inputDoc.attr('placeholder', 'Ingrese el número de documento');
        }
    });
});
// ================================
// ENVÍO DEL FORMULARIO VIA AJAX
// ================================
function sendFrmUsuarioInsert() {
    const form = $('#frmUsuarioInsert');
    const fv = form.data('formValidation');

    if (!fv) return console.error('❌ FormValidation NO se inicializó.');

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
        text: '¿Desea registrar este usuario?',
        icon: 'warning',
        buttons: ['No, cancelar.', 'Sí, proceder.']
    }).then(proceed => {
        if (!proceed) return;

        const ruta = form.attr('action');
        const formData = new FormData(form[0]);

        $.ajax({
            url: ruta,
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,

            success: function (res) {
                if (res.status === "success") {
                    // ✅ Cambiado al ID correcto
                    const tabla = $('#tablaExample2').DataTable();

                    const botonesAccion = `
                        <a href="/admin/usuarios/edit/${res.data.id}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${res.data.id}" data-name="${res.data.name}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;

                    const rowNode = tabla.row.add([
                        tabla.rows().count() + 1,
                        res.data.name + (res.data.last_name ? ' ' + res.data.last_name : ''),
                        `<i class="fas fa-envelope text-muted"></i> ${res.data.email}`,
                        res.data.phone ? `<i class="fas fa-phone text-muted"></i> ${res.data.phone}` : '<span class="text-muted">-</span>',
                        res.data.roles?.length ? res.data.roles.map(r => `<span class="badge ${r.name==='admin'?'badge-danger':r.name==='docente'?'badge-primary':'badge-secondary'}">${r.name.charAt(0).toUpperCase()+r.name.slice(1)}</span>`).join(' ') : '<span class="badge badge-warning">Sin rol</span>',
                        res.data.status === 'active' ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>' : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>',
                        botonesAccion
                    ]).draw(false).node();

                    $(rowNode).attr('id', 'usuarioRow' + res.data.id);
                    $(rowNode).find('td').addClass('text-center');
                    $(rowNode).find('td:eq(1)').removeClass('text-center');

                    new PNotify({ title: 'Éxito', text: res.message, type: 'success' });

                    form[0].reset();
                    fv.resetForm(true);
                    $('#modalCrearUsuario').modal('hide');
                }
            },

            error: function (xhr) {
                if (xhr.status === 422) {
                    const errores = xhr.responseJSON.errors;
                    new PNotify({ title: 'Validación', text: Object.values(errores).join('<br>'), type: 'error' });
                } else {
                    new PNotify({ title: 'Error', text: 'Ocurrió un error inesperado.', type: 'error' });
                }
            }
        });
    });
}
