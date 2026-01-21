'use strict';

// CSRF
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

/* =========================
   Funciones de Evidencias
========================= */

/**
 * Descargar evidencia
 */
function downloadEvidence(evidenceId) {
    const url = `${_urlBase}/admin/evidencia_recuperacion/download/${evidenceId}`;
    
    // Usar window.location.href para que el servidor fuerce la descarga
    // El controlador ya envía las cabeceras correctas (Content-Disposition: attachment)
    window.location.href = url;
    
    new PNotify({
        title: 'Descarga iniciada',
        text: 'El archivo se está descargando...',
        type: 'info',
        delay: 2000
    });
}

/**
 * Ver evidencia en modal o nueva pestaña
 */
function viewEvidence(evidenceId) {
    const url = `${_urlBase}/admin/evidencia_recuperacion/ver/${evidenceId}`;
    window.open(url, '_blank');
}