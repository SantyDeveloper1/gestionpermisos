'use strict';

// ================================
// CSRF GLOBAL
// ================================
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(() => {
    // ================================
    // VALIDACIÓN CICLO
    // ================================
    $('#frmCicloInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '.notValidate'],
        live: 'enabled',
        trigger: null,
        message: '<b style="color:#9d9d9d;">Campo inválido.</b>',
        fields: {
            NombreCiclo: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">Ingrese el nombre del ciclo.</b>' },
                    stringLength: {
                        max: 100,
                        message: '<b style="color:red;">Máximo 100 caracteres.</b>'
                    }
                }
            },
            NumeroCiclo: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">Ingrese el número del ciclo (N° romano).</b>' },
                    regexp: {
                        regexp: /^(I|II|III|IV|V|VI|VII|VIII|IX|X)$/i,
                        message: '<b style="color:red;">Solo números romanos válidos (I - X).</b>'
                    }
                }
            }
        }
    });

    $('#frmCicloInsert').on('status.field.fv', () => {
        $('#btnGuardarCiclo').prop('disabled', false);
    });
});

// ================================
// ENVÍO AJAX CICLO
// ================================
function sendFrmCicloInsert() {
    const form = $('#frmCicloInsert');
    const fv = form.data('formValidation');

    fv.validate();
    if (!fv.isValid()) {
        new PNotify({
            title: 'No se pudo proceder',
            text: 'Complete correctamente la información.',
            type: 'error'
        });
        return;
    }

    swal({
        title: 'Confirmar operación',
        text: '¿Registrar nuevo ciclo?',
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
                            onclick="showEditCiclo('${res.data.id}')">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn btn-danger btn-sm"
                            onclick="deleteCiclo('${res.data.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;

                    // Agregar la fila con N° temporal
                    const row = tabla.row.add([
                        '1', // N° temporal, se actualizará después
                        res.data.nombre,
                        res.data.numero,
                        res.data.fecha,
                        botones
                    ]).node();

                    $(row).attr('id', 'cicloRow' + res.data.id);
                    
                    // Aplicar clases específicas a cada celda
                    $(row).find('td').eq(0).addClass('text-center'); // N°
                    $(row).find('td').eq(1).addClass('tdNombreCiclo'); // Nombre (sin centrar)
                    $(row).find('td').eq(2).addClass('tdNumeroCiclo text-center'); // Número Romano
                    $(row).find('td').eq(3).addClass('text-center'); // Fecha
                    $(row).find('td').eq(4).addClass('text-center'); // Acciones

                    // Ordenar por fecha (columna 3) descendente y redibujar
                    tabla.order([3, 'desc']).draw(false);

                    // Renumerar TODAS las filas después de que se complete el draw
                    setTimeout(function() {
                        $('#tablaExample2 tbody tr').each(function(index) {
                            $(this).find('td:first').text(index + 1);
                        });
                    }, 50);

                    new PNotify({
                        title: 'Éxito',
                        text: 'Ciclo registrado correctamente.',
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
