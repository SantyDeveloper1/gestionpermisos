/**
 * Reprogramación de Sesiones de Recuperación con Sistema de Pasos
 * Maneja la funcionalidad de reprogramar sesiones existentes
 */

'use strict';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {

    let currentStep = 1;
    let tipoReprogramacion = '';
    let datosAsignatura = null;
    let datosSesionActual = null;

    // ==========================================
    // FUNCIONES AUXILIARES PARA FORMATEAR FECHAS Y HORAS
    // ==========================================
    
    /**
     * Convierte un timestamp o fecha a formato YYYY-MM-DD para input type="date"
     */
    function formatDateForInput(dateString) {
        if (!dateString) return '';
        if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
            return dateString;
        }
        return dateString.split(' ')[0];
    }

    /**
     * Convierte un timestamp a formato HH:mm para input type="time"
     */
    function formatTimeForInput(timeString) {
        if (!timeString) return '';
        
        if (timeString.includes(' ')) {
            timeString = timeString.split(' ')[1];
        }
        
        const timeParts = timeString.split(':');
        if (timeParts.length >= 2) {
            return `${timeParts[0]}:${timeParts[1]}`;
        }
        
        return timeString;
    }

    // ==========================================
    // ABRIR MODAL DE REPROGRAMACIÓN
    // ==========================================
    $(document).on('click', '.btn-reprogramar', function () {
        const sesionId = $(this).data('sesion-id');
        const fecha = $(this).data('fecha');
        const horaInicio = $(this).data('hora-inicio');
        const horaFin = $(this).data('hora-fin');
        const aula = $(this).data('aula');

        // Guardar datos de la sesión actual
        datosSesionActual = {
            id: sesionId,
            fecha: fecha,
            horaInicio: horaInicio,
            horaFin: horaFin,
            aula: aula
        };

        // Resetear modal y cargar datos
        resetModal();
        $('#rep_id_sesion').val(sesionId);
        cargarDatosSesion(sesionId);

        // Abrir el modal
        $('#modalReprogramarSesion').modal('show');
    });

    // ==========================================
    // GESTIÓN DE PASOS
    // ==========================================
    
    function resetModal() {
        currentStep = 1;
        tipoReprogramacion = '';
        datosAsignatura = null;
        updateStepUI();
        $('.card-option').removeClass('selected');
        $('#tipo_sesion').val('');
        $('#sesionMultiple').addClass('d-none');
        $('#sesionUnica').removeClass('d-none');
        $('#formReprogramarSesion')[0].reset();
    }

    function updateStepUI() {
        // Actualizar título
        $('#modalStepTitle').text(`Paso ${currentStep} de 3`);
        
        // Actualizar barra de progreso
        const progress = ((currentStep - 1) * 33) + 33;
        $('#stepProgressBar').css('width', `${progress}%`);
        
        // Actualizar indicadores de paso
        $('.step').removeClass('active');
        $(`.step[data-step="${currentStep}"]`).addClass('active');
        
        // Mostrar/ocultar contenido de pasos
        $('.step-content').removeClass('active').addClass('d-none');
        $(`.step-${currentStep}`).removeClass('d-none').addClass('active');
        
        // Actualizar botones
        $('#btnStepPrev').toggle(currentStep > 1);
        $('#btnStepNext').toggle(currentStep < 3);
        $('#btnReprogramar').toggle(currentStep === 3);
    }

    // ==========================================
    // PASO 1: SELECCIÓN DE TIPO
    // ==========================================
    
    $('.btn-select-tipo').click(function() {
        const card = $(this).closest('.card-option');
        tipoReprogramacion = card.data('tipo');
        
        $('.card-option').removeClass('selected');
        card.addClass('selected');
        
        // Actualizar motivo según tipo seleccionado
        let motivo = '';
        if (tipoReprogramacion === 'permiso') {
            motivo = 'Reprogramación por permiso institucional';
        } else if (tipoReprogramacion === 'plan') {
            motivo = 'Reprogramación dentro del plan de recuperación';
        }
        $('#rep_motivo').val(motivo);
        
        // Habilitar botón siguiente
        $('#btnStepNext').prop('disabled', false);
    });

    // ==========================================
    // NAVEGACIÓN ENTRE PASOS
    // ==========================================
    
    $('#btnStepNext').click(function() {
        if (currentStep === 1 && !tipoReprogramacion) {
            new PNotify({
                title: 'Selección requerida',
                text: 'Por favor seleccione un tipo de reprogramación',
                type: 'error',
                delay: 3000
            });
            return;
        }
        
        if (currentStep === 2 && !datosAsignatura) {
            new PNotify({
                title: 'Error',
                text: 'No se pudo cargar la información de la asignatura',
                type: 'error',
                delay: 3000
            });
            return;
        }
        
        if (currentStep < 3) {
            currentStep++;
            updateStepUI();
            
            if (currentStep === 2) {
                cargarInformacionAsignatura();
            } else if (currentStep === 3) {
                prepararPasoFinal();
            }
        }
    });

    $('#btnStepPrev').click(function() {
        if (currentStep > 1) {
            currentStep--;
            updateStepUI();
        }
    });

    // ==========================================
    // PASO 2: INFORMACIÓN DE ASIGNATURA
    // ==========================================
    
    function cargarDatosSesion(idSesion) {
        $.ajax({
            url: `/admin/sesion_recuperacion/${idSesion}/data`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.sesion) {
                    const sesion = response.sesion;
                    
                    // Extraer información de la sesión
                    const asignaturaNombre = sesion.asignatura?.nom_asignatura || 'No especificada';
                    const docenteApellido = sesion.plan_recuperacion?.permiso?.docente?.user?.last_name || '';
                    const docenteNombre = sesion.plan_recuperacion?.permiso?.docente?.user?.name || '';
                    const docenteCompleto = `${docenteApellido} ${docenteNombre}`.trim() || 'No especificado';
                    
                    // Usar las horas pendientes calculadas por el backend
                    // que ya incluyen la suma de todas las sesiones del plan
                    const horasPendientes = parseFloat(response.horasPendientes) || 0;
                    const totalHorasPlan = parseFloat(response.totalHorasPlan) || 0;
                    const horasYaRecuperadasPlan = parseFloat(response.horasYaRecuperadasPlan) || 0;
                    
                    datosAsignatura = {
                        nombre: asignaturaNombre,
                        docente: docenteCompleto,
                        horasPendientes: horasPendientes, // Horas pendientes del plan
                        totalHorasPlan: totalHorasPlan, // Total de horas del plan
                        horasYaRecuperadasPlan: horasYaRecuperadasPlan, // Total recuperado en el plan
                        totalHoras: parseFloat(sesion.asignatura?.total_horas) || 48,
                        horasCompletadas: parseFloat(sesion.horas_recuperadas) || 0,
                        idAsignatura: sesion.idAsignatura,
                        idPlan: sesion.id_plan
                    };
                } else {
                    usarDatosEjemplo();
                }
            },
            error: function(xhr, status, error) {
                new PNotify({
                    title: 'Advertencia',
                    text: 'No se pudieron cargar todos los datos de la sesión. Usando valores predeterminados.',
                    type: 'warning',
                    delay: 3000
                });
                
                usarDatosEjemplo();
            }
        });
    }
    
    function usarDatosEjemplo() {
        datosAsignatura = {
            nombre: 'Asignatura de ejemplo',
            docente: 'Docente de ejemplo',
            horasPendientes: 8,
            totalHoras: 48,
            horasCompletadas: 40
        };
        console.log('Usando datos de ejemplo:', datosAsignatura);
    }

    function cargarInformacionAsignatura() {
        if (datosAsignatura) {
            $('#infoAsignatura').text(datosAsignatura.nombre);
            $('#infoDocente').text(datosAsignatura.docente);
            $('#infoHorasPendientes').text(datosAsignatura.horasPendientes);
            $('#infoTotalHoras').text(datosAsignatura.totalHoras);
            $('#total_horas_asignatura').val(datosAsignatura.totalHoras);
            
            // Configurar límites para horas totales
            $('#horas_totales').attr({
                'min': 1,
                'max': datosAsignatura.horasPendientes,
                'value': datosAsignatura.horasPendientes
            });
            $('#horas_pendientes_display').text(datosAsignatura.horasPendientes);
            $('#horas_maximas_display').text(datosAsignatura.horasPendientes);
        }
    }

    // ==========================================
    // PASO 3: CONFIGURACIÓN DE REPROGRAMACIÓN
    // ==========================================
    
    function prepararPasoFinal() {
        // Prellenar con datos actuales de la sesión
        if (datosSesionActual) {
            $('#rep_fecha').val(formatDateForInput(datosSesionActual.fecha));
            $('#rep_hora_inicio').val(formatTimeForInput(datosSesionActual.horaInicio));
            $('#rep_hora_fin').val(formatTimeForInput(datosSesionActual.horaFin));
            $('#rep_aula').val(datosSesionActual.aula || '');
        }
        
        actualizarResumenHoras();
    }

    // Calcular horas automáticamente para sesión única
    $('#rep_hora_inicio, #rep_hora_fin').on('change', function() {
        calcularHorasSesionUnica();
    });

    function calcularHorasSesionUnica() {
        const inicio = $('#rep_hora_inicio').val();
        const fin = $('#rep_hora_fin').val();
        
        if (inicio && fin) {
            const horas = calcularDiferenciaHoras(inicio, fin);
            
            const $inputHoras = $('#horas_calculadas');
            const $smallText = $inputHoras.closest('.form-group').find('.form-text');
            
            // Mostrar las horas calculadas
            $('#horas_calculadas').val(horas.toFixed(1));
            
            // Validar que no supere las horas afectadas del permiso
            if (datosAsignatura && datosAsignatura.horasPendientes) {
                const horasPendientes = datosAsignatura.horasPendientes;
                
                if (horas > horasPendientes) {
                    // Mostrar advertencia si excede
                    $inputHoras.addClass('is-invalid').removeClass('is-valid');
                    $smallText.removeClass('text-muted text-success').addClass('text-danger');
                    $smallText.html(`
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Advertencia:</strong> Las horas calculadas (${horas.toFixed(1)}h) superan las horas pendientes (${horasPendientes.toFixed(1)}h)
                    `);
                    
                    new PNotify({
                        title: 'Error',
                        text: `Las horas programadas (${horas.toFixed(1)}h) superan las horas pendientes del plan (${horasPendientes.toFixed(1)}h)`,
                        type: 'error',
                        delay: 4000
                    });
                } else {
                    // Remover advertencia si está dentro del límite
                    $inputHoras.removeClass('is-invalid').addClass('is-valid');
                    $smallText.removeClass('text-danger text-muted').addClass('text-success');
                    $smallText.html(`
                        <i class="fas fa-check-circle mr-1"></i>
                        Calculado automáticamente. Quedarán ${(horasPendientes - horas).toFixed(1)}h pendientes
                    `);
                }
            }
            
            actualizarResumenHoras();
        }
    }

    function calcularDiferenciaHoras(inicio, fin) {
        const [h1, m1] = inicio.split(':').map(Number);
        const [h2, m2] = fin.split(':').map(Number);
        
        // Convertir a minutos totales para comparar
        const minutosInicio = h1 * 60 + m1;
        const minutosFin = h2 * 60 + m2;
        
        // Validar que la hora de fin sea posterior a la hora de inicio
        if (minutosFin <= minutosInicio) {
            return 0; // Retornar 0 si la hora de fin no es posterior
        }
        
        // Calcular la diferencia en horas
        const diferenciaMinutos = minutosFin - minutosInicio;
        return diferenciaMinutos / 60;
    }

    function actualizarResumenHoras() {
        if (!datosAsignatura) return;
        
        const horasPendientes = datosAsignatura.horasPendientes;
        const horasProgramadas = parseFloat($('#horas_calculadas').val()) || 0;
        const nuevoTotal = datosAsignatura.horasCompletadas + horasProgramadas;
        
        $('#resumenHorasPendientes').text(horasPendientes.toFixed(1));
        $('#resumenHorasProgramadas').text(horasProgramadas.toFixed(1));
        $('#resumenNuevoTotal').text(nuevoTotal.toFixed(1));
    }

    // ==========================================
    // VALIDAR FORMULARIO ANTES DE ENVIAR
    // ==========================================
    
    function validarFormulario() {
        // Validar que se haya seleccionado un tipo de reprogramación
        if (!tipoReprogramacion) {
            new PNotify({
                title: 'Error',
                text: 'Debe seleccionar un tipo de reprogramación',
                type: 'error',
                delay: 3000
            });
            return false;
        }

        // Validar fecha
        const fecha = $('#rep_fecha').val();
        if (!fecha) {
            new PNotify({
                title: 'Error',
                text: 'Debe ingresar una fecha',
                type: 'error',
                delay: 3000
            });
            return false;
        }

        // Validar horas
        const horaInicio = $('#rep_hora_inicio').val();
        const horaFin = $('#rep_hora_fin').val();
        
        if (!horaInicio || !horaFin) {
            new PNotify({
                title: 'Error',
                text: 'Debe ingresar hora de inicio y fin',
                type: 'error',
                delay: 3000
            });
            return false;
        }

        // Validar que el campo de horas calculadas no tenga errores
        const $horasCalculadas = $('#horas_calculadas');
        if ($horasCalculadas.hasClass('is-invalid')) {
            new PNotify({
                title: 'Error',
                text: 'Por favor corrija los errores en el cálculo de horas antes de continuar',
                type: 'error',
                delay: 3000
            });
            return false;
        }

        // Validar que hora fin sea mayor que hora inicio
        const horas = calcularDiferenciaHoras(horaInicio, horaFin);
        if (horas === 0) {
            new PNotify({
                title: 'Error',
                text: 'La hora de fin debe ser posterior a la hora de inicio',
                type: 'error',
                delay: 3000
            });
            return false;
        }

        // Validar que las horas no superen las horas pendientes del plan
        if (datosAsignatura && datosAsignatura.horasPendientes) {
            if (horas > datosAsignatura.horasPendientes) {
                new PNotify({
                    title: 'Error',
                    text: `Las horas calculadas (${horas.toFixed(1)}h) superan las horas pendientes del plan (${datosAsignatura.horasPendientes.toFixed(1)}h)`,
                    type: 'error',
                    delay: 4000
                });
                return false;
            }
        }

        // Validar aula
        const aula = $('#rep_aula').val();
        if (!aula || aula.trim() === '') {
            new PNotify({
                title: 'Error',
                text: 'Debe ingresar el aula',
                type: 'error',
                delay: 3000
            });
            return false;
        }

        // Validar motivo
        const motivo = $('#rep_motivo').val();
        if (!motivo || motivo.trim() === '') {
            new PNotify({
                title: 'Error',
                text: 'Debe ingresar el motivo de reprogramación',
                type: 'error',
                delay: 3000
            });
            return false;
        }

        return true;
    }

    // ==========================================
    // ENVIAR REPROGRAMACIÓN
    // ==========================================
    
    $('#btnReprogramar').click(function() {
        if (!validarFormulario()) return;
        
        const formData = {
            id_sesion: $('#rep_id_sesion').val(),
            fecha_nueva: $('#rep_fecha').val(),
            hora_inicio_nueva: $('#rep_hora_inicio').val(),
            hora_fin_nueva: $('#rep_hora_fin').val(),
            aula_nueva: $('#rep_aula').val(),
            motivo: $('#rep_motivo').val(),
            tipo_reprogramacion: tipoReprogramacion
        };
        
        // Validación adicional: fecha no puede ser anterior a hoy
        const fechaNueva = new Date(formData.fecha_nueva);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        if (fechaNueva < hoy) {
            new PNotify({
                title: 'Fecha inválida',
                text: 'No puede reprogramar a una fecha pasada',
                type: 'error',
                delay: 3000
            });
            return;
        }
        
        // Deshabilitar botón
        const $btn = $('#btnReprogramar');
        $btn.prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Reprogramando...');
        
        // Enviar datos
        $.ajax({
            url: '/admin/sesion_recuperacion/reprogramar',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    new PNotify({
                        title: '¡Sesión reprogramada!',
                        text: 'La sesión ha sido reprogramada exitosamente. Se ha enviado una notificación por correo al docente.',
                        type: 'success',
                        delay: 4000
                    });
                    
                    $('#modalReprogramarSesion').modal('hide');
                    
                    // Actualizar la fila de la tabla dinámicamente
                    if (response.sesion) {
                        const sesion = response.sesion;
                        const idSesion = sesion.id_sesion;
                        
                        // Formatear la fecha correctamente (de ISO a DD/MM/YYYY)
                        let fechaFormateada = sesion.fecha_sesion;
                        if (fechaFormateada && fechaFormateada.includes('T')) {
                            // Convertir de ISO a YYYY-MM-DD primero
                            fechaFormateada = fechaFormateada.split('T')[0];
                        }
                        // Convertir de YYYY-MM-DD a DD/MM/YYYY
                        if (fechaFormateada && fechaFormateada.match(/\d{4}-\d{2}-\d{2}/)) {
                            const partes = fechaFormateada.split('-');
                            fechaFormateada = `${partes[2]}/${partes[1]}/${partes[0]}`;
                        }
                        
                        // Buscar el botón de reprogramar con este ID de sesión
                        const $btnReprogramar = $(`.btn-reprogramar[data-id="${idSesion}"], button[data-id="${idSesion}"].btn-reprogramar, .btn-reprogramar[data-sesion-id="${idSesion}"]`);
                        
                        if ($btnReprogramar.length > 0) {
                            // Obtener la fila que contiene este botón
                            const $row = $btnReprogramar.closest('tr');
                            
                            if ($row.length > 0) {
                                // Actualizar celdas por índice (ajustar según tu tabla)
                                const $cells = $row.find('td');
                                
                                // Buscar y actualizar cada celda que contenga los datos
                                $cells.each(function(index) {
                                    const $cell = $(this);
                                    const cellText = $cell.text().trim();
                                    
                                    // Actualizar fecha (formato YYYY-MM-DD o DD/MM/YYYY)
                                    if (cellText.match(/\d{4}-\d{2}-\d{2}/) || cellText.match(/\d{2}\/\d{2}\/\d{4}/)) {
                                        $cell.text(fechaFormateada);
                                    }
                                    
                                    // Actualizar horario (formato HH:MM - HH:MM)
                                    if (cellText.match(/\d{2}:\d{2}\s*-\s*\d{2}:\d{2}/)) {
                                        $cell.text(`${sesion.hora_inicio} - ${sesion.hora_fin}`);
                                    }
                                    
                                    // Actualizar horas recuperadas (solo números con decimales)
                                    if (cellText.match(/^\d+(\.\d+)?$/) && parseFloat(cellText) > 0) {
                                        $cell.text(sesion.horas_recuperadas);
                                    }
                                });
                                
                                // Actualizar el badge de estado a REPROGRAMADA
                                const estadoBadgeHtml = `
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: rgba(255, 152, 0, 0.1); color: #ff9800; border: 1px solid rgba(255, 152, 0, 0.2);">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #ff9800;"></span>
                                        Reprogramada
                                    </span>
                                `;
                                
                                // Buscar la celda de estado y actualizarla
                                $cells.each(function() {
                                    const $cell = $(this);
                                    // Si la celda contiene un span con estilos de badge (tiene border-radius: 20px)
                                    if ($cell.find('span[style*="border-radius: 20px"]').length > 0) {
                                        $cell.html(estadoBadgeHtml);
                                    }
                                });
                                
                                // Actualizar el data-estado del tr
                                $row.attr('data-estado', 'REPROGRAMADA');
                                
                                // Actualizar los data attributes del botón para futuras reprogramaciones
                                $btnReprogramar.attr('data-fecha', fechaFormateada);
                                $btnReprogramar.attr('data-hora-inicio', sesion.hora_inicio);
                                $btnReprogramar.attr('data-hora-fin', sesion.hora_fin);
                                $btnReprogramar.attr('data-aula', sesion.aula);
                            }
                        } else {
                            // Si no se encuentra el botón, recargar la página como fallback
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    }
                    
                    // Resetear el botón
                    $btn.prop('disabled', false);
                    $btn.html('<i class="fas fa-save mr-1"></i> Reprogramar');
                } else {
                    new PNotify({
                        title: 'Error',
                        text: response.message || 'No se pudo reprogramar la sesión',
                        type: 'error',
                        delay: 4000
                    });
                    
                    $btn.prop('disabled', false);
                    $btn.html('<i class="fas fa-save mr-1"></i> Reprogramar');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Ocurrió un error al reprogramar la sesión';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let errorList = '<ul style="margin: 0; padding-left: 20px;">';
                    
                    for (let field in errors) {
                        errors[field].forEach(function(error) {
                            errorList += `<li>${error}</li>`;
                        });
                    }
                    errorList += '</ul>';
                    
                    errorMessage = errorList;
                }
                
                new PNotify({
                    title: 'Error',
                    text: errorMessage,
                    type: 'error',
                    delay: 5000
                });
                
                $btn.prop('disabled', false);
                $btn.html('<i class="fas fa-save mr-1"></i> Reprogramar');
            }
        });
    });

    function validarFormulario() {
        if (!$('#rep_fecha').val()) {
            new PNotify({
                title: 'Campo requerido',
                text: 'Ingrese la fecha de la sesión',
                type: 'error',
                delay: 3000
            });
            return false;
        }
        
        if (!$('#rep_hora_inicio').val() || !$('#rep_hora_fin').val()) {
            new PNotify({
                title: 'Campos requeridos',
                text: 'Ingrese la hora de inicio y fin',
                type: 'error',
                delay: 3000
            });
            return false;
        }
        
        if (!$('#rep_aula').val()) {
            new PNotify({
                title: 'Campo requerido',
                text: 'Ingrese el aula para la sesión',
                type: 'error',
                delay: 3000
            });
            return false;
        }
        
        if (!$('#rep_motivo').val()) {
            new PNotify({
                title: 'Campo requerido',
                text: 'Ingrese el motivo de reprogramación',
                type: 'error',
                delay: 3000
            });
            return false;
        }
        
        return true;
    }

    // ==========================================
    // LIMPIAR AL CERRAR MODAL
    // ==========================================
    
    $('#modalReprogramarSesion').on('hidden.bs.modal', function() {
        resetModal();
        $('#formReprogramarSesion')[0].reset();
        $('#rep_id_sesion').val('');
        datosSesionActual = null;
    });

});
