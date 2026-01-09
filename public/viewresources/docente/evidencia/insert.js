// Variables globales
let selectedFiles = [];

// Inicialización cuando el DOM está listo
$(document).ready(function() {
    initializeFileUpload();
    initializeSelect2();
    initializeFormValidation();
});

/**
 * Inicializar Select2 para el selector de sesiones
 */
function initializeSelect2() {
    if ($('.select2').length) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Buscar sesión...',
            allowClear: true
        });
    }
}

/**
 * Inicializar FormValidation
 */
function initializeFormValidation() {
    $('#frmEvidenciaInsert').formValidation({
        framework: 'bootstrap',
        excluded: [':disabled', ':hidden', ':not(:visible)', '[class*="notValidate"]'],
        live: 'enabled',
        message: '<b style="color: #9d9d9d;">Asegúrese que realmente no necesita este valor.</b>',
        trigger: null,
        fields: {
            tipo_evidencia: {
                validators: {
                    notEmpty: {
                        message: '<b style="color:red;">Seleccione un tipo de evidencia.</b>'
                    }
                }
            },
            id_sesion_select: {
                validators: {
                    callback: {
                        message: '<b style="color:red;">Seleccione una sesión de recuperación.</b>',
                        callback: function(value, validator, $field) {
                            // Validar que al menos uno de los dos campos tenga valor
                            const hiddenSesion = $('#id_sesion').val();
                            return value !== '' || hiddenSesion !== '';
                        }
                    }
                }
            }
        }
    });
}

/**
 * Inicializar funcionalidad de carga de archivos
 */
function initializeFileUpload() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');

    if (!uploadZone || !fileInput) return;

    // Drag and drop events
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    // Click to upload
    uploadZone.addEventListener('click', (e) => {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I') {
            fileInput.click();
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
}

/**
 * Manejar archivos seleccionados
 */
function handleFiles(files) {
    const validExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
    const maxSize = 10 * 1024 * 1024; // 10MB

    Array.from(files).forEach(file => {
        const extension = file.name.split('.').pop().toLowerCase();
        
        // Validar extensión
        if (!validExtensions.includes(extension)) {
            new PNotify({
                title: 'Error',
                text: `El archivo ${file.name} no tiene una extensión válida`,
                type: 'error'
            });
            return;
        }

        // Validar tamaño
        if (file.size > maxSize) {
            new PNotify({
                title: 'Error',
                text: `El archivo ${file.name} excede el tamaño máximo de 10MB`,
                type: 'error'
            });
            return;
        }

        // Agregar a la lista de archivos seleccionados
        selectedFiles.push(file);
    });

    // Mostrar preview de archivos
    displayFilePreview();
}

/**
 * Mostrar preview de archivos seleccionados
 */
function displayFilePreview() {
    const container = document.getElementById('filePreviewContainer');
    const previewsDiv = document.getElementById('filePreviews');

    if (selectedFiles.length === 0) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    previewsDiv.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'file-preview';
        
        const fileIcon = getFileIcon(file.name);
        const fileSize = formatFileSize(file.size);

        fileDiv.innerHTML = `
            <div class="${getFileClass(file.name)}">
                <i class="file-icon ${fileIcon}"></i>
            </div>
            <div class="file-info">
                <div class="file-name">${file.name}</div>
                <div class="file-size">${fileSize}</div>
            </div>
            <i class="fas fa-times file-remove" onclick="removeFile(${index})"></i>
        `;

        previewsDiv.appendChild(fileDiv);
    });
}

/**
 * Obtener ícono según tipo de archivo
 */
function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    const icons = {
        'pdf': 'fas fa-file-pdf',
        'jpg': 'fas fa-file-image',
        'jpeg': 'fas fa-file-image',
        'png': 'fas fa-file-image',
        'doc': 'fas fa-file-word',
        'docx': 'fas fa-file-word',
        'xls': 'fas fa-file-excel',
        'xlsx': 'fas fa-file-excel'
    };
    return icons[extension] || 'fas fa-file';
}

/**
 * Obtener clase CSS según tipo de archivo
 */
function getFileClass(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    if (extension === 'pdf') return 'file-pdf';
    if (['jpg', 'jpeg', 'png'].includes(extension)) return 'file-image';
    if (['doc', 'docx'].includes(extension)) return 'file-word';
    if (['xls', 'xlsx'].includes(extension)) return 'file-excel';
    return 'file-generic';
}

/**
 * Formatear tamaño de archivo
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Remover archivo de la lista
 */
function removeFile(index) {
    selectedFiles.splice(index, 1);
    displayFilePreview();
}

/**
 * Limpiar formulario
 */
function clearForm() {
    selectedFiles = [];
    document.getElementById('frmEvidenciaInsert').reset();
    displayFilePreview();
    
    // Resetear FormValidation y limpiar todos los mensajes de error
    const fv = $('#frmEvidenciaInsert').data('formValidation');
    if (fv) {
        fv.resetForm(true); // true para limpiar todos los mensajes
    }
    
    // Resetear Select2
    if ($('.select2').length) {
        $('.select2').val(null).trigger('change');
    }
}

/**
 * Enviar evidencia - Siguiendo el patrón de sesion_recuperacion
 */
function submitEvidence() {
    // Resetear y validar el formulario con FormValidation
    $('#frmEvidenciaInsert').data('formValidation').resetForm();
    $('#frmEvidenciaInsert').data('formValidation').validate();

    let isValid = $('#frmEvidenciaInsert').data('formValidation').isValid();

    if (!isValid) {
        new PNotify({
            title: 'Formulario incompleto',
            text: 'Complete y corrija toda la información del formulario.',
            type: 'error'
        });
        return;
    }

    // Validar que haya archivos seleccionados
    if (selectedFiles.length === 0) {
        new PNotify({
            title: 'Archivo requerido',
            text: 'Debe seleccionar al menos un archivo para cargar como evidencia.',
            type: 'error'
        });
        return;
    }

    // Obtener datos del formulario
    const form = document.getElementById('frmEvidenciaInsert');
    
    // Obtener el ID de sesión correcto
    const sesionSelect = document.querySelector('select[name="id_sesion_select"]');
    const sesionId = sesionSelect ? sesionSelect.value : form.querySelector('input[name="id_sesion"]')?.value;

    // Validar tipo de evidencia
    const tipoEvidencia = form.querySelector('select[name="tipo_evidencia"]')?.value;

    // Obtener descripción
    const descripcion = form.querySelector('input[name="descripcion"]')?.value || '';

    // Procesar cada archivo
    let uploadedCount = 0;
    let totalFiles = selectedFiles.length;
    let hasErrors = false;

    selectedFiles.forEach((file, index) => {
        const formData = new FormData();
        formData.append('_token', form.querySelector('input[name="_token"]').value);
        formData.append('id_sesion', sesionId);
        formData.append('tipo_evidencia', tipoEvidencia);
        formData.append('descripcion', descripcion);
        formData.append('archivo', file);

        // Enviar archivo usando jQuery AJAX (igual que sesion_recuperacion)
        $.ajax({
            url: _urlBase + '/docente/evidencia_recuperacion/insert',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                uploadedCount++;
                
                // Agregar la evidencia a la tabla dinámicamente
                if (response.evidencia) {
                    agregarFilaEvidencia(response.evidencia);
                }
                
                if (uploadedCount === totalFiles && !hasErrors) {
                    new PNotify({
                        title: '¡Éxito!',
                        text: `${totalFiles} evidencia(s) registrada(s) exitosamente`,
                        type: 'success'
                    });
                    
                    // Limpiar formulario
                    clearForm();
                }
            },
            error: function(xhr) {
                hasErrors = true;
                let errorMessage = 'Error al subir el archivo: ' + file.name;
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                }

                new PNotify({
                    title: 'Error',
                    text: errorMessage,
                    type: 'error'
                });
            }
        });
    });
}

/**
 * Scroll a la sección de carga
 */
function scrollToUpload() {
    document.querySelector('.upload-section').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}

/**
 * Agregar una nueva fila a la tabla de evidencias dinámicamente
 */
function agregarFilaEvidencia(evidencia) {
    // Formatear fecha - usar fecha_subida en lugar de created_at
    let fechaFormateada = 'Fecha no disponible';
    if (evidencia.fecha_subida) {
        // Parsear la fecha correctamente
        const fecha = new Date(evidencia.fecha_subida.replace(' ', 'T'));
        if (!isNaN(fecha.getTime())) {
            fechaFormateada = fecha.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }) + ' ' + fecha.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
    
    // Determinar badge de tipo
    let tipoBadge = '';
    let tipoIcon = '';
    let badgeClass = '';
    
    if (evidencia.tipo_evidencia === 'ACTA') {
        badgeClass = 'badge-acta';
        tipoIcon = 'fa-file-signature';
        tipoBadge = 'Acta';
    } else if (evidencia.tipo_evidencia === 'ASISTENCIA') {
        badgeClass = 'badge-asistencia';
        tipoIcon = 'fa-clipboard-list';
        tipoBadge = 'Asistencia';
    } else if (evidencia.tipo_evidencia === 'CAPTURA') {
        badgeClass = 'badge-captura';
        tipoIcon = 'fa-camera';
        tipoBadge = 'Captura';
    } else {
        badgeClass = 'badge-otro';
        tipoIcon = 'fa-file-alt';
        tipoBadge = 'Otro';
    }
    
    // Determinar ícono de archivo
    const fileExt = evidencia.archivo.split('.').pop().toLowerCase();
    let fileClass = 'file-generic';
    let fileIcon = 'fa-file';
    
    if (fileExt === 'pdf') {
        fileClass = 'file-pdf';
        fileIcon = 'fa-file-pdf';
    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
        fileClass = 'file-image';
        fileIcon = 'fa-file-image';
    } else if (['doc', 'docx'].includes(fileExt)) {
        fileClass = 'file-word';
        fileIcon = 'fa-file-word';
    } else if (['xls', 'xlsx'].includes(fileExt)) {
        fileClass = 'file-excel';
        fileIcon = 'fa-file-excel';
    }
    
    // Obtener la instancia de DataTable
    const table = $('#tablaExample2').DataTable();
    
    if (table) {
        // Usar la API de DataTables para agregar la fila correctamente
        const newRow = table.row.add([
            // Columna 1: N°
            `<div style="text-align: center; color: var(--dark-gray); font-weight: 600;">${table.rows().count() + 1}</div>`,
            // Columna 2: Sesión
            `<div>
                <strong style="color: var(--dark-gray);">Sesión #${evidencia.id_sesion}</strong><br>
                <small style="color: var(--medium-gray);">${evidencia.sesion_info || ''}</small>
            </div>`,
            // Columna 3: Tipo
            `<span class="evidence-type-badge ${badgeClass}">
                <i class="fas ${tipoIcon}"></i>
                ${tipoBadge}
            </span>`,
            // Columna 4: Archivo
            `<div class="evidence-file">
                <div class="file-icon-table ${fileClass}">
                    <i class="fas ${fileIcon}"></i>
                </div>
                <div class="file-info-table">
                    <div class="file-name-table">${evidencia.archivo_nombre || evidencia.archivo}</div>
                    <div class="file-meta">
                        <span><i class="fas fa-file-alt mr-1"></i>${fileExt.toUpperCase()}</span>
                    </div>
                </div>
            </div>`,
            // Columna 5: Fecha Subida
            `<div style="color: var(--dark-gray);">${fechaFormateada}</div>`,
            // Columna 6: Acciones
            `<div class="action-buttons-evidence">
                <a href="${['jpg', 'jpeg', 'png', 'gif'].includes(fileExt) ? _urlBase + '/admin/evidencia_recuperacion/ver/' + evidencia.id_evidencia : _urlBase + '/' + evidencia.archivo}" target="_blank" class="btn-action-evidence btn-view-evidence" title="Ver">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="${_urlBase}/${evidencia.archivo}" download class="btn-action-evidence btn-download-evidence" title="Descargar">
                    <i class="fas fa-download"></i>
                </a>
                <button class="btn-action-evidence btn-delete-evidence" onclick="deleteEvidencia('${evidencia.id_evidencia}')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`
        ]);
        
        // Agregar atributo data-type a la fila
        $(newRow.node()).attr('data-type', evidencia.tipo_evidencia).addClass('slide-in');
        
        // Redibujar la tabla
        table.draw(false);
        
        // Animar la nueva fila
        const filaElement = newRow.node();
        if (filaElement) {
            filaElement.style.opacity = '0';
            setTimeout(() => {
                filaElement.style.transition = 'opacity 0.5s ease-in';
                filaElement.style.opacity = '1';
            }, 100);
        }
    }
}