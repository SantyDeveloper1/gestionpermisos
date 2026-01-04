'use strict';

// ================================
// CSRF GLOBAL
// ================================
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(() => {
    // ================================
    // INICIALIZAR FORMVALIDATION
    // ================================
    $('#frmTipoPermisoInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '.notValidate'],
        live: 'enabled',
        trigger: null,
        message: '<b style="color:#9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        fields: {
            nombre: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Debe ingresar el nombre del tipo de permiso.</b>'
                    },
                    stringLength: {
                        max: 100,
                        message: '<b style="color:red;">Máximo 100 caracteres permitidos.</b>'
                    }
                }
            }
        }
    });

    $('#frmTipoPermisoInsert').on('status.field.fv', () => {
        $('#frmTipoPermisoInsert button[type="submit"]').prop('disabled', false);
    });
});

// ================================
// ENVÍO AJAX
// ================================
function sendFrmTipoPermisoInsert() {
    const form = $('#frmTipoPermisoInsert');
    const fv = form.data('formValidation');

    if (!fv) {
        console.error('❌ FormValidation NO se inicializó.');
        return;
    }

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
        text: '¿Registrar nuevo tipo de permiso?',
        icon: 'warning',
        buttons: ['Cancelar', 'Sí, registrar']
    }).then(ok => {
        if (!ok) return;

        // Preparar datos con checkboxes
        const formData = form.serializeArray();
        const data = {};
        
        formData.forEach(item => {
            data[item.name] = item.value;
        });

        // Agregar checkboxes (si no están marcados, no se envían)
        data.requiere_recupero = $('#requiere_recupero').is(':checked') ? 1 : 0;
        data.con_goce_haber = $('#con_goce_haber').is(':checked') ? 1 : 0;
        data.requiere_documento = $('#requiere_documento').is(':checked') ? 1 : 0;

        $.ajax({
            url: `${_urlBase}/admin/tipo_permiso/insert`,
            type: 'POST',
            data: data,
            dataType: 'json',

            success: function(res) {
                if (res.success) {
                    const tabla = $("#tablaExample2").DataTable();

                    // Crear badges
                    const badgeGoce = res.data.con_goce_haber 
                        ? '<span class="badge badge-success">Sí</span>'
                        : '<span class="badge badge-secondary">No</span>';
                    
                    const badgeRecupero = res.data.requiere_recupero
                        ? '<span class="badge badge-warning">Sí</span>'
                        : '<span class="badge badge-secondary">No</span>';
                    
                    const badgeDocumento = res.data.requiere_documento
                        ? '<span class="badge badge-info">Sí</span>'
                        : '<span class="badge badge-secondary">No</span>';

                    // Botones de acción
                    const botones = `
                        <button class="btn btn-sm btn-warning"
                            onclick="editTipoPermiso('${res.data.id}')">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn btn-danger btn-sm"
                            onclick="deleteTipoPermiso('${res.data.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;

                    const row = tabla.row.add([
                        res.data.numero || (tabla.rows().count() + 1),
                        res.data.nombre,
                        badgeGoce,
                        badgeRecupero,
                        badgeDocumento,
                        res.data.fecha,
                        botones
                    ]).draw(false).node();

                    $(row).attr('id', 'row' + res.data.id);
                    $(row).find('td:eq(0)').addClass('text-center');
                    $(row).find('td:eq(2)').addClass('text-center');
                    $(row).find('td:eq(3)').addClass('text-center');
                    $(row).find('td:eq(4)').addClass('text-center');
                    $(row).find('td:eq(5)').addClass('text-center');
                    $(row).find('td:eq(6)').addClass('text-center');

                    // Reenumerar
                    tabla.rows().every(function(i) {
                        this.cell(i, 0).data(i + 1);
                    });

                    new PNotify({
                        title: 'Éxito',
                        text: 'Tipo de permiso registrado correctamente.',
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
                        text: xhr.responseJSON?.message || 'Ocurrió un problema inesperado.',
                        type: 'error'
                    });
                }
            }
        });
    });
}