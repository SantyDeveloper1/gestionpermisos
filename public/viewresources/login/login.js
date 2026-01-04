'use strict';

// Configuración CSRF para AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {
    // Verificar si ya está autenticado
    checkAuthentication();

    // Inicializar validación con FormValidation
    $('#loginForm').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
        live: 'enabled',
        message: '<b style="color:red;">Complete correctamente este campo.</b>',
        trigger: null,
        fields: {
            email: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">El correo es obligatorio.</b>',
                    },
                    stringLength: {
                        min: 4,
                        message: '<b style="color:red;">Debe contener mínimo 4 caracteres.</b>'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">La contraseña es obligatoria.</b>'
                    },
                    stringLength: {
                        min: 6,
                        message: '<b style="color:red;">Debe contener mínimo 6 caracteres.</b>'
                    }
                }
            }
        }
    });

    // Botón login (NO submit)
    $('.btn-login').on('click', function (e) {
        e.preventDefault();
        handleLoginSubmit();
    });

    // Toggle mostrar/ocultar contraseña
    $('#togglePassword').on('click', function () {
        const input = $('#password');
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
        $(this).toggleClass('fa-eye fa-eye-slash');
    });
});

/* ==========================================================================
   Verificar autenticación
   ========================================================================== */
function checkAuthentication() {
    // Si por alguna razón el usuario está en login pero autenticado, redirigir
    $.ajax({
        url: LOGIN_URL,
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            // Si la respuesta es un JSON, significa que está autenticado
            if (typeof response === 'object' && response.redirect) {
                window.location.replace(response.redirect);
            }
        }
    });
}

/* ==========================================================================
   Manejo del envío del formulario de login con AJAX
   ========================================================================== */
function handleLoginSubmit() {

    const $form = $('#loginForm');
    const fv = $form.data('formValidation');

    // Resetear estados previos y validar
    fv.resetForm();
    fv.validate();

    if (!fv.isValid()) {
        new PNotify({
            title: 'No se pudo proceder',
            text: 'Complete y corrija toda la información del formulario.',
            type: 'error'
        });
        return;
    }

    // Enviar datos por AJAX directamente (sin confirmación)
    $.ajax({
        type: 'POST',
        url: LOGIN_URL,
        data: $form.serialize(),
        dataType: 'json',
        beforeSend: function() {
            $('.btn-login').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        },
        success: function(response) {
            if (response.success) {
                // Login exitoso - limpiar inputs
                $('#email').val('');
                $('#password').val('');
                
                // Obtener la URL de redirección del servidor (según rol)
                const redirectUrl = response.redirect || HOME_URL;
                
                // Mostrar notificación de éxito
                new PNotify({
                    title: 'Acceso exitoso',
                    text: 'Redirigiendo...',
                    type: 'success',
                    delay: 1000
                });
                
                // Redirigir usando replace para evitar retroceso al login
                setTimeout(function() {
                    window.location.replace(redirectUrl);
                }, 1000);
                
            } else {
                // Error en login
                new PNotify({
                    title: 'Error de acceso',
                    text: response.message || 'Credenciales incorrectas.',
                    type: 'error'
                });
                $('.btn-login').prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> Ingresar');
            }
        },
            error: function(xhr, status, error) {
                let errorMessage = 'Error en el servidor. Intente nuevamente.';
                let errorTitle = 'Error';
                
                if (xhr.status === 419) {
                    // Error de token CSRF
                    errorTitle = 'Sesión expirada';
                    errorMessage = 'Su sesión ha expirado. Por favor, recargue la página e intente nuevamente.';
                } else if (xhr.status === 401) {
                    // Credenciales incorrectas
                    errorTitle = 'Credenciales incorrectas';
                    errorMessage = xhr.responseJSON?.message || 'El correo o la contraseña son incorrectos.';
                } else if (xhr.status === 403) {
                    // Usuario sin roles asignados
                    errorTitle = 'Acceso denegado';
                    errorMessage = xhr.responseJSON?.message || 'El usuario no tiene permisos para iniciar sesión. Contacte al administrador.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    errorTitle = 'Error de validación';
                    errorMessage = 'Verifique que el correo y la contraseña sean correctos.';
                } else if (xhr.status === 500) {
                    errorTitle = 'Error del servidor';
                    errorMessage = 'Error interno del servidor. Contacte al administrador.';
                }
                
                // Verificar si PNotify está disponible
                if (typeof PNotify !== 'undefined') {
                    new PNotify({
                        title: errorTitle,
                        text: errorMessage,
                        type: 'error',
                        delay: 3000
                    });
                } else {
                    console.error('PNotify no está disponible');
                    alert(errorTitle + '\n' + errorMessage);
                }
                
                $('.btn-login').prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> Ingresar');
            }
        });
}

/* ==========================================================================
   Notificación reutilizable con PNotify
   ========================================================================== */
function showNotification(title, text, type = 'info', delay = 3000) {
    new PNotify({
        title: title,
        text: text,
        type: type,
        delay: delay,
        styling: 'bootstrap3'
    });
}