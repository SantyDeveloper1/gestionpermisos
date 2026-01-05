'use strict';

// CSRF
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Función global para eliminar evidencia
window.deleteEvidencia = function(idEvidencia) {

    swal({
        title: 'Confirmar Eliminación',
        text: '¿Está seguro de que desea eliminar esta evidencia? Esta acción es irreversible y eliminará permanentemente el archivo y sus datos.',
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

        // Buscar la fila en la tabla
        let $row = $(`tr[data-evidencia-id="${idEvidencia}"]`);
        if (!$row.length) {
            // Si no tiene data-evidencia-id, buscar por el botón
            const $btn = $(`.btn-delete-evidence[onclick*="${idEvidencia}"]`);
            $row = $btn.closest('tr');
        }
        
        const $deleteBtn = $row.find('.btn-delete-evidence');
        
        // Deshabilitar botón mientras se procesa
        $deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: `${_urlBase}/admin/evidencia_recuperacion/delete/${idEvidencia}`,
            type: 'POST',
            data: { _method: 'DELETE' },
            success: function(response) {
                const ok = response && (response.status === 'success' || response.success === true);
                
                if (ok) {
                    // Obtener la tabla DataTable si existe
                    const table = $('#tablaExample2').DataTable();
                    
                    if (table) {
                        // Si es DataTable, usar su API para eliminar la fila
                        table.row($row).remove().draw(false);
                    } else {
                        // Si no es DataTable, eliminar con animación normal
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Renumerar las filas restantes
                            $('table tbody tr').each(function(index) {
                                $(this).find('td:first-child').text(index + 1);
                            });
                        });
                    }

                    new PNotify({
                        title: 'Eliminación Exitosa',
                        text: response.message || 'La evidencia ha sido eliminada exitosamente del sistema.',
                        type: 'success'
                    });
                } else {
                    new PNotify({
                        title: 'Error',
                        text: response.message || 'No se pudo completar la operación de eliminación.',
                        type: 'error'
                    });
                    $deleteBtn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
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
                    errorMsg = xhr.responseJSON?.message || 'Evidencia no encontrada en el sistema.';
                } else if (xhr.status === 500) {
                    errorMsg = xhr.responseJSON?.message || 'Ha ocurrido un error en el servidor. Por favor, contacte al administrador del sistema.';
                } else if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                new PNotify({
                    title: 'No se puede eliminar',
                    text: errorMsg,
                    type: 'warning'
                });
                
                $deleteBtn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                console.error('Error al eliminar evidencia:', xhr);
            }
        });
    });
};