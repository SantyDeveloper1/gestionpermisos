<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Evidencia de Recuperación</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 25px;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .titulo {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .subtitulo {
            font-size: 12px;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 14px;
        }

        .label {
            font-weight: bold;
            width: 170px;
            display: inline-block;
        }

        .tipo {
            font-weight: bold;
            padding: 3px 6px;
            border-bottom: 1px solid #000;
            display: inline-block;
        }

        .preview {
            text-align: center;
            margin-top: 18px;
        }

        img {
            max-width: 100%;
            max-height: 600px;
        }

        .footer {
            margin-top: 25px;
            font-size: 9px;
            text-align: center;
            color: #555;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 10px 0 14px;
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <div class="header">
        <div class="titulo">UNIVERSIDAD NACIONAL MICAELA BASTIDAS DE APURÍMAC</div>
        <div class="titulo">VICERRECTORIA ACADÉMICO</div>
        <div class="titulo">FACULTADE DE INGENIERÍA</div>
        <div class="titulo">DEPARTAMENTO ACADEMICO DE INFORMÁTICA Y SISTEMAS</div>
        <div class="subtitulo">FORMATO DE RECUPERACION DE SESIONES ACADÉMICAS</div>
    </div>

    <hr>

    <!-- DATOS DE LA EVIDENCIA -->
    <div class="section">
        <div><span class="label">ESCUELA PROFESIONAL::</span> Ingeniería Informática y Sistemas </div>
        <div>
            <span class="label">NOMBRE DEL DOCENTE:</span>
            {{ $evidencia->sesionRecuperacion->planRecuperacion->permiso->docente->user->last_name }}
            {{ $evidencia->sesionRecuperacion->planRecuperacion->permiso->docente->user->name }}
        </div>
        <div>
            <span class="label">NOMBRE ASIGNATURA:</span>
            {{ $evidencia->sesionRecuperacion->asignatura->nom_asignatura ?? 'No especificada' }}
        </div>
        <div>
            <span class="label">CORRESPONDIENTE AL DIA:</span>
            {{ $evidencia->sesionRecuperacion->planRecuperacion->permiso->docente->user->document }}
        </div>
        <div>
            <span class="label">TEMA DESARROLLADO:</span> {{ $evidencia->sesionRecuperacion->tema ?? 'No especificado' }}
        </div>
        <div>
            <span class="label">RECUPERACION:</span> 
            Ambiente {{ $evidencia->sesionRecuperacion->aula ?? 'No especificado' }} 
            Hora: {{ \Carbon\Carbon::parse($evidencia->sesionRecuperacion->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($evidencia->sesionRecuperacion->hora_fin)->format('H:i') }} 
            Fecha: día: {{ \Carbon\Carbon::parse($evidencia->sesionRecuperacion->fecha_sesion)->format('d') }} 
            mes: {{ \Carbon\Carbon::parse($evidencia->sesionRecuperacion->fecha_sesion)->locale('es')->translatedFormat('F') }} 
            año: {{ \Carbon\Carbon::parse($evidencia->sesionRecuperacion->fecha_sesion)->format('Y') }}
        </div>
        <div>
            <span class="label">FECHA:</span>
            {{ \Carbon\Carbon::parse($evidencia->sesionRecuperacion->fecha_sesion)->format('d/m/Y') }}
        </div>
        <div>
            <span class="label">Fecha de Subida:</span>
            {{ $evidencia->fecha_subida->format('d/m/Y H:i') }}
        </div>
        <div>
            <span class="label">MOTIVO:</span>
            <span class="tipo">{{ $evidencia->tipo_evidencia }}</span>
        </div>

        @if($evidencia->descripcion)
            <div>
                <span class="label">Descripción:</span>
                {{ $evidencia->descripcion }}
            </div>
        @endif
    </div>

    <hr>

    <!-- ARCHIVO DE EVIDENCIA -->
    <div class="section preview">
        <strong>Archivo de Evidencia</strong>
        <br><br>

        @php
            $ext = strtolower(pathinfo($evidencia->archivo, PATHINFO_EXTENSION));
            $ruta = public_path($evidencia->archivo);
        @endphp

        @if(in_array($ext, ['jpg','jpeg','png']))
            <img src="{{ $ruta }}">
        @else
            <p>Archivo adjunto: {{ basename($evidencia->archivo) }}</p>
        @endif
    </div>

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión de Permisos Docentes
    </div>

</body>
</html>