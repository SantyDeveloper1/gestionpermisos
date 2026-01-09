'use strict';

// CSRF global
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

/**
 * Marcar un semestre como actual
 * Actualiza dinámicamente la columna "Actual" de TODOS los semestres
 */
window.marcarComoActual = function(idSemestre) {
    
    swal({
        title: 'Confirmar',
        text: '¿Marcar este semestre como actual? El semestre actual anterior será desmarcado.',
        icon: 'info',
        buttons: ['Cancelar', 'Sí, marcar'],
        dangerMode: false
    }).then(function(confirmed) {
        
        if (!confirmed) return;
        
        $.ajax({
            url: `${_urlBase}/admin/academico/semestre_academico/marcar_actual`,
            type: 'POST',
            data: { id: idSemestre },
            dataType: 'json',
            success: function(res) {
                
                if (res.success) {
                    
                    // Actualizar TODAS las filas: desmarcar todos
                    $('#tablaExample2 tbody tr').each(function() {
                        const $row = $(this);
                        const actualHtml = `<span class="badge badge-secondary px-3 py-2">
                                              <i class="fas fa-times-circle"></i> No
                                            </span>`;
                        $row.find('td:eq(6)').html(actualHtml);
                    });
                    
                    // Marcar solo el nuevo como actual
                    const $newRow = $(`#semRow${idSemestre}`);
                    const actualHtml = `<span class="badge badge-success px-3 py-2">
                                          <i class="fas fa-check-circle"></i> Actual
                                        </span>`;
                    $newRow.find('td:eq(6)').html(actualHtml);
                    
                    // Feedback
                    new PNotify({
                        title: 'Éxito',
                        text: res.message || 'Semestre marcado como actual',
                        type: 'success'
                    });
                    
                } else {
                    new PNotify({
                        title: 'Advertencia',
                        text: res.message || 'No se pudo marcar como actual',
                        type: 'error'
                    });
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error inesperado.';
                swal('Error', msg, 'error');
                console.error(xhr);
            }
        });
    });
};
