'use strict';

// ==================== CONFIGURAR CSRF ====================
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ==================== DOCUMENT READY ====================
$(document).ready(function() {

    // Evento botón actualizar
    $('#btnActualizarAsig').on('click', function() {
        updateAsignatura();
    });

    // Evitar autofocus y problemas de cierre del modal
    $('#editAsignaturaModal').on('hide.bs.modal', function(e) {
        if (e.target === this) {
            $(this).find('button, input, select, textarea').blur();

            setTimeout(() => {
                $(document.activeElement).blur();
            }, 10);
        }
    });

    $('#editAsignaturaModal').on('hidden.bs.modal', function() {
        limpiarModal();
        $(this).removeAttr('aria-hidden');
    });

    $('#editAsignaturaModal').on('show.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });

    $('#editAsignaturaModal').on('shown.bs.modal', function() {
        setTimeout(() => {
            const focused = $(':focus');
            if (focused.is('input, select, textarea')) focused.blur();
        }, 150);
    });

});

// ==================== LIMPIAR MODAL ====================
function limpiarModal() {
    $('#editAsigForm')[0].reset();
    $('#editAsignaturaModal').removeData('idAsignatura');
    $('#btnActualizarAsig').prop('disabled', false).html('Guardar cambios');
}

// ==================== CARGAR DATOS AL MODAL ====================
function showEditAsignatura(idAsignatura) {

    const row = $(`#asigRow${idAsignatura}`);

    const nom = row.find('.tdNombre').text().trim();
    const cred = row.find('.tdCreditos').text().trim();
    const ht = row.find('.tdHT').text().trim();
    const hp = row.find('.tdHP').text().trim();
    const tipo = row.find('.tdTipo').text().trim();

    const idCiclo = row.data('id-ciclo');
    const idPlan = row.data('id-plan');

    $('#txtNomAsig').val(nom);
    $('#txtCreditos').val(cred);
    $('#txtHT').val(ht);
    $('#txtHP').val(hp);
    $('#tipo').val(tipo);

    $('#IdCiclo').val(idCiclo);
    $('#idPlanEstudio').val(idPlan);

    $('#editAsignaturaModal').data('idAsignatura', idAsignatura);
    $('#editAsignaturaModal').modal('show');
}

// ==================== ACTUALIZAR CON AJAX ====================
function updateAsignatura() {

    const id = $('#editAsignaturaModal').data('idAsignatura');

    const formData = {
        nom_asignatura: $('#txtNomAsig').val(),
        creditos: $('#txtCreditos').val(),
        horas_teoria: $('#txtHT').val(),
        horas_practica: $('#txtHP').val(),
        tipo: $('#tipo').val(),
        IdCiclo: $('#IdCiclo').val(),
        idPlanEstudio: $('#idPlanEstudio').val(),
    };

    $('#btnActualizarAsig').prop('disabled', true).html('Actualizando...');

    $.ajax({
        url: `${_urlBase}/admin/academico/asignatura/update/${id}`,
        type: 'POST',
        data: formData,

        success: function(response) {
            cerrarModalSeguro(response.message);

            // Actualizar fila sin recargar página
            actualizarFilaTabla(id, formData, response);
        },

        error: function(xhr) {
            $('#btnActualizarAsig').prop('disabled', false).html('Guardar cambios');

            if (xhr.responseJSON && xhr.responseJSON.message) {
                showError(xhr.responseJSON.message);
            } else {
                showError('Ocurrió un error inesperado.');
            }
        }
    });
}

// ==================== CERRAR MODAL + NOTIFICAR ====================
function cerrarModalSeguro(message) {
    $('#editAsignaturaModal').find('button, input, select').blur();

    setTimeout(() => {
        $('#editAsignaturaModal').modal('hide');

        new PNotify({
            title: 'Éxito',
            text: message,
            type: 'success',
            delay: 3000
        });

    }, 100);
}

// ==================== ERROR ====================
function showError(message) {
    new PNotify({
        title: 'Error',
        text: message,
        type: 'error',
        delay: 4000
    });
}

// ==================== ACTUALIZAR FILA DINÁMICA ====================
function actualizarFilaTabla(id, data, response) {

    const row = $(`#asigRow${id}`);

    row.find('.tdNombre').text(data.nom_asignatura);
    row.find('.tdCreditos').text(data.creditos);
    row.find('.tdHT').text(data.horas_teoria);
    row.find('.tdHP').text(data.horas_practica);
    row.find('.tdTipo').html(`<span class="badge badge-info">${data.tipo}</span>`);
    row.find('.tdCiclo').text(response.nombreCiclo);

    // Actualizar data attributes
    row.data('id-ciclo', data.IdCiclo);
    row.data('id-plan', data.idPlanEstudio);
}
