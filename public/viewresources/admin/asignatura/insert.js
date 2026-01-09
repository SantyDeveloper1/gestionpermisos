'use strict';
// Configurar CSRF para AJAX (si usas AJAX en lugar de submit directo)
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
// Inicializar validación del formulario de asignatura
$(() => {
    $('#frmAsignaturaInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
        live: 'enabled',
        message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        trigger: null,
        fields: {
            codigo_asignatura: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    }
                }
            },
            nom_asignatura: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    }
                }
            },
            creditos: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    },
                    numeric: {
                        message: '<b style="color: red;">Solo se permiten números.</b>'
                    }
                }
            },
            horas_teoria: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    },
                    numeric: {
                        message: '<b style="color: red;">Solo se permiten números.</b>'
                    }
                }
            },
            horas_practica: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">Este campo es requerido.</b>'
                    },
                    numeric: {
                        message: '<b style="color: red;">Solo se permiten números.</b>'
                    }
                }
            },
            IdCiclo: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">Debe seleccionar el ciclo.</b>'
                    }
                }
            },
            tipo: {
                validators: {
                    notEmpty: {
                        message: '<b style="color: red;">Debe seleccionar un tipo.</b>'
                    }
                }
            }
        }
    });
});
/**
 * Función para enviar el formulario de asignatura
 */
function sendFrmAsignaturaInsert() {
    var isValid = null;
    // Reiniciar y validar formulario
    $('#frmAsignaturaInsert').data('formValidation').resetForm();
    $('#frmAsignaturaInsert').data('formValidation').validate();
    isValid = $('#frmAsignaturaInsert').data('formValidation').isValid();
    if(!isValid) {
        new PNotify({
            title: 'No se pudo proceder',
            text: 'Complete y corrija toda la información del formulario.',
            type: 'error'
        });
        return;
    }
    swal({
        title: 'Confirmar operación',
        text: '¿Realmente desea registrar la asignatura?',
        icon: 'warning',
        buttons: ['No, cancelar.', 'Sí, proceder.']
    })
    .then((proceed) => {
        if(proceed) {
            // Enviar formulario
            $('#frmAsignaturaInsert')[0].submit();
        }
    });
}