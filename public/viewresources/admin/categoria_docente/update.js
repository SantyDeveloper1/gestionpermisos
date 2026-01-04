'use strict';

// CSRF
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// READY
$(document).ready(function() {

    $('#btnActualizarCategoria').on('click', function () {
        updateCategoria();
    });

    $('#btnCancelarCategoria').on('click', function () {
        cerrarModalCategoriaSeguro();
    });

    $('#editCategoriaModal').on('hidden.bs.modal', function() {
        limpiarModalCategoria();
        $(this).removeAttr('aria-hidden');
    });

    $('#editCategoriaModal').on('show.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });
});

/**
 * Mostrar modal y llenar campos
 */
function showEditCategoria(idCategoria) {
    const row = $(`#categoriaRow${idCategoria}`);

    if (!row.length) return console.error('Categoría no encontrada:', idCategoria);

    const nombre = row.find('.tdNombreCategoria').text().trim();

    $('#txtNombreCategoria').val(nombre);

    $('#editCategoriaModal')
        .data('idCategoria', idCategoria)
        .modal('show');
}

/**
 * Limpiar modal
 */
function limpiarModalCategoria() {
    $('#editCategoriaForm')[0].reset();
    $('#editCategoriaModal').removeData('idCategoria');
    $('#btnActualizarCategoria')
        .prop('disabled', false)
        .html('Guardar cambios');
}

/**
 * Cerrar modal
 */
function cerrarModalCategoriaSeguro() {
    $('#editCategoriaModal').find('input,button').blur();
    setTimeout(() => {
        $('#editCategoriaModal').modal('hide');
    }, 50);
}

/**
 * AJAX: actualizar categoría docente
 */
function updateCategoria() {
    const idCategoria = $('#editCategoriaModal').data('idCategoria');

    if (!idCategoria) {
        showError('No se encontró el ID de la categoría');
        return;
    }

    const data = {
        nombre: $('#txtNombreCategoria').val().trim()
    };

    if (!data.nombre) {
        showError('El nombre de la categoría es obligatorio');
        return;
    }

    const $btn = $('#btnActualizarCategoria');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
        url: `${_urlBase}/admin/docente/categoria-docente/update/${idCategoria}`,
        method: 'POST',
        data: data,
        success: function(response) {

            if (response.status === 'success') {
                updateTableRowCategoria(idCategoria, data);
                showSuccess('Categoría docente actualizada correctamente');
                cerrarModalCategoriaSeguro();
            } else {
                showError(response.message || 'No se pudo actualizar la categoría docente');
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
function updateTableRowCategoria(idCategoria, data) {
    const row = $(`#categoriaRow${idCategoria}`);
    if (row.length) {
        row.find('.tdNombreCategoria').text(data.nombre);
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