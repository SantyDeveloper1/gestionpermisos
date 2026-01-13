'use strict';

// Función para editar permiso
function editPermiso(id) {
    // Mostrar el modal inmediatamente
    $('#editPermisoModal').modal('show');
    
    // Resetear el formulario
    $('#frmPermisoEdit')[0].reset();
    
    // Mostrar estado de carga
    $('#editIdPermiso').val('Cargando...');
    $('#editEstadoPermiso').prop('disabled', true);
    $('#editFechaResolucion').prop('disabled', true);
    $('#editObservacion').prop('disabled', true);

    // Realizar petición AJAX para obtener los datos
    $.ajax({
        url: `permiso/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.permiso) {
                const permiso = response.permiso;
                
                // Verificar si el permiso tiene plan de recuperación
                if (permiso.planRecuperacion) {
                    // Si tiene plan de recuperación, deshabilitar la opción SOLICITADO
                    $('#editEstadoPermiso option[value="SOLICITADO"]').prop('disabled', true);
                    
                    // Mostrar mensaje informativo
                    new PNotify({
                        title: 'Información',
                        text: 'Este permiso tiene un plan de recuperación. No se puede cambiar el estado a SOLICITADO.',
                        type: 'info',
                        delay: 3000
                    });
                } else {
                    // Si no tiene plan, habilitar todas las opciones
                    $('#editEstadoPermiso option').prop('disabled', false);
                }
                
                // Llenar el formulario con los datos
                $('#editIdPermiso').val(permiso.id_permiso);
                $('#editEstadoPermiso').val(permiso.estado_permiso);
                $('#editFechaResolucion').val(permiso.fecha_resolucion || '');
                $('#editObservacion').val(permiso.observacion || '');
                
                // Mostrar fechas de creación y actualización
                if (permiso.created_at) {
                    $('#editCreatedAt').html(formatearFechaHora(permiso.created_at));
                }
                if (permiso.updated_at) {
                    $('#editUpdatedAt').html(formatearFechaHora(permiso.updated_at));
                }
                
                // Habilitar campos
                $('#editEstadoPermiso').prop('disabled', false);
                $('#editFechaResolucion').prop('disabled', false);
                $('#editObservacion').prop('disabled', false);
                
            } else {
                new PNotify({
                    title: 'Error',
                    text: 'No se pudieron cargar los datos del permiso.',
                    type: 'error'
                });
                $('#editPermisoModal').modal('hide');
            }
        },
        error: function(xhr) {
            console.error('Error al cargar permiso:', xhr);
            
            let errorMsg = 'Error al cargar los datos del permiso.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            new PNotify({
                title: 'Error',
                text: errorMsg,
                type: 'error'
            });
            
            $('#editPermisoModal').modal('hide');
        }
    });
}

// Función para actualizar permiso
function updatePermiso() {
    const id = $('#editIdPermiso').val();
    const formData = $('#frmPermisoEdit').serialize();
    
    // Validar que se haya seleccionado un estado
    if (!$('#editEstadoPermiso').val()) {
        new PNotify({
            title: 'Validación',
            text: 'Debe seleccionar un estado.',
            type: 'warning'
        });
        return;
    }
    
    // Confirmar actualización
    swal({
        title: 'Confirmar actualización',
        text: '¿Está seguro de actualizar este permiso?',
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, actualizar']
    }).then((proceed) => {
        if (proceed) {
            actualizarPermiso(id, formData);
        }
    });
}

// Función para realizar la actualización
function actualizarPermiso(id, formData) {
    const btnActualizar = $('#frmPermisoEdit button[type="submit"]');
    
    // Deshabilitar botón mientras se procesa
    btnActualizar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Actualizando...');

    $.ajax({
        url: `${_urlBase}/admin/permiso/update/${id}`,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                new PNotify({
                    title: '¡Éxito!',
                    text: response.message || 'Permiso actualizado correctamente.',
                    type: 'success'
                });

                // Cerrar modal
                $('#editPermisoModal').modal('hide');

                // Actualizar la fila en la tabla
                if (response.permiso) {
                    actualizarFilaTabla(response.permiso);
                } else {
                    // Si no viene el objeto, recargar la página
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            } else {
                new PNotify({
                    title: 'Error',
                    text: response.message || 'No se pudo actualizar el permiso.',
                    type: 'error'
                });
            }
        },
        error: function(xhr) {
            console.error('Error al actualizar:', xhr);
            
            let errorMsg = 'Ocurrió un error al actualizar el permiso.';
            
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
            btnActualizar.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Guardar Cambios');
        }
    });
}

// Función para actualizar la fila en la tabla
function actualizarFilaTabla(permiso) {
    const row = $(`#permisoRow${permiso.id_permiso}`);
    
    if (row.length) {
        // Actualizar el badge de estado (columna 6: N°, Docente, Tipo, Período, Duración, Recuperación, Estado)
        const estadoClass = `badge-estado badge-${permiso.estado_permiso.toLowerCase()}`;
        row.find('td:eq(6)').html(`<span class="${estadoClass}">${permiso.estado_permiso}</span>`);
        
        // Actualizar fechas de solicitud/resolución (columna 7)
        let fechasHtml = `<strong>Solicitado:</strong><br>${formatearFecha(permiso.fecha_solicitud)}`;
        if (permiso.fecha_resolucion) {
            fechasHtml += `<br><strong>Resuelto:</strong><br>${formatearFecha(permiso.fecha_resolucion)}`;
        }
        row.find('td:eq(7)').html(fechasHtml);
        
        // Actualizar created_at (columna 8)
        if (permiso.created_at) {
            row.find('td:eq(8)').html(formatearFechaHora(permiso.created_at));
        }
        
        // Actualizar updated_at (columna 9)
        if (permiso.updated_at) {
            row.find('td:eq(9)').html(formatearFechaHora(permiso.updated_at));
        }
        
        // Actualizar botones de acción si cambió el estado
        if (permiso.estado_permiso !== 'SOLICITADO') {
            // Remover botón de aprobar si existe
            row.find('.btn-success').remove();
        }
    }
}

// Función para formatear fecha
function formatearFecha(fecha) {
    if (!fecha) return '';
    
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const anio = date.getFullYear();
    
    return `${dia}/${mes}/${anio}`;
}

// Función para formatear fecha y hora
function formatearFechaHora(fechaHora) {
    if (!fechaHora) return '<strong>--/--/----</strong><br><small class="text-muted">--:-- --</small>';
    
    const date = new Date(fechaHora);
    
    // Formatear fecha
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const anio = date.getFullYear();
    
    // Formatear hora en formato 12 horas
    let horas = date.getHours();
    const minutos = String(date.getMinutes()).padStart(2, '0');
    const ampm = horas >= 12 ? 'PM' : 'AM';
    horas = horas % 12;
    horas = horas ? horas : 12; // La hora '0' debe ser '12'
    const horasStr = String(horas).padStart(2, '0');
    
    return `<strong>${dia}/${mes}/${anio}</strong><br><small class="text-muted">${horasStr}:${minutos} ${ampm}</small>`;
}
