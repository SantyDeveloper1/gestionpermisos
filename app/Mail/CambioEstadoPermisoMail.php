<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CambioEstadoPermisoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $docente;
    public $estado;
    public $tipoPermiso;
    public $fechaSolicitud;
    public $fechaPermiso;
    public $periodo;
    public $motivo;
    public $comentario;
    public $validador;
    public $urlSistema;

    public function __construct($data)
    {
        $this->docente = $data['docente'] ?? 'Docente';
        $this->estado = $data['estado'] ?? 'Sin estado';
        $this->tipoPermiso = $data['tipoPermiso'] ?? null;
        $this->fechaSolicitud = $data['fechaSolicitud'] ?? null;
        $this->fechaPermiso = $data['fechaPermiso'] ?? null;
        $this->periodo = $data['periodo'] ?? null;
        $this->motivo = $data['motivo'] ?? null;
        $this->comentario = $data['comentario'] ?? null;
        $this->validador = $data['validador'] ?? 'Departamento Académico';
        $this->urlSistema = $data['urlSistema'] ?? url('/');
    }

    public function build()
    {
        return $this->subject('Actualización de estado de permiso')
            ->markdown('admin.emails.cambio_estado');
    }
}
