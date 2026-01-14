<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Permisos Docentes</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 25px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .titulo1 {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .titulo2 {
            font-size: 12px;
            margin-top: 3px;
        }

        .info {
            margin: 10px 0;
        }

        .info div {
            margin: 3px 0;
        }

        .info span {
            display: inline-block;
            min-width: 220px;
            font-weight: bold;
        }

        .banner {
            background: #f5f5f5;
            color: #333;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            padding: 8px;
            border: 2px solid #666;
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            display: table-header-group;
        }

        th {
            background: transparent;
            color: #000;
            padding: 6px 4px;
            font-size: 10px;
            border: 1px solid #000;
            font-weight: bold;
        }

        tbody tr {
            page-break-inside: avoid;
        }

        td {
            padding: 5px 3px;
            border: 1px solid #000;
            font-size: 9px;
            vertical-align: top;
        }

        .total-row td {
            background: transparent;
            color: #000;
            font-weight: bold;
            text-align: right;
            padding: 6px 4px;
            border: 1px solid #000;
        }

        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 12px 0;
        }
    </style>
</head>
<body>

<!-- ENCABEZADO -->
<div class="header">
    <div class="titulo1">UNIVERSIDAD NACIONAL MICAELA BASTIDAS DE APURIMAC</div>
    <div class="titulo1">VICERRECTORADO ACADÉMICO</div>
    <div class="titulo2">REPORTE DE PERMISOS DE DOCENTES</div>
</div>

<!-- INFORMACIÓN GENERAL -->
<div class="info">
    <div><span>FACULTAD:</span> INGENIERÍA</div>
    <div><span>DEPARTAMENTO ACADÉMICO:</span> ESCUELA PROFESIONAL DE INGENIERÍA INFORMÁTICA Y SISTEMAS</div>

    <div>
        <span>AÑO Y SEMESTRE ACADÉMICO:</span>
        {{ $semestre->codigo_Academico ?? '---' }} - {{ $semestre->anio_academico ?? '---' }}
    </div>

    <div>
        <span>FECHA DE REPORTE:</span>
        {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>
</div>

<!-- BANNER -->
<div class="banner">
    LISTA DE DOCENTES QUE SOLICITARON PERMISO
</div>

<!-- TABLA -->
<table>
    <thead>
        <tr>
            <th>N°</th>
            <th>DOCENTE</th>
            <th>TIPO PERMISO</th>
            <th>F. INICIO</th>
            <th>F. FIN</th>
            <th>MOTIVO</th>
            <th>ESTADO</th>
        </tr>
    </thead>

    <tbody>
        @php $totalPermisos = 0; @endphp

        @forelse ($permisos as $index => $p)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td>
                    {{ $p->docente->user->last_name ?? '' }}
                    {{ $p->docente->user->name ?? '' }}
                </td>

                <td style="text-align:center">
                    {{ $p->tipoPermiso->nombre ?? '---' }}
                </td>

                <td style="text-align:center">
                    {{ \Carbon\Carbon::parse($p->fecha_inicio)->format('d/m/Y') }}
                </td>

                <td style="text-align:center">
                    {{ \Carbon\Carbon::parse($p->fecha_fin)->format('d/m/Y') }}
                </td>

                <td>
                    {{ $p->motivo ?? '---' }}
                </td>

                <td style="text-align:center">
                    {{ strtoupper($p->estado_permiso) }}
                </td>
            </tr>

            @php $totalPermisos++; @endphp
        @empty
            <tr>
                <td colspan="7" style="text-align:center; padding: 20px;">
                    No hay permisos registrados para este semestre
                </td>
            </tr>
        @endforelse
    </tbody>

</table>

</body>
</html>