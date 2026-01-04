'use strict';

// ==================== CONFIGURAR CSRF ====================
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ==================== DOCUMENT READY ====================
$(document).ready(function() {

    // Evento botón actualizar usuario
    $('#btnActualizarUsuario').on('click', function() {
        updateUsuario();
    });

    // Evitar autofocus y problemas de cierre del modal
    $('#modalEditarUsuario').on('hide.bs.modal', function(e) {
        if (e.target === this) {
            $(this).find('button, input, select, textarea').blur();

            setTimeout(() => {
                $(document.activeElement).blur();
            }, 10);
        }
    });

    $('#modalEditarUsuario').on('hidden.bs.modal', function() {
        limpiarModalEdicion();
        $(this).removeAttr('aria-hidden');
    });

    $('#modalEditarUsuario').on('show.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });

    $('#modalEditarUsuario').on('shown.bs.modal', function() {
        setTimeout(() => {
            const focused = $(':focus');
            if (focused.is('input, select, textarea')) focused.blur();
        }, 150);
    });

});

// ==================== LIMPIAR MODAL ====================
function limpiarModalEdicion() {
    $('#frmUsuarioUpdate')[0].reset();
    $('#modalEditarUsuario').removeData('idUsuario');
    $('#btnActualizarUsuario').prop('disabled', false).html('<i class="fas fa-save"></i> Actualizar Usuario');
}

// ==================== CARGAR DATOS AL MODAL ====================
window.showEditUsuario = function(idUsuario) {
    
    const row = $(`#usuarioRow${idUsuario}`);

    if (row.length === 0) {
        showError('No se encontró el usuario en la tabla.');
        return;
    }

    // Extraer datos de la fila
    const nombreCompleto = row.find('td:eq(1)').text().trim();
    const telefono = row.find('td:eq(3)').text().trim();
    const genderBadge = row.find('td:eq(4) .badge').text().trim();

    // Mapear el texto del badge al valor del select
    let genderValue = '';
    if (genderBadge.includes('Masculino')) {
        genderValue = 'male';
    } else if (genderBadge.includes('Femenino')) {
        genderValue = 'female';
    } else if (genderBadge.includes('Otro')) {
        genderValue = 'other';
    }

    // Separar nombre y apellido (asumiendo formato "Nombre Apellido")
    const nombreParts = nombreCompleto.split(' ');
    const nombre = nombreParts[0] || '';
    const apellido = nombreParts.slice(1).join(' ') || '';

    // Cargar datos en el formulario
    $('#edit_user_id').val(idUsuario);
    $('#edit_name').val(nombre);
    $('#edit_last_name').val(apellido);
    $('#edit_phone').val(telefono);
    $('#edit_gender').val(genderValue);

    // Guardar ID del usuario en el modal
    $('#modalEditarUsuario').data('idUsuario', idUsuario);
    $('#modalEditarUsuario').modal('show');
};

// ==================== ACTUALIZAR CON AJAX ====================
function updateUsuario() {

    const idUsuario = $('#modalEditarUsuario').data('idUsuario');

    if (!idUsuario) {
        showError('No se ha identificado el usuario a actualizar.');
        return;
    }

    const formData = {
        name: $('#edit_name').val().trim(),
        last_name: $('#edit_last_name').val().trim(),
        phone: $('#edit_phone').val().trim(),
        gender: $('#edit_gender').val()
    };

    // Validaciones básicas
    if (!formData.name) {
        showError('El nombre del usuario es obligatorio.');
        return;
    }

    // Validar teléfono si se proporciona
    if (formData.phone && !/^[0-9]{9}$/.test(formData.phone)) {
        showError('El teléfono debe tener exactamente 9 dígitos.');
        return;
    }

    const $btn = $('#btnActualizarUsuario');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');

    $.ajax({
        url: `${_urlBase}/admin/usuarios/update/${idUsuario}`,
        type: 'POST',
        data: formData,

        success: function(response) {
            if (response.success || response.status === 'success') {
                cerrarModalSeguro(response.message || 'Los datos del usuario han sido actualizados exitosamente.');
                
                // Actualizar fila sin recargar página
                actualizarFilaTabla(idUsuario, formData);
            } else {
                showError(response.message || 'No se pudo actualizar la información del usuario.');
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Actualizar Usuario');
            }
        },

        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Actualizar Usuario');

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
                errorMsg = xhr.responseJSON?.message || 'Usuario no encontrado en el sistema.';
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
    $('#modalEditarUsuario').find('button, input, select').blur();

    setTimeout(() => {
        $('#modalEditarUsuario').modal('hide');

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
function actualizarFilaTabla(idUsuario, data) {
    
    const row = $(`#usuarioRow${idUsuario}`);

    if (row.length === 0) {
        console.warn('No se encontró la fila del usuario:', idUsuario);
        return;
    }

    // Actualizar datos en la tabla
    const nombreCompleto = `${data.name} ${data.last_name}`;
    row.find('td:eq(1)').text(nombreCompleto);
    row.find('td:eq(3)').html(data.phone ? `<i class="fas text-muted"></i> ${data.phone}` : '<span class="text-muted">-</span>');
    
    // Actualizar género
    let genderBadge = '<span class="text-muted">-</span>';
    if (data.gender === 'male') {
        genderBadge = '<span class="badge badge-info"><i class="fas fa-mars"></i> Masculino</span>';
    } else if (data.gender === 'female') {
        genderBadge = '<span class="badge badge-pink"><i class="fas fa-venus"></i> Femenino</span>';
    } else if (data.gender === 'other') {
        genderBadge = '<span class="badge badge-secondary"><i class="fas fa-genderless"></i> Otro</span>';
    }
    row.find('td:eq(4)').html(genderBadge);
}
