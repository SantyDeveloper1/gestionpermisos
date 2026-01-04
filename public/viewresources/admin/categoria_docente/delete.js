'use strict';

// Configuración CSRF global para Laravel
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Función global para ELIMINAR una categoría docente
window.deleteCategoria = function(idCategoria) {

    swal({
        title: 'Confirmar eliminación',
        text: '¿Realmente desea eliminar esta categoría docente?',
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, eliminar'],
        dangerMode: true
    }).then(function(willDelete) {

        if (!willDelete) return;

        // Deshabilitar botón mientras se procesa
        const $btn = $(`#categoriaRow${idCategoria} .btn-danger`);
        $btn.prop('disabled', true);

        $.ajax({
            url: `${_urlBase}/admin/docente/categoria-docente/delete/${idCategoria}`, // NUEVA RUTA
            type: 'DELETE',
            success: function(response) {

                if (response && response.status === 'success') {

                    // Eliminar fila del DataTable
                    const tabla = $("#tablaExample2").DataTable();
                    tabla.row($(`#categoriaRow${idCategoria}`)).remove().draw(false);

                    // Reenumerar filas
                    tabla.rows().every(function(rowIdx) {
                        const cell = this.node().querySelector('td:first-child');
                        if (cell) cell.innerText = rowIdx + 1;
                    });

                    swal('Eliminado', 'La categoría docente fue eliminada correctamente.', 'success');

                } else {
                    swal('Error', response.message || 'No se pudo eliminar la categoría docente.', 'error');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Hubo un problema al intentar eliminar.';
                swal('Error', msg, 'error');
                console.error(xhr);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });

    });
};