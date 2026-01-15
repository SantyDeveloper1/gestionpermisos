// Función para ver detalles del plan
function viewPlan(id) {
    $.ajax({
        url: _urlBase + '/admin/plan_recuperacion/' + id,
        type: 'GET',
        success: function(response) {
            $('#viewPermisoInfo').html(`
                <strong>Permiso #${response.permiso.id_permiso}</strong><br>
                ${response.permiso.tipoPermiso.nombre}<br>
                <small>${formatDate(response.permiso.fecha_inicio)} al ${formatDate(response.permiso.fecha_fin)}</small>
            `);
            
            $('#viewDocenteInfo').html(`
                <strong>${response.permiso.docente.full_name}</strong>
            `);
            
            $('#viewFechaPresentacion').html(formatDate(response.fecha_presentacion));
            $('#viewHorasRecuperar').html(`${response.total_horas_recuperar} horas`);
            
            // Configurar badge de estado
            $('#viewEstadoPlan').html(response.estado_plan)
                .removeClass()
                .addClass('badge-estado badge-' + response.estado_plan.toLowerCase());
            
            $('#viewHorasAfectadas').html(response.permiso.horas_afectadas);
            $('#viewHorasRecuperarCalc').html(response.total_horas_recuperar);
            
            if (response.observacion) {
                $('#viewObservacionPlan').html(response.observacion);
                $('#viewObservacionSection').show();
            } else {
                $('#viewObservacionSection').hide();
            }
            
            $('#viewPlanModal').modal('show');
        },
        error: function(xhr) {
            new PNotify({
                title: 'Error',
                text: 'No se pudieron cargar los detalles del plan',
                type: 'error'
            });
        }
    });
}

// Función para editar plan
function editPlan(id) {
    $.ajax({
        url: _urlBase + '/admin/plan_recuperacion/' + id,
        type: 'GET',
        success: function(response) {
            $('#editIdPlan').val(response.id_plan);
            $('#editEstadoPlan').val(response.estado_plan);
            $('#editFechaPresentacion').val(response.fecha_presentacion);
            
            // Cargar las horas afectadas del permiso automáticamente
            $('#editTotalHorasRecuperar').val(response.permiso.horas_afectadas);
            $('#editObservacionPlan').val(response.observacion || '');
            
            // Mostrar información del permiso relacionado
            $('#editInfoDocente').html(`
                ${response.permiso.docente.full_name}
            `);
            $('#editInfoHorasAfectadas').html(`${response.permiso.horas_afectadas} horas`);
            $('#editInfoTipoPermiso').html(response.permiso.tipoPermiso.nombre);
            $('#editInfoPeriodo').html(`
                ${formatDate(response.permiso.fecha_inicio)} al ${formatDate(response.permiso.fecha_fin)}
            `);
            
            $('#editPlanModal').modal('show');
        },
        error: function(xhr) {
            new PNotify({
                title: 'Error',
                text: 'No se pudieron cargar los datos del plan',
                type: 'error'
            });
        }
    });
}

// Función para aprobar plan
function aprobarPlan(id) {
    swal({
        title: '¿Aprobar Plan de Recuperación?',
        text: "¿Está seguro de aprobar este plan de recuperación?",
        icon: 'warning',
        buttons: ['Cancelar', 'Sí, aprobar']
    }).then((proceed) => {
        if (proceed) {
            $.ajax({
                url: _urlBase + '/admin/plan_recuperacion/aprobar/' + id,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'PATCH'
                },
                success: function(response) {
                    // Actualizar el badge de estado en la tabla
                    const $row = $(`tr:has(button[onclick*="${id}"])`);
                    const $estadoCell = $row.find('td:eq(5)'); // Columna de estado
                    
                    $estadoCell.html(`
                        <span class="badge-modern badge-aprobado">
                            <span class="status-dot dot-aprobado"></span>
                            APROBADO
                        </span>
                    `);
                    
                    // Ocultar el botón de aprobar
                    $row.find('.btn-approve').fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    new PNotify({
                        title: '¡Aprobado!',
                        text: 'El plan ha sido aprobado correctamente',
                        type: 'success'
                    });
                },
                error: function(xhr) {
                    new PNotify({
                        title: 'Error',
                        text: 'No se pudo aprobar el plan',
                        type: 'error'
                    });
                }
            });
        }
    });
}

// Función para actualizar plan
function updatePlan() {
    const form = $('#frmPlanEdit');
    const idPlan = $('#editIdPlan').val();
    const formData = new FormData(form[0]);
    
    // Validar que las horas a recuperar sean mayores a 0
    const horasRecuperar = parseFloat($('#editTotalHorasRecuperar').val());
    if (horasRecuperar <= 0) {
        new PNotify({
            title: 'Error',
            text: 'Las horas a recuperar deben ser mayores a 0',
            type: 'error'
        });
        return;
    }
    
    $.ajax({
        url: _urlBase + '/admin/plan_recuperacion/update/' + idPlan,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            // Actualizar la fila en la tabla
            const $row = $(`tr:has(button[onclick*="${idPlan}"])`);
            const estadoPlan = $('#editEstadoPlan').val();
            const fechaPresentacion = $('#editFechaPresentacion').val();
            
            // Actualizar fecha de presentación (columna 4)
            $row.find('td:eq(4)').html(`
                <div class="text-center">
                    <span style="color: var(--dark-gray);">
                        ${formatDate(fechaPresentacion)}
                    </span>
                </div>
            `);
            
            // Actualizar estado (columna 5)
            let estadoBadge = '';
            if (estadoPlan === 'PRESENTADO') {
                estadoBadge = `
                    <span class="badge-modern badge-presentado">
                        <span class="status-dot dot-presentado"></span>
                        PRESENTADO
                    </span>
                `;
            } else if (estadoPlan === 'APROBADO') {
                estadoBadge = `
                    <span class="badge-modern badge-aprobado">
                        <span class="status-dot dot-aprobado"></span>
                        APROBADO
                    </span>
                `;
            } else {
                estadoBadge = `
                    <span class="badge-modern badge-observado">
                        <span class="status-dot dot-observado"></span>
                        ${estadoPlan}
                    </span>
                `;
            }
            $row.find('td:eq(5)').html(estadoBadge);
            
            new PNotify({
                title: '¡Éxito!',
                text: 'Plan de recuperación actualizado correctamente',
                type: 'success'
            });

            // Guardar el ID del plan para enviar email
            window.planActualizadoId = idPlan;

            // Cerrar modal de edición (el evento hidden.bs.modal abrirá el modal de email)
            $('#editPlanModal').modal('hide');
        },
        error: function(xhr) {
            let errorMsg = 'No se pudo actualizar el plan de recuperación';
            
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

// Función para enviar correo de notificación del plan
function enviarCorreoPlan() {
    const planId = window.planActualizadoId;
    
    if (!planId) {
        new PNotify({
            title: 'Error',
            text: 'No se pudo identificar el plan.',
            type: 'error'
        });
        $('#emailConfirmModalPlan').modal('hide');
        return;
    }

    // Deshabilitar botón mientras se envía
    const btnEnviar = $('#emailConfirmModalPlan .btn-success');
    const originalText = btnEnviar.html();
    btnEnviar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Enviando...');

    $.ajax({
        url: `${_urlBase}/admin/plan_recuperacion/enviar-email/${planId}`,
        type: 'POST',
        dataType: 'json',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                new PNotify({
                    title: '¡Correo Enviado!',
                    text: response.message || 'El correo ha sido enviado al docente.',
                    type: 'success'
                });
            } else {
                new PNotify({
                    title: 'Advertencia',
                    text: response.message || 'El plan se actualizó pero no se pudo enviar el correo.',
                    type: 'warning'
                });
            }
            
            // Cerrar modal
            $('#emailConfirmModalPlan').modal('hide');
        },
        error: function(xhr) {
            console.error('Error al enviar correo:', xhr);
            
            let errorMsg = 'No se pudo enviar el correo.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }

            new PNotify({
                title: 'Error',
                text: errorMsg,
                type: 'error'
            });
            
            // Cerrar modal de todas formas
            $('#emailConfirmModalPlan').modal('hide');
        },
        complete: function() {
            // Restaurar botón
            btnEnviar.prop('disabled', false).html(originalText);
            
            // Limpiar variable global
            window.planActualizadoId = null;
        }
    });
}

// Función para formatear fecha
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}
