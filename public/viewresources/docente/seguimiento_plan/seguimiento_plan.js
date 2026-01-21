// ========================================
// SEGUIMIENTO DE PLAN DE RECUPERACIÓN
// ========================================

let planesData = {};
let docenteData = {};

// Cargar planes al iniciar la página
$(document).ready(function() {
    cargarPlanes();
});

/**
 * Cargar todos los planes del docente
 */
function cargarPlanes() {
    $.ajax({
        url: _urlBase + '/docente/seguimiento_plan/planes',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            
            if (response.success) {
                planesData = {};
                docenteData = response.docente;
                
                // Actualizar información del docente
                if (docenteData) {
                    $('.docente-name').text(docenteData.nombre);
                    $('.docente-id').text('ID: ' + docenteData.id + ' | Departamento: ' + docenteData.departamento);
                }
                
                // Limpiar selector
                const $planSelect = $('#planSelect');
                $planSelect.empty();
                $planSelect.append('<option value="" disabled selected>-- Seleccione un plan --</option>');
                
                if (response.planes && response.planes.length > 0) {
                    // Agregar planes al selector
                    response.planes.forEach(function(plan, index) {
                        const planKey = 'plan' + (index + 1);
                        planesData[planKey] = plan;
                        
                        const estadoBadge = getEstadoBadge(plan.estado_plan);
                        $planSelect.append(
                            `<option value="${planKey}">${plan.nombre} (${plan.estado_plan})</option>`
                        );
                    });
                    
                    // Cargar el primer plan por defecto
                    if (Object.keys(planesData).length > 0) {
                        const primerPlan = Object.keys(planesData)[0];
                        $planSelect.val(primerPlan);
                        cargarPlan(primerPlan);
                    }
                } else {
                    mostrarMensajeNoPlanes();
                }
            } else {
                new PNotify({
                    title: 'Error',
                    text: response.message || 'No se pudieron cargar los planes',
                    type: 'error'
                });
                mostrarMensajeNoPlanes();
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Error al conectar con el servidor';
            
            if (xhr.status === 404) {
                errorMessage = 'Ruta no encontrada (404). Verifique que la ruta esté correctamente configurada.';
            } else if (xhr.status === 500) {
                errorMessage = 'Error interno del servidor (500). Revise los logs del servidor.';
            } else if (xhr.status === 403) {
                errorMessage = 'Acceso denegado (403). No tiene permisos para acceder a este recurso.';
            } else if (xhr.status === 401) {
                errorMessage = 'No autenticado (401). Por favor, inicie sesión nuevamente.';
            } else if (xhr.status === 0) {
                errorMessage = 'No se pudo conectar con el servidor. Verifique su conexión a internet.';
            }
            
            new PNotify({
                title: 'Error',
                text: errorMessage,
                type: 'error',
                delay: 5000
            });
            
            mostrarMensajeNoPlanes();
        }
    });
}

/**
 * Cargar un plan específico
 */
function cargarPlan(planKey) {
    const plan = planesData[planKey];
    
    if (!plan) {
        return;
    }
    
    // Generar HTML del plan
    const planHTML = generarHTMLPlan(plan);
    
    // Actualizar contenido
    $('#planContent').html(planHTML);
}

/**
 * Generar HTML completo del plan
 */
function generarHTMLPlan(plan) {
    const estadoPlanBadge = getEstadoPlanBadge(plan.estado_plan);
    const porcentajeCompletado = plan.total_sesiones > 0 
        ? Math.round((plan.sesiones_realizadas / plan.total_sesiones) * 100) 
        : 0;
    
    return `
        <!-- Estado del Plan de Recuperación -->
        <div class="status-grid">
            <!-- Card: Estado del Plan -->
            <div class="status-card">
                <div class="card-header">
                    <div class="card-icon" style="background-color: ${getColorEstadoPlan(plan.estado_plan)};">
                        <i class="fas ${getIconoEstadoPlan(plan.estado_plan)}"></i>
                    </div>
                    <h2 class="card-title">Estado del Plan</h2>
                </div>

                <div class="plan-info">
                    <div class="info-row">
                        <span class="info-label">Tipo de Permiso:</span>
                        <span class="info-value">${plan.permiso.tipo}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha presentación:</span>
                        <span class="info-value">${plan.fecha_presentacion}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total horas a recuperar:</span>
                        <span class="info-value">${plan.total_horas_recuperar} horas</span>
                    </div>
                    ${plan.observacion ? `
                    <div class="info-row">
                        <span class="info-label">Observación:</span>
                        <span class="info-value">${plan.observacion}</span>
                    </div>
                    ` : ''}
                </div>

                <ul class="status-list">
                    <li class="status-item ${plan.estado_plan === 'APROBADO' ? 'active' : ''}">
                        <div class="status-indicator ind-aprobado"></div>
                        <div class="status-text">APROBADO</div>
                        <div class="status-count">${plan.estado_plan === 'APROBADO' ? '1' : '0'}</div>
                    </li>
                    <li class="status-item ${plan.estado_plan === 'PRESENTADO' ? 'active' : ''}">
                        <div class="status-indicator ind-presentado"></div>
                        <div class="status-text">PRESENTADO</div>
                        <div class="status-count">${plan.estado_plan === 'PRESENTADO' ? '1' : '0'}</div>
                    </li>
                    <li class="status-item ${plan.estado_plan === 'OBSERVADO' ? 'active' : ''}">
                        <div class="status-indicator ind-observado"></div>
                        <div class="status-text">OBSERVADO</div>
                        <div class="status-count">${plan.estado_plan === 'OBSERVADO' ? '1' : '0'}</div>
                    </li>
                </ul>
            </div>

            <!-- Card: Estado de Sesiones -->
            <div class="status-card">
                <div class="card-header">
                    <div class="card-icon" style="background-color: var(--secondary-color);">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h2 class="card-title">Estado de Sesiones</h2>
                </div>
                <ul class="status-list">
                    <li class="status-item ${plan.sesiones_programadas > 0 ? 'active' : ''}">
                        <div class="status-indicator ind-programada"></div>
                        <div class="status-text">PROGRAMADA</div>
                        <div class="status-count">${plan.sesiones_programadas}</div>
                    </li>
                    <li class="status-item ${plan.sesiones_realizadas > 0 ? 'active' : ''}">
                        <div class="status-indicator ind-realizada"></div>
                        <div class="status-text">REALIZADA</div>
                        <div class="status-count">${plan.sesiones_realizadas}</div>
                    </li>
                    <li class="status-item ${plan.sesiones_reprogramadas > 0 ? 'active' : ''}">
                        <div class="status-indicator ind-reprogramada"></div>
                        <div class="status-text">REPROGRAMADA</div>
                        <div class="status-count">${plan.sesiones_reprogramadas}</div>
                    </li>
                    <li class="status-item ${plan.sesiones_canceladas > 0 ? 'active' : ''}">
                        <div class="status-indicator ind-cancelada"></div>
                        <div class="status-text">CANCELADA</div>
                        <div class="status-count">${plan.sesiones_canceladas}</div>
                    </li>
                </ul>
            </div>

            <!-- Card: Evidencias por Tipo -->
            <div class="status-card">
                <div class="card-header">
                    <div class="card-icon" style="background-color: var(--info-color);">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <h2 class="card-title">Evidencias por Tipo</h2>
                </div>
                <ul class="status-list">
                    <li class="status-item ${plan.evidencias_acta > 0 ? 'active' : ''}">
                        <div class="status-text">ACTA</div>
                        <div class="status-count tipo-acta">${plan.evidencias_acta}</div>
                    </li>
                    <li class="status-item ${plan.evidencias_asistencia > 0 ? 'active' : ''}">
                        <div class="status-text">ASISTENCIA</div>
                        <div class="status-count tipo-asistencia">${plan.evidencias_asistencia}</div>
                    </li>
                    <li class="status-item ${plan.evidencias_captura > 0 ? 'active' : ''}">
                        <div class="status-text">CAPTURA</div>
                        <div class="status-count tipo-captura">${plan.evidencias_captura}</div>
                    </li>
                    <li class="status-item ${plan.evidencias_otro > 0 ? 'active' : ''}">
                        <div class="status-text">OTRO</div>
                        <div class="status-count tipo-otro">${plan.evidencias_otro}</div>
                    </li>
                </ul>
                <div class="plan-info" style="margin-top: 20px;">
                    <div class="info-row">
                        <span class="info-label">Total evidencias:</span>
                        <span class="info-value">${plan.total_evidencias} documentos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Sesiones con Evidencias -->
        <div class="sessions-section">
            <h2 class="section-title">Sesiones del Plan</h2>
            <div class="session-list">
                ${generarHTMLSesiones(plan.sesiones)}
            </div>
        </div>

        <!-- Resumen General -->
        <div class="summary-section">
            <h2 class="section-title">Resumen General del Plan</h2>
            <div class="summary-grid">
                <div class="summary-card" style="background: linear-gradient(135deg, #dfe6e9, #b2bec3);">
                    <div class="summary-label">Estado del Plan</div>
                    <div class="summary-number">${plan.estado_plan}</div>
                    <div class="summary-desc">${getDescripcionEstado(plan.estado_plan)}</div>
                </div>

                <div class="summary-card" style="background: linear-gradient(135deg, #d5f4e6, #a8e6cf);">
                    <div class="summary-label">Sesiones Realizadas</div>
                    <div class="summary-number">${plan.sesiones_realizadas}/${plan.total_sesiones}</div>
                    <div class="summary-desc">${porcentajeCompletado}% completado</div>
                </div>

                <div class="summary-card" style="background: linear-gradient(135deg, #e8daef, #d7bde2);">
                    <div class="summary-label">Evidencias Cargadas</div>
                    <div class="summary-number">${plan.total_evidencias}</div>
                    <div class="summary-desc">En ${plan.sesiones.filter(s => s.tiene_evidencia).length} de ${plan.total_sesiones} sesiones</div>
                </div>

                <div class="summary-card" style="background: linear-gradient(135deg, #fef5e7, #fdebd0);">
                    <div class="summary-label">Próxima Sesión</div>
                    <div class="summary-number">${plan.proxima_sesion ? plan.proxima_sesion.fecha : 'N/A'}</div>
                    <div class="summary-desc">${plan.proxima_sesion ? plan.proxima_sesion.tema : 'No hay sesiones programadas'}</div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Generar HTML de las sesiones
 */
function generarHTMLSesiones(sesiones) {
    if (!sesiones || sesiones.length === 0) {
        return '<div class="no-data"><i class="fas fa-calendar-times"></i><h3>No hay sesiones registradas</h3></div>';
    }
    
    return sesiones.map(sesion => {
        const colorBorde = getColorEstadoSesion(sesion.estado_sesion);
        const badgeEstado = getBadgeEstadoSesion(sesion.estado_sesion);
        const evidenciasHTML = generarHTMLEvidencias(sesion.evidencias);
        
        // Manejar valores null
        const asignatura = sesion.asignatura || 'Sin asignatura';
        const tema = sesion.tema || 'Sin tema especificado';
        const aula = sesion.aula || 'Por definir';
        const horaInicio = sesion.hora_inicio || '00:00';
        const horaFin = sesion.hora_fin || '00:00';
        const horasRecuperadas = sesion.horas_recuperadas || 0;
        
        return `
            <div class="session-item" style="border-left-color: ${colorBorde};">
                <div class="session-date">${sesion.fecha_sesion}</div>
                <div class="session-status ${badgeEstado}">${sesion.estado_sesion}</div>
                <div class="session-desc">
                    <strong>${asignatura}</strong><br>
                    ${tema}<br>
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> ${horaInicio} - ${horaFin} | 
                        <i class="fas fa-door-open"></i> ${aula} | 
                        <i class="fas fa-hourglass-half"></i> ${horasRecuperadas} hrs
                    </small>
                </div>
                <div class="evidence-indicator">
                    ${evidenciasHTML}
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Generar HTML de evidencias
 */
function generarHTMLEvidencias(evidencias) {
    if (!evidencias || evidencias.length === 0) {
        return `
            <span class="evidence-badge sin-evidencia">SIN EVIDENCIA</span>
            <i class="fas fa-exclamation-circle" style="color: #e74c3c;" title="Falta evidencia"></i>
        `;
    }
    
    const badges = evidencias.map(ev => {
        const tipoClass = getTipoEvidenciaClass(ev.tipo);
        return `<span class="evidence-badge ${tipoClass}">${ev.tipo}</span>`;
    }).join('');
    
    return `
        ${badges}
        <i class="fas fa-check-circle" style="color: #27ae60;" title="Evidencia completa"></i>
    `;
}

/**
 * Mostrar mensaje cuando no hay planes
 */
function mostrarMensajeNoPlanes() {
    $('#planContent').html(`
        <div class="no-data">
            <i class="fas fa-clipboard-list"></i>
            <h3>No hay planes de recuperación disponibles</h3>
            <p>No se encontraron planes de recuperación asociados a sus permisos aprobados</p>
        </div>
    `);
}

// ========================================
// FUNCIONES AUXILIARES
// ========================================

function getEstadoBadge(estado) {
    const badges = {
        'APROBADO': 'badge-success',
        'PRESENTADO': 'badge-warning',
        'OBSERVADO': 'badge-danger'
    };
    return badges[estado] || 'badge-secondary';
}

function getEstadoPlanBadge(estado) {
    const badges = {
        'APROBADO': 'estado-aprobado',
        'PRESENTADO': 'estado-presentado',
        'OBSERVADO': 'estado-observado'
    };
    return badges[estado] || '';
}

function getColorEstadoPlan(estado) {
    const colores = {
        'APROBADO': '#27ae60',
        'PRESENTADO': '#f39c12',
        'OBSERVADO': '#e74c3c'
    };
    return colores[estado] || '#95a5a6';
}

function getIconoEstadoPlan(estado) {
    const iconos = {
        'APROBADO': 'fa-check-circle',
        'PRESENTADO': 'fa-clock',
        'OBSERVADO': 'fa-exclamation-triangle'
    };
    return iconos[estado] || 'fa-question-circle';
}

function getDescripcionEstado(estado) {
    const descripciones = {
        'APROBADO': 'Listo para ejecución',
        'PRESENTADO': 'En revisión',
        'OBSERVADO': 'Requiere correcciones'
    };
    return descripciones[estado] || 'Estado desconocido';
}

function getColorEstadoSesion(estado) {
    const colores = {
        'REALIZADA': '#27ae60',
        'PROGRAMADA': '#3498db',
        'REPROGRAMADA': '#9b59b6',
        'CANCELADA': '#7f8c8d'
    };
    return colores[estado] || '#ddd';
}

function getBadgeEstadoSesion(estado) {
    const badges = {
        'REALIZADA': 'estado-realizada',
        'PROGRAMADA': 'estado-programada',
        'REPROGRAMADA': 'estado-reprogramada',
        'CANCELADA': 'estado-cancelada'
    };
    return badges[estado] || '';
}

function getTipoEvidenciaClass(tipo) {
    const clases = {
        'ACTA': 'tipo-acta',
        'ASISTENCIA': 'tipo-asistencia',
        'CAPTURA': 'tipo-captura',
        'OTRO': 'tipo-otro'
    };
    return clases[tipo] || 'tipo-otro';
}

// ========================================
// EVENT LISTENERS
// ========================================

// Cambio de plan en el selector
$(document).on('change', '#planSelect', function() {
    const planKey = $(this).val();
    if (planKey && planesData[planKey]) {
        cargarPlan(planKey);
    }
});
