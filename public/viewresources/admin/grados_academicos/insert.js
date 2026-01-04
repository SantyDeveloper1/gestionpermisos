'use strict';

// ================================
// CSRF para todas las peticiones AJAX
// ================================
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(() => {
    // ================================
    // INICIALIZAR FORMVALIDATION
    // ================================
    $('#frmGradoInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '.notValidate'],
        live: 'enabled',
        trigger: null,
        message: '<b style="color:#9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        fields: {
            nombre: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Debe ingresar el nombre del grado académico.</b>'
                    },
                    stringLength: {
                        max: 100,
                        message: '<b style="color:red;">Máximo 100 caracteres permitidos.</b>'
                    }
                }
            }
        }
    });

    // Evitar que FormValidation deshabilite el botón
    $('#frmGradoInsert').on('status.field.fv', () => {
        $('#btnGuardarGrado').prop('disabled', false);
    });
});

// ================================
// ENVÍO DEL FORMULARIO VIA AJAX
// ================================
function sendFrmGradoInsert() {
    const form = $('#frmGradoInsert');
    const fv = form.data('formValidation');

    if (!fv) {
        console.error('❌ FormValidation NO se inicializó.');
        return;
    }

    // Validar el formulario
    fv.validate();

    if (!fv.isValid()) {
        new PNotify({
            title: 'No se pudo proceder',
            text: 'Complete y corrija toda la información del formulario.',
            type: 'error'
        });
        return;
    }

    // Confirmación antes de enviar
    swal({
        title: 'Confirmar operación',
        text: '¿Realmente desea registrar el grado académico?',
        icon: 'warning',
        buttons: ['No, cancelar.', 'Sí, proceder.']
    }).then(proceed => {
        if (!proceed) return;

        const ruta = form.attr('action');

        $.ajax({
            url: ruta,
            type: "POST",
            data: form.serialize(),
            dataType: 'json',

            success: function (res) {
                if (res.status === "success") {

                    // ================================
                    // INSERTAR FILA USANDO DATATABLES API
                    // ================================
                    const tabla = $("#tablaExample2").DataTable();
                    
                    // Crear el HTML de los botones de acción
                    const botonesAccion = `
                        <button class="btn btn-sm btn-warning" onclick="showEditGrado('${res.data.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteGrado('${res.data.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;

                    // Agregar la fila usando DataTables API
                    const rowNode = tabla.row.add([
                        res.data.numero,
                        res.data.nombre,
                        res.data.fecha,
                        botonesAccion
                    ]).draw(false).node();

                    // Asignar el ID a la fila y la clase al nombre
                    $(rowNode).attr('id', 'gradoRow' + res.data.id);
                    $(rowNode).find('td:eq(0)').addClass('text-center');
                    $(rowNode).find('td:eq(1)').addClass('tdNombreGrado');
                    $(rowNode).find('td:eq(2)').addClass('text-center');
                    $(rowNode).find('td:eq(3)').addClass('text-center');

                    // Reenumerar todas las filas
                    tabla.rows().every(function(index) {
                        this.cell(index, 0).data(index + 1);
                    });
                    tabla.draw(false);

                    // Notificación de éxito
                    new PNotify({
                        title: 'Éxito',
                        text: 'Grado académico registrado correctamente.',
                        type: 'success'
                    });

                    // Limpiar formulario y resetear validación
                    form[0].reset();
                    fv.resetForm(true);
                }
            },

            // Captura profesional de errores
            error: function (xhr) {

                // Errores de validación Laravel (422)
                if (xhr.status === 422) {
                    const errores = xhr.responseJSON?.errors;
                    if (errores) {
                        const mensaje = Object.values(errores).join('<br>');
                        new PNotify({ title: 'Validación', text: mensaje, type: 'error' });
                    }
                    return; // Evita que Chrome muestre el error en consola
                }

                // Otros errores inesperados
                new PNotify({
                    title: 'Error inesperado',
                    text: 'Ocurrió un problema en el servidor.',
                    type: 'error'
                });
            }
        });
    });
}
