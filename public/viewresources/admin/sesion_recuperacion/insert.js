// Variables globales para el wizard
let currentStep = 1;
let planData = null;
let sessionData = {};

// Función para cargar detalles del plan seleccionado
function cargarDetallesPlan() {
    // Primero verificar si hay campos ocultos (plan pre-seleccionado)
    const hiddenPlanId = document.getElementById('hiddenPlanId');
    
    if (hiddenPlanId && hiddenPlanId.value) {
        // Cargar datos desde campos ocultos
        planData = {
            horasTotales: parseFloat(document.getElementById('hiddenHorasTotales').value),
            horasRealizadas: parseFloat(document.getElementById('hiddenHorasRealizadas').value),
            horasProgramadas: parseFloat(document.getElementById('hiddenHorasProgramadas').value),
            horasRecuperadas: parseFloat(document.getElementById('hiddenHorasRecuperadas').value),
            horasPendientes: parseFloat(document.getElementById('hiddenHorasPendientes').value),
            docente: document.getElementById('hiddenDocente').value
        };
    } else {
        // Cargar datos desde el select normal
        const select = document.getElementById('selectPlanRecuperacion');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            planData = {
                horasTotales: parseFloat(selectedOption.getAttribute('data-horas-totales')),
                horasRealizadas: parseFloat(selectedOption.getAttribute('data-horas-realizadas')),
                horasProgramadas: parseFloat(selectedOption.getAttribute('data-horas-programadas')),
                horasRecuperadas: parseFloat(selectedOption.getAttribute('data-horas-recuperadas')),
                horasPendientes: parseFloat(selectedOption.getAttribute('data-horas-pendientes')),
                docente: selectedOption.getAttribute('data-docente')
            };
        } else {
            document.getElementById('planSummary').style.display = 'none';
            planData = null;
            return;
        }
    }
    
    // Actualizar display
    document.getElementById('horasTotales').textContent = planData.horasTotales;
    document.getElementById('horasRecuperadas').textContent = planData.horasRecuperadas;
    document.getElementById('horasPendientes').textContent = planData.horasPendientes;
    
    // Mostrar resumen
    document.getElementById('planSummary').style.display = 'block';
    
    // Validar si hay horas pendientes
    if (planData.horasPendientes <= 0) {
        let mensaje = 'Este plan ya ha completado todas las horas de recuperación';
        
        // Si hay horas programadas, sugerir cambiar el estado
        if (planData.horasProgramadas > 0) {
            mensaje += `\n\nHay ${planData.horasProgramadas} horas en sesiones PROGRAMADAS.\nCambie el estado de esas sesiones a REALIZADA o VALIDADA para poder registrar nuevas sesiones.`;
        }
        
        new PNotify({
            title: 'Advertencia',
            text: mensaje,
            type: 'warning',
            styling: 'bootstrap3',
            delay: 5000
        });
    }
}

// Función para validar horas ingresadas
function validarHoras() {
    const horasInput = document.querySelector('input[name="horas_recuperadas"]');
    const horas = parseFloat(horasInput.value) || 0;
    
    if (horas < 0.5 || horas > 8) {
        horasInput.classList.add('is-invalid');
        horasInput.classList.remove('is-valid');
    } else {
        horasInput.classList.remove('is-invalid');
        horasInput.classList.add('is-valid');
    }
    
    // Si hay plan seleccionado, validar acumulación
    if (planData && horas > 0) {
        const nuevoTotal = planData.horasRecuperadas + horas;
        const excede = nuevoTotal > planData.horasTotales;
        
        if (excede) {
            mostrarAlertaValidacion(`Las horas ingresadas (${horas}) exceden las horas pendientes (${planData.horasPendientes})`);
        } else {
            ocultarAlertaValidacion();
        }
    }
}

function mostrarAlertaValidacion(mensaje) {
    const alertDiv = document.getElementById('validationAlert');
    const alertMessage = document.getElementById('alertMessage');
    
    if (alertDiv && alertMessage) {
        alertMessage.textContent = mensaje;
        alertDiv.style.display = 'block';
    }
}

function ocultarAlertaValidacion() {
    const alertDiv = document.getElementById('validationAlert');
    if (alertDiv) {
        alertDiv.style.display = 'none';
    }
}

// Sistema de wizard (pasos)
function nextStep() {
    // Validar paso actual
    if (!validarPaso(currentStep)) {
        return;
    }
    
    // Ocultar paso actual
    document.getElementById(`step${currentStep}-content`).classList.remove('active');
    document.getElementById(`step${currentStep}-content`).style.display = 'none';
    document.getElementById(`step${currentStep}`).classList.remove('active');
    
    // Mostrar siguiente paso
    currentStep++;
    document.getElementById(`step${currentStep}-content`).classList.add('active');
    document.getElementById(`step${currentStep}-content`).style.display = 'block';
    document.getElementById(`step${currentStep}`).classList.add('active');
    
    // Actualizar botones
    actualizarBotones();
    
    // Si es paso 3, realizar validación
    if (currentStep === 3) {
        realizarValidacion();
    }
    
    // Si es paso 4, cargar confirmación
    if (currentStep === 4) {
        cargarConfirmacion();
    }
}

function prevStep() {
    // Ocultar paso actual
    document.getElementById(`step${currentStep}-content`).classList.remove('active');
    document.getElementById(`step${currentStep}-content`).style.display = 'none';
    document.getElementById(`step${currentStep}`).classList.remove('active');
    
    // Mostrar paso anterior
    currentStep--;
    document.getElementById(`step${currentStep}-content`).classList.add('active');
    document.getElementById(`step${currentStep}-content`).style.display = 'block';
    document.getElementById(`step${currentStep}`).classList.add('active');
    
    // Actualizar botones
    actualizarBotones();
}

function validarPaso(paso) {
    switch(paso) {
        case 1:
            if (!planData) {
                new PNotify({
                    title: 'Error',
                    text: 'Debe seleccionar un plan de recuperación',
                    type: 'error'
                });
                return false;
            }
            if (planData.horasPendientes <= 0) {
                new PNotify({
                    title: 'Advertencia',
                    text: 'Este plan ya ha completado todas las horas',
                    type: 'warning'
                });
                return false;
            }
            return true;
            
        case 2:
            // Validar que se haya ingresado horas
            const horasInput = document.querySelector('input[name="horas_recuperadas"]');
            if (!horasInput || !horasInput.value) {
                new PNotify({
                    title: 'Error',
                    text: 'Debe ingresar las horas a recuperar',
                    type: 'error'
                });
                return false;
            }
            
            const horas = parseFloat(horasInput.value);
            if (isNaN(horas) || horas < 0.5 || horas > 8) {
                new PNotify({
                    title: 'Error',
                    text: 'Las horas deben estar entre 0.5 y 8',
                    type: 'error'
                });
                return false;
            }
            
            // Validar asignatura
            const asignaturaInput = document.querySelector('input[name="asignatura"]');
            if (!asignaturaInput || !asignaturaInput.value || asignaturaInput.value.trim() === '') {
                new PNotify({
                    title: 'Error',
                    text: 'Debe ingresar la asignatura',
                    type: 'error'
                });
                return false;
            }
            
            // Validar semestre
            const semestreSelect = document.querySelector('select[name="semestre"]');
            if (!semestreSelect || !semestreSelect.value) {
                new PNotify({
                    title: 'Error',
                    text: 'Debe seleccionar el semestre',
                    type: 'error'
                });
                return false;
            }
            
            // Validar tipo de sesión
            const tipoSesionSelect = document.querySelector('select[name="tipo_sesion"]');
            if (!tipoSesionSelect || !tipoSesionSelect.value) {
                new PNotify({
                    title: 'Error',
                    text: 'Debe seleccionar el tipo de sesión',
                    type: 'error'
                });
                return false;
            }
            
            // Validar acumulación
            if (planData) {
                const nuevoTotal = planData.horasRecuperadas + horas;
                if (nuevoTotal > planData.horasTotales) {
                    new PNotify({
                        title: 'Error',
                        text: `Las horas exceden el total del plan. Máximo permitido: ${planData.horasPendientes} horas`,
                        type: 'error'
                    });
                    return false;
                }
            }
            
            // Guardar datos de sesión
            sessionData.horas = horas;
            sessionData.asignatura = asignaturaInput.value.trim();
            sessionData.semestre = semestreSelect.value;
            sessionData.semestreTexto = semestreSelect.options[semestreSelect.selectedIndex].text;
            sessionData.aula = document.querySelector('input[name="aula"]').value.trim() || 'No especificada';
            sessionData.tipoSesion = tipoSesionSelect.value;
            sessionData.tipoSesionTexto = tipoSesionSelect.options[tipoSesionSelect.selectedIndex].text;
            sessionData.modalidad = document.querySelector('select[name="modalidad"]').value;
            sessionData.modalidadTexto = document.querySelector('select[name="modalidad"]').options[document.querySelector('select[name="modalidad"]').selectedIndex].text;
            sessionData.fecha = document.querySelector('input[name="fecha_sesion"]').value;
            
            return true;
            
        default:
            return true;
    }
}

function actualizarBotones() {
    const btnPrev = document.getElementById('btnPrevStep');
    const btnNext = document.getElementById('btnNextStep');
    const btnSubmit = document.getElementById('btnSubmit');
    
    // Mostrar/ocultar botón anterior
    btnPrev.style.display = currentStep > 1 ? 'inline-flex' : 'none';
    
    // Cambiar texto del botón siguiente a "Finalizar" en último paso
    if (currentStep === 4) {
        btnNext.style.display = 'none';
        btnSubmit.style.display = 'inline-flex';
    } else {
        btnNext.style.display = 'inline-flex';
        btnSubmit.style.display = 'none';
    }
    
    // Cambiar ícono del botón anterior en paso 1
    if (currentStep === 1) {
        btnPrev.innerHTML = '<i class="fas fa-times mr-2"></i>Cancelar';
        btnPrev.onclick = function() {
            $('#nuevaSesionModal').modal('hide');
        };
    } else {
        btnPrev.innerHTML = '<i class="fas fa-arrow-left mr-2"></i>Anterior';
        btnPrev.onclick = prevStep;
    }
}

function realizarValidacion() {
    const horasSesion = sessionData.horas || 0;
    const horasRecuperadas = planData.horasRecuperadas || 0;
    const nuevoTotal = horasRecuperadas + horasSesion;
    
    // Actualizar displays
    document.getElementById('horasSesion').textContent = horasSesion;
    document.getElementById('nuevoTotal').textContent = nuevoTotal;
    
    // Mostrar validación si excede
    if (nuevoTotal > planData.horasTotales) {
        mostrarAlertaValidacion(`Las horas ingresadas exceden el total del plan en ${(nuevoTotal - planData.horasTotales).toFixed(1)} horas`);
    } else {
        ocultarAlertaValidacion();
    }
}

function cargarConfirmacion() {
    // Obtener datos del formulario
    const docente = planData.docente;
    const plan = document.getElementById('selectPlanRecuperacion').selectedOptions[0]?.text || '';
    const asignatura = sessionData.asignatura;
    const semestre = sessionData.semestreTexto;
    const aula = sessionData.aula;
    const tipoSesion = sessionData.tipoSesionTexto;
    const modalidad = sessionData.modalidadTexto;
    const horas = sessionData.horas;
    const fecha = sessionData.fecha;
    const estado = document.querySelector('select[name="estado_sesion"]').value;
    
    // Actualizar confirmación
    document.getElementById('confirmDocente').textContent = docente;
    document.getElementById('confirmPlan').textContent = plan.split(' - ')[0];
    document.getElementById('confirmCurso').innerHTML = `
        <strong>${asignatura}</strong><br>
        <small style="color: var(--medium-gray);">Semestre: ${semestre} | Aula: ${aula}</small>
    `;
    document.getElementById('confirmHoras').innerHTML = `
        ${horas} horas<br>
        <small style="color: var(--medium-gray);">${tipoSesion} - ${modalidad}</small>
    `;
    document.getElementById('confirmFecha').textContent = formatFecha(fecha);
    document.getElementById('confirmEstado').textContent = estado === 'REALIZADA' ? 'Realizada' : 
                                                           estado === 'PROGRAMADA' ? 'Programada' : 
                                                           estado === 'VALIDADA' ? 'Validada' : 'Pendiente';
}


// Función para registrar la sesión
function registrarSesion() {
    const form = document.getElementById('frmSesionInsert');
    const formData = new FormData(form);
    
    $.ajax({
        url: _urlBase + '/admin/sesion_recuperacion/insert',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                new PNotify({
                    title: '¡Éxito!',
                    text: 'Sesión registrada correctamente',
                    type: 'success'
                });
                $('#nuevaSesionModal').modal('hide');
                
                // Agregar la nueva fila a la tabla dinámicamente
                if (response.sesion) {
                    agregarFilaSesion(response.sesion);
                }
                
                // Limpiar formulario
                form.reset();
                currentStep = 1;
                planData = null;
                sessionData = {};
            } else {
                new PNotify({
                    title: 'Error',
                    text: response.message || 'Error al registrar la sesión',
                    type: 'error'
                });
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error al registrar la sesión';
            if (xhr.responseJSON && xhr.responseJSON.message) {
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

// Función para agregar una nueva fila a la tabla
function agregarFilaSesion(sesion) {
    const tbody = document.querySelector('.table-modern tbody');
    if (!tbody) return;
    
    // Formatear fecha
    const fecha = new Date(sesion.fecha_sesion);
    const fechaFormateada = fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    // Formatear hora de creación
    const createdAt = new Date(sesion.created_at);
    const horaCreacion = createdAt.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Determinar icono y color de tipo de sesión
    let tipoSesionHTML = '';
    if (sesion.tipo_sesion === 'TEORIA') {
        tipoSesionHTML = `<span class="status-indicator-execution" style="background: rgba(108, 92, 231, 0.1); color: var(--purple);">
            <i class="fas fa-book mr-1"></i> Teoría
        </span>`;
    } else if (sesion.tipo_sesion === 'PRACTICA') {
        tipoSesionHTML = `<span class="status-indicator-execution" style="background: rgba(0, 184, 148, 0.1); color: var(--success-green);">
            <i class="fas fa-flask mr-1"></i> Práctica
        </span>`;
    } else {
        tipoSesionHTML = `<span class="status-indicator-execution" style="background: rgba(225, 112, 85, 0.1); color: var(--danger-red);">
            <i class="fas fa-file-alt mr-1"></i> Examen
        </span>`;
    }
    
    // Determinar icono y color de modalidad
    let modalidadHTML = '';
    if (sesion.modalidad === 'PRESENCIAL') {
        modalidadHTML = `<span class="status-indicator-execution" style="background: rgba(0, 139, 220, 0.1); color: var(--primary-blue);">
            <i class="fas fa-building mr-1"></i> Presencial
        </span>`;
    } else if (sesion.modalidad === 'VIRTUAL') {
        modalidadHTML = `<span class="status-indicator-execution" style="background: rgba(0, 206, 201, 0.1); color: var(--info-cyan);">
            <i class="fas fa-video mr-1"></i> Virtual
        </span>`;
    } else {
        modalidadHTML = `<span class="status-indicator-execution" style="background: rgba(253, 203, 110, 0.1); color: #e17055;">
            <i class="fas fa-star mr-1"></i> Extra
        </span>`;
    }
    
    // Determinar estado
    let estadoHTML = '';
    if (sesion.estado_sesion === 'VALIDADA') {
        estadoHTML = `<span class="status-indicator-execution" style="background: rgba(0, 184, 148, 0.1); color: var(--success-green);">
            <i class="fas fa-check-double mr-1"></i> Validada
        </span>`;
    } else if (sesion.estado_sesion === 'REALIZADA') {
        estadoHTML = `<span class="status-indicator-execution" style="background: rgba(0, 139, 220, 0.1); color: var(--primary-blue);">
            <i class="fas fa-check mr-1"></i> Realizada
        </span>`;
    } else {
        estadoHTML = `<span class="status-indicator-execution" style="background: rgba(253, 203, 110, 0.1); color: var(--warning-orange);">
            <i class="fas fa-clock mr-1"></i> Programada
        </span>`;
    }
    
    // Crear la nueva fila
    const nuevaFila = `
        <tr class="fade-in" data-estado="${sesion.estado_sesion}" data-modalidad="${sesion.modalidad}" data-plan="${sesion.id_plan}">
            <td>
                <div style="font-weight: 700; color: var(--primary-blue);">
                    #${sesion.id_sesion}
                </div>
                <small style="color: var(--medium-gray);">
                    ${horaCreacion}
                </small>
            </td>
            <td>
                <div style="font-weight: 600; color: var(--dark-gray);">
                    Plan #${sesion.id_plan}
                </div>
                <small style="color: var(--medium-gray);">
                    ${sesion.plan_horas_totales || 0} horas totales
                </small>
            </td>
            <td>
                <div style="font-weight: 600; color: var(--dark-gray);">
                    ${sesion.docente_apellidos || ''}
                </div>
                <small style="color: var(--medium-gray);">
                    ${sesion.docente_nombres || ''}
                </small>
            </td>
            <td>
                <div style="font-weight: 600; color: var(--dark-gray);">
                    ${sesion.asignatura || 'No especificada'}
                </div>
            </td>
            <td class="text-center">
                <span class="badge" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); color: white; padding: 6px 12px; border-radius: 12px; font-size: 0.85rem;">
                    ${sesion.semestre ? sesion.semestre.charAt(0) + sesion.semestre.slice(1).toLowerCase() : 'N/A'}
                </span>
            </td>
            <td class="text-center">
                <div style="font-weight: 500; color: var(--medium-gray);">
                    <i class="fas fa-door-open mr-1"></i>
                    ${sesion.aula || 'No asignada'}
                </div>
            </td>
            <td>
                ${tipoSesionHTML}
            </td>
            <td>
                ${modalidadHTML}
            </td>
            <td>
                <div style="text-align: center;">
                    <span style="font-size: 1.3rem; font-weight: 700; color: var(--primary-blue);">
                        ${sesion.horas_recuperadas}
                    </span>
                    <br>
                    <small style="color: var(--medium-gray);">horas</small>
                </div>
            </td>
            <td>
                <div style="font-weight: 600; color: var(--dark-gray);">
                    ${fechaFormateada}
                </div>
            </td>
            <td>
                ${estadoHTML}
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon btn-view" onclick="verSesion('${sesion.id_sesion}')" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${sesion.estado_sesion !== 'VALIDADA' ? `
                    <button class="btn-icon btn-edit" onclick="editarSesion('${sesion.id_sesion}')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon btn-approve" onclick="completarSesion('${sesion.id_sesion}')" title="Marcar como validada">
                        <i class="fas fa-check"></i>
                    </button>
                    ` : ''}
                    <button class="btn-icon btn-delete" onclick="eliminarSesion('${sesion.id_sesion}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
    
    // Insertar al inicio de la tabla
    tbody.insertAdjacentHTML('afterbegin', nuevaFila);
    
    // Animar la nueva fila
    const primeraFila = tbody.querySelector('tr:first-child');
    if (primeraFila) {
        primeraFila.style.opacity = '0';
        setTimeout(() => {
            primeraFila.style.transition = 'opacity 0.5s ease-in';
            primeraFila.style.opacity = '1';
        }, 100);
    }
}

// Funciones auxiliares
function formatFecha(fechaStr) {
    const fecha = new Date(fechaStr);
    return fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}
