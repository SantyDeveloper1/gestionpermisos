// Variables globales para el wizard
let currentStep = 1;
const totalSteps = 4;
let selectedAsignatura = null;
let permisoFechaFin = null;
let horasPendientes = 0; // Horas pendientes de recuperar

// Inicialización cuando el DOM está listo
$(document).ready(function() {
    initializeFormValidation();
    initializeWizard();
});

/**
 * Inicializar el wizard de pasos
 */
function initializeWizard() {
    // Resetear al paso 1 cuando se abre el modal
    $('#nuevoPlanModal').on('show.bs.modal', function() {
        goToStep(1);
        resetForm();
    });

    // Evento para buscar asignatura
    $('#btnBuscarAsignatura').on('click', buscarAsignatura);
    
    // Permitir buscar con Enter
    $('#codigoAsignatura').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            buscarAsignatura();
        }
    });
}

/**
 * Navegar al siguiente paso
 */
function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            goToStep(currentStep + 1);
        }
    }
}

/**
 * Navegar al paso anterior
 */
function prevStep() {
    if (currentStep > 1) {
        goToStep(currentStep - 1);
    }
}

/**
 * Ir a un paso específico
 */
function goToStep(step) {
    // Ocultar todos los pasos
    $('.step-content').removeClass('active');
    $('.step').removeClass('active completed');
    
    // Mostrar el paso actual
    $('#step' + step + '-content').addClass('active');
    $('#stepIndicator' + step).addClass('active');
    
    // Marcar pasos anteriores como completados
    for (let i = 1; i < step; i++) {
        $('#stepIndicator' + i).addClass('completed');
    }
    
    currentStep = step;
    
    // Actualizar botones
    updateNavigationButtons();
    
    // Si es el paso 3, actualizar validación
    if (step === 3) {
        updateValidationStep();
    }
    
    // Si es el paso 4, actualizar confirmación
    if (step === 4) {
        updateConfirmationStep();
    }
}

/**
 * Actualizar botones de navegación
 */
function updateNavigationButtons() {
    const btnPrev = $('#btnPrevStep');
    const btnNext = $('#btnNextStep');
    const btnSubmit = $('#btnSubmit');
    
    // Botón anterior
    if (currentStep === 1) {
        btnPrev.hide();
    } else {
        btnPrev.show();
    }
    
    // Botón siguiente / enviar
    if (currentStep === totalSteps) {
        btnNext.hide();
        btnSubmit.show();
    } else {
        btnNext.show();
        btnSubmit.hide();
    }
}

/**
 * Validar el paso actual antes de avanzar
 */
function validateCurrentStep() {
    switch (currentStep) {
        case 1:
            return validateStep1();
        case 2:
            return validateStep2();
        case 3:
            return validateStep3();
        default:
            return true;
    }
}

/**
 * Validar paso 1: Permiso y configuración del plan
 */
function validateStep1() {
    const idPermiso = $('#selectPermiso').val();
    const totalHoras = $('#totalHorasRecuperar').val();
    const fechaPresentacion = $('input[name="fecha_presentacion"]').val();
    const estadoPlan = $('select[name="estado_plan"]').val();
    
    if (!idPermiso) {
        new PNotify({
            title: 'Permiso requerido',
            text: 'Debe seleccionar un permiso para crear el plan de recuperación',
            type: 'error'
        });
        return false;
    }
    
    if (!totalHoras || parseFloat(totalHoras) <= 0) {
        new PNotify({
            title: 'Horas inválidas',
            text: 'Las horas a recuperar deben ser mayores a 0',
            type: 'error'
        });
        return false;
    }
    
    // Validar si el plan ya está completado
    if (horasPendientes <= 0) {
        let mensaje = 'Este plan ya ha completado todas las horas de recuperación.';
        
        // Agregar información sobre sesiones programadas si existen
        const sesionesProg = parseInt(window.sesionesProgramadas) || 0;
        const horasProg = parseFloat(window.horasProgramadas) || 0;
        
        if (sesionesProg > 0) {
            mensaje += `\n\nHay ${horasProg.toFixed(2)} horas en ${sesionesProg} sesión(es) PROGRAMADA(S).\n`;
            mensaje += 'Cambie el estado de esas sesiones a REALIZADA o CANCELADA para poder registrar nuevas sesiones.';
        }
        
        new PNotify({
            title: 'Advertencia',
            text: mensaje,
            type: 'warning',
            delay: 6000
        });
        return false;
    }
    
    if (!fechaPresentacion) {
        new PNotify({
            title: 'Fecha requerida',
            text: 'Debe ingresar la fecha de presentación del plan',
            type: 'error'
        });
        return false;
    }
    
    if (!estadoPlan) {
        new PNotify({
            title: 'Estado requerido',
            text: 'Debe seleccionar el estado del plan',
            type: 'error'
        });
        return false;
    }
    
    return true;
}

/**
 * Validar paso 2: Detalles de la sesión
 */
function validateStep2() {
    const idAsignatura = $('#idAsignatura').val();
    const fechaSesion = $('#fecha_sesion').val();
    const horaInicio = $('#hora_inicio').val();
    const horaFin = $('#hora_fin').val();
    const horasRecuperadas = $('#horas_recuperadas').val();
    const tema = $('#tema').val().trim();
    
    if (!idAsignatura) {
        new PNotify({
            title: 'Asignatura requerida',
            text: 'Debe buscar y seleccionar una asignatura',
            type: 'error'
        });
        return false;
    }
    
    if (!fechaSesion) {
        new PNotify({
            title: 'Fecha requerida',
            text: 'Debe ingresar la fecha de la sesión',
            type: 'error'
        });
        return false;
    }
    
    if (!horaInicio || !horaFin) {
        new PNotify({
            title: 'Horario requerido',
            text: 'Debe ingresar la hora de inicio y fin de la sesión',
            type: 'error'
        });
        return false;
    }
    
    if (horaInicio >= horaFin) {
        new PNotify({
            title: 'Horario inválido',
            text: 'La hora de inicio debe ser anterior a la hora de fin',
            type: 'error'
        });
        return false;
    }
    
    if (!horasRecuperadas || parseFloat(horasRecuperadas) <= 0) {
        new PNotify({
            title: 'Horas inválidas',
            text: 'Las horas de la sesión deben ser mayores a 0',
            type: 'error'
        });
        return false;
    }
    
    // Validar tema
    if (!tema) {
        new PNotify({
            title: 'Tema requerido',
            text: 'Debe ingresar el tema que se desarrollará en la sesión',
            type: 'error'
        });
        return false;
    }
    
    // Validar que las horas no excedan las horas pendientes
    const horasSesion = parseFloat(horasRecuperadas);
    if (horasSesion > horasPendientes) {
        new PNotify({
            title: 'Error',
            text: `Las horas exceden el total del plan. Máximo permitido: ${horasPendientes} horas`,
            type: 'error'
        });
        return false;
    }
    
    return true;
}

/**
 * Validar paso 3: Validación de horas
 */
function validateStep3() {
    const horasSesion = parseFloat($('#horas_recuperadas').val()) || 0;
    const totalHorasPlan = parseFloat($('#totalHorasRecuperar').val()) || 0;
    
    if (horasSesion > totalHorasPlan) {
        new PNotify({
            title: 'Horas excedidas',
            text: 'Las horas de la sesión (' + horasSesion + ') exceden el total del plan (' + totalHorasPlan + ')',
            type: 'error'
        });
        return false;
    }
    
    return true;
}

/**
 * Actualizar el paso 3 con la validación de horas
 */
function updateValidationStep() {
    const horasSesion = parseFloat($('#horas_recuperadas').val()) || 0;
    const totalHorasPlan = parseFloat($('#totalHorasRecuperar').val()) || 0;
    
    $('#horasSesion').text(horasSesion.toFixed(1));
    $('#nuevoTotal').text(horasSesion.toFixed(1));
    
    const alertDiv = $('#validationAlert');
    const alertMsg = $('#alertMessage');
    
    if (horasSesion > totalHorasPlan) {
        alertMsg.text(`Las horas de la sesión (${horasSesion}) exceden el total del plan (${totalHorasPlan})`);
        alertDiv.show();
    } else {
        alertDiv.hide();
    }
}

/**
 * Actualizar el paso 4 con los datos de confirmación
 */
function updateConfirmationStep() {
    // Datos del permiso/docente
    const selectedOption = $('#selectPermiso option:selected');
    const docente = selectedOption.data('docente') || '-';
    $('#confirmDocente').text(docente);
    
    const idPermiso = $('#selectPermiso').val();
    $('#confirmPlan').text('Plan #' + (idPermiso || '-'));
    
    // Datos de la asignatura
    const nombreAsignatura = $('#nombreAsignatura').val() || '-';
    $('#confirmCurso').text(nombreAsignatura);
    
    // Horario
    const horaInicio = $('#hora_inicio').val();
    const horaFin = $('#hora_fin').val();
    $('#confirmHorario').text((horaInicio && horaFin) ? `${horaInicio} - ${horaFin}` : '-');
    
    // Fecha
    const fechaSesion = $('#fecha_sesion').val();
    $('#confirmFecha').text(fechaSesion ? formatDate(fechaSesion) : '-');
    
    // Horas
    const horas = $('#horas_recuperadas').val() || '0';
    $('#confirmHoras').text(horas + ' horas');
    
    // Estado
    const estado = $('select[name="estado_sesion"] option:selected').text() || '-';
    $('#confirmEstado').text(estado);
    
    // Tema
    const tema = $('#tema').val() || '-';
    // Agregar tema después del estado si no existe el elemento
    if ($('#confirmTema').length === 0) {
        $('#confirmEstado').parent().after(`
            <div class="col-md-12 mt-2">
                <div style="color: var(--medium-gray); font-size: 0.9rem;">
                    <i class="fas fa-book-open mr-2"></i>
                    Tema: <span id="confirmTema">-</span>
                </div>
            </div>
        `);
    }
    $('#confirmTema').text(tema);
}

/**
 * Buscar asignatura por código
 */
function buscarAsignatura() {
    const codigo = $('#codigoAsignatura').val().trim().toUpperCase();
    
    if (!codigo) {
        new PNotify({
            title: 'Código requerido',
            text: 'Ingrese el código de la asignatura',
            type: 'warning'
        });
        return;
    }
    
    $.ajax({
        url: _urlBase + '/admin/asignatura/buscar',
        type: 'GET',
        data: { codigo: codigo },
        success: function(response) {
            if (response.success && response.asignatura) {
                selectedAsignatura = response.asignatura;
                $('#idAsignatura').val(response.asignatura.idAsignatura);
                $('#nombreAsignatura').val(response.asignatura.nom_asignatura);
                
                new PNotify({
                    title: 'Asignatura encontrada',
                    text: response.asignatura.nom_asignatura,
                    type: 'success'
                });
            } else {
                $('#idAsignatura').val('');
                $('#nombreAsignatura').val('');
                selectedAsignatura = null;
                
                new PNotify({
                    title: 'No encontrada',
                    text: 'No se encontró ninguna asignatura con el código: ' + codigo,
                    type: 'error'
                });
            }
        },
        error: function() {
            new PNotify({
                title: 'Error',
                text: 'Error al buscar la asignatura',
                type: 'error'
            });
        }
    });
}

/**
 * Calcular horas a recuperar basado en hora inicio y fin
 */
function calcularHorasRecuperar() {
    const horaInicio = $('#hora_inicio').val();
    const horaFin = $('#hora_fin').val();
    
    if (horaInicio && horaFin) {
        const inicio = new Date('1970-01-01T' + horaInicio);
        const fin = new Date('1970-01-01T' + horaFin);
        
        if (fin > inicio) {
            const diffMs = fin - inicio;
            const diffHrs = diffMs / (1000 * 60 * 60);
            $('#horas_recuperadas').val(diffHrs.toFixed(1));
        } else {
            $('#horas_recuperadas').val('');
        }
    }
}

/**
 * Formatear fecha para mostrar
 */
function formatDate(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/**
 * Resetear formulario
 */
function resetForm() {
    $('#frmPlanInsert')[0].reset();
    $('#permisoInfo').hide();
    $('#totalHorasDisplay').text('0');
    $('#totalHorasRecuperar').val('');
    $('#nombreAsignatura').val('');
    $('#idAsignatura').val('');
    $('#codigoAsignatura').val('');
    selectedAsignatura = null;
    permisoFechaFin = null;
    horasPendientes = 0; // Resetear horas pendientes
    
    if ($('#frmPlanInsert').data('formValidation')) {
        $('#frmPlanInsert').data('formValidation').resetForm();
    }
}

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
    .on('change', 'select[name="id_permiso"]', function(e) {
        $('#frmPlanInsert').formValidation('revalidateField', 'id_permiso');
    });
}

/**
 * Función para enviar formulario de inserción
 */
function sendFrmPlanInsert() {
    // Validar todos los pasos
    if (!validateStep1() || !validateStep2() || !validateStep3()) {
        new PNotify({
            title: 'Validación incompleta',
            text: 'Complete todos los pasos correctamente',
            type: 'error'
        });
        return;
    }

    // Confirmar antes de enviar
    swal({
        title: 'Confirmar operación',
        text: '¿Desea registrar el plan de recuperación con la sesión inicial?',
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, proceder']
    }).then((proceed) => {
        if (proceed) {
            registrarPlanYSesion();
        }
    });
}

/**
 * Registrar plan de recuperación con sesión
 */
function registrarPlanYSesion() {
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
                text: 'Plan de recuperación y sesión registrados correctamente',
                type: 'success'
            });
            
            $('#nuevoPlanModal').modal('hide');
            resetForm();
            
            // Agregar la nueva fila a la tabla dinámicamente
            if (response.plan) {
                agregarFilaPlan(response.plan);
            }
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

/**
 * Agregar una nueva fila a la tabla de planes dinámicamente
 */
function agregarFilaPlan(plan) {
    const fecha = new Date(plan.fecha_presentacion);
    const fechaFormateada = fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    let estadoBadge = '';
    if (plan.estado_plan === 'PRESENTADO') {
        estadoBadge = `<span class="badge-modern badge-presentado">
            <span class="status-dot dot-presentado"></span>
            ${plan.estado_plan}
        </span>`;
    } else if (plan.estado_plan === 'APROBADO') {
        estadoBadge = `<span class="badge-modern badge-aprobado">
            <span class="status-dot dot-aprobado"></span>
            ${plan.estado_plan}
        </span>`;
    } else {
        estadoBadge = `<span class="badge-modern badge-observado">
            <span class="status-dot dot-observado"></span>
            ${plan.estado_plan}
        </span>`;
    }
    
    const table = $('#tablaExample2').DataTable();
    
    if (table) {
        const newRow = table.row.add([
            table.rows().count() + 1,
            `<div style="color: var(--dark-gray);">${plan.tipo_permiso}</div>`,
            `<div><strong>${plan.docente_apellido}, ${plan.docente_nombre}</strong></div>`,
            `<div class="text-center">
                <span style="color: var(--dark-gray);">${plan.total_horas_recuperar}</span><br>
                <small style="color: var(--medium-gray);">horas</small>
            </div>`,
            `<div class="text-center">
                <span style="color: var(--dark-gray);">${fechaFormateada}</span>
            </div>`,
            estadoBadge,
            `<div class="action-buttons">
                <a href="${_urlBase}/admin/sesion_recuperacion?plan_id=${plan.id_plan}" class="btn-icon btn-view" title="Ver sesiones de recuperación">
                    <i class="fas fa-eye"></i>
                </a>
                <button class="btn-icon btn-edit" onclick="editPlan('${plan.id_plan}')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                ${plan.estado_plan === 'PRESENTADO' ? `
                <button class="btn-icon btn-approve" onclick="aprobarPlan('${plan.id_plan}')" title="Aprobar Plan">
                    <i class="fas fa-check"></i>
                </button>
                ` : ''}
                <button class="btn-icon btn-delete" onclick="deletePlan('${plan.id_plan}')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`
        ]);
        
        table.draw(false);
        
        const filaElement = newRow.node();
        if (filaElement) {
            filaElement.style.opacity = '0';
            setTimeout(() => {
                filaElement.style.transition = 'opacity 0.5s ease-in';
                filaElement.style.opacity = '1';
            }, 100);
        }
    }
}

/**
 * Cargar horas del permiso seleccionado
 */
function cargarHorasPermiso() {
    const selectedOption = $('#selectPermiso option:selected');
    const idPermiso = $('#selectPermiso').val();
    const horas = selectedOption.data('horas');
    const docente = selectedOption.data('docente');
    const tipo = selectedOption.data('tipo');
    const periodo = selectedOption.data('periodo');
    const fechaFin = selectedOption.data('fecha-fin');
    
    if (horas && idPermiso) {
        $('#totalHorasDisplay').text(horas);
        $('#totalHorasRecuperar').val(horas);
        $('#infoDocente').text(docente || '-');
        $('#infoTipoPermiso').text(tipo || '-');
        $('#infoPeriodo').text(periodo || '-');
        $('#infoHorasAfectadas').text(horas + ' horas');
        $('#permisoInfo').slideDown();
        
        // Guardar fecha fin para referencia
        permisoFechaFin = fechaFin;
        
        // Establecer fecha mínima para la sesión (fecha actual)
        const today = new Date().toISOString().split('T')[0];
        $('#fecha_sesion').attr('min', today);
        
        // Obtener el progreso de recuperación de este permiso
        obtenerProgresoRecuperacion(idPermiso, horas);
    } else {
        $('#totalHorasDisplay').text('0');
        $('#totalHorasRecuperar').val('');
        $('#permisoInfo').slideUp();
        $('#planSummary').slideUp();
        permisoFechaFin = null;
        
        // Establecer fecha mínima para la sesión (fecha actual)
        const today = new Date().toISOString().split('T')[0];
        $('#fecha_sesion').attr('min', today);
    }
}

/**
 * Obtener el progreso de recuperación de un permiso
 */
function obtenerProgresoRecuperacion(idPermiso, horasTotales) {
    $.ajax({
        url: _urlBase + '/admin/plan_recuperacion/progreso/' + idPermiso,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const horasRecuperadas = parseFloat(response.horas_recuperadas) || 0;
                horasPendientes = parseFloat(horasTotales) - horasRecuperadas; // Actualizar variable global
                
                // Actualizar el resumen
                $('#horasTotales').text(parseFloat(horasTotales).toFixed(1));
                $('#horasRecuperadas').text(horasRecuperadas.toFixed(1));
                $('#horasPendientes').text(horasPendientes.toFixed(1));
                
                // Mostrar el resumen
                $('#planSummary').slideDown();
                
                // Guardar información de sesiones programadas para usar en validateStep1
                window.sesionesProgramadas = response.sesiones_programadas || 0;
                window.horasProgramadas = response.horas_programadas || 0;
            } else {
                // Si no hay plan existente, mostrar todo como pendiente
                horasPendientes = parseFloat(horasTotales); // Actualizar variable global
                $('#horasTotales').text(parseFloat(horasTotales).toFixed(1));
                $('#horasRecuperadas').text('0.0');
                $('#horasPendientes').text(horasPendientes.toFixed(1));
                $('#planSummary').slideDown();
                window.sesionesProgramadas = 0;
                window.horasProgramadas = 0;
            }
        },
        error: function() {
            // En caso de error, asumir que no hay recuperación previa
            horasPendientes = parseFloat(horasTotales); // Actualizar variable global
            $('#horasTotales').text(parseFloat(horasTotales).toFixed(1));
            $('#horasRecuperadas').text('0.0');
            $('#horasPendientes').text(horasPendientes.toFixed(1));
            $('#planSummary').slideDown();
            window.sesionesProgramadas = 0;
            window.horasProgramadas = 0;
        }
    });
}

