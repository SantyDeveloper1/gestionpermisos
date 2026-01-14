// Función para vista previa del PDF de reporte por semestre
function vistaPreviewPdfSemestre() {
    const semestreId = document.getElementById('semestre_id').value;
    
    if (!semestreId) {
        new PNotify({
            title: 'Error',
            text: 'Debe seleccionar un semestre',
            type: 'error'
        });
        return;
    }
    
    // Construir la URL para el PDF
    const url = `/admin/reportes/pdf/semestre/${semestreId}`;
    
    // Abrir en una nueva ventana
    window.open(url, '_blank');
}

// Función para descargar el PDF de reporte por semestre
function descargarPdfSemestre() {
    const semestreId = document.getElementById('semestre_id').value;
    
    if (!semestreId) {
        new PNotify({
            title: 'Error',
            text: 'Debe seleccionar un semestre',
            type: 'error'
        });
        return;
    }
    
    // Construir la URL para descargar el PDF
    const url = `/admin/reportes/pdf/descargar/semestre/${semestreId}`;
    
    // Crear un enlace temporal y hacer clic para descargar
    const link = document.createElement('a');
    link.href = url;
    link.download = `reporte_semestre_${semestreId}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
