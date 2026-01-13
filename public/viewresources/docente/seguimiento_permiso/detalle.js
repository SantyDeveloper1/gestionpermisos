/**
 * Detalle de Permiso - JavaScript
 * Maneja la visualización del modal de detalle del permiso
 */

/**
 * Ver detalles del permiso
 */
function verDetallePermiso(idPermiso) {
    // Realizar petición AJAX para obtener los detalles
    fetch(`/docente/permiso/${idPermiso}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarModalDetalle(data.permiso);
        } else {
            new PNotify({
                title: 'Error',
                text: 'Error al cargar el detalle del permiso: ' + (data.message || 'Error desconocido'),
                type: 'error'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        new PNotify({
            title: 'Error',
            text: 'Error al cargar el detalle del permiso. Por favor intente nuevamente.',
            type: 'error'
        });
    });
}

/**
 * Mostrar modal con los detalles del permiso
 */
function mostrarModalDetalle(permiso) {
    // Función auxiliar para formatear fechas
    const formatearFecha = (fecha) => {
        if (!fecha) return 'No disponible';
        const date = new Date(fecha);
        return date.toLocaleDateString('es-PE', { year: 'numeric', month: '2-digit', day: '2-digit' });
    };

    // Función auxiliar para obtener badge de estado
    const obtenerBadgeEstado = (estado) => {
        let badgeClass = 'status-pending';
        let estadoTexto = estado;
        
        if (estado === 'APROBADO') {
            badgeClass = 'status-approved';
            estadoTexto = 'Aprobado';
        } else if (estado === 'EN_RECUPERACION') {
            badgeClass = 'status-review';
            estadoTexto = 'En Recuperación';
        } else if (estado === 'RECUPERADO') {
            badgeClass = 'status-approved';
            estadoTexto = 'Recuperado';
        } else if (estado === 'CERRADO') {
            badgeClass = 'status-approved';
            estadoTexto = 'Cerrado';
        } else if (estado === 'SOLICITADO') {
            badgeClass = 'status-pending';
            estadoTexto = 'Solicitado';
        } else if (estado === 'RECHAZADO') {
            badgeClass = 'status-review';
            estadoTexto = 'Rechazado';
        }
        
        return `<span class="status-badge ${badgeClass}">${estadoTexto}</span>`;
    };

    // Información Básica
    document.getElementById('detalle_estado').innerHTML = obtenerBadgeEstado(permiso.estado_permiso);
    document.getElementById('detalle_dias').textContent = permiso.dias_permiso ? `${permiso.dias_permiso} día(s)` : '-';
    document.getElementById('detalle_horas').textContent = permiso.horas_afectadas ? `${permiso.horas_afectadas} hora(s)` : '-';

    // Fechas
    document.getElementById('detalle_fecha_solicitud').textContent = formatearFecha(permiso.fecha_solicitud);
    document.getElementById('detalle_fecha_inicio').textContent = formatearFecha(permiso.fecha_inicio);
    document.getElementById('detalle_fecha_fin').textContent = formatearFecha(permiso.fecha_fin);
    document.getElementById('detalle_fecha_resolucion').textContent = formatearFecha(permiso.fecha_resolucion);

    // Información del Docente
    if (permiso.docente) {
        document.getElementById('detalle_docente_nombres').textContent = permiso.docente.nombres || '-';
        document.getElementById('detalle_docente_apellidos').textContent = 
            `${permiso.docente.appDocente || ''} ${permiso.docente.apmDocente || ''}`.trim() || '-';
        document.getElementById('detalle_docente_documento').textContent = permiso.docente.numero_documento || '-';
    } else {
        document.getElementById('detalle_docente_nombres').textContent = '-';
        document.getElementById('detalle_docente_apellidos').textContent = '-';
        document.getElementById('detalle_docente_documento').textContent = '-';
    }

    // Tipo de Permiso
    if (permiso.tipoPermiso) {
        document.getElementById('detalle_tipo_nombre').textContent = permiso.tipoPermiso.nombre || '-';
        document.getElementById('detalle_tipo_goce').textContent = permiso.tipoPermiso.con_goce_haber ? '✅ Sí' : '❌ No';
        document.getElementById('detalle_tipo_recupero').textContent = permiso.tipoPermiso.requiere_recupero ? '✅ Sí' : '❌ No';
        document.getElementById('detalle_tipo_descripcion').textContent = permiso.tipoPermiso.descripcion || 'Sin descripción';
    } else {
        document.getElementById('detalle_tipo_nombre').textContent = '-';
        document.getElementById('detalle_tipo_goce').textContent = '-';
        document.getElementById('detalle_tipo_recupero').textContent = '-';
        document.getElementById('detalle_tipo_descripcion').textContent = '-';
    }

    // Motivo y Observación
    document.getElementById('detalle_motivo').textContent = permiso.motivo || 'Sin motivo especificado';
    document.getElementById('detalle_observacion').textContent = permiso.observacion || 'Sin observaciones';

    // Plan de Recuperación
    const planContainer = document.getElementById('detalle_plan_container');
    if (permiso.planRecuperacion) {
        planContainer.style.display = 'block';
        
        // Estado del plan con badge
        const estadoPlan = permiso.planRecuperacion.estado_plan || '-';
        let planBadge = `<span class="status-badge status-pending">${estadoPlan}</span>`;
        if (estadoPlan === 'APROBADO') {
            planBadge = `<span class="status-badge status-approved">Aprobado</span>`;
        } else if (estadoPlan === 'RECHAZADO') {
            planBadge = `<span class="status-badge status-review">Rechazado</span>`;
        } else if (estadoPlan === 'PENDIENTE') {
            planBadge = `<span class="status-badge status-pending">Pendiente</span>`;
        }
        document.getElementById('detalle_plan_estado').innerHTML = planBadge;
        
        document.getElementById('detalle_plan_fecha').textContent = formatearFecha(permiso.planRecuperacion.fecha_presentacion);
        document.getElementById('detalle_plan_horas').textContent = permiso.planRecuperacion.total_horas_recuperar ? 
            `${permiso.planRecuperacion.total_horas_recuperar} hora(s)` : '-';
        document.getElementById('detalle_plan_observacion').textContent = permiso.planRecuperacion.observacion || 'Sin observaciones';
    } else {
        planContainer.style.display = 'none';
    }

    // Mostrar el modal
    const modal = document.getElementById('modalDetallePermiso');
    modal.style.display = 'flex';
}

/**
 * Cerrar modal de detalle
 */
function cerrarModalDetalle() {
    const modal = document.getElementById('modalDetallePermiso');
    modal.style.display = 'none';
}