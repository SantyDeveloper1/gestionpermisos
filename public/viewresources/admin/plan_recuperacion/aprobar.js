'use strict';

// CSRF
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

/**
 * Aprobar plan de recuperación
 */
window.aprobarPlan = function(idPlan) {
    swal({
        title: 'Aprobar Plan de Recuperación',
        text: '¿Está seguro de que desea aprobar este plan de recuperación? Una vez aprobado, el docente podrá registrar sesiones de recuperación.',
        icon: 'info',
        buttons: {
            cancel: {
                text: 'Cancelar',
                visible: true,
                className: 'btn-danger'
            },
            confirm: {
                text: 'Sí, aprobar',
                className: 'btn-primary'
            }
        }
    }).then(function(willApprove) {
        if (!willApprove) return;

        // Buscar el botón y deshabilitarlo
        const $btn = $(`.btn-approve[onclick*="${idPlan}"]`);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: `${_urlBase}/admin/plan_recuperacion/aprobar/${idPlan}`,
            type: 'PATCH',
            success: function(response) {
                if (response && response.success) {
                    new PNotify({
                        title: '¡Aprobado!',
                        text: response.message || 'Plan de recuperación aprobado exitosamente.',
                        type: 'success'
                    });

                    // Actualizar la fila en la tabla
                    const $row = $btn.closest('tr');
                    
                    // Actualizar el badge de estado - buscar el badge correcto
                    const $estadoBadge = $row.find('.badge-modern');
                    if ($estadoBadge.length) {
                        $estadoBadge.removeClass('badge-presentado badge-observado')
                                   .addClass('badge-aprobado')
                                   .html('<span class="status-dot dot-aprobado"></span> APROBADO');
                    }
                    
                    // Remover el botón de aprobar con animación
                    $btn.fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    new PNotify({
                        title: 'Error',
                        text: response.message || 'No se pudo aprobar el plan.',
                        type: 'error'
                    });
                    $btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Ha ocurrido un error al aprobar el plan.';
                
                if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMsg = 'Plan de recuperación no encontrado.';
                } else if (xhr.status === 422) {
                    errorMsg = 'El plan no puede ser aprobado en su estado actual.';
                }
                
                new PNotify({
                    title: 'Error al aprobar',
                    text: errorMsg,
                    type: 'error'
                });
                
                $btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
                console.error('Error al aprobar plan:', xhr);
            }
        });
    });
};
