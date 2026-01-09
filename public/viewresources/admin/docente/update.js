'use strict';

// ==================== CONFIGURAR CSRF ====================
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ==================== DOCUMENT READY ====================
$(document).ready(function() {

    // Evento botón actualizar docente
    $('#btnActualizarDoc').on('click', function() {
        updateDocente();
    });

    // Evitar autofocus y problemas de cierre del modal
    $('#editDocenteModal').on('hide.bs.modal', function(e) {
        if (e.target === this) {
            $(this).find('button, input, select, textarea').blur();

            setTimeout(() => {
                $(document.activeElement).blur();
            }, 10);
        }
    });

    $('#editDocenteModal').on('hidden.bs.modal', function() {
        limpiarModalEdicion();
        $(this).removeAttr('aria-hidden');
    });

    $('#editDocenteModal').on('show.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });

    $('#editDocenteModal').on('shown.bs.modal', function() {
        setTimeout(() => {
            const focused = $(':focus');
            if (focused.is('input, select, textarea')) focused.blur();
        }, 150);
    });

});

// ==================== LIMPIAR MODAL ====================
function limpiarModalEdicion() {
    $('#editDocForm')[0].reset();
    $('#editDocenteModal').removeData('idDocente');
    $('#btnActualizarDoc').prop('disabled', false).html('Guardar cambios');
}

// ==================== CARGAR DATOS AL MODAL ====================
window.showEditDocente = function(idDocente) {
    
    // Hacer petición AJAX para obtener los datos completos del docente
    $.ajax({
        url: `${_urlBase}/admin/docente/show/${idDocente}`,
        type: 'GET',
        success: function(response) {
            if (response.success && response.docente) {
                const docente = response.docente;
                
                // Cargar datos básicos del usuario
                $('#txtDni').val(docente.user.document_number || '');
                $('#txtNombre').val(docente.user.name || '');
                $('#txtApellido').val(docente.user.last_name || '');
                $('#txtCorreo').val(docente.user.email || '');
                $('#txtTelefono').val(docente.user.phone || '');
                
                // Seleccionar grado académico
                $('#txtGrado').val(docente.grado_id || '');
                
                // Seleccionar tipo de contrato
                $('#txtCondicion').val(docente.tipo_contrato_id || '');
                
                // Guardar ID del docente en el modal
                $('#editDocenteModal').data('idDocente', idDocente);
                $('#editDocenteModal').modal('show');
            } else {
                new PNotify({
                    title: 'Error',
                    text: 'No se pudieron cargar los datos del docente',
                    type: 'error'
                });
            }
        },
        error: function(xhr) {
            new PNotify({
                title: 'Error',
                text: 'Error al obtener los datos del docente',
                type: 'error'
            });
            console.error('Error al cargar docente:', xhr);
        }
    });
};

// ==================== ACTUALIZAR CON AJAX ====================
function updateDocente() {

    const idDocente = $('#editDocenteModal').data('idDocente');

    if (!idDocente) {
        showError('No se ha identificado el docente a actualizar.');
        return;
    }

    const formData = {
        dni: $('#txtDni').val().trim(),
        nombre: $('#txtNombre').val().trim(),
        apellido: $('#txtApellido').val().trim(),
        correo: $('#txtCorreo').val().trim(),
        telefono: $('#txtTelefono').val().trim(),
        grado_id: $('#txtGrado').val(),
        tipo_contrato_id: $('#txtCondicion').val()
    };

    // Validaciones básicas
    if (!formData.nombre) {
        showError('El nombre del docente es obligatorio.');
        return;
    }

    if (!formData.correo) {
        showError('El correo electrónico es obligatorio.');
        return;
    }

    // Validar formato de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.correo)) {
        showError('Por favor, ingrese un correo electrónico válido.');
        return;
    }

    const $btn = $('#btnActualizarDoc');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');

    $.ajax({
        url: `${_urlBase}/admin/docente/update/${idDocente}`,
        type: 'POST',
        data: formData,

        success: function(response) {
            if (response.success) {
                cerrarModalSeguro(response.message || 'Los datos del docente han sido actualizados exitosamente.');
                
                // Actualizar fila sin recargar página
                actualizarFilaTabla(idDocente, formData);
            } else {
                showError(response.message || 'No se pudo actualizar la información del docente.');
                $btn.prop('disabled', false).html('Guardar cambios');
            }
        },

        error: function(xhr) {
            $btn.prop('disabled', false).html('Guardar cambios');

            let errorMsg = 'Ha ocurrido un error al procesar la actualización.';

            if (xhr.status === 422) {
                // Errores de validación
                if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON?.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                }
            } else if (xhr.status === 404) {
                errorMsg = xhr.responseJSON?.message || 'Docente no encontrado en el sistema.';
            } else if (xhr.status === 500) {
                errorMsg = xhr.responseJSON?.message || 'Error del servidor. Por favor, contacte al administrador del sistema.';
            } else if (xhr.responseJSON?.message) {
                errorMsg = xhr.responseJSON.message;
            }

            showError(errorMsg);
        }
    });
}

// ==================== CERRAR MODAL + NOTIFICAR ====================
function cerrarModalSeguro(message) {
    $('#editDocenteModal').find('button, input, select').blur();

    setTimeout(() => {
        $('#editDocenteModal').modal('hide');

        new PNotify({
            title: 'Actualización Exitosa',
            text: message,
            type: 'success',
            delay: 3000
        });

    }, 100);
}

// ==================== ERROR ====================
function showError(message) {
    new PNotify({
        title: 'Error de Validación',
        text: message,
        type: 'error',
        delay: 4000
    });
}

// ==================== ACTUALIZAR FILA DINÁMICA ====================
function actualizarFilaTabla(idDocente, data) {
    
    const row = $(`#docRow${idDocente}`);

    if (row.length === 0) {
        console.warn('No se encontró la fila del docente:', idDocente);
        return;
    }

    // Actualizar datos en la tabla
    row.find('td:eq(1)').text(data.dni);
    row.find('td:eq(2)').text(data.nombre);
    row.find('td:eq(3)').text(data.correo);
    row.find('td:eq(4)').text(data.telefono);
    row.find('td:eq(5)').html(`<span class="badge badge-warning">${data.condicion}</span>`);
}
