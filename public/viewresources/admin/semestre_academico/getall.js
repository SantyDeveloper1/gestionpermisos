'use strict';

// ================================
// CSRF GLOBAL
// ================================
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(() => {

    // ================================
    // VALIDACIÓN FORMULARIO SEMESTRE
    // ================================
    $('#frmSemestreAcademicoInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '.notValidate'],
        live: 'enabled',
        trigger: null,
        message: '<b style="color:#9d9d9d;">Campo inválido.</b>',
        fields: {

            codigo_Academico: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">Ingrese el código académico.</b>' },
                    stringLength: {
                        max: 20,
                        message: '<b style="color:red;">Máximo 20 caracteres permitidos.</b>'
                    }
                }
            },

            anio_academico: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">Ingrese el año académico.</b>' },
                    between: {
                        min: 2000,
                        max: 2100,
                        message: '<b style="color:red;">Ingrese un año válido (2000 - 2100).</b>'
                    }
                }
            },

            FechaInicioAcademico: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">Ingrese la fecha de inicio.</b>' }
                }
            },

            FechaFinAcademico: {
                validators: {
                    notEmpty: { message: '<b style="color:red;">Ingrese la fecha de fin.</b>' }
                }
            },

            DescripcionAcademico: {
                validators: {
                    stringLength: { max: 255, message: '<b style="color:red;">Máximo 255 caracteres.</b>' }
                }
            }
        }
    });

    $('#frmSemestreAcademicoInsert').on('status.field.fv', () => {
        $('#btnGuardarSemestre').prop('disabled', false);
    });
});


// ================================
// ENVÍO AJAX SEMESTRE
// ================================
function sendFrmSemestreAcademicoInsert() {

    const form = $('#frmSemestreAcademicoInsert');
    const fv = form.data('formValidation');

    fv.validate();

    if (!fv.isValid()) {
        new PNotify({
            title: 'No se pudo proceder',
            text: 'Complete correctamente la información.',
            type: 'error'
        });
        return;
    }

    swal({
        title: 'Confirmar registro',
        text: '¿Registrar nuevo semestre académico?',
        icon: 'warning',
        buttons: ['Cancelar', 'Sí, registrar']
    }).then(ok => {

        if (!ok) return;

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',

            success: function(res) {

                if (res.status === "success") {

                    const tabla = $("#tablaExample2").DataTable();

                    // Badge para "Estado"
                    let badgeEstado = '';
                    if (res.data.estado === 'Planificado') {
                        badgeEstado = '<span class="badge badge-info px-3 py-2">Planificado</span>';
                    } else if (res.data.estado === 'Activo') {
                        badgeEstado = '<span class="badge badge-success px-3 py-2">Activo</span>';
                    } else if (res.data.estado === 'Cerrado') {
                        badgeEstado = '<span class="badge badge-danger px-3 py-2">Cerrado</span>';
                    } else {
                        badgeEstado = '<span class="badge badge-secondary px-3 py-2">—</span>';
                    }

                    // Badge para "Es Actual"
                    const badgeActual = res.data.es_actual 
                        ? '<span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle"></i> Actual</span>'
                        : '<span class="badge badge-secondary px-3 py-2"><i class="fas fa-times-circle"></i> No</span>';

                    // Botones de acción según el estado
                    let botonesAccion = `
                        <button class="btn btn-sm btn-warning"
                            onclick="showEditSemestre('${res.data.IdSemestreAcademico}')">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn btn-danger btn-sm"
                            onclick="deleteSemestre('${res.data.IdSemestreAcademico}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;

                    // Agregar botones según el estado
                    if (res.data.estado === 'Planificado') {
                        botonesAccion += `
                            <button class="btn btn-primary btn-sm btnCambiarEstado"
                                onclick="cambiarEstadoSemestre('${res.data.IdSemestreAcademico}', 'Activo')">
                                <i class="fas fa-play"></i> Activar
                            </button>
                        `;
                    } else if (res.data.estado === 'Activo') {
                        botonesAccion += `
                            <button class="btn btn-danger btn-sm btnCambiarEstado"
                                onclick="cambiarEstadoSemestre('${res.data.IdSemestreAcademico}', 'Cerrado')">
                                <i class="fas fa-lock"></i> Cerrar
                            </button>
                        `;
                    } else if (res.data.estado === 'Cerrado') {
                        botonesAccion += `
                            <button class="btn btn-info btn-sm btnCambiarEstado"
                                onclick="cambiarEstadoSemestre('${res.data.IdSemestreAcademico}', 'Planificado')">
                                <i class="fas fa-undo"></i> Reabrir
                            </button>
                        `;
                    }

                    const row = tabla.row.add([
                        res.data.numero,           // #
                        res.data.codigo,           // Código
                        res.data.anio,             // Año
                        res.data.inicio,           // Fecha Inicio
                        res.data.fin,              // Fecha Fin
                        badgeEstado,               // Estado
                        badgeActual,               // Es Actual
                        res.data.created_at || '', // Registrado
                        botonesAccion              // Acciones
                    ]).draw(false).node();

                    $(row).attr('id', 'semRow' + res.data.IdSemestreAcademico);

                    // Estilos
                    $(row).find('td').addClass('text-center');
                    $(row).find('td:eq(1)').addClass('tdCodigo');
                    $(row).find('td:last').addClass('accionesSemestre');

                    // Reordenar índices
                    tabla.rows().every(function(i) {
                        this.cell(i, 0).data(i + 1);
                    });

                    new PNotify({
                        title: 'Éxito',
                        text: 'Semestre académico registrado correctamente.',
                        type: 'success'
                    });

                    // Reset form
                    form[0].reset();
                    fv.resetForm(true);
                }
            },

            error: function(xhr) {
                if (xhr.status === 422) {
                    const errs = xhr.responseJSON.errors;
                    new PNotify({
                        title: 'Validación',
                        text: Object.values(errs).join('<br>'),
                        type: 'error'
                    });
                } else {
                    new PNotify({
                        title: 'Error',
                        text: 'Ocurrió un problema inesperado.',
                        type: 'error'
                    });
                }
            }
        });
    });
}