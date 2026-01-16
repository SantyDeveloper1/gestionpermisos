@component('mail::message')
# Notificación de Reprogramación de Sesión Académica

Estimado/a **{{ $docente }}**,

Le informamos que la sesión programada para la fecha **{{ $fechaOriginal }}** ha sido reprogramada.

@component('mail::panel')
📋 **Motivo de reprogramación:**

{{ $motivo }}
@endcomponent

## Detalle de la Reprogramación

@component('mail::table')
| Concepto | Fecha Original | Nueva Fecha |
|----------|----------------|-------------|
| **Fecha** | {{ $fechaOriginal }} | {{ $fechaNueva }} |
| **Horario** | {{ $horaOriginal }} | {{ $horaNueva }} |
| **Aula** | {{ $aulaOriginal }} | {{ $aulaNueva }} |
@endcomponent

## Información de la Sesión

@component('mail::table')
| Campo | Información |
|-------|-------------|
| Asignatura | {{ $asignatura }} |
| Horas de recuperación | {{ $horasRecuperadas }} |
| Estado | REPROGRAMADA |
@endcomponent

@component('mail::button', ['url' => $urlSistema ?? '#'])
Consultar en el sistema
@endcomponent

Por favor, tome nota de los nuevos horarios y aula asignada para la sesión de recuperación.

Atentamente,
**Departamento Académico**
**Escuela Profesional de Ingeniería Informática y Sistemas**

@endcomponent