// Función para eliminar plan
function deletePlan(id) {
    swal({
        title: '¿Eliminar Plan de Recuperación?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        buttons: ['Cancelar', 'Sí, eliminar'],
        dangerMode: true
    }).then((proceed) => {
        if (proceed) {
            // Buscar la fila y el botón
            const $btn = $(`.btn-delete[onclick*="${id}"]`);
            const $row = $btn.closest('tr');
            
            // Deshabilitar botón mientras se procesa
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: _urlBase + '/admin/plan_recuperacion/delete/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Intentar obtener la tabla DataTable si existe
                    let table = null;
                    try {
                        table = $('#tablaExample2').DataTable();
                    } catch (e) {
                        table = null;
                    }
                    
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
                        title: '¡Eliminado!',
                        text: response.message || 'El plan ha sido eliminado correctamente',
                        type: 'success'
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'No se pudo eliminar el plan';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    new PNotify({
                        title: 'Error',
                        text: errorMessage,
                        type: 'error'
                    });
                    
                    // Rehabilitar botón en caso de error
                    $btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            });
        }
    });
}
