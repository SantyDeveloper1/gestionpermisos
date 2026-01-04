'use strict';

// CSRF
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Función global para eliminar docente
window.deleteDocente = function(idDocente) {

    swal({
        title: 'Confirmar Eliminación',
        text: '¿Está seguro de que desea eliminar este docente del sistema? Esta acción es irreversible y eliminará permanentemente todos sus datos.',
        icon: 'warning',
        buttons: {
            cancel: {
                text: 'Cancelar',
                visible: true,
                className: 'btn-secondary'
            },
            confirm: {
                text: 'Sí, eliminar',
                className: 'btn-danger'
            }
        },
        dangerMode: true
    }).then(function(willDelete) {
        if (!willDelete) return;

        const $row = $(`#docRow${idDocente}`);
        const $btn = $row.find('.btn-danger');
        
        // Deshabilitar botón mientras se procesa
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: `${_urlBase}/admin/docente/delete/${idDocente}`,
            type: 'POST',
            data: { _method: 'DELETE' },
            success: function(response) {
                const ok = response && (response.status === 'success' || response.success === true);
                
                if (ok) {
                    // Encontrar la tabla padre
                    const $table = $row.closest('table');
                    
                    // Eliminar la fila con animación
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Renumerar las filas restantes
                        $table.find('tbody tr').each(function(index) {
                            $(this).find('td:first-child').text(index + 1);
                        });
                    });

                    swal({
                        title: 'Eliminación Exitosa',
                        text: response.message || 'El docente ha sido eliminado exitosamente del sistema.',
                        icon: 'success',
                        button: 'Aceptar'
                    });
                } else {
                    swal({
                        title: 'Error',
                        text: response.message || 'No se pudo completar la operación de eliminación.',
                        icon: 'error',
                        button: 'Aceptar'
                    });
                    $btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Ha ocurrido un error inesperado al procesar la solicitud.';
                
                if (xhr.status === 422) {
                    // Errores de validación o restricciones de integridad
                    if (xhr.responseJSON?.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg = errors.join('<br>');
                    }
                } else if (xhr.status === 404) {
                    errorMsg = xhr.responseJSON?.message || 'Docente no encontrado en el sistema.';
                } else if (xhr.status === 500) {
                    errorMsg = xhr.responseJSON?.message || 'Ha ocurrido un error en el servidor. Por favor, contacte al administrador del sistema.';
                } else if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                swal({
                    title: 'No se puede eliminar',
                    text: errorMsg,
                    icon: 'warning',
                    button: 'Entendido'
                });
                
                $btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                console.error('Error al eliminar docente:', xhr);
            }
        });
    });
};