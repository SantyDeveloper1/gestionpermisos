<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReprogramacionSesionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $docente;
    public $asignatura;
    public $tipoPermiso;
    public $fechaOriginal;
    public $fechaNueva;
    public $horaOriginal;
    public $horaNueva;
    public $aulaOriginal;
    public $aulaNueva;
    public $motivo;
    public $planId;
    public $horasRecuperadas;
    public $urlSistema;

    public function __construct($data)
    {
        $this->docente = $data['docente'] ?? 'Docente';
        $this->asignatura = $data['asignatura'] ?? 'No especificada';
        $this->tipoPermiso = $data['tipoPermiso'] ?? 'No especificado';
        $this->fechaOriginal = $data['fechaOriginal'] ?? '';
        $this->fechaNueva = $data['fechaNueva'] ?? '';
        $this->horaOriginal = $data['horaOriginal'] ?? '';
        $this->horaNueva = $data['horaNueva'] ?? '';
        $this->aulaOriginal = $data['aulaOriginal'] ?? '';
        $this->aulaNueva = $data['aulaNueva'] ?? '';
        $this->motivo = $data['motivo'] ?? '';
        $this->planId = $data['planId'] ?? '';
        $this->horasRecuperadas = $data['horasRecuperadas'] ?? '';
        $this->urlSistema = $data['urlSistema'] ?? url('/');
    }

    public function build()
    {
        return $this->subject('Notificación de Reprogramación de Sesión Académica')
            ->markdown('admin.emails.reprogramacion_sesion');
    }
}
