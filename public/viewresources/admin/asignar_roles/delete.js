'use strict';

// ==================== CONFIGURAR CSRF ====================
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

/**
 * =================================
 * ELIMINAR ROL DEL USUARIO
 * =================================
 */
window.deleteRol = function(userId) {
    
    swal({
        title: 'Confirmar Eliminación',
        text: '¿Está seguro de que desea eliminar el rol de este usuario? Esta acción eliminará permanentemente el rol asignado.',
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

        $.ajax({
            url: `${_urlBase}/admin/usuarios/eliminar_rol/${userId}`,
            type: 'POST',
            
            success: function(response) {
                const ok = response && (response.status === 'success' || response.success === true);
                
                if (ok) {
                    // Recargar tabla solo si existe
                    const table = $('#tablaExample3');
                    if (table.length && $.fn.DataTable.isDataTable('#tablaExample3')) {
                        table.DataTable().ajax.reload(null, false);
                    }

                    swal({
                        title: 'Rol Eliminado',
                        text: response.message || 'El rol ha sido eliminado correctamente.',
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
                
                console.error('Error al eliminar rol:', xhr);
            }
        });
    });
};
