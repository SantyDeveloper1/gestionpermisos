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
        <div class="subtitulo">EVIDENCIA DE SESIÓN DE RECUPERACIÓN</div>
    </div>

    <hr>

    <!-- DATOS DE LA EVIDENCIA -->
    <div class="section">
        <div><span class="label">ID Evidencia:</span> {{ $evidencia->id_evidencia }}</div>
        <div>
            <span class="label">Tipo de Evidencia:</span>
            <span class="tipo">{{ $evidencia->tipo_evidencia }}</span>
        </div>
        <div><span class="label">Sesión:</span> {{ $evidencia->id_sesion }}</div>
        <div>
            <span class="label">Docente:</span>
            {{ $evidencia->sesionRecuperacion->planRecuperacion->permiso->docente->user->last_name }}
            {{ $evidencia->sesionRecuperacion->planRecuperacion->permiso->docente->user->name }}
        </div>
        <div>
            <span class="label">Fecha de Sesión:</span>
            {{ \Carbon\Carbon::parse($evidencia->sesionRecuperacion->fecha_sesion)->format('d/m/Y') }}
        </div>
        <div>
            <span class="label">Fecha de Subida:</span>
            {{ $evidencia->fecha_subida->format('d/m/Y H:i') }}
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