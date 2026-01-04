'use strict';

// CSRF
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// READY
$(document).ready(function() {

    $('#btnActualizarContrato').on('click', function () {
        updateContrato();
    });

    $('#btnCancelarContrato').on('click', function () {
        cerrarModalContratoSeguro();
    });

    $('#editContratoModal').on('hidden.bs.modal', function() {
        limpiarModalContrato();
        $(this).removeAttr('aria-hidden');
    });

    $('#editContratoModal').on('show.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });
});

/**
 * Mostrar modal y llenar campos
 */
function showEditContrato(idContrato) {
    const row = $(`#contratoRow${idContrato}`);

    if (!row.length) return console.error('Contrato no encontrado:', idContrato);

    const nombre = row.find('.tdNombreContrato').text().trim();

    $('#txtNombreContrato').val(nombre);

    $('#editContratoModal')
        .data('idContrato', idContrato)
        .modal('show');
}

/**
 * Limpiar modal
 */
function limpiarModalContrato() {
    $('#editContratoForm')[0].reset();
    $('#editContratoModal').removeData('idContrato');
    $('#btnActualizarContrato')
        .prop('disabled', false)
        .html('Guardar cambios');
}

/**
 * Cerrar modal
 */
function cerrarModalContratoSeguro() {
    $('#editContratoModal').find('input,button').blur();
    setTimeout(() => {
        $('#editContratoModal').modal('hide');
    }, 50);
}

/**
 * AJAX: actualizar categoría docente
 */
function updateContrato() {
    const idContrato = $('#editContratoModal').data('idContrato');

    if (!idContrato) {
        showError('No se encontró el ID del contrato');
        return;
    }

    const data = {
        nombre: $('#txtNombreContrato').val().trim()
    };

    if (!data.nombre) {
        showError('El nombre del contrato es obligatorio');
        return;
    }

    const $btn = $('#btnActualizarContrato');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
        url: `${_urlBase}/admin/docente/tipo_contrato/update/${idContrato}`,
        method: 'POST',
        data: data,
        success: function(response) {

            if (response.status === 'success') {
                updateTableRowContrato(idContrato, data);
                showSuccess('Contrato actualizado correctamente');
                cerrarModalContratoSeguro();
            } else {
                showError(response.message || 'No se pudo actualizar el contrato');
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
function updateTableRowContrato(idContrato, data) {
    const row = $(`#contratoRow${idContrato}`);
    if (row.length) {
        row.find('.tdNombreContrato').text(data.nombre);
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
