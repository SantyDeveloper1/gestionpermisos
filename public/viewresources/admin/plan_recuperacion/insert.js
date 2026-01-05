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
    // Formatear fecha
    const fecha = new Date(plan.fecha_presentacion);
    const fechaFormateada = fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    // Determinar badge de estado
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
    
    // Obtener la instancia de DataTable
    const table = $('#tablaExample2').DataTable();
    
    if (table) {
        // Usar la API de DataTables para agregar la fila correctamente
        const newRow = table.row.add([
            // Columna 1: #
            table.rows().count() + 1,
            // Columna 2: Permiso
            `<div style="color: var(--dark-gray);">${plan.tipo_permiso}</div>`,
            // Columna 3: Docente
            `<div><strong>${plan.docente_apellido}, ${plan.docente_nombre}</strong></div>`,
            // Columna 4: Horas a Recuperar
            `<div class="text-center">
                <span style="color: var(--dark-gray);">${plan.total_horas_recuperar}</span><br>
                <small style="color: var(--medium-gray);">horas</small>
            </div>`,
            // Columna 5: Fecha Presentación
            `<div class="text-center">
                <span style="color: var(--dark-gray);">${fechaFormateada}</span>
            </div>`,
            // Columna 6: Estado
            estadoBadge,
            // Columna 7: Acciones
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
        
        // Redibujar la tabla
        table.draw(false);
        
        // Animar la nueva fila
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
