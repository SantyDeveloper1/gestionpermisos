'use strict';

// =========================================
// CSRF PARA AJAX
// =========================================
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// =========================================
// INICIALIZACIÓN
// =========================================
$(document).ready(function () {
    $('#btnActualizarSemestre').on('click', updateSemestre);
    $('#btnCancelarSemestre').on('click', cerrarModalSemestreSeguro);
    
    $('#editSemestreModal')
        .on('hidden.bs.modal', limpiarModalSemestre)
        .on('show.bs.modal', function() { 
            $(this).removeAttr('aria-hidden'); 
        })
        .on('shown.bs.modal', function() {
            setTimeout(() => { $(':focus').blur(); }, 150);
        });
});

// =========================================
// ABRIR MODAL Y LLENAR CON DATOS
// =========================================
window.showEditSemestre = function (idSemestre) {
    const row = $(`#semRow${idSemestre}`);

    if (!row.length) {
        console.error('No se encontró la fila del semestre:', idSemestre);
        return;
    }

    // Leer datos de las columnas (usando los índices de las columnas)
    const codigo = row.find('td:eq(1)').text().trim(); // Columna 1: Código
    const anio = row.find('td:eq(2)').text().trim();   // Columna 2: Año
    
    // Para las fechas, necesitamos convertir de d/m/Y a Y-m-d para el input type="date"
    const inicioTexto = row.find('td:eq(3)').text().trim(); // Columna 3: Inicio
    const finTexto = row.find('td:eq(4)').text().trim();    // Columna 4: Fin
    
    // Convertir formato d/m/Y a Y-m-d
    const inicioDate = convertirFechaParaInput(inicioTexto);
    const finDate = convertirFechaParaInput(finTexto);

    // Cargar en el modal
    $('#editCodigo').val(codigo);
    $('#editAnio').val(anio);
    $('#editInicio').val(inicioDate);
    $('#editFin').val(finDate);
    $('#editDescripcion').val(''); // Si no tienes descripción en la tabla, dejar vacío

    // Guardar ID
    $('#editIdSemestre').val(idSemestre);

    // Mostrar modal
    $('#editSemestreModal').modal('show');
};

// =========================================
// FUNCIÓN PARA CONVERTIR FECHAS
// =========================================
function convertirFechaParaInput(fechaTexto) {
    if (!fechaTexto) return '';
    
    // Convertir de "dd/mm/yyyy" a "yyyy-mm-dd"
    const partes = fechaTexto.split('/');
    if (partes.length === 3) {
        return `${partes[2]}-${partes[1].padStart(2, '0')}-${partes[0].padStart(2, '0')}`;
    }
    return '';
}

function convertirFechaParaTabla(fechaTexto) {
    if (!fechaTexto) return '';
    
    // Convertir de "yyyy-mm-dd" a "dd/mm/yyyy"
    const partes = fechaTexto.split('-');
    if (partes.length === 3) {
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }
    return '';
}

// =========================================
// ACTUALIZAR SEMESTRE POR AJAX
// =========================================
function updateSemestre() {
    const idSem = $('#editIdSemestre').val();

    if (!idSem) {
        showError('ID del semestre no encontrado');
        return;
    }

    const data = {
        codigo_Academico: $('#editCodigo').val().trim(),
        anio_academico: $('#editAnio').val().trim(),
        FechaInicioAcademico: $('#editInicio').val(),
        FechaFinAcademico: $('#editFin').val(),
        DescripcionAcademico: $('#editDescripcion').val().trim()
    };

    // Validación básica
    if (!data.codigo_Academico || !data.anio_academico || !data.FechaInicioAcademico || !data.FechaFinAcademico) {
        showError('Todos los campos obligatorios deben ser completados');
        return;
    }

    const $btn = $('#btnActualizarSemestre');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
         url: `${_urlBase}/admin/academico/semestre_academico/update/${idSem}`,
        method: 'POST',
        data: data,
        success: function (response) {
            if (response.success) {
                // Actualizar la fila en la tabla
                actualizarFilaTabla(idSem, response.data);
                showSuccess('Semestre actualizado correctamente');
                cerrarModalSemestreSeguro();
            } else {
                showError(response.message || 'No se pudo actualizar el semestre');
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                showError(errors.join('<br>'));
            } else {
                showError('Ocurrió un error al actualizar');
            }
        },
        complete: function () {
            $btn.prop('disabled', false).html('Guardar cambios');
        }
    });
}

// =========================================
// ACTUALIZAR LA FILA DE LA TABLA
// =========================================
function actualizarFilaTabla(id, data) {
    const row = $(`#semRow${id}`);

    if (row.length) {
        // Actualizar cada columna según su posición
        // Columna 1: Código
        row.find('td:eq(1)').text(data.codigo);
        
        // Columna 2: Año
        row.find('td:eq(2)').text(data.anio);
        
        // Columna 3: Inicio (formatear a d/m/Y)
        const inicioFormateada = data.inicio || convertirFechaParaTabla(data.FechaInicioAcademico);
        row.find('td:eq(3)').text(inicioFormateada);
        
        // Columna 4: Fin (formatear a d/m/Y)
        const finFormateada = data.fin || convertirFechaParaTabla(data.FechaFinAcademico);
        row.find('td:eq(4)').text(finFormateada);
        
        // Las demás columnas (Estado, Actual, Registrado, Acciones) se mantienen igual
    }
}

// =========================================
// FUNCIONES AUXILIARES
// =========================================
function limpiarModalSemestre() {
    $('#editSemestreForm')[0].reset();
    $('#editIdSemestre').val('');
    $('#btnActualizarSemestre').prop('disabled', false).html('Guardar cambios');
}

function cerrarModalSemestreSeguro() {
    $('#editSemestreModal').find('button, input').blur();
    setTimeout(() => { 
        $('#editSemestreModal').modal('hide'); 
    }, 50);
}

function showSuccess(msg) {
    new PNotify({ 
        title: 'Éxito', 
        text: msg, 
        type: 'success' 
    });
}

function showError(msg) {
    new PNotify({ 
        title: 'Error', 
        text: msg, 
        type: 'error' 
    });
}