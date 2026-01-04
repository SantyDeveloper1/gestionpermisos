// Inicialización cuando el DOM está listo
$(document).ready(function() {
    initializeFormValidation();
});

/**
 * Inicializar FormValidation para el formulario de plan de recuperación
 */
function initializeFormValidation() {
    $('#frmPlanInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
        live: 'enabled',
        message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        trigger: null,
        // Configuración para que los errores aparezcan en el contenedor correcto
        row: {
            selector: '.form-group-modern, .permission-card'
        },
        fields: {
            id_permiso: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Seleccione un permiso para recuperación.</b>'
                    }
                }
            },
            total_horas_recuperar: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Las horas a recuperar son requeridas.</b>'
                    },
                    greaterThan: {
                        value: 0,
                        message: '<b style="color:red;">Las horas a recuperar deben ser mayores a 0.</b>'
                    }
                }
            },
            fecha_presentacion: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">La fecha de presentación es requerida.</b>'
                    },
                    date: {
                        format: 'YYYY-MM-DD',
                        message: '<b style="color:red;">La fecha debe tener un formato válido.</b>'
                    }
                }
            },
            estado_plan: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Seleccione el estado del plan.</b>'
                    }
                }
            }
        }
    })
    // Revalidar cuando cambia el Select2
    .on('change', 'select[name="id_permiso"]', function(e) {
        $('#frmPlanInsert').formValidation('revalidateField', 'id_permiso');
    });
}

/**
 * Función para enviar formulario de inserción
 */
function sendFrmPlanInsert() {
    // Obtener la instancia de FormValidation
    const fv = $('#frmPlanInsert').data('formValidation');
    
    // Resetear el estado de validación anterior
    fv.resetForm();
    
    // Validar el formulario
    fv.validate();

    let isValid = fv.isValid();

    if (!isValid) {
        new PNotify({
            title: 'Formulario incompleto',
            text: 'Complete y corrija toda la información del formulario.',
            type: 'error'
        });
        return;
    }

    // Validación adicional de horas
    const horasRecuperar = parseFloat($('#totalHorasRecuperar').val());
    if (horasRecuperar <= 0 || isNaN(horasRecuperar)) {
        new PNotify({
            title: 'Error de validación',
            text: 'Las horas a recuperar deben ser mayores a 0',
            type: 'error'
        });
        return;
    }

    // Confirmar antes de enviar
    swal({
        title: 'Confirmar operación',
        text: '¿Realmente desea registrar este plan de recuperación?',
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, proceder']
    }).then((proceed) => {
        if (proceed) {
            registrarPlanRecuperacion();
        }
    });
}

/**
 * Registrar plan de recuperación
 */
function registrarPlanRecuperacion() {
    const form = $('#frmPlanInsert');
    const formData = new FormData(form[0]);

    $.ajax({
        url: form.attr('action') || _urlBase + '/admin/plan_recuperacion/insert',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            new PNotify({
                title: '¡Éxito!',
                text: 'Plan de recuperación registrado correctamente',
                type: 'success'
            });
            
            $('#nuevoPlanModal').modal('hide');
            
            // Resetear formulario y FormValidation
            form[0].reset();
            if ($('#frmPlanInsert').data('formValidation')) {
                $('#frmPlanInsert').data('formValidation').resetForm();
            }
            
            // Recargar la página
            setTimeout(() => {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            let errorMsg = 'No se pudo registrar el plan de recuperación';
            
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                errorMsg = Object.values(errors).flat().join('<br>');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            new PNotify({
                title: 'Error',
                text: errorMsg,
                type: 'error'
            });
        }
    });
}
