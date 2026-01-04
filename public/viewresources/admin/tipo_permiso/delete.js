'use strict';

// Función para eliminar tipo de permiso
window.deleteTipoPermiso = function(id) {
    
    swal({
        title: '¿Está seguro?',
        text: 'Esta acción eliminará el tipo de permiso permanentemente.',
        icon: 'warning',
        buttons: ['Cancelar', 'Sí, eliminar'],
        dangerMode: true
    }).then(willDelete => {
        if (!willDelete) return;

        $.ajax({
            url: `${_urlBase}/admin/tipo_permiso/delete/${id}`,
            type: 'POST',
            data: { _method: 'DELETE' },
            
            success: function(response) {
                if (response.success) {
                    // Eliminar fila de la tabla
                    const tabla = $('#tablaExample2').DataTable();
                    const row = $(`#row${id}`);
                    
                    tabla.row(row).remove().draw(false);

                    // Reenumerar filas
                    tabla.rows().every(function(index) {
                        this.cell(index, 0).data(index + 1);
                    });
                    tabla.draw(false);

                    new PNotify({
                        title: 'Éxito',
                        text: response.message || 'Tipo de permiso eliminado correctamente.',
                        type: 'success'
                    });
                }
            },

            error: function(xhr) {
                const mensaje = xhr.responseJSON?.message || 'Error al eliminar el tipo de permiso.';
                
                new PNotify({
                    title: 'Error',
                    text: mensaje,
                    type: 'error'
                });
            }
        });
    });
};
