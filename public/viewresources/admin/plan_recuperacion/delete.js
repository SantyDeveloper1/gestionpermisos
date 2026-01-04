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
            $.ajax({
                url: _urlBase + '/admin/plan_recuperacion/delete/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    new PNotify({
                        title: '¡Eliminado!',
                        text: 'El plan ha sido eliminado correctamente',
                        type: 'success'
                    });
                    setTimeout(() => location.reload(), 1000);
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
                }
            });
        }
    });
}
