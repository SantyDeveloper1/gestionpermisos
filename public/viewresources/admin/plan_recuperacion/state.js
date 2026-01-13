/**
 * state.js - Manejo del cambio de estado de planes de recuperación
 * Gestiona el modal y la actualización de estados de planes
 */

// Variable global para almacenar los datos del plan actual
let currentPlanData = null;

// Función para mostrar/ocultar el campo de observación
function toggleObservacion() {
    const estado = $('#editEstado').val();
    const observacionContainer = $('#observacionContainer');
    
    if (estado === 'OBSERVADO') {
        observacionContainer.slideDown(300);
    } else {
        observacionContainer.slideUp(300);
        $('#editObservacion').val(''); // Limpiar el campo
    }
}

// Función para determinar la clase CSS según el estado
function getEstadoClass(estado) {
    switch(estado) {
        case 'PRESENTADO':
            return 'badge-presentado';
        case 'APROBADO':
            return 'badge-aprobado';
        case 'OBSERVADO':
            return 'badge-observado';
        case 'CANCELADO':
            return 'badge-cancelado';
        default:
            return 'badge-presentado';
    }
}

// Función para obtener el HTML del badge según el estado
function getEstadoBadgeHTML(estado) {
    let badgeClass = '';
    let dotClass = '';
    
    switch(estado) {
        case 'PRESENTADO':
            badgeClass = 'badge-presentado';
            dotClass = 'dot-presentado';
            break;
        case 'APROBADO':
            badgeClass = 'badge-aprobado';
            dotClass = 'dot-aprobado';
            break;
        case 'OBSERVADO':
            badgeClass = 'badge-observado';
            dotClass = 'dot-observado';
            break;
        case 'CANCELADO':
            badgeClass = 'badge-cancelado';
            dotClass = 'dot-cancelado';
            break;
        default:
            badgeClass = 'badge-presentado';
            dotClass = 'dot-presentado';
    }
    
    return `
        <span class="badge-modern ${badgeClass}">
            <span class="status-dot ${dotClass}"></span>
            ${estado}
        </span>
    `;
}

// Sobrescribir la función editPlan para usar el nuevo modal
window.editPlan = function(id) {
    $.ajax({
        url: _urlBase + '/admin/plan_recuperacion/' + id,
        type: 'GET',
        success: function(response) {
            // Guardar los datos completos del plan
            currentPlanData = response;
            
            // Guardar el ID del plan
            $('#editIdPlan').val(response.id_plan);
            
            // Mostrar estado actual con badge
            const estadoActual = $('#editEstadoActual');
            estadoActual.html(getEstadoBadgeHTML(response.estado_plan));
            
            // Limpiar y preparar el select de nuevo estado
            $('#editEstado').val('');
            $('#editObservacion').val('');
            $('#observacionContainer').hide();
            
            // Mostrar el modal
            $('#editPlanModal').modal('show');
        },
        error: function(xhr) {
            new PNotify({
                title: 'Error',
                text: 'No se pudieron cargar los datos del plan',
                type: 'error'
            });
        }
    });
};

// Función para actualizar el estado del plan
window.updatePlan = function() {
    const idPlan = $('#editIdPlan').val();
    const nuevoEstado = $('#editEstado').val();
    const observacion = $('#editObservacion').val();
    
    // Validar que se haya seleccionado un estado
    if (!nuevoEstado) {
        new PNotify({
            title: 'Error de Validación',
            text: 'Por favor seleccione un estado',
            type: 'error'
        });
        return;
    }
    
    // Validar que si es OBSERVADO, tenga observación
    if (nuevoEstado === 'OBSERVADO' && !observacion.trim()) {
        new PNotify({
            title: 'Error de Validación',
            text: 'Por favor ingrese una observación cuando el plan es observado',
            type: 'error'
        });
        $('#editObservacion').focus();
        return;
    }
    
    // Confirmar con SweetAlert
    swal({
        title: 'Confirmar cambio de estado',
        text: `¿Está seguro de cambiar el estado del plan a ${nuevoEstado}?`,
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, cambiar']
    }).then((proceed) => {
        if (!proceed) return;
        
        // Preparar datos - incluir todos los campos requeridos
        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('estado_plan', nuevoEstado);
        
        // Incluir los campos requeridos del plan actual
        if (currentPlanData) {
            formData.append('fecha_presentacion', currentPlanData.fecha_presentacion);
            formData.append('total_horas_recuperar', currentPlanData.total_horas_recuperar);
        }
        
        // Incluir observación si existe
        if (observacion) {
            formData.append('observacion', observacion);
        }
        
        // Enviar petición AJAX
        $.ajax({
            url: _urlBase + '/admin/plan_recuperacion/update/' + idPlan,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Buscar la fila en la tabla - intentar múltiples selectores
                let $row = $(`tr:has(button[onclick*="editPlan('${idPlan}')"])`);
                
                if ($row.length === 0) {
                    $row = $(`tr:has(button[onclick*='editPlan("${idPlan}")'])`);
                }
                
                if ($row.length === 0) {
                    $row = $(`tr:has(button.btn-edit[onclick*="${idPlan}"])`);
                }
                
                if ($row.length > 0) {
                    // Actualizar estado (columna 5)
                    const estadoHTML = getEstadoBadgeHTML(nuevoEstado);
                    $row.find('td:eq(5)').html(estadoHTML);
                    
                    // Si se cambió a APROBADO, ocultar el botón de aprobar
                    if (nuevoEstado === 'APROBADO') {
                        $row.find('.btn-approve').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                }
                
                new PNotify({
                    title: '¡Éxito!',
                    text: response.message || 'Estado del plan actualizado correctamente',
                    type: 'success'
                });
                
                $('#editPlanModal').modal('hide');
            },
            error: function(xhr) {
                let errorMsg = 'No se pudo actualizar el estado del plan';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                new PNotify({
                    title: 'Error',
                    text: errorMsg,
                    type: 'error'
                });
            }
        });
    });
};

// Limpiar el modal cuando se cierra
$('#editPlanModal').on('hidden.bs.modal', function() {
    $('#editIdPlan').val('');
    $('#editEstado').val('');
    $('#editObservacion').val('');
    $('#observacionContainer').hide();
    $('#editEstadoActual').html('');
    currentPlanData = null; // Limpiar datos almacenados
});
