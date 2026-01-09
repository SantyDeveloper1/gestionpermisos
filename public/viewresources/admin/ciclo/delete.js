'use strict';

// Configuración CSRF global para Laravel
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Función global para ELIMINAR una categoría docente
window.deleteCiclo = function(idCiclo) {
    swal({
        title: 'Confirmar eliminación',
        text: '¿Realmente desea eliminar este ciclo?',
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, eliminar'],
        dangerMode: true
    }).then(function(willDelete) {
        if (!willDelete) return;

        const $btn = $(`#cicloRow${idCiclo} .btn-danger`);
        $btn.prop('disabled', true);

        $.ajax({
            url: `${_urlBase}/admin/academico/ciclo/delete/${idCiclo}`,
            type: 'POST',
            data: { _method: 'DELETE' },
            success: function(response) {
                if (response?.status === 'success') {
                    const tabla = $("#tablaExample2").DataTable();
                    tabla.row($(`#cicloRow${idCiclo}`)).remove().draw(false);

                    tabla.rows().every(function(rowIdx) {
                        const cell = this.node().querySelector('td:first-child');
                        if (cell) cell.innerText = rowIdx + 1;
                    });

                    swal('Eliminado', 'El ciclo fue eliminado correctamente.', 'success');
                } else {
                    swal('Error', response.message || 'No se pudo eliminar el ciclo.', 'error');
                }
            },
            error: function(xhr) {
                swal('Error', xhr.responseJSON?.message || 'Hubo un problema al intentar eliminar.', 'error');
                console.error(xhr);
            },
            complete: function() { $btn.prop('disabled', false); }
        });
    });
};
