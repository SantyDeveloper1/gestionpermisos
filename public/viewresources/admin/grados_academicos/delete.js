'use strict';

// Configuración global CSRF para Laravel
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Función global para ELIMINAR un grado académico
window.deleteGrado = function(idGrado) {

    swal({
        title: 'Confirmar eliminación',
        text: '¿Realmente desea eliminar este grado académico?',
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, eliminar'],
        dangerMode: true
    }).then(function(willDelete) {

        if (!willDelete) return;

        // Deshabilitar botón mientras se procesa
        const $btn = $(`#gradoRow${idGrado} .btn-danger`);
        $btn.prop('disabled', true);

        $.ajax({
            url: `${_urlBase}/admin/docente/grados-academicos/delete/${idGrado}`, // URL de tu ruta
            type: 'DELETE', // Método DELETE
            success: function(response) {

                if (response && response.status === 'success') {
                    // Eliminar fila de DataTable
                    const tabla = $("#tablaExample2").DataTable();
                    tabla.row($(`#gradoRow${idGrado}`)).remove().draw(false);

                    // Reenumerar filas
                    tabla.rows().every(function(rowIdx) {
                        const cell = this.node().querySelector('td:first-child');
                        if (cell) cell.innerText = rowIdx + 1;
                    });

                    swal('Eliminado', 'El grado académico fue eliminado correctamente.', 'success');

                } else {
                    swal('Error', response.message || 'No se pudo eliminar el grado académico.', 'error');
                }
            },
            error: function(xhr) {
                // Manejo de errores
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