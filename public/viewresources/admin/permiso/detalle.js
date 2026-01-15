'use strict';

function viewPermiso(id) {

    $('#viewPermisoModal').modal('show');

    // Estado loading
    const loading = '<span class="text-muted">Cargando...</span>';

    $('#viewDocenteNombre').html(loading);
    $('#viewDocenteInfo').html(loading);
    $('#viewDocenteTipo').html(loading);

    $('#viewTipoPermiso').html(loading);
    $('#viewFechaInicio').html(loading);
    $('#viewFechaFin').html(loading);
    $('#viewDiasSolicitados').html('0');

    $('#viewEstado').html(loading)
        .removeClass()
        .addClass('badge badge-secondary badge-pill px-3 py-2');

    $('#viewEstadoRecuperacion').html(loading)
        .removeClass()
        .addClass('badge badge-secondary badge-pill px-3 py-2');

    $('#viewMotivo').html(loading);

    $.ajax({
        url: `permiso/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function (response) {

            if (!response.success || !response.permiso) {
                mostrarErrorCarga('No se pudo cargar el permiso');
                return;
            }

            const p = response.permiso;

            /* =====================
               DOCENTE
            ====================== */
            // Cambiar avatar según género
            const avatarImg = p.docente.gender === 'female' 
                ? 'plugins/adminlte/dist/img/image.png' 
                : 'plugins/adminlte/dist/img/avatar5.png';
            
            $('.widget-user-image img').attr('src', `/${avatarImg}`);
            
            $('#viewDocenteNombre').html(
                `${p.docente.appDocente} ${p.docente.apmDocente}, ${p.docente.nombres}`
            );

            $('#viewDocenteInfo').html(
                p.docente.area || 'Área no especificada'
            );

            $('#viewDocenteTipo')
                .html(p.docente.tipo_contrato || 'Nombrado')
                .removeClass()
                .addClass('badge badge-primary badge-pill');

            /* =====================
               PERMISO
            ====================== */
            $('#viewTipoPermiso').html(
                p.tipoPermiso.nombre
            );

            $('#viewFechaInicio').html(
                formatearFecha(p.fecha_inicio)
            );

            $('#viewFechaFin').html(
                formatearFecha(p.fecha_fin)
            );

            $('#viewDiasSolicitados').html(
                p.dias_permiso || 0
            );

            /* =====================
               ESTADO PERMISO
            ====================== */
            if (p.estado_permiso === 'APROBADO') {
                $('#viewEstado')
                    .html('✔ Aprobado')
                    .removeClass()
                    .addClass('badge badge-success badge-pill px-3 py-2');
            } else if (p.estado_permiso === 'RECHAZADO') {
                $('#viewEstado')
                    .html('✖ Rechazado')
                    .removeClass()
                    .addClass('badge badge-danger badge-pill px-3 py-2');
            } else {
                $('#viewEstado')
                    .html('⏳ Pendiente')
                    .removeClass()
                    .addClass('badge badge-warning badge-pill px-3 py-2');
            }

            /* =====================
               RECUPERACIÓN
            ====================== */
            // Verificar si el tipo de permiso requiere recuperación
            if (p.tipoPermiso.requiere_recupero) {
                // Si tiene plan de recuperación
                if (p.planRecuperacion) {
                    const estadoPlan = p.planRecuperacion.estado_plan;
                    
                    if (estadoPlan === 'APROBADO') {
                        $('#viewEstadoRecuperacion')
                            .html('<i class="fas fa-check-circle"></i> Completada')
                            .removeClass()
                            .addClass('badge badge-success badge-pill px-3 py-2');
                        
                        $('#btnRegistrarPlan')
                            .html('<i class="fas fa-eye mr-2"></i> Ver Plan de Recuperación')
                            .removeClass('btn-primary')
                            .addClass('btn-success')
                            .show();
                    } else if (estadoPlan === 'PRESENTADO') {
                        $('#viewEstadoRecuperacion')
                            .html('<i class="fas fa-clock"></i> En Proceso')
                            .removeClass()
                            .addClass('badge badge-info badge-pill px-3 py-2');
                        
                        $('#btnRegistrarPlan')
                            .html('<i class="fas fa-eye mr-2"></i> Ver Plan de Recuperación')
                            .removeClass('btn-primary')
                            .addClass('btn-info')
                            .show();
                    } else if (estadoPlan === 'OBSERVADO') {
                        $('#viewEstadoRecuperacion')
                            .html('<i class="fas fa-exclamation-triangle"></i> Observado')
                            .removeClass()
                            .addClass('badge badge-danger badge-pill px-3 py-2');
                        
                        $('#btnRegistrarPlan')
                            .html('<i class="fas fa-edit mr-2"></i> Revisar Plan de Recuperación')
                            .removeClass('btn-primary btn-success')
                            .addClass('btn-warning')
                            .show();
                    }
                } else {
                    // No tiene plan de recuperación
                    $('#viewEstadoRecuperacion')
                        .html('<i class="fas fa-file-alt"></i> Sin Plan')
                        .removeClass()
                        .addClass('badge badge-warning badge-pill px-3 py-2');

                    $('#btnRegistrarPlan')
                        .html('<i class="fas fa-calendar-plus mr-2"></i> Registrar Plan de Recuperación')
                        .removeClass('btn-success btn-info btn-warning')
                        .addClass('btn-primary')
                        .show();
                }
            } else {
                // No requiere recuperación
                $('#viewEstadoRecuperacion')
                    .html('<i class="fas fa-times-circle"></i> No Requiere')
                    .removeClass()
                    .addClass('badge badge-secondary badge-pill px-3 py-2');
                
                $('#btnRegistrarPlan').hide();
            }

            /* =====================
               MOTIVO
            ====================== */
            $('#viewMotivo').html(
                p.motivo || 'No se registró un motivo'
            );
        },

        error: function () {
            mostrarErrorCarga('Error al obtener los datos del permiso');
        }
    });
}

/* =====================
   ERROR
====================== */
function mostrarErrorCarga(mensaje) {
    const error = `<span class="text-danger">${mensaje}</span>`;
    $('#viewDocenteNombre').html(error);
    $('#viewDocenteInfo').html(error);
    $('#viewDocenteTipo').html(error);
    $('#viewTipoPermiso').html(error);
    $('#viewFechaInicio').html(error);
    $('#viewFechaFin').html(error);
    $('#viewDiasSolicitados').html('0');
    $('#viewEstado').html('Error')
        .removeClass()
        .addClass('badge badge-danger badge-pill px-3 py-2');
    $('#viewEstadoRecuperacion').html(error);
    $('#viewMotivo').html(error);
}

/* =====================
   FECHAS
====================== */
function formatearFecha(fecha) {
    if (!fecha) return 'No especificada';

    const f = new Date(fecha);
    const dia = f.getDate();
    const mes = f.toLocaleDateString('es-ES', { month: 'long' });
    const anio = f.getFullYear();

    return `${dia} de ${mes}, ${anio}`;
}

/* =====================
   BOTÓN
====================== */
$('#btnRegistrarPlan').on('click', function () {
    // Aquí enlazas al módulo real
    alert('Abrir módulo de Plan de Recuperación');
});
