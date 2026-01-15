@component('mail::message')
# Notificación de cambio de estado de permiso

Estimado/a **{{ $docente }}**,

Se le informa que la solicitud de permiso registrada en el sistema ha cambiado al siguiente estado:

@component('mail::panel')
📌 **Estado actual:** {{ $estado }}
@endcomponent

@if(!empty($comentario))
    **Observaciones del departamento académico:**

    > {{ $comentario }}
@endif

## Detalles de la solicitud

@component('mail::table')
| Campo | Información |
|-------|-------------|
| Docente | {{ $docente }} |
| Tipo de permiso | {{ $tipoPermiso ?? 'No especificado' }} |
| Fecha de solicitud | {{ $fechaSolicitud ?? now()->format('d/m/Y') }} |
| Fecha del permiso | {{ $fechaPermiso ?? 'No especificada' }} |
| Periodo | {{ $periodo ?? 'No especificado' }} |
| Motivo | {{ $motivo ?? 'No especificado' }} |
| Validado por | {{ $validador ?? 'Departamento Académico' }} |
@endcomponent

@component('mail::button', ['url' => $urlSistema ?? '#'])
Consultar en el sistema
@endcomponent

Atentamente,
**Departamento Académico**
**Escuela Profesional de Ingeniería Informática y Sistemas**

@endcomponent