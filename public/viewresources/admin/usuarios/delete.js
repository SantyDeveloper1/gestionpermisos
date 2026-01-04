'use strict';

// ==================== CONFIGURAR CSRF ====================
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ==================== DOCUMENT READY ====================
$(document).ready(function() {
    
    // Evento para botones de eliminar
    $(document).on('click', '.btn-delete', function() {
        const userId = $(this).data('id');
        const userName = $(this).data('name');
        
        desactivarUsuario(userId, userName);
    });
    
});

// ==================== DESACTIVAR USUARIO ====================
function desactivarUsuario(userId, userName) {
    
    swal({
        title: 'Confirmar Eliminación',
        text: `¿Está seguro de que desea eliminar al usuario ${userName}? El usuario será desactivado y desaparecerá de esta lista.`,
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

        const $row = $(`#usuarioRow${userId}`);
        const $btn = $row.find('.btn-danger');
        
        // Deshabilitar botón mientras se procesa
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: `${_urlBase}/admin/usuarios/desactivar/${userId}`,
            type: 'POST',
            
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

                        // Verificar si quedan usuarios
                        if ($table.find('tbody tr').length === 0) {
                            $table.find('tbody').html(`
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> No hay usuarios registrados
                                    </td>
                                </tr>
                            `);
                        }
                    });

                    swal({
                        title: 'Usuario Eliminado',
                        text: response.message || 'El usuario ha sido desactivado correctamente.',
                        icon: 'success',
                        button: 'Aceptar'
                    });
                } else {
                    swal({
                        title: 'Error',
                        text: response.message || 'No se pudo completar la operación.',
                        icon: 'error',
                        button: 'Aceptar'
                    });
                    $btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            },
            
            error: function(xhr) {
                let errorMsg = 'Ha ocurrido un error inesperado al procesar la solicitud.';
                
                if (xhr.status === 422) {
                    if (xhr.responseJSON?.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg = errors.join('<br>');
                    }
                } else if (xhr.status === 404) {
                    errorMsg = xhr.responseJSON?.message || 'Usuario no encontrado en el sistema.';
                } else if (xhr.status === 500) {
                    errorMsg = xhr.responseJSON?.message || 'Ha ocurrido un error en el servidor.';
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
                console.error('Error al desactivar usuario:', xhr);
            }
        });
    });
}
