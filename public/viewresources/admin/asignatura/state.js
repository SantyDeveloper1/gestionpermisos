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
    $('#btnGuardarEstadoAsig').on('click', function() {
        updateEstadoAsignatura();
    });

    // SOLUCIÓN PARA CLICK FUERA DEL MODAL - ESTADO ASIGNATURA
    $('#estadoAsignaturaModal').on('hide.bs.modal', function(e) {
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
    $('#estadoAsignaturaModal').on('hidden.bs.modal', function() {
        limpiarModalEstado();
        // Asegurar que no quede aria-hidden
        $(this).removeAttr('aria-hidden');
    });

    // Cuando el modal se abre
    $('#estadoAsignaturaModal').on('show.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });

    // Cuando el modal termina de abrirse - EVITAR AUTOFOCUS
    $('#estadoAsignaturaModal').on('shown.bs.modal', function() {
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
    function actualizarMensajeEstadoAsig() {
        const estado = $('input[name="estadoAsignatura"]:checked').val();
        const msg = $('#estadoMensajeAsig');

        if (estado == "1") {
            msg.html('<i class="fas fa-info-circle text-primary"></i><span class="ml-2"> La asignatura estará habilitada y disponible para uso.</span>')
               .removeClass('danger')
               .addClass('success');

        } else if (estado == "0") {
            msg.html('<i class="fas fa-info-circle text-primary"></i><span class="ml-2"> La asignatura estará deshabilitada y no podrá ser utilizada.</span>')
               .removeClass('success')
               .addClass('danger');

        } else {
            msg.html('<i class="fas fa-info-circle text-primary"></i><span class="ml-2">Aquí aparecerá la descripción del estado seleccionado.</span>')
               .removeClass('success danger')
               .css({
                   'background-color': '#e3f5ff',
                   'border-left': '4px solid #007bff'
               });
        }
    }

    $('input[name="estadoAsignatura"]').on('change', actualizarMensajeEstadoAsig);
    $('#estadoAsignaturaModal').on('shown.bs.modal', actualizarMensajeEstadoAsig);
});

/**
 * Abrir modal para cambiar estado de Asignatura
 */
window.toggleEstadoAsignatura = function(idAsignatura, nombreAsignatura) {
    const row = $(`#asigRow${idAsignatura}`);
    const activo = row.find('.badge-success').length > 0;

    // Radios
    $('#radioAsigActivo').prop('checked', activo);
    $('#radioAsigInactivo').prop('checked', !activo);

    // Nombre dinámico
    $('#nombreAsignaturaEstado').text(nombreAsignatura);

    // Guardar ID en el modal
    $('#estadoAsignaturaModal')
        .data('idAsignatura', idAsignatura)
        .modal('show');
};

/**
 * Limpiar el modal de estado
 */
function limpiarModalEstado() {
    $('#estadoAsignaturaModal').removeData('idAsignatura');
    $('#btnGuardarEstadoAsig').prop('disabled', false).html('Guardar Cambios');
}

/**
 * Cerrar el modal de estado de manera segura
 */
function cerrarModalEstadoSeguro() {
    // Quitar el foco primero de cualquier elemento
    $('#estadoAsignaturaModal').find('button, input, select').blur();
    
    // Pequeño delay antes de cerrar
    setTimeout(() => {
        $('#estadoAsignaturaModal').modal('hide');
    }, 50);
}

/**
 * Actualizar Estado Asignatura AJAX
 */
function updateEstadoAsignatura() {
    const idAsignatura = $('#estadoAsignaturaModal').data('idAsignatura');
    const nuevoEstado  = $('input[name="estadoAsignatura"]:checked').val();

    if (!idAsignatura) {
        showError('No se encontró el ID de la asignatura');
        return;
    }

    if (!nuevoEstado) {
        showError('Debe seleccionar un estado');
        return;
    }
    const $btn = $('#btnGuardarEstadoAsig');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    $.ajax({
        url: `${_urlBase}/admin/academico/asignatura/estado/${idAsignatura}`,
        method: 'POST',
        data: { estado: nuevoEstado, _method: 'PUT' },
        success: function(response) {
            if (response.success) {
                const row = $(`#asigRow${idAsignatura}`);
                const btnEditar   = row.find('.btn-warning');
                const btnEliminar = row.find('.btn-danger');
                // Botón correcto: btn-info
                const btnEstadoIcon = row.find('.btn-info i');
                // ⭐ Actualizar badge dinámicamente
                if (nuevoEstado == "1") {
                    row.find('.tdEstado')
                        .html('<span class="badge badge-success">Activo</span>');
                    btnEditar.prop('disabled', false);
                    btnEliminar.prop('disabled', false);
                    btnEstadoIcon.removeClass('fa-toggle-off')
                                 .addClass('fa-toggle-on');
                } else {
                    row.find('.tdEstado')
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
            $btn.prop('disabled', false).html('Guardar Cambios');
        }
    });
}

/**
 * Mostrar mensaje de éxito
 */
function showSuccess(message) {
    // Usar PNotify en lugar de swal para consistencia
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