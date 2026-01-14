'use strict';

// ==================== CONFIGURAR CSRF ====================
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ==================== INICIALIZAR FORMVALIDATION ====================
$(() => {
    $('#userProfileForm').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
        live: 'enabled',
        message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        trigger: null,
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">El nombre es obligatorio.</b>'
                    },
                    stringLength: {
                        max: 100,
                        message: '<b style="color: red;">Máximo 100 caracteres permitidos.</b>'
                    }
                }
            },
            last_name: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">El apellido es obligatorio.</b>'
                    },
                    stringLength: {
                        max: 100,
                        message: '<b style="color: red;">Máximo 100 caracteres permitidos.</b>'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">El correo electrónico es obligatorio.</b>'
                    },
                    emailAddress: {
                        message: '<b style="color: red;">Ingrese un correo electrónico válido.</b>'
                    }
                }
            },
            document_type: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">Debe seleccionar el tipo de documento.</b>'
                    }
                }
            },
            document_number: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">El número de documento es obligatorio.</b>'
                    },
                    stringLength: {
                        min: 8,
                        max: 20,
                        message: '<b style="color: red;">El documento debe tener entre 8 y 20 caracteres.</b>'
                    }
                }
            },
            gender: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">Debe seleccionar el género.</b>'
                    }
                }
            }
        }
    });
});

/**
 * Función para enviar el formulario de perfil
 */
function sendFrmProfileUpdate() {
    var formValidation = $('#userProfileForm').data('formValidation');
    
    if (!formValidation) {
        new PNotify({
            title: 'Error',
            text: 'Error de inicialización del formulario.',
            type: 'error'
        });
        return;
    }
    
    var isValid = null;
    
    // Reiniciar y validar formulario
    formValidation.resetForm();
    formValidation.validate();
    isValid = formValidation.isValid();
    
    if (!isValid) {
        new PNotify({
            title: 'No se pudo proceder',
            text: 'Complete y corrija toda la información del formulario.',
            type: 'error'
        });
        return;
    }

    // Proceder con el envío AJAX
    updateProfile();
}

// ==================== ACTUALIZAR PERFIL CON AJAX ====================
function updateProfile() {

    const formData = new FormData($('#userProfileForm')[0]);

    // Usar un selector más específico para el botón visible
    const $btn = $('.card-footer button[type="submit"].btn-primary');
    const originalButtonHtml = '<i class="fas fa-save mr-1"></i> Guardar Cambios';
    
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
        url: `${_urlBase}/admin/profile/update`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function(response) {
            if (response.success) {
                showSuccess(response.message || 'Perfil actualizado correctamente.');
                
                // Actualizar la imagen de perfil si se cambió
                if (response.user && response.user.image) {
                    updateProfileImage(response.user.image);
                }

                // Actualizar el nombre en el sidebar si cambió
                if (response.user && response.user.name) {
                    $('.sidebar .info a.d-block').text(response.user.name);
                }

                // Ocultar la vista previa después de guardar
                $('#imagePreview').hide();
                $('#imageInput').val(''); // Limpiar el input de archivo

                $btn.prop('disabled', false).html(originalButtonHtml);
            } else {
                showError(response.message || 'No se pudo actualizar el perfil.');
                $btn.prop('disabled', false).html(originalButtonHtml);
            }
        },

        error: function(xhr) {
            $btn.prop('disabled', false).html(originalButtonHtml);

            let errorMsg = 'Ha ocurrido un error al actualizar el perfil.';

            if (xhr.status === 422) {
                // Errores de validación
                if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON?.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                }
            } else if (xhr.status === 404) {
                errorMsg = xhr.responseJSON?.message || 'Usuario no encontrado.';
            } else if (xhr.status === 500) {
                errorMsg = xhr.responseJSON?.message || 'Error del servidor. Por favor, contacte al administrador.';
            } else if (xhr.responseJSON?.message) {
                errorMsg = xhr.responseJSON.message;
            }

            showError(errorMsg);
        }
    });
}

// ==================== ACTUALIZAR IMAGEN DE PERFIL ====================
function updateProfileImage(imagePath) {
    const imageUrl = `${_urlBase}/storage/${imagePath}`;
    
    // Actualizar imagen en el card de perfil
    $('#profileImage').attr('src', imageUrl);
    
    // Actualizar imagen en el navbar si existe
    $('.navbar .image img').attr('src', imageUrl);
    
    // Actualizar imagen en el sidebar si existe
    $('.sidebar .user-panel .image img').attr('src', imageUrl);
}

// ==================== MOSTRAR ÉXITO ====================
function showSuccess(message) {
    new PNotify({
        title: 'Perfil Actualizado',
        text: message,
        type: 'success',
        delay: 3000
    });
}

// ==================== MOSTRAR ERROR ====================
function showError(message) {
    new PNotify({
        title: 'Error',
        text: message,
        type: 'error',
        delay: 4000
    });
}

// ==================== PREVISUALIZAR IMAGEN ====================
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    const profileImage = document.getElementById('profileImage');
    const profileDefault = document.getElementById('profileDefault');
    
    if (input.files && input.files[0]) {
        // Validar tamaño (máximo 2MB)
        if (input.files[0].size > 2048 * 1024) {
            showError('La imagen no debe superar los 2MB.');
            input.value = '';
            return;
        }

        // Validar tipo de archivo
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!validTypes.includes(input.files[0].type)) {
            showError('Solo se permiten imágenes (JPEG, PNG, JPG, GIF, WEBP).');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Mostrar previsualización
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
            
            // Actualizar imagen principal
            if (profileImage) {
                profileImage.src = e.target.result;
            } else if (profileDefault) {
                profileDefault.style.display = 'none';
                if (!profileImage) {
                    const newImg = document.createElement('img');
                    newImg.id = 'profileImage';
                    newImg.className = 'profile-user-img img-fluid img-circle';
                    newImg.src = e.target.result;
                    newImg.style = 'width: 150px; height: 150px; object-fit: cover;';
                    profileDefault.parentNode.insertBefore(newImg, profileDefault);
                }
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
