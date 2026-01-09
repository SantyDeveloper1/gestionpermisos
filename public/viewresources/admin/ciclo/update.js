'use strict';

// =========================================
// CSRF PARA AJAX
// =========================================
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// =========================================
// READY
// =========================================
$(document).ready(function () {

    // Botón actualizar
    $('#btnActualizarCiclo').on('click', function () {
        updateCiclo();
    });

    // Botón cancelar
    $('#btnCancelarCiclo').on('click', function () {
        cerrarModalCicloSeguro();
    });

    // Al cerrar modal
    $('#editCicloModal').on('hidden.bs.modal', function () {
        limpiarModalCiclo();
        $(this).removeAttr('aria-hidden');
    });

    // Al abrir modal
    $('#editCicloModal').on('show.bs.modal', function () {
        $(this).removeAttr('aria-hidden');
    });

    // Evitar autofocus
    $('#editCicloModal').on('shown.bs.modal', function () {
        setTimeout(() => {
            const focused = $(':focus');
            if (focused.is('input, select, textarea')) focused.blur();
        }, 150);
    });
});

// =========================================
// ABRIR MODAL Y LLENAR CON DATOS
// =========================================
function showEditCiclo(idCiclo) {

    const row = $(`#cicloRow${idCiclo}`);

    if (!row.length) {
        console.error('Fila de ciclo no encontrada:', idCiclo);
        return;
    }

    // Leer datos de la tabla
    const nombre = row.find('.tdNombreCiclo').text().trim();
    const numero = row.find('.tdNumeroCiclo').text().trim();

    // Cargar en el modal
    $('#txtNombreCiclo').val(nombre);
    $('#txtNumeroCiclo').val(numero);

    // Guardar el idCiclo en el modal
    $('#editCicloModal').data('idCiclo', idCiclo);

    // Mostrar modal
    $('#editCicloModal').modal('show');
}

// =========================================
// LIMPIAR MODAL
// =========================================
function limpiarModalCiclo() {
    $('#editCicloForm')[0].reset();
    $('#editCicloModal').removeData('idCiclo');
    $('#btnActualizarCiclo').prop('disabled', false).html('Guardar cambios');
}

// =========================================
// CERRAR MODAL
// =========================================
function cerrarModalCicloSeguro() {
    $('#editCicloModal').find('button, input').blur();
    setTimeout(() => { $('#editCicloModal').modal('hide'); }, 50);
}

// =========================================
// ACTUALIZAR CICLO POR AJAX
// =========================================
function updateCiclo() {

    const idCiclo = $('#editCicloModal').data('idCiclo');

    if (!idCiclo) {
        showError('No se encontró el ID del ciclo');
        return;
    }

    const data = {
        NombreCiclo: $('#txtNombreCiclo').val().trim(),
        NumeroCiclo: $('#txtNumeroCiclo').val().trim()
    };

    if (!data.NombreCiclo || !data.NumeroCiclo) {
        showError('Todos los campos son obligatorios');
        return;
    }

    const $btn = $('#btnActualizarCiclo');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
        url: `${_urlBase}/admin/academico/ciclo/update/${idCiclo}`,
        method: 'POST',
        data: data,
        success: function (response) {

            if (response.status === 'success') {
                updateTableRowCiclo(idCiclo, data);
                showSuccess('Ciclo actualizado correctamente');
                cerrarModalCicloSeguro();
            } else {
                showError(response.message || 'No se pudo actualizar el ciclo');
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                showError(errors.join('<br>'));
            } else {
                showError('Ocurrió un error al actualizar');
            }
        },
        complete: function () {
            $btn.prop('disabled', false).html('Guardar cambios');
        }
    });
}

// =========================================
// ACTUALIZAR FILA EN TABLA
// =========================================
function updateTableRowCiclo(idCiclo, data) {
    const row = $(`#cicloRow${idCiclo}`);
    if (row.length) {
        row.find('.tdNombreCiclo').text(data.NombreCiclo);
        row.find('.tdNumeroCiclo').text(data.NumeroCiclo);
    }
}

// =========================================
// NOTIFICACIONES
// =========================================
function showSuccess(msg) {
    new PNotify({ title: 'Éxito', text: msg, type: 'success' });
}

function showError(msg) {
    new PNotify({ title: 'Error', text: msg, type: 'error' });
}