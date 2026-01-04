'use strict';

// ================================
// CSRF GLOBAL
// ================================
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(() => {
    // Inicializar DataTable
    $('#tablaContratos').DataTable({
        responsive: true,
        autoWidth: false,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: -1 }
        ]
    });

    $('#frmContratoInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '.notValidate'],
        live: 'enabled',
        trigger: null,
        message: '<b style="color:#9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        fields: {
            nombre: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Debe ingresar el nombre del tipo de contrato.</b>'
                    },
                    stringLength: {
                        max: 100,
                        message: '<b style="color:red;">Máximo 100 caracteres permitidos.</b>'
                    }
                }
            }
        }
    });

    $('#frmContratoInsert').on('status.field.fv', () => {
        $('#btnGuardarContrato').prop('disabled', false);
    });
});

// ================================
// ENVÍO AJAX
// ================================
function sendFrmContratoInsert() {
    const form = $('#frmContratoInsert');
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
        text: '¿Registrar nuevo tipo de contrato?',
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
                    const tabla = $("#tablaContratos").DataTable();

                    const botones = `
                        <button class="btn btn-sm btn-warning"
                            onclick="showEditContrato('${res.data.id}')">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn btn-danger btn-sm"
                            onclick="deleteContrato('${res.data.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;

                    const row = tabla.row.add([
                        res.data.numero,
                        res.data.nombre,
                        res.data.fecha,
                        botones
                    ]).draw(false).node();

                    $(row).attr('id', 'contratoRow' + res.data.id);
                    $(row).find('td:eq(0)').addClass('text-center');
                    $(row).find('td:eq(1)').addClass('tdNombreContrato');
                    $(row).find('td:eq(2)').addClass('text-center');
                    $(row).find('td:eq(3)').addClass('text-center');

                    tabla.rows().every(function(i) {
                        this.cell(i, 0).data(i + 1);
                    });

                    new PNotify({
                        title: 'Éxito',
                        text: 'Tipo de contrato registrado correctamente.',
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
