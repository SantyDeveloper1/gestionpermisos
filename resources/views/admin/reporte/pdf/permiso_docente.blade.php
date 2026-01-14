<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Permisos del Docente</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 25px; }
        .header { text-align: center; margin-bottom: 15px; }
        .titulo1 { font-weight: bold; text-transform: uppercase; font-size: 14px; }
        .titulo2 { font-size: 12px; margin-top: 3px; }
        .info { margin: 10px 0; }
        .info span { display: inline-block; min-width: 220px; font-weight: bold; }
        .banner { background:#f5f5f5; font-weight:bold; text-align:center; padding:8px; margin:15px 0; border:2px solid #666; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #000; padding:5px; font-size:9px; }
        th { font-size:10px; }
    </style>
</head>
<body>

<!-- ENCABEZADO -->
<div class="header">
    <div class="titulo1">UNIVERSIDAD NACIONAL MICAELA BASTIDAS DE APURIMAC</div>
    <div class="titulo1">VICERRECTORADO ACADÉMICO</div>
    <div class="titulo1">FACULTAD DE INGENIERÍA</div>
    <div class="titulo1">DEPARTAMENTO ACADÉMICO DE INGENIERÍA DE SISTEMAS</div>
    <div class="titulo2">REPORTE DE PERMISOS DEL DOCENTE</div>
</div>

<!-- INFORMACIÓN GENERAL -->
<div class="info">

    <div>
        <span>DOCENTE:</span>
        {{ $docente->user->last_name }} {{ $docente->user->name }}
    </div>

    <div>
        <span>SEMESTRE CONSULTADO:</span>
        @if($semestre)
            {{ $semestre->codigo_Academico }} - {{ $semestre->anio_academico }}
        @else
            TODOS LOS SEMESTRES
        @endif
    </div>

    <div>
        <span>FECHA DE REPORTE:</span>
        {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

</div>

<!-- BANNER -->
<div class="banner">
    HISTORIAL DE PERMISOS DEL DOCENTE
</div>

<!-- TABLA -->
<table>
    <thead>
        <tr>
            <th>N°</th>
            <th>SEMESTRE</th>
            <th>TIPO PERMISO</th>
            <th>F. INICIO</th>
            <th>F. FIN</th>
            <th>MOTIVO</th>
            <th>ESTADO</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($permisos as $index => $p)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>

                <td style="text-align:center">
                    {{ $p->semestreAcademico->codigo_Academico ?? 'N/A' }} - {{ $p->semestreAcademico->anio_academico ?? '' }}
                </td>

                <td style="text-align:center">
                    {{ $p->tipoPermiso->nombre }}
                </td>

                <td style="text-align:center">
                    {{ \Carbon\Carbon::parse($p->fecha_inicio)->format('d/m/Y') }}
                </td>

                <td style="text-align:center">
                    {{ \Carbon\Carbon::parse($p->fecha_fin)->format('d/m/Y') }}
                </td>

                <td>{{ $p->motivo }}</td>

                <td style="text-align:center">
                    {{ strtoupper($p->estado_permiso) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:15px;">
                    No existen permisos registrados
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>