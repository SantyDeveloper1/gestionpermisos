'use strict';

// ==================== CONFIGURAR CSRF ====================
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ==================== DOCUMENT READY ====================
$(document).ready(function() {

    // Evento submit del formulario de cambio de contraseña
    $('#form_password').on('submit', function(e) {
        e.preventDefault();
        updatePassword();
    });

    // Limpiar formulario al cerrar el modal
    $('#modal-password').on('hidden.bs.modal', function() {
        limpiarFormulario();
    });

});

// ==================== LIMPIAR FORMULARIO ====================
function limpiarFormulario() {
    $('#form_password')[0].reset();
}

// ==================== ACTUALIZAR CONTRASEÑA ====================
function updatePassword() {
    
    // Obtener valores del formulario
    const currentPassword = $('input[name="pass"]').val().trim();
    const newPassword = $('input[name="pass1"]').val().trim();
    const confirmPassword = $('input[name="pass2"]').val().trim();

    // Validaciones del lado del cliente
    if (!currentPassword) {
        showError('Por favor, ingrese su contraseña actual.');
        return;
    }

    if (!newPassword) {
        showError('Por favor, ingrese la nueva contraseña.');
        return;
    }

    if (newPassword.length < 6) {
        showError('La nueva contraseña debe tener al menos 6 caracteres.');
        return;
    }

    if (!confirmPassword) {
        showError('Por favor, confirme la nueva contraseña.');
        return;
    }

    if (newPassword !== confirmPassword) {
        showError('Las contraseñas no coinciden.');
        return;
    }

    if (currentPassword === newPassword) {
        showError('La nueva contraseña debe ser diferente a la actual.');
        return;
    }

    // Deshabilitar botón de envío
    const $submitBtn = $('#form_password button[type="submit"]');
    const originalText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');

    // Enviar petición AJAX
    $.ajax({
        url: `${_urlBase}/docente/password/update`,
        type: 'POST',
        data: {
            current_password: currentPassword,
            new_password: newPassword,
            confirm_password: confirmPassword
        },
        success: function(response) {
            if (response.success) {
                // Cerrar modal
                $('#modal-password').modal('hide');
                
                // Mostrar notificación de éxito
                new PNotify({
                    title: 'Contraseña Actualizada',
                    text: 'Su contraseña ha sido actualizada correctamente. Cerrando sesión...',
                    type: 'success',
                    delay: 2000
                });

                // Limpiar formulario
                limpiarFormulario();

                // Cerrar sesión automáticamente después de 2 segundos
                setTimeout(function() {
                    // Limpiar sesión del navegador
                    sessionStorage.clear();
                    localStorage.clear();

                    // Hacer logout con AJAX para destruir la sesión en el servidor
                    $.ajax({
                        url: `${_urlBase}/logout`,
                        type: 'POST',
                        dataType: 'json',
                        success: function(logoutResponse) {
                            if (logoutResponse.success) {
                                // Redirigir usando replace para evitar retroceso
                                window.location.replace(logoutResponse.redirect || `${_urlBase}/login`);
                            }
                        },
                        error: function() {
                            // Si falla AJAX, forzar redirección al login
                            window.location.replace(`${_urlBase}/login`);
                        }
                    });
                }, 2000);
            } else {
                showError(response.message || 'No se pudo actualizar la contraseña.');
                // Restaurar botón solo si hay error
                $submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr) {
            // Restaurar botón
            $submitBtn.prop('disabled', false).html(originalText);

            let errorMsg = 'Ha ocurrido un error al actualizar la contraseña.';

            if (xhr.status === 422) {
                // Errores de validación
                if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON?.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                }
            } else if (xhr.status === 401) {
                errorMsg = 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.';
            } else if (xhr.status === 500) {
                errorMsg = xhr.responseJSON?.message || 'Error del servidor. Por favor, contacte al administrador.';
            } else if (xhr.responseJSON?.message) {
                errorMsg = xhr.responseJSON.message;
            }

            showError(errorMsg);
        }
    });
}

// ==================== MOSTRAR ERROR ====================
function showError(message) {
    // Solo mostrar notificación PNotify
    new PNotify({
        title: 'Error',
        text: message,
        type: 'error',
        delay: 4000
    });
}
