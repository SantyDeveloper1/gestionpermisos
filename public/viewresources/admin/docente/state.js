'use strict';

// Configurar CSRF para AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Inicializar eventos cuando el documento esté listo
$(document).ready(function() {
    // Asignar evento al botón de guardar
    $('#btnGuardarEstadoDoc').on('click', function() {
        updateEstadoDocente();
    });

    // SOLUCIÓN PARA CLICK FUERA DEL MODAL - ESTADO DOCENTE
    $('#estadoDocenteModal').on('hide.bs.modal', function(e) {
        // Si el cierre es por click fuera del modal (backdrop)
        if (e.target === this) {
            // Quitar el foco inmediatamente del modal
            $(this).find('button, input, select, textarea').blur();
            
            // Pequeño delay para asegurar que el foco se quitó
            setTimeout(() => {
                // Forzar el blur en el documento también
                $(document.activeElement).blur();
            }, 10);
        } else {
            // Para cierres normales (botones cancelar, etc.)
            $(this).find('button, input, select').blur();
        }
    });

    // Cuando el modal se cierra completamente
    $('#estadoDocenteModal').on('hidden.bs.modal', function() {
        limpiarModalEstado();
        // Asegurar que no quede aria-hidden
        $(this).removeAttr('aria-hidden');
    });

    // Cuando el modal se abre
    $('#estadoDocenteModal').on('show.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });

    // Cuando el modal termina de abrirse - EVITAR AUTOFOCUS
    $('#estadoDocenteModal').on('shown.bs.modal', function() {
        setTimeout(() => {
            // Quitar el foco automático de cualquier elemento
            const focusedElement = $(':focus');
            if (focusedElement.is('input, select, textarea, button')) {
                focusedElement.blur();
            }
        }, 150);
    });

    /**
     * Mensaje dinámico del modal
     */
    function actualizarMensajeEstadoDoc() {
        const estado = $('input[name="estadoDocente"]:checked').val();
        const msg = $('#estadoMensajeDoc');

        if (estado == "1") {
            msg.html('<i class="fas fa-info-circle text-primary"></i><span class="ml-2"> El docente estará activo y disponible para asignaciones.</span>')
               .css({
                   'background-color': '#d4edda',
                   'border-left': '4px solid #28a745'
               });

        } else if (estado == "0") {
            msg.html('<i class="fas fa-info-circle text-primary"></i><span class="ml-2"> El docente estará inactivo y no podrá ser asignado.</span>')
               .css({
                   'background-color': '#f8d7da',
                   'border-left': '4px solid #dc3545'
               });

        } else {
            msg.html('<i class="fas fa-info-circle text-primary"></i><span class="ml-2">Aquí aparecerá la descripción del estado seleccionado.</span>')
               .css({
                   'background-color': '#e8f4fc',
                   'border-left': '4px solid #007bff'
               });
        }
    }

    $('input[name="estadoDocente"]').on('change', actualizarMensajeEstadoDoc);
    $('#estadoDocenteModal').on('shown.bs.modal', actualizarMensajeEstadoDoc);
});

/**
 * Abrir modal para cambiar estado de Docente
 */
window.toggleEstadoDocente = function(idDocente, nombreDocente) {
    const row = $(`#docRow${idDocente}`);
    const activo = row.find('.badge-success').length > 0;

    // Radios
    $('#radioDocActivo').prop('checked', activo);
    $('#radioDocInactivo').prop('checked', !activo);

    // Nombre dinámico
    $('#nombreDocEstado').text(nombreDocente);

    // Guardar ID en el modal
    $('#estadoDocenteModal')
        .data('idDocente', idDocente)
        .modal('show');
};

/**
 * Limpiar el modal de estado
 */
function limpiarModalEstado() {
    $('#estadoDocenteModal').removeData('idDocente');
    $('#btnGuardarEstadoDoc').prop('disabled', false).html('Guardar cambios');
}

/**
 * Cerrar el modal de estado de manera segura
 */
function cerrarModalEstadoSeguro() {
    // Quitar el foco primero de cualquier elemento
    $('#estadoDocenteModal').find('button, input, select').blur();
    
    // Pequeño delay antes de cerrar
    setTimeout(() => {
        $('#estadoDocenteModal').modal('hide');
    }, 50);
}

/**
 * Actualizar Estado Docente AJAX
 */
function updateEstadoDocente() {
    const idDocente = $('#estadoDocenteModal').data('idDocente');
    const nuevoEstado = $('input[name="estadoDocente"]:checked').val();

    if (!idDocente) {
        showError('No se encontró el ID del docente');
        return;
    }

    if (nuevoEstado === undefined) {
        showError('Debe seleccionar un estado');
        return;
    }

    const $btn = $('#btnGuardarEstadoDoc');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: `${_urlBase}/admin/docente/estado/${idDocente}`,
        method: 'POST',
        data: { 
            estado: nuevoEstado,
            _method: 'PUT'
        },
        success: function(response) {
            if (response.success) {
                const row = $(`#docRow${idDocente}`);
                const btnEditar = row.find('.btn-warning');
                const btnEliminar = row.find('.btn-danger');
                const btnEstadoIcon = row.find('.btn-info i');
                
                // ⭐ Actualizar badge dinámicamente
                if (nuevoEstado == "1") {
                    row.find('td:eq(8)')
                        .html('<span class="badge badge-success">Activo</span>');
                    btnEditar.prop('disabled', false);
                    btnEliminar.prop('disabled', false);
                    btnEstadoIcon.removeClass('fa-toggle-off')
                                 .addClass('fa-toggle-on');
                } else {
                    row.find('td:eq(8)')
                        .html('<span class="badge badge-danger">Inactivo</span>');
                    btnEditar.prop('disabled', true);
                    btnEliminar.prop('disabled', true);
                    btnEstadoIcon.removeClass('fa-toggle-on')
                                 .addClass('fa-toggle-off');
                }
                
                showSuccess('Estado actualizado correctamente');
                cerrarModalEstadoSeguro();
            } else {
                showError(response.message || 'Error al actualizar el estado');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                showError(errors.join('<br>'));
            } else if (xhr.status === 500) {
                showError('Error del servidor. Por favor, intente nuevamente.');
            } else {
                showError('No se pudo actualizar el estado');
            }
        },
        complete: function() {
            $btn.prop('disabled', false).html('Guardar cambios');
        }
    });
}

/**
 * Mostrar mensaje de éxito
 */
function showSuccess(message) {
    new PNotify({
        title: 'Éxito',
        text: message,
        type: 'success',
        delay: 3000
    });
}

/**
 * Mostrar mensaje de error
 */
function showError(message) {
    new PNotify({
        title: 'Error',
        text: message,
        type: 'error',
        delay: 4000
    });
}