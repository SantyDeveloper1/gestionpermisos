@component('mail::message')
# Notificación de cambio de estado de Plan de Recuperación

Estimado/a **{{ $docente }}**,

Se le informa que el Plan de Recuperación registrado en el sistema ha cambiado al siguiente estado:

@component('mail::panel')
📌 **Estado actual:** {{ $estado }}
@endcomponent

@if(!empty($observacion))
    **Observaciones del departamento académico:**

    > {{ $observacion }}
@endif

## Detalles del Plan de Recuperación

@component('mail::table')
| Campo | Información |
|-------|-------------|
| Docente | {{ $docente }} |
| Tipo de permiso | {{ $tipoPermiso ?? 'No especificado' }} |
| Fecha de presentación | {{ $fechaPresentacion ?? 'No especificada' }} |
| Total de horas a recuperar | {{ $totalHoras ?? 'No especificado' }} horas |
| Estado actual | {{ $estado }} |
@endcomponent

@component('mail::button', ['url' => $urlSistema ?? '#'])
Consultar en el sistema
@endcomponent

Atentamente,
**Departamento Académico**
**Escuela Profesional de Ingeniería Informática y Sistemas**

@endcomponent