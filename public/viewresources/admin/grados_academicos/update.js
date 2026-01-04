'use strict';

// CSRF
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// READY
$(document).ready(function() {

    $('#btnActualizarGrado').on('click', function () {
        updateGrado();
    });

    $('#btnCancelarGrado').on('click', function () {
        cerrarModalGradoSeguro();
    });

    $('#editGradoModal').on('hidden.bs.modal', function() {
        limpiarModalGrado();
        $(this).removeAttr('aria-hidden');
    });

    $('#editGradoModal').on('show.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });
});

/**
 * Mostrar modal y llenar campos
 */
function showEditGrado(idGrado) {
    const row = $(`#gradoRow${idGrado}`);

    if (!row.length) return console.error('Grado no encontrado:', idGrado);

    const nombre = row.find('.tdNombreGrado').text().trim();

    $('#txtNombreGrado').val(nombre);

    $('#editGradoModal')
        .data('idGrado', idGrado)
        .modal('show');
}

/**
 * Limpiar modal
 */
function limpiarModalGrado() {
    $('#editGradoForm')[0].reset();
    $('#editGradoModal').removeData('idGrado');
    $('#btnActualizarGrado').prop('disabled', false).html('Guardar cambios');
}

/**
 * Cerrar modal
 */
function cerrarModalGradoSeguro() {
    $('#editGradoModal').find('input,button').blur();
    setTimeout(() => {
        $('#editGradoModal').modal('hide');
    }, 50);
}

/**
 * AJAX: actualizar grado
 */
function updateGrado() {
    const idGrado = $('#editGradoModal').data('idGrado');

    if (!idGrado) {
        showError('No se encontró el ID del grado');
        return;
    }

    const data = {
        nombre: $('#txtNombreGrado').val().trim()
    };

    if (!data.nombre) {
        showError('El nombre del grado es obligatorio');
        return;
    }

    const $btn = $('#btnActualizarGrado');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
        url: `${_urlBase}/admin/docente/grados-academicos/update/${idGrado}`,
        method: 'POST',
        data: data,
        success: function(response) {

            if (response.success) {
                updateTableRowGrado(idGrado, data);
                showSuccess('Grado académico actualizado correctamente');
                cerrarModalGradoSeguro();
            } else {
                showError(response.message || 'No se pudo actualizar el grado');
            }

        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                showError(errors.join('<br>'));
            } else {
                showError('Ocurrió un error al actualizar');
            }
        },
        complete: function() {
            $btn.prop('disabled', false).html('Guardar cambios');
        }
    });
}

/**
 * Actualizar fila en vista
 */
function updateTableRowGrado(idGrado, data) {
    const row = $(`#gradoRow${idGrado}`);
    if (row.length) {
        row.find('.tdNombreGrado').text(data.nombre);
    }
}

/**
 * Helpers
 */
function showSuccess(msg) {
    new PNotify({ title:'Éxito', text:msg, type:'success' });
}

function showError(msg) {
    new PNotify({ title:'Error', text:msg, type:'error' });
}
