'use strict';

// CSRF
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Función global para eliminar asignatura
window.deleteAsignatura = function(idAsignatura) {

    swal({
        title: 'Confirmar eliminación',
        text: '¿Realmente desea eliminar esta asignatura?',
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, eliminar'],
        dangerMode: true
    }).then(function(willDelete) {
        if (!willDelete) return;
        const $row = $(`#asigRow${idAsignatura}`);
        const $btn = $row.find('.btn-danger');
        $btn.prop('disabled', true);
        $.ajax({
            url: `${_urlBase}/admin/academico/asignatura/delete/${idAsignatura}`,
            type: 'POST',
            data: { _method: 'DELETE' },
            success: function(response) {
                const ok = response && (response.status === 'success' || response.success === true);
                if (ok) {
                    // Encontrar la tabla padre
                    const $table = $row.closest('table');
                    const $card = $row.closest('.card');
                    // Eliminar la fila
                    $row.remove();
                    // Renumerar las filas restantes en la misma tabla
                    $table.find('tbody tr').each(function(index) {
                        $(this).find('td:first-child').text(index + 1);
                    });
                    // Si no quedan filas en la tabla, eliminar toda la card del ciclo
                    if ($table.find('tbody tr').length === 0) {
                        $card.fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                    swal('Eliminado', 'La asignatura fue eliminada correctamente.', 'success');
                } else {
                    swal('Error', 'No se pudo eliminar la asignatura.', 'error');
                }
            },
            error: function(xhr) {
                swal('Error', 'Hubo un problema al intentar eliminar.', 'error');
                console.error(xhr);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
};
