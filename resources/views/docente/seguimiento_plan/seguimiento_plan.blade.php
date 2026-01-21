@extends('docente.template.layout')

@section('titleGeneral', 'Ejecución de Recuperación de Clases')

@section('sectionGeneral')
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #9b59b6;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header {
            background-color: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
        }

        .header-top {
            background-color: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .subtitle {
            color: #7f8c8d;
            font-size: 1rem;
            margin: 0;
        }

        .docente-info {
            background-color: var(--light-color);
            padding: 15px 20px;
            border-radius: var(--border-radius);
            text-align: right;
        }

        .docente-name {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .docente-id {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .plan-selector {
            background-color: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
        }

        .selector-title {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .plan-select {
            width: 100%;
            padding: 12px 15px;
            border-radius: var(--border-radius);
            border: 2px solid #ddd;
            font-size: 1rem;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }

        .plan-select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .plan-select option {
            padding: 10px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .status-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            transition: transform 0.3s ease;
        }

        .status-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.3rem;
            color: white;
        }

        .card-title {
            font-size: 1.4rem;
            color: var(--primary-color);
        }

        .plan-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            min-width: 180px;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .status-list {
            list-style: none;
        }

        .status-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px 15px;
            border-radius: var(--border-radius);
            background-color: #f8f9fa;
            transition: all 0.2s;
        }

        .status-item:hover {
            background-color: #edf2f7;
        }

        .status-indicator {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 12px;
        }

        .status-text {
            font-weight: 500;
            flex-grow: 1;
        }

        .status-count {
            background-color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .status-item.active {
            border-left: 4px solid var(--secondary-color);
            background-color: #e8f4fc;
        }

        .sessions-section {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-top: 25px;
        }

        .section-title {
            color: var(--primary-color);
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .session-list {
            margin-top: 20px;
        }

        .session-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            border-left: 4px solid #ddd;
        }

        .session-date {
            font-weight: bold;
            min-width: 120px;
            color: var(--dark-color);
        }

        .session-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            margin: 0 15px;
        }

        .session-desc {
            flex-grow: 1;
        }

        .evidence-indicator {
            margin-left: auto;
            display: flex;
            align-items: center;
        }

        .evidence-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            margin-right: 10px;
        }

        .evidence-icon {
            font-size: 1.2rem;
        }

        .summary-section {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-top: 25px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .summary-card {
            padding: 20px;
            border-radius: var(--border-radius);
            text-align: center;
            color: #000000;
        }

        .summary-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
            color: #000000;
        }

        .summary-label {
            font-size: 1.1rem;
            opacity: 0.8;
            color: #000000;
        }

        /* Colores para estados del plan */
        .estado-presentado {
            background-color: var(--warning-color);
            color: white;
        }

        .estado-aprobado {
            background-color: var(--success-color);
            color: white;
        }

        .estado-observado {
            background-color: var(--danger-color);
            color: white;
        }

        /* Colores para estados de sesión */
        .estado-programada {
            background-color: #3498db;
            color: white;
        }

        .estado-reprogramada {
            background-color: #9b59b6;
            color: white;
        }

        .estado-realizada {
            background-color: #27ae60;
            color: white;
        }

        .estado-cancelada {
            background-color: #7f8c8d;
            color: white;
        }

        /* Colores para tipos de evidencia */
        .tipo-acta {
            background-color: #1abc9c;
            color: white;
        }

        .tipo-asistencia {
            background-color: #3498db;
            color: white;
        }

        .tipo-captura {
            background-color: #f39c12;
            color: white;
        }

        .tipo-otro {
            background-color: #95a5a6;
            color: white;
        }

        .sin-evidencia {
            background-color: #e74c3c;
            color: white;
        }

        /* Colores para indicadores */
        .ind-presentado {
            background-color: var(--warning-color);
        }

        .ind-aprobado {
            background-color: var(--success-color);
        }

        .ind-observado {
            background-color: var(--danger-color);
        }

        .ind-programada {
            background-color: #3498db;
        }

        .ind-reprogramada {
            background-color: #9b59b6;
        }

        .ind-realizada {
            background-color: #27ae60;
        }

        .ind-cancelada {
            background-color: #7f8c8d;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }

        .no-data i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #bdc3c7;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .status-grid {
                grid-template-columns: 1fr;
            }

            .header-top {
                flex-direction: column;
                text-align: center;
            }

            .docente-info {
                text-align: center;
                margin-top: 15px;
            }

            .session-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .session-date,
            .session-status,
            .evidence-indicator {
                margin: 5px 0;
            }

            .evidence-indicator {
                margin-left: 0;
                margin-top: 10px;
            }

            .info-row {
                flex-direction: column;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
    <div class="header-top">
        <div>
            <h1>Seguimiento de Plan de Recuperación</h1>
            <p class="subtitle">Visualización del estado del plan, sesiones y evidencias</p>
        </div>
    </div>
    <div class="plan-selector"
        style="background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-bottom: 25px;">
        <div class="selector-title" style="color: #2c3e50; margin-bottom: 15px; font-size: 1.2rem;">Seleccionar Plan de
            Recuperación</div>
        <select id="planSelect" class="plan-select form-control select2bs4">
            <option value="" disabled selected>-- Cargando planes --</option>
        </select>
    </div>

    <!-- Contenido dinámico según el plan seleccionado -->
    <div id="planContent">
        <!-- Este contenido se actualizará según la selección -->
        <div class="no-data">
            <i class="fas fa-spinner fa-spin"></i>
            <h3>Cargando información...</h3>
            <p>Por favor espere mientras se cargan los datos</p>
        </div>
    </div>

    <!-- Incluir el script JavaScript al final para asegurar que jQuery esté cargado -->
    <script>
        // Esperar a que el documento esté listo y jQuery esté disponible
        document.addEventListener('DOMContentLoaded', function () {
            // Cargar el script dinámicamente
            var script = document.createElement('script');
            script.src = '{{ asset("viewresources/docente/seguimiento_plan/seguimiento_plan.js") }}';
            script.defer = true;
            document.body.appendChild(script);
        });
    </script>
@endsection