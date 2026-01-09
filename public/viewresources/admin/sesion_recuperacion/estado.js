/**
 * estado.js - Manejo del modal de validación de estado de sesión
 * Gestiona el cambio de estado de las sesiones de recuperación
 */

$(document).ready(function() {
    // Event delegation para botones de cambiar estado
    $(document).on('click', '.btn-cambiar-estado', function() {
        const idSesion = $(this).data('sesion-id');
        const estadoActual = $(this).attr('data-estado-actual'); // Usar .attr() en lugar de .data()
        abrirModalValidacion(idSesion, estadoActual);
    });

    // Mostrar campo de comentario cuando se selecciona un nuevo estado
    $('#nuevoEstado').on('change', function() {
        if ($(this).val()) {
            $('#divComentario').slideDown();
        } else {
            $('#divComentario').slideUp();
        }
    });

    // Confirmar cambio de estado
    $('#btnConfirmarCambio').on('click', function() {
        const idSesion = $('#modalValidarSesion').data('id-sesion');
        const nuevoEstado = $('#nuevoEstado').val();
        const comentario = $('#comentario').val();
        
        if (!nuevoEstado) {
            new PNotify({
                title: 'Validación',
                text: 'Por favor seleccione un nuevo estado',
                type: 'warning'
            });
            return;
        }
        
        // Confirmar con SweetAlert
        swal({
            title: 'Confirmar cambio',
            text: `¿Está seguro de cambiar el estado de la sesión a ${nuevoEstado}?`,
            icon: 'warning',
            buttons: ['No, cancelar', 'Sí, cambiar']
        }).then((proceed) => {
            if (proceed) {
                actualizarEstadoSesion(idSesion, nuevoEstado, comentario);
            }
        });
    });
});

/**
 * Abre el modal de validación de estado
 * @param {number} idSesion - ID de la sesión
 * @param {string} estadoActual - Estado actual de la sesión
 */
function abrirModalValidacion(idSesion, estadoActual) {
    // Guardar el ID de la sesión en el modal
    $('#modalValidarSesion').data('id-sesion', idSesion);
    
    // Establecer el estado actual
    $('#estadoActual').val(estadoActual);
    
    // Limpiar y configurar el select de nuevo estado
    $('#nuevoEstado').val('');
    $('#comentario').val('');
    $('#divComentario').hide();
    
    // Mensaje de validación
    $('#mensajeValidacion').html(`
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            Está a punto de cambiar el estado de la sesión <strong>#${idSesion}</strong>
            <br>
            <strong>Estado actual:</strong> ${estadoActual}
        </div>
    `);
    
    // Mostrar el modal
    $('#modalValidarSesion').modal('show');
}

/**
 * Actualiza el estado de la sesión vía AJAX
 */
function actualizarEstadoSesion(idSesion, nuevoEstado, comentario) {
    const btnConfirmar = $('#btnConfirmarCambio');
    
    // Deshabilitar botón mientras se procesa
    btnConfirmar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Actualizando...');
    
    // Petición AJAX para actualizar el estado
    $.ajax({
        url: `${baseUrl}/admin/sesion_recuperacion/update-estado/${idSesion}`,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            estado_sesion: nuevoEstado,
            comentario: comentario
        },
        success: function(response) {
            if (response.success) {
                new PNotify({
                    title: '¡Éxito!',
                    text: response.message || 'Estado de la sesión actualizado correctamente',
                    type: 'success'
                });
                
                // Cerrar modal
                $('#modalValidarSesion').modal('hide');
                
                // Actualizar la fila en la tabla
                if (response.sesion) {
                    actualizarFilaTablaSesion(response.sesion);
                } else {
                    // Si no viene el objeto, recargar después de 1 segundo
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            } else {
                new PNotify({
                    title: 'Error',
                    text: response.message || 'No se pudo actualizar el estado',
                    type: 'error'
                });
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error al actualizar el estado';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                errorMsg = Object.values(errors).flat().join('<br>');
            }
            
            new PNotify({
                title: 'Error',
                text: errorMsg,
                type: 'error'
            });
        },
        complete: function() {
            // Rehabilitar botón
            btnConfirmar.prop('disabled', false).html('Confirmar Cambio');
        }
    });
}

/**
 * Actualiza la fila de la tabla con los nuevos datos de la sesión
 */
function actualizarFilaTablaSesion(sesion) {
    const row = $(`#sesionRow${sesion.id_sesion}`);
    
    if (row.length) {
        // Actualizar el badge de estado (columna 8: Estado)
        let estadoBadge = '';
        let estadoIcon = '';
        let estadoColor = '';
        
        switch(sesion.estado_sesion) {
            case 'PROGRAMADA':
                estadoIcon = 'fa-clock';
                estadoColor = 'background: rgba(253, 203, 110, 0.1); color: #f39c12;';
                break;
            case 'REALIZADA':
                estadoIcon = 'fa-check-circle';
                estadoColor = 'background: rgba(0, 184, 148, 0.1); color: var(--success-green);';
                break;
            case 'VALIDADA':
                estadoIcon = 'fa-check-double';
                estadoColor = 'background: rgba(0, 139, 220, 0.1); color: var(--primary-blue);';
                break;
            case 'CANCELADA':
                estadoIcon = 'fa-times-circle';
                estadoColor = 'background: rgba(225, 112, 85, 0.1); color: var(--danger-red);';
                break;
        }
        
        estadoBadge = `
            <span class="status-indicator-execution" style="${estadoColor}">
                <i class="fas ${estadoIcon} mr-1"></i>
                ${sesion.estado_sesion}
            </span>
        `;
        
        // Actualizar la celda de estado (columna 8)
        row.find('td:eq(8)').html(estadoBadge);
        
        // Actualizar el atributo data-estado de la fila
        row.attr('data-estado', sesion.estado_sesion);
        
        // Actualizar el atributo data-estado-actual del botón
        const btnCambiar = row.find('.btn-cambiar-estado');
        btnCambiar.attr('data-estado-actual', sesion.estado_sesion);
    }
}
