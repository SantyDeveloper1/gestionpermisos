'use strict';

// CSRF global (ya lo tienes)
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

/**
 * Construye HTML del bloque de acciones según estado y si es actual.
 * @param {string} idSemestre
 * @param {string} estado  -> 'Planificado'|'Activo'|'Cerrado'
 * @param {number|boolean} esActual -> 1/0 or true/false
 * @returns {string} html
 */
function buildActionsHtml(idSemestre, estado) {
    let html = '';

    // Editar
    html += `<button class="btn btn-sm btn-warning" onclick="showEditSemestre('${idSemestre}')">
                <i class="fas fa-edit"></i>
             </button> `;

    // Eliminar
    html += `<button class="btn btn-danger btn-sm" onclick="deleteSemestre('${idSemestre}')">
                <i class="fas fa-trash"></i>
             </button> `;

    // Cambios de estado
    if (estado === 'Planificado') {
        html += `<button class="btn btn-primary btn-sm btnCambiarEstado"
                        onclick="cambiarEstadoSemestre('${idSemestre}', 'Activo')">
                    <i class="fas fa-play"></i> Activar
                 </button> `;
    }

    if (estado === 'Activo') {
        html += `<button class="btn btn-danger btn-sm btnCambiarEstado"
                        onclick="cambiarEstadoSemestre('${idSemestre}', 'Cerrado')">
                    <i class="fas fa-lock"></i> Cerrar
                 </button> `;
    }

    if (estado === 'Cerrado') {
        html += `<button class="btn btn-info btn-sm btnCambiarEstado"
                        onclick="cambiarEstadoSemestre('${idSemestre}', 'Planificado')">
                    <i class="fas fa-undo"></i> Reabrir
                 </button> `;
    }

    return html;
}


/**
 * Cambia el estado y actualiza fila completa (estado, es_actual y acciones).
 * Requiere que el controlador devuelva res.success y res.data: { EstadoAcademico, EsActualAcademico }.
 */
window.cambiarEstadoSemestre = function(idSemestre, nuevoEstado) {

    swal({
        title: 'Confirmar cambio',
        text: `¿Cambiar estado del semestre a "${nuevoEstado}"?`,
        icon: 'warning',
        buttons: ['Cancelar', 'Sí, cambiar'],
        dangerMode: true
    }).then(function(confirmed) {

        if (!confirmed) return;

        const $btn = $(`#semRow${idSemestre} .btnCambiarEstado`);
        $btn.prop('disabled', true);

        $.ajax({
            url: `${_urlBase}/admin/academico/semestre_academico/cambiar_estado`,
            type: 'POST',
            data: { id: idSemestre, estado: nuevoEstado },
            dataType: 'json',
            success: function(res) {

                if (res.success) {

                    // Si el controlador nos devolvió los campos actualizados, usarlos.
                    // Si no vienen, intentamos inferir: usar nuevoEstado y mantener esActual previo.
                    const newEstado = res.data?.EstadoAcademico ?? nuevoEstado;
                    const newEsActual = typeof res.data?.EsActualAcademico !== 'undefined'
                        ? res.data.EsActualAcademico
                        : ($(`#semRow${idSemestre}`).find('td:eq(6) .fa-check-circle').length ? 1 : 0);

                    const rowNode = $(`#semRow${idSemestre}`);

                    // 1) Actualizar badge ESTADO -> columna td:eq(5)
                    let badgeHtml = '';
                    if (newEstado === 'Planificado') badgeHtml = `<span class="badge badge-info px-3 py-2">Planificado</span>`;
                    else if (newEstado === 'Activo') badgeHtml = `<span class="badge badge-success px-3 py-2">Activo</span>`;
                    else if (newEstado === 'Cerrado') badgeHtml = `<span class="badge badge-danger px-3 py-2">Cerrado</span>`;
                    else badgeHtml = `<span class="badge badge-secondary px-3 py-2">—</span>`;

                    rowNode.find('td:eq(5)').html(badgeHtml);

                    // 2) Actualizar badge ES_ACTUAL -> columna td:eq(6)
                    // Si este semestre se marcó como actual, desmarcar TODOS los demás primero
                    if (newEsActual == 1 || newEsActual === true) {
                        // Desmarcar todos
                        $('#tablaExample2 tbody tr').each(function() {
                            const $row = $(this);
                            const noActualHtml = `<span class="badge badge-secondary px-3 py-2"><i class="fas fa-times-circle"></i> No</span>`;
                            $row.find('td:eq(6)').html(noActualHtml);
                        });
                        
                        // Marcar solo este como actual
                        const actualHtml = `<span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle"></i> Actual</span>`;
                        rowNode.find('td:eq(6)').html(actualHtml);
                    } else {
                        // Solo actualizar esta fila
                        const actualHtml = `<span class="badge badge-secondary px-3 py-2"><i class="fas fa-times-circle"></i> No</span>`;
                        rowNode.find('td:eq(6)').html(actualHtml);
                    }

                    // 3) Reconstruir el bloque de acciones (td con .accionesSemestre)
                    const actionsHtml = buildActionsHtml(idSemestre, newEstado, newEsActual);
                    rowNode.find('.accionesSemestre').html(actionsHtml);

                    // 4) Feedback al usuario
                    new PNotify({
                        title: 'Éxito',
                        text: res.message || 'Estado actualizado correctamente',
                        type: 'success'
                    });

                } else {
                    // respuesta no exitosa (p. ej validación en servidor)
                    new PNotify({
                        title: 'Advertencia',
                        text: res.message || 'No se pudo actualizar el estado',
                        type: 'error'
                    });
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error inesperado.';
                swal('Error', msg, 'error');
                console.error(xhr);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });

    });
};
