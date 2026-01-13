/**
 * Seguimiento de Permiso Docente - JavaScript
 */

/**
 * Contactar con soporte
 */
function contactar() {
    new PNotify({
        title: 'Información de Contacto',
        text: 'Número de teléfono: 620-2333 Opción 3<br><br>Horario de atención: Lunes a Viernes de 8:00 AM a 5:00 PM',
        type: 'info'
    });
}

/**
 * Abrir modal para actualizar estado
 */
function abrirModalActualizarEstado(idPermiso, estadoActual) {
    document.getElementById('permisoIdActualizar').value = idPermiso;
    document.getElementById('nuevoEstado').value = estadoActual;
    document.getElementById('observacion').value = '';
    
    const modal = document.getElementById('modalActualizarEstado');
    modal.style.display = 'flex';
}

/**
 * Cerrar modal de actualización de estado
 */
function cerrarModalActualizarEstado() {
    const modal = document.getElementById('modalActualizarEstado');
    modal.style.display = 'none';
}

/**
 * Actualizar el timeline según el estado del permiso
 */
function actualizarTimeline(estadoPermiso) {
    // Definir los pasos del timeline
    const pasos = [
        { nombre: 'Solicitud enviada', icono: 'fa-file-upload' },
        { nombre: 'Revisión inicial', icono: 'fa-clipboard-check' },
        { nombre: 'Aprobación/Rechazo', icono: 'fa-user-check' },
        { nombre: 'En Recuperación', icono: 'fa-sync-alt' },
        { nombre: 'Completado', icono: 'fa-check-circle' }
    ];

    // Determinar qué paso está activo
    let pasoActivo = 1;
    const esRechazado = estadoPermiso === 'RECHAZADO';
    
    if (estadoPermiso === 'SOLICITADO') {
        pasoActivo = 1;
    } else if (estadoPermiso === 'RECHAZADO') {
        pasoActivo = 3; // Se detiene en Aprobación/Rechazo
    } else if (estadoPermiso === 'APROBADO') {
        pasoActivo = 3;
    } else if (estadoPermiso === 'EN_RECUPERACION') {
        pasoActivo = 4;
    } else if (estadoPermiso === 'RECUPERADO' || estadoPermiso === 'CERRADO') {
        pasoActivo = 5;
    }

    // Generar el HTML del timeline
    let timelineHTML = '';
    pasos.forEach((paso, index) => {
        const numeroPaso = index + 1;
        let claseEstado = '';
        let textoLabel = paso.nombre;
        
        // Si está rechazado, solo los primeros 3 pasos se muestran
        if (esRechazado) {
            if (numeroPaso < 3) {
                claseEstado = 'completed';
            } else if (numeroPaso === 3) {
                claseEstado = 'rejected'; // Clase especial para rechazado
                textoLabel = 'Rechazado'; // Cambiar texto
            }
            // Los pasos 4 y 5 quedan sin clase (grises/inactivos)
        } else {
            // Flujo normal (aprobado)
            if (numeroPaso < pasoActivo) {
                claseEstado = 'completed';
            } else if (numeroPaso === pasoActivo) {
                claseEstado = 'active';
            }
            
            // Si es paso 3 y está aprobado, cambiar texto
            if (numeroPaso === 3 && estadoPermiso === 'APROBADO') {
                textoLabel = 'Aprobado';
            }
        }

        timelineHTML += `
            <div class="timeline-step ${claseEstado}">
                <div class="timeline-icon"><i class="fas ${paso.icono}"></i></div>
                <div class="timeline-label">${textoLabel}</div>
            </div>
        `;
    });

    // Actualizar el contenedor del timeline
    const timelineContainer = document.getElementById('timeline-container');
    if (timelineContainer) {
        timelineContainer.innerHTML = timelineHTML;
    }
}

/**
 * Actualizar la tabla con los datos del permiso
 */
function actualizarTabla(permiso) {
    // Determinar la clase y texto del badge según el estado
    let badgeClass = 'status-pending';
    let estadoTexto = permiso.estado_permiso;
    
    if (permiso.estado_permiso === 'APROBADO') {
        badgeClass = 'status-approved';
        estadoTexto = 'Aprobado';
    } else if (permiso.estado_permiso === 'EN_RECUPERACION') {
        badgeClass = 'status-review';
        estadoTexto = 'En Recuperación';
    } else if (permiso.estado_permiso === 'RECUPERADO') {
        badgeClass = 'status-approved';
        estadoTexto = 'Recuperado';
    } else if (permiso.estado_permiso === 'CERRADO') {
        badgeClass = 'status-approved';
        estadoTexto = 'Cerrado';
    } else if (permiso.estado_permiso === 'SOLICITADO') {
        badgeClass = 'status-pending';
        estadoTexto = 'Solicitado';
    } else if (permiso.estado_permiso === 'RECHAZADO') {
        badgeClass = 'status-review';
        estadoTexto = 'Rechazado';
    }

    // Generar el HTML de la tabla
    const tableHTML = `
        <tr>
            <td><strong>${permiso.id_permiso}</strong></td>
            <td>${permiso.tipo_permiso}</td>
            <td>${permiso.fecha_inicio}</td>
            <td>${permiso.fecha_fin}</td>
            <td>${permiso.dias_permiso} días</td>
            <td>
                <span class="status-badge ${badgeClass}">${estadoTexto}</span>
            </td>
            <td>
                <button class="action-button" onclick="verDetallePermiso('${permiso.id_permiso}')">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-button" onclick="abrirModalActualizarEstado('${permiso.id_permiso}', '${permiso.estado_permiso}')" style="background-color: var(--warning-color); margin-left: 10px;">
                    <i class="fas fa-edit"></i>
                </button>
            </td>
        </tr>
    `;

    // Actualizar el cuerpo de la tabla
    const tableBody = document.getElementById('table-body');
    if (tableBody) {
        tableBody.innerHTML = tableHTML;
    }

    // Actualizar la última actualización
    const lastUpdate = document.getElementById('last-update');
    if (lastUpdate) {
        lastUpdate.innerHTML = `<i class="fas fa-clock"></i> Última actualización: ${permiso.updated_at}`;
    }
}

/**
 * Manejar el cambio de selección de permiso
 */
function handlePermisoChange(idPermiso) {
    if (!idPermiso) {
        return;
    }

    // Realizar la petición AJAX para obtener los datos del permiso
    fetch(`/docente/seguimiento_permiso/get/${idPermiso}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar el timeline
            actualizarTimeline(data.permiso.estado_permiso);
            
            // Actualizar la tabla
            actualizarTabla(data.permiso);
        } else {
            console.error('Error:', data.message);
            new PNotify({
                title: 'Error',
                text: 'Error al cargar el permiso: ' + data.message,
                type: 'error'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        new PNotify({
            title: 'Error',
            text: 'Error al cargar el permiso. Por favor intente nuevamente.',
            type: 'error'
        });
    });
}

/**
 * Manejar el envío del formulario de actualización
 */
document.addEventListener('DOMContentLoaded', function() {
    // Listener para el cambio de selección de permiso - COMPATIBLE CON SELECT2
    // Select2 requiere jQuery para los eventos
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#permiso_id').on('change', function() {
            const selectedValue = $(this).val();
            if (selectedValue) {
                handlePermisoChange(selectedValue);
            }
        });
    } else {
        // Fallback para si no hay jQuery (aunque Select2 lo requiere)
        const permisoSelect = document.getElementById('permiso_id');
        if (permisoSelect) {
            permisoSelect.addEventListener('change', function() {
                if (this.value) {
                    handlePermisoChange(this.value);
                }
            });
        }
    }

    // Listener para el formulario de actualización de estado
    const form = document.getElementById('formActualizarEstado');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const idPermiso = document.getElementById('permisoIdActualizar').value;
            const nuevoEstado = document.getElementById('nuevoEstado').value;
            const observacion = document.getElementById('observacion').value;
            
            if (!nuevoEstado) {
                new PNotify({
                    title: 'Advertencia',
                    text: 'Por favor seleccione un estado',
                    type: 'warning'
                });
                return;
            }
            
            // Obtener el token CSRF
            const csrfToken = document.querySelector('input[name="_token"]').value;
            
            // Realizar la petición AJAX
            fetch(`/docente/seguimiento_permiso/update/${idPermiso}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    estado_permiso: nuevoEstado,
                    observacion: observacion,
                    fecha_resolucion: new Date().toISOString().split('T')[0]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    new PNotify({
                        title: 'Éxito',
                        text: 'Estado actualizado correctamente',
                        type: 'success'
                    });
                    cerrarModalActualizarEstado();
                    // Recargar los datos del permiso actual
                    const permisoSelect = document.getElementById('permiso_id');
                    if (permisoSelect && permisoSelect.value) {
                        handlePermisoChange(permisoSelect.value);
                    }
                } else {
                    new PNotify({
                        title: 'Error',
                        text: 'Error: ' + (data.message || 'No se pudo actualizar el estado'),
                        type: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                new PNotify({
                    title: 'Error',
                    text: 'Error al actualizar el estado. Por favor intente nuevamente.',
                    type: 'error'
                });
            });
        });
    }
});
