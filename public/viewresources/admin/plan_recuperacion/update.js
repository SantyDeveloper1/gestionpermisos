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
                    new PNotify({
                        title: '¡Aprobado!',
                        text: 'El plan ha sido aprobado correctamente',
                        type: 'success'
                    });
                    setTimeout(() => location.reload(), 1000);
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
            new PNotify({
                title: '¡Éxito!',
                text: 'Plan de recuperación actualizado correctamente',
                type: 'success'
            });
            $('#editPlanModal').modal('hide');
            setTimeout(() => location.reload(), 1000);
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

// Función para formatear fecha
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}
