'use strict';

// ================================
// ABRIR MODAL Y CARGAR DATOS
// ================================
window.editTipoPermiso = function(id) {
    
    // Obtener la fila
    const row = $(`#row${id}`);
    
    if (row.length === 0) {
        new PNotify({
            title: 'Error',
            text: 'No se encontró el registro.',
            type: 'error'
        });
        return;
    }

    // Extraer datos de la fila
    const nombre = row.find('td:eq(1)').text().trim();
    const goceHaber = row.find('td:eq(2) .badge').text().trim() === 'Sí';
    const recupero = row.find('td:eq(3) .badge').text().trim() === 'Sí';
    const documento = row.find('td:eq(4) .badge').text().trim() === 'Sí';

    // Cargar datos en el modal
    $('#editNombre').val(nombre);
    $('#editDescripcion').val(''); // No tenemos descripción en la tabla
    $('#editRequiereRecupero').prop('checked', recupero);
    $('#editConGoceHaber').prop('checked', goceHaber);
    $('#editRequiereDocumento').prop('checked', documento);

    // Guardar ID en el modal
    $('#editTipoPermisoModal').data('id', id);

    // Abrir modal
    $('#editTipoPermisoModal').modal('show');
};

// ================================
// ACTUALIZAR TIPO PERMISO
// ================================
$('#btnActualizarTipoPermiso').on('click', function() {
    
    const id = $('#editTipoPermisoModal').data('id');
    const nombre = $('#editNombre').val().trim();

    if (!nombre) {
        new PNotify({
            title: 'Validación',
            text: 'El nombre es obligatorio.',
            type: 'error'
        });
        return;
    }

    swal({
        title: 'Confirmar actualización',
        text: '¿Desea guardar los cambios?',
        icon: 'warning',
        buttons: ['Cancelar', 'Sí, actualizar']
    }).then(ok => {
        if (!ok) return;

        const data = {
            nombre: nombre,
            descripcion: $('#editDescripcion').val().trim(),
            requiere_recupero: $('#editRequiereRecupero').is(':checked') ? 1 : 0,
            con_goce_haber: $('#editConGoceHaber').is(':checked') ? 1 : 0,
            requiere_documento: $('#editRequiereDocumento').is(':checked') ? 1 : 0
        };

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: `${_urlBase}/admin/tipo_permiso/update/${id}`,
            type: 'POST',
            data: data,

            success: function(res) {
                if (res.success) {
                    
                    // Actualizar fila en la tabla
                    const row = $(`#row${id}`);
                    
                    row.find('td:eq(1)').text(data.nombre);
                    
                    // Actualizar badges
                    const badgeGoce = data.con_goce_haber 
                        ? '<span class="badge badge-success">Sí</span>'
                        : '<span class="badge badge-secondary">No</span>';
                    
                    const badgeRecupero = data.requiere_recupero
                        ? '<span class="badge badge-warning">Sí</span>'
                        : '<span class="badge badge-secondary">No</span>';
                    
                    const badgeDocumento = data.requiere_documento
                        ? '<span class="badge badge-info">Sí</span>'
                        : '<span class="badge badge-secondary">No</span>';

                    row.find('td:eq(2)').html(badgeGoce);
                    row.find('td:eq(3)').html(badgeRecupero);
                    row.find('td:eq(4)').html(badgeDocumento);

                    new PNotify({
                        title: 'Éxito',
                        text: res.message || 'Tipo de permiso actualizado correctamente.',
                        type: 'success'
                    });

                    $('#editTipoPermisoModal').modal('hide');
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
                        text: xhr.responseJSON?.message || 'Error al actualizar.',
                        type: 'error'
                    });
                }
            },

            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Guardar cambios');
            }
        });
    });
});
