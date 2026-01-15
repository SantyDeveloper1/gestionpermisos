'use strict';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(() => {

    // Calcular días automáticamente cuando cambian las fechas
    $('input[name="fecha_inicio"], input[name="fecha_fin"]').on('change', function() {
        const fechaInicio = $('input[name="fecha_inicio"]').val();
        const fechaFin = $('input[name="fecha_fin"]').val();
        
        if (fechaInicio && fechaFin) {
            const inicio = new Date(fechaInicio);
            const fin = new Date(fechaFin);
            
            if (fin >= inicio) {
                const diffTime = Math.abs(fin - inicio);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 para incluir ambos días
                $('input[name="dias_permiso"]').val(diffDays);
            } else {
                new PNotify({
                    title: 'Fechas inválidas',
                    text: 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
                    type: 'warning'
                });
                $('input[name="fecha_fin"]').val('');
                $('input[name="dias_permiso"]').val('');
            }
        }
    });

    // Inicializar FormValidation
    $('#frmPermisoInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
        live: 'enabled',
        trigger: null,
        fields: {

            id_docente: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Seleccione un docente.</b>'
                    }
                }
            },

            id_tipo_permiso: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Seleccione un tipo de permiso.</b>'
                    }
                }
            },

            fecha_inicio: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Ingrese la fecha de inicio.</b>'
                    },
                    date: {
                        format: 'YYYY-MM-DD',
                        message: '<b style="color:red;">Formato de fecha inválido.</b>'
                    }
                }
            },

            fecha_fin: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Ingrese la fecha de fin.</b>'
                    },
                    date: {
                        format: 'YYYY-MM-DD',
                        message: '<b style="color:red;">Formato de fecha inválido.</b>'
                    }
                }
            },

            horas_afectadas: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Ingrese las horas afectadas.</b>'
                    },
                    numeric: {
                        message: '<b style="color:red;">Debe ser un número válido.</b>'
                    },
                    greaterThan: {
                        value: 0,
                        inclusive: true,
                        message: '<b style="color:red;">Debe ser mayor o igual a 0.</b>'
                    }
                }
            },

            fecha_solicitud: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Ingrese la fecha de solicitud.</b>'
                    },
                    date: {
                        format: 'YYYY-MM-DD',
                        message: '<b style="color:red;">Formato de fecha inválido.</b>'
                    }
                }
            },

            motivo: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Ingrese el motivo del permiso.</b>'
                    },
                    stringLength: {
                        min: 10,
                        message: '<b style="color:red;">El motivo debe tener al menos 10 caracteres.</b>'
                    }
                }
            }

        }
    });
});


function sendFrmPermisoInsert() {

    $('#frmPermisoInsert').data('formValidation').resetForm();
    $('#frmPermisoInsert').data('formValidation').validate();

    let isValid = $('#frmPermisoInsert').data('formValidation').isValid();

    if (!isValid) {
        new PNotify({
            title: 'No se pudo proceder',
            text: 'Complete y corrija toda la información del formulario.',
            type: 'error'
        });
        return;
    }

    swal({
        title: 'Confirmar operación',
        text: '¿Realmente desea registrar este permiso?',
        icon: 'warning',
        buttons: ['No, cancelar', 'Sí, proceder']
    }).then((proceed) => {
        if (proceed) {
            registrarPermiso();
        }
    });
}

function registrarPermiso() {
    // Crear FormData para soportar archivos
    const formData = new FormData($('#frmPermisoInsert')[0]);

    $.ajax({
        url: $('#frmPermisoInsert').attr('action'),
        type: 'POST',
        data: formData,
        processData: false,  // No procesar los datos
        contentType: false,  // No establecer contentType
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                new PNotify({
                    title: '¡Éxito!',
                    text: response.message || 'Permiso registrado correctamente.',
                    type: 'success'
                });

                // Cerrar modal
                $('#nuevoPermisoModal').modal('hide');

                // Limpiar formulario
                $('#frmPermisoInsert')[0].reset();
                $('#frmPermisoInsert').data('formValidation').resetForm();
                
                // Resetear Select2
                $('.select2').val(null).trigger('change');
                
                // Resetear label del archivo
                $('.custom-file-label').text('Seleccionar archivo...');

                // Agregar nueva fila a la tabla
                if (response.permiso) {
                    agregarFilaTabla(response.permiso);
                } else {
                    // Si no viene el objeto, recargar la página
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            } else {
                new PNotify({
                    title: 'Error',
                    text: response.message || 'No se pudo registrar el permiso.',
                    type: 'error'
                });
            }
        },
        error: function(xhr) {
            let errorMsg = 'Ocurrió un error al registrar el permiso.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                errorMsg = Object.values(errors).flat().join('<br>');
            }

            new PNotify({
                title: 'Error',
                text: errorMsg,
                type: 'error'
            });
        }
    });
}

function agregarFilaTabla(permiso) {
    const table = $('#tablaExample2').DataTable();
    
    // Formatear fechas
    const fechaInicio = formatearFecha(permiso.fecha_inicio);
    const fechaFin = formatearFecha(permiso.fecha_fin);
    const fechaSolicitud = formatearFecha(permiso.fecha_solicitud);
    const fechaResolucion = permiso.fecha_resolucion ? formatearFecha(permiso.fecha_resolucion) : '';

    // Determinar clase del badge según estado
    const estadoClass = `badge-${permiso.estado_permiso.toLowerCase()}`;
    
    // Determinar si requiere recuperación
    const requiereRecupero = permiso.tipo_permiso.requiere_recupero || false;

    // Construir HTML de la fila
    const nuevaFila = `
        <tr id="permisoRow${permiso.id_permiso}">
            <td class="text-center">${table.rows().count() + 1}</td>
            <td>
                <strong>${permiso.docente.appDocente}</strong><br>
                <small class="text-muted">${permiso.docente.nombres}</small>
            </td>
            <td>${permiso.tipo_permiso.nombre}</td>
            <td class="text-center">
                <strong>${fechaInicio}</strong><br>
                <small class="text-muted">al</small><br>
                <strong>${fechaFin}</strong>
            </td>
            <td class="text-center">
                <span class="badge badge-primary">${permiso.dias_permiso} días</span><br>
                <small class="text-muted">${permiso.horas_afectadas} horas</small>
            </td>
            <td class="text-center">
                ${getPlanRecuperacionBadge(permiso)}
            </td>
            <td class="text-center">
                <span class="badge-estado ${estadoClass}">
                    ${permiso.estado_permiso}
                </span>
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-primary btn-action" onclick="viewPermiso('${permiso.id_permiso}')" title="Ver detalles">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-warning btn-action" onclick="editPermiso('${permiso.id_permiso}')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                ${permiso.estado_permiso === 'SOLICITADO' ? `
                    <button class="btn btn-sm btn-success btn-action" onclick="aprobarPermiso('${permiso.id_permiso}')" title="Aprobar">
                        <i class="fas fa-check"></i>
                    </button>
                ` : ''}
                <button class="btn btn-sm btn-danger btn-action" onclick="deletePermiso('${permiso.id_permiso}')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    // Agregar fila a la tabla usando DataTables
    table.row.add($(nuevaFila)).draw(false);
}

function getPlanRecuperacionBadge(permiso) {
    const requiereRecupero = permiso.tipo_permiso.requiere_recupero || false;
    
    if (!requiereRecupero) {
        return '<span class="badge badge-secondary"><i class="fas fa-times-circle"></i> No Requiere</span>';
    }
    
    // Si requiere recuperación, verificar si existe plan
    if (permiso.plan_recuperacion) {
        const estadoPlan = permiso.plan_recuperacion.estado_plan;
        
        if (estadoPlan === 'APROBADO') {
            return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Completada</span>';
        } else if (estadoPlan === 'PRESENTADO') {
            return '<span class="badge badge-info"><i class="fas fa-clock"></i> En Proceso</span>';
        } else if (estadoPlan === 'OBSERVADO') {
            return '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Observado</span>';
        }
    }
    
    // Si requiere pero no tiene plan
    return '<span class="badge badge-warning"><i class="fas fa-file-alt"></i> Sin Plan</span>';
}

function formatearFecha(fecha) {
    if (!fecha) return '';
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const anio = date.getFullYear();
    return `${dia}/${mes}/${anio}`;
}