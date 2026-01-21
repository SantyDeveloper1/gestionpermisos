// Función para vista previa del PDF de reporte por docente
function vistaPreviewPdfDocente() {
    const docenteId = document.getElementById('docente_id').value;
    const semestreId = document.getElementById('docente_semestre_id').value;
    
    if (!docenteId) {
        new PNotify({
            title: 'Error',
            text: 'Debe seleccionar un docente',
            type: 'error'
        });
        return;
    }
    
    // Construir la URL para el PDF
    let url = `/admin/reportes/pdf/docente/${docenteId}`;
    
    // Agregar semestre si está seleccionado
    if (semestreId) {
        url += `?semestre_id=${semestreId}`;
    }
    
    // Abrir en una nueva ventana
    window.open(url, '_blank');
}

// Función para descargar el PDF de reporte por docente
function descargarPdfDocente() {
    const docenteId = document.getElementById('docente_id').value;
    const semestreId = document.getElementById('docente_semestre_id').value;
    
    if (!docenteId) {
        new PNotify({
            title: 'Error',
            text: 'Debe seleccionar un docente',
            type: 'error'
        });
        return;
    }
    
    // Construir la URL para descargar el PDF
    let url = `/admin/reportes/pdf/descargar/docente/${docenteId}`;
    
    // Agregar semestre si está seleccionado
    if (semestreId) {
        url += `?semestre_id=${semestreId}`;
    }
    
    // Redirigir a la URL de descarga
    // El servidor enviará las cabeceras correctas para forzar la descarga
    window.location.href = url;
}