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
            docente: document.getElementById('hiddenDocente').value,
            fechaFin: document.getElementById('hiddenFechaFin')?.value || null
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
                docente: selectedOption.getAttribute('data-docente'),
                fechaFin: selectedOption.getAttribute('data-fecha-fin')
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
    
    // Establecer fecha mínima para el input de fecha_sesion (fecha actual)
    const fechaSesionInput = document.querySelector('input[name="fecha_sesion"]');
    if (fechaSesionInput) {
        const fechaActual = new Date().toISOString().split('T')[0]; // Formato YYYY-MM-DD
        fechaSesionInput.setAttribute('min', fechaActual);
        
        // Mostrar mensaje informativo
        const fechaActualFormateada = new Date(fechaActual).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        console.log(`Fecha mínima establecida: ${fechaActualFormateada}`);
    }
    
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

// Función para calcular horas a recuperar basado en hora inicio y hora fin
function calcularHorasRecuperar() {
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    const horasInput = document.getElementById('horas_recuperadas');
    
    if (horaInicio && horaFin) {
        // Crear objetos Date para calcular la diferencia
        const inicio = new Date(`2000-01-01T${horaInicio}`);
        const fin = new Date(`2000-01-01T${horaFin}`);
        
        // Calcular diferencia en milisegundos
        let diferencia = fin - inicio;
        
        // Si la hora fin es menor que la hora inicio, asumimos que cruza medianoche
        if (diferencia < 0) {
            new PNotify({
                title: 'Advertencia',
                text: 'La hora de fin debe ser posterior a la hora de inicio',
                type: 'warning',
                styling: 'bootstrap3'
            });
            horasInput.value = '';
            horasInput.classList.remove('is-valid');
            horasInput.classList.add('is-invalid');
            return;
        }
        
        // Convertir a horas (redondeado a 0.5)
        const horas = Math.round((diferencia / (1000 * 60 * 60)) * 2) / 2;
        
        // Validar que esté en el rango permitido
        if (horas < 0.5) {
            new PNotify({
                title: 'Advertencia',
                text: 'La sesión debe ser de al menos 30 minutos (0.5 horas)',
                type: 'warning',
                styling: 'bootstrap3'
            });
            horasInput.value = '';
            horasInput.classList.remove('is-valid');
            horasInput.classList.add('is-invalid');
            return;
        }
        
        if (horas > 8) {
            new PNotify({
                title: 'Advertencia',
                text: 'La sesión no puede exceder 8 horas',
                type: 'warning',
                styling: 'bootstrap3'
            });
            horasInput.value = '';
            horasInput.classList.remove('is-valid');
            horasInput.classList.add('is-invalid');
            return;
        }
        
        // Asignar el valor calculado
        horasInput.value = horas;
        
        // Validar las horas
        validarHoras();
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
            // Validar hora inicio
            const horaInicio = document.getElementById('hora_inicio');
            if (!horaInicio || !horaInicio.value) {
                new PNotify({
                    title: 'Error',
                    text: 'Debe ingresar la hora de inicio',
                    type: 'error'
                });
                return false;
            }
            
            // Validar hora fin
            const horaFin = document.getElementById('hora_fin');
            if (!horaFin || !horaFin.value) {
                new PNotify({
                    title: 'Error',
                    text: 'Debe ingresar la hora de fin',
                    type: 'error'
                });
                return false;
            }
            
            // Validar que se haya calculado las horas
            const horasInput = document.querySelector('input[name="horas_recuperadas"]');
            if (!horasInput || !horasInput.value) {
                new PNotify({
                    title: 'Error',
                    text: 'Las horas no se han calculado correctamente',
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
            
            // Validar asignatura (ahora es idAsignatura)
            const idAsignaturaInput = document.getElementById('idAsignatura');
            if (!idAsignaturaInput || !idAsignaturaInput.value || idAsignaturaInput.value.trim() === '') {
                new PNotify({
                    title: 'Error',
                    text: 'Debe buscar y seleccionar una asignatura',
                    type: 'error'
                });
                return false;
            }
            
            // Validar que la fecha de sesión no sea anterior a la fecha actual
            const fechaSesion = document.querySelector('input[name="fecha_sesion"]').value;
            const fechaActual = new Date().toISOString().split('T')[0]; // Formato YYYY-MM-DD
            
            if (fechaSesion < fechaActual) {
                const fechaActualFormateada = new Date(fechaActual).toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                new PNotify({
                    title: 'Error de Validación',
                    text: `La fecha de sesión no puede ser anterior a la fecha actual (${fechaActualFormateada}). Las sesiones de recuperación deben programarse desde hoy en adelante.`,
                    type: 'error',
                    delay: 5000
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
            sessionData.horaInicio = horaInicio.value;
            sessionData.horaFin = horaFin.value;
            sessionData.idAsignatura = idAsignaturaInput.value.trim();
            sessionData.nombreAsignatura = document.getElementById('nombreAsignatura').value.trim();
            sessionData.aula = document.querySelector('input[name="aula"]').value.trim() || 'No especificada';
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
    const horaInicio = sessionData.horaInicio;
    const horaFin = sessionData.horaFin;
    const fecha = sessionData.fecha;
    const estado = document.querySelector('select[name="estado_sesion"]').value;
    
    // Actualizar confirmación
    document.getElementById('confirmDocente').textContent = docente;
    document.getElementById('confirmPlan').textContent = plan.split(' - ')[0];
    document.getElementById('confirmCurso').innerHTML = `
        <strong>${asignatura}</strong><br>
        <small style="color: var(--medium-gray);">Semestre: ${semestre} | Aula: ${aula}</small>
    `;
    document.getElementById('confirmHorario').innerHTML = `
        ${horaInicio} - ${horaFin}<br>
        <small style="color: var(--medium-gray);">${tipoSesion} - ${modalidad}</small>
    `;
    document.getElementById('confirmHoras').textContent = `${horas} horas`;
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
            console.log('AJAX Response:', response);
            if (response.success) {
                new PNotify({
                    title: '¡Éxito!',
                    text: 'Sesión registrada correctamente',
                    type: 'success'
                });
                $('#nuevaSesionModal').modal('hide');
                
                // Agregar la nueva fila a la tabla dinámicamente
                if (response.sesion) {
                    console.log('Llamando a agregarFilaSesion con:', response.sesion);
                    agregarFilaSesion(response.sesion);
                } else {
                    console.error('No se recibió el objeto sesion en la respuesta');
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
    console.log('agregarFilaSesion llamada con:', sesion);
    const tbody = document.querySelector('.table-execution tbody');
    console.log('tbody encontrado:', tbody);
    if (!tbody) {
        console.error('No se encontró el tbody de la tabla');
        return;
    }
    
    // Formatear fecha
    const fecha = new Date(sesion.fecha_sesion);
    const fechaFormateada = fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    // Formatear horario
    let horarioHTML = '<small style="color: var(--medium-gray);">No especificado</small>';
    if (sesion.hora_inicio && sesion.hora_fin) {
        // Extraer solo la parte de la hora (HH:MM) del timestamp
        let horaInicio = sesion.hora_inicio;
        let horaFin = sesion.hora_fin;
        
        // Si viene en formato ISO (con T), extraer la hora
        if (horaInicio.includes('T')) {
            horaInicio = horaInicio.split('T')[1].substring(0, 5);
        } else {
            horaInicio = horaInicio.substring(0, 5);
        }
        
        if (horaFin.includes('T')) {
            horaFin = horaFin.split('T')[1].substring(0, 5);
        } else {
            horaFin = horaFin.substring(0, 5);
        }
        
        horarioHTML = `
            <span style="color: var(--dark-gray);">${horaInicio}</span>
            <span style="color: var(--dark-gray);"> - ${horaFin}</span>
        `;
    }
    
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
        estadoHTML = `<span class="status-indicator-execution status-completed">
            <span class="dot-status dot-completed"></span> Validada
        </span>`;
    } else if (sesion.estado_sesion === 'REALIZADA') {
        estadoHTML = `<span class="status-indicator-execution status-in-progress">
            <span class="dot-status dot-in-progress"></span> Realizada
        </span>`;
    } else {
        estadoHTML = `<span class="status-indicator-execution status-pending">
            <span class="dot-status dot-pending"></span> Programada
        </span>`;
    }
    
    // Obtener el número de fila (contar las filas actuales + 1)
    const numeroFila = tbody.querySelectorAll('tr').length + 1;
    
    // Crear la nueva fila - INCLUIR TODAS las columnas (visibles y ocultas con class="none")
    const nuevaFila = `
        <tr class="fade-in" data-estado="${sesion.estado_sesion}" data-modalidad="${sesion.modalidad}" data-plan="${sesion.id_plan}">
            <td>
                <div style="color: var(--dark-gray); text-align: center;">
                    ${numeroFila}
                </div>
            </td>
            <td class="none">
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
            </td>
            <td>
                <div style="color: var(--dark-gray);">
                    ${sesion.asignatura || 'No especificada'}
                </div>
            </td>
            <td>
                <div style="color: var(--dark-gray);">
                    ${fechaFormateada}
                </div>
            </td>
            <td>
                <div style="color: var(--dark-gray);">
                    ${horarioHTML}
                </div>
            </td>
            <td>
                <div style="color: var(--dark-gray);">
                    <span style="color: var(--dark-gray);">
                        ${sesion.horas_recuperadas}
                    </span>
                    <br>
                    <small style="color: var(--medium-gray);">horas</small>
                </div>
            </td>
            <td>
                ${modalidadHTML}
            </td>
            <td class="none text-center">
                <span class="badge" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); color: white; padding: 6px 12px; border-radius: 12px; font-size: 0.85rem;">
                    ${sesion.semestre ? sesion.semestre.charAt(0) + sesion.semestre.slice(1).toLowerCase() : 'N/A'}
                </span>
            </td>
            <td class="none text-center">
                <div style="font-weight: 500; color: var(--medium-gray);">
                    <i class="fas fa-door-open mr-1"></i>
                    ${sesion.aula || 'No asignada'}
                </div>
            </td>
            <td class="none">
                ${tipoSesionHTML}
            </td>
            <td>
                ${estadoHTML}
            </td>
            <td>
                <div class="action-buttons">
                    <a href="${_urlBase}/admin/evidencia_recuperacion?sesion_id=${sesion.id_sesion}" class="btn-icon btn-view" title="Ver evidencias de esta sesión">
                        <i class="fas fa-file-contract"></i>
                    </a>
                    ${sesion.estado_sesion !== 'VALIDADA' ? `
                    <button class="btn-icon btn-edit" onclick="editarSesion(${sesion.id_sesion})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon btn-approve" onclick="completarSesion(${sesion.id_sesion})" title="Marcar como validada">
                        <i class="fas fa-check"></i>
                    </button>
                    ` : ''}
                    <button class="btn-icon btn-delete" onclick="eliminarSesion(${sesion.id_sesion})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
    
    // Obtener la instancia de DataTable
    const table = $('#tablaExample2').DataTable();
    
    if (table) {
        // Usar la API de DataTables para agregar la fila correctamente
        // Esto asegura que Responsive funcione automáticamente
        const newRow = table.row.add([
            // Columna 1: N° #
            `<div style="color: var(--dark-gray); text-align: center;">${table.rows().count() + 1}</div>`,
            // Columna 2: Plan (hidden - class="none")
            `<div style="font-weight: 600; color: var(--dark-gray);">Plan #${sesion.id_plan}</div><small style="color: var(--medium-gray);">${sesion.plan_horas_totales || 0} horas totales</small>`,
            // Columna 3: Docente
            `<div style="font-weight: 600; color: var(--dark-gray);">${sesion.docente_apellidos || ''}</div>`,
            // Columna 4: Curso
            `<div style="color: var(--dark-gray);">${sesion.asignatura?.nom_asignatura || sesion.asignatura || 'No especificada'}</div>`,
            // Columna 5: Fecha
            `<div style="color: var(--dark-gray);">${fechaFormateada}</div>`,
            // Columna 6: Horario
            `<div style="color: var(--dark-gray);">${horarioHTML}</div>`,
            // Columna 7: Horas
            `<div style="color: var(--dark-gray);"><span style="color: var(--dark-gray);">${sesion.horas_recuperadas}</span><br><small style="color: var(--medium-gray);">horas</small></div>`,
            // Columna 8: Modalidad
            modalidadHTML,
            // Columna 9: Semestre (hidden - class="none")
            `<span class="badge" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); color: white; padding: 6px 12px; border-radius: 12px; font-size: 0.85rem;">${sesion.semestre ? sesion.semestre.charAt(0) + sesion.semestre.slice(1).toLowerCase() : 'N/A'}</span>`,
            // Columna 10: Aula (hidden - class="none")
            `<div style="font-weight: 500; color: var(--medium-gray);"><i class="fas fa-door-open mr-1"></i>${sesion.aula || 'No asignada'}</div>`,
            // Columna 11: Tipo (hidden - class="none")
            tipoSesionHTML,
            // Columna 12: Estado
            estadoHTML,
            // Columna 13: Acciones
            `<div class="action-buttons">
                <a href="${_urlBase}/admin/evidencia_recuperacion?sesion_id=${sesion.id_sesion}" class="btn-icon btn-view" title="Ver evidencias de esta sesión">
                    <i class="fas fa-file-contract"></i>
                </a>
                ${sesion.estado_sesion !== 'VALIDADA' ? `
                <button class="btn-icon btn-edit" onclick="editarSesion(${sesion.id_sesion})" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon btn-approve" onclick="completarSesion(${sesion.id_sesion})" title="Marcar como validada">
                    <i class="fas fa-check"></i>
                </button>
                ` : ''}
                <button class="btn-icon btn-delete" onclick="eliminarSesion(${sesion.id_sesion})" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`
        ]);
        
        // Agregar atributos data a la fila
        $(newRow.node())
            .addClass('fade-in')
            .attr('data-estado', sesion.estado_sesion)
            .attr('data-modalidad', sesion.modalidad)
            .attr('data-plan', sesion.id_plan);
        
        // Redibujar la tabla en la primera página
        table.order([0, 'desc']).draw(false);
        
        // Animar la nueva fila
        const filaElement = newRow.node();
        if (filaElement) {
            filaElement.style.opacity = '0';
            setTimeout(() => {
                filaElement.style.transition = 'opacity 0.5s ease-in';
                filaElement.style.opacity = '1';
            }, 100);
        }
    } else {
        // Fallback: insertar HTML directamente si DataTable no está disponible
        const numeroFila = tbody.querySelectorAll('tr').length + 1;
        const nuevaFila = `
            <tr class="fade-in" data-estado="${sesion.estado_sesion}" data-modalidad="${sesion.modalidad}" data-plan="${sesion.id_plan}">
                <td><div style="color: var(--dark-gray); text-align: center;">${numeroFila}</div></td>
                <td class="none"><div style="font-weight: 600; color: var(--dark-gray);">Plan #${sesion.id_plan}</div><small style="color: var(--medium-gray);">${sesion.plan_horas_totales || 0} horas totales</small></td>
                <td><div style="font-weight: 600; color: var(--dark-gray);">${sesion.docente_apellidos || ''}</div></td>
                <td><div style="color: var(--dark-gray);">${sesion.asignatura?.nom_asignatura || sesion.asignatura || 'No especificada'}</div></td>
                <td><div style="color: var(--dark-gray);">${fechaFormateada}</div></td>
                <td><div style="color: var(--dark-gray);">${horarioHTML}</div></td>
                <td><div style="color: var(--dark-gray);"><span style="color: var(--dark-gray);">${sesion.horas_recuperadas}</span><br><small style="color: var(--medium-gray);">horas</small></div></td>
                <td>${modalidadHTML}</td>
                <td class="none text-center"><span class="badge" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); color: white; padding: 6px 12px; border-radius: 12px; font-size: 0.85rem;">${sesion.semestre ? sesion.semestre.charAt(0) + sesion.semestre.slice(1).toLowerCase() : 'N/A'}</span></td>
                <td class="none text-center"><div style="font-weight: 500; color: var(--medium-gray);"><i class="fas fa-door-open mr-1"></i>${sesion.aula || 'No asignada'}</div></td>
                <td class="none">${tipoSesionHTML}</td>
                <td>${estadoHTML}</td>
                <td><div class="action-buttons"><a href="${_urlBase}/admin/evidencia_recuperacion?sesion_id=${sesion.id_sesion}" class="btn-icon btn-view" title="Ver evidencias de esta sesión"><i class="fas fa-file-contract"></i></a>${sesion.estado_sesion !== 'VALIDADA' ? `<button class="btn-icon btn-edit" onclick="editarSesion(${sesion.id_sesion})" title="Editar"><i class="fas fa-edit"></i></button><button class="btn-icon btn-approve" onclick="completarSesion(${sesion.id_sesion})" title="Marcar como validada"><i class="fas fa-check"></i></button>` : ''}<button class="btn-icon btn-delete" onclick="eliminarSesion(${sesion.id_sesion})" title="Eliminar"><i class="fas fa-trash"></i></button></div></td>
            </tr>
        `;
        tbody.insertAdjacentHTML('afterbegin', nuevaFila);
        
        const primeraFila = tbody.querySelector('tr:first-child');
        if (primeraFila) {
            primeraFila.style.opacity = '0';
            setTimeout(() => {
                primeraFila.style.transition = 'opacity 0.5s ease-in';
                primeraFila.style.opacity = '1';
            }, 100);
        }
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
