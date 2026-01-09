'use strict';

// Configuración CSRF global para Laravel
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Función global para ELIMINAR un semestre académico
window.deleteSemestre = function(idSemestre) {

    swal({
        title: 'Confirmar eliminación',
        text: '¿Realmente desea eliminar este semestre académico?',
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, eliminar'],
        dangerMode: true
    }).then(function(willDelete) {

        if (!willDelete) return;

        // Deshabilitar botón mientras se procesa
        const $btn = $(`#semRow${idSemestre} .btn-danger`);
        $btn.prop('disabled', true);

        $.ajax({
            url: `${_urlBase}/admin/academico/semestre_academico/delete/${idSemestre}`,
            type: 'DELETE',
            success: function(response) {

                if (response && response.status === 'success') {

                    // Eliminar fila del DataTable
                    const tabla = $("#tablaExample2").DataTable();
                    tabla.row($(`#semRow${idSemestre}`)).remove().draw(false);

                    // Reenumerar filas
                    tabla.rows().every(function(rowIdx) {
                        const cell = this.node().querySelector('td:first-child');
                        if (cell) cell.innerText = rowIdx + 1;
                    });

                    swal('Eliminado', 'El semestre académico fue eliminado correctamente.', 'success');

                } else {
                    swal('Error', response.message || 'No se pudo eliminar el semestre académico.', 'error');
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