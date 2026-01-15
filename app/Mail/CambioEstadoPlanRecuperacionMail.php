<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CambioEstadoPlanRecuperacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $docente;
    public $estado;
    public $tipoPermiso;
    public $fechaPresentacion;
    public $totalHoras;
    public $observacion;
    public $urlSistema;

    public function __construct($data)
    {
        $this->docente = $data['docente'] ?? 'Docente';
        $this->estado = $data['estado'] ?? 'Sin estado';
        $this->tipoPermiso = $data['tipoPermiso'] ?? null;
        $this->fechaPresentacion = $data['fechaPresentacion'] ?? null;
        $this->totalHoras = $data['totalHoras'] ?? null;
        $this->observacion = $data['observacion'] ?? null;
        $this->urlSistema = $data['urlSistema'] ?? url('/');
    }

    public function build()
    {
        return $this->subject('Actualización de estado de Plan de Recuperación')
            ->markdown('admin.emails.cambio_estado_plan');
    }
}
