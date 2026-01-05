'use strict';

// CSRF
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

/* =========================
   Funciones de Evidencias
========================= */

/**
 * Descargar evidencia
 */
function downloadEvidence(evidenceId) {
    const url = `${_urlBase}/admin/evidencia_recuperacion/download/${evidenceId}`;
    window.open(url, '_blank');
    
    new PNotify({
        title: 'Descarga iniciada',
        text: 'El archivo se está descargando...',
        type: 'info',
        delay: 2000
    });
}

/**
 * Eliminar evidencia
 */
function deleteEvidence(evidenceId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará la evidencia permanentemente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${_urlBase}/admin/evidencia_recuperacion/delete/${evidenceId}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        new PNotify({
                            title: 'Éxito',
                            text: 'Evidencia eliminada correctamente',
                            type: 'success',
                            delay: 2000
                        });
                        
                        // Eliminar la fila de la tabla
                        $(`#row${evidenceId}`).fadeOut(400, function() {
                            $(this).remove();
                            
                            // Si no quedan más evidencias, recargar la página
                            if ($('.table-evidence tbody tr:visible').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        new PNotify({
                            title: 'Error',
                            text: response.message || 'No se pudo eliminar la evidencia',
                            type: 'error',
                            delay: 3000
                        });
                    }
                },
                error: function(xhr) {
                    new PNotify({
                        title: 'Error',
                        text: 'Error al eliminar la evidencia',
                        type: 'error',
                        delay: 3000
                    });
                }
            });
        }
    });
}

/**
 * Ver evidencia en modal o nueva pestaña
 */
function viewEvidence(evidenceId) {
    const url = `${_urlBase}/admin/evidencia_recuperacion/ver/${evidenceId}`;
    window.open(url, '_blank');
}