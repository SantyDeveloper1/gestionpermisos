@extends('admin.template.layout')

@section('titleGeneral', 'Ejecución de Recuperación de Clases')

@section('sectionGeneral')

    <style>
        :root {
            --primary-blue: #008BDC;
            --secondary-blue: #00A3E8;
            --light-blue: #E6F4FF;
            --success-green: #00b894;
            --warning-orange: #fdcb6e;
            --danger-red: #e17055;
            --info-cyan: #00cec9;
            --dark-gray: #2d3436;
            --medium-gray: #636e72;
            --light-gray: #dfe6e9;
            --purple: #6c5ce7;
            --teal: #00cec9;
        }

        .execution-module {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 139, 220, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .module-header-gradient {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            padding: 30px 35px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .module-header-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.1;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .module-title-section {
            position: relative;
            z-index: 2;
        }

        .module-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .module-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 400;
            margin: 0;
        }

        .progress-summary {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin: -40px 35px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 3;
            border: 1px solid rgba(0, 139, 220, 0.1);
        }

        .progress-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card-progress {
            background: white;
            border-radius: 12px;
            padding: 25px;
            border: 2px solid var(--light-gray);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card-progress:hover {
            transform: translateY(-5px);
            border-color: var(--primary-blue);
            box-shadow: 0 10px 25px rgba(0, 139, 220, 0.15);
        }

        .stat-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 2rem;
            color: white;
        }

        .icon-completed {
            background: linear-gradient(135deg, var(--success-green) 0%, #55efc4 100%);
        }

        .icon-pending {
            background: linear-gradient(135deg, var(--warning-orange) 0%, #ffeaa7 100%);
        }

        .icon-total {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        }

        .icon-progress {
            background: linear-gradient(135deg, var(--purple) 0%, #a29bfe 100%);
        }

        .stat-content h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: var(--dark-gray);
        }

        .stat-content h3 span {
            font-size: 1.5rem;
            color: var(--medium-gray);
            font-weight: 500;
        }

        .stat-content p {
            margin: 0;
            color: var(--medium-gray);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .progress-bar-container {
            margin-top: 15px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: var(--medium-gray);
            font-weight: 600;
        }

        .progress-bar {
            height: 12px;
            background: var(--light-gray);
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 6px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            transition: width 1s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.4) 50%,
                    transparent 100%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .action-bar-execution {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin: 30px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            border: 2px dashed rgba(0, 139, 220, 0.2);
        }

        .btn-execution {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary-execution {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
        }

        .btn-primary-execution:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 139, 220, 0.3);
            color: white;
        }

        .btn-success-execution {
            background: linear-gradient(135deg, var(--success-green) 0%, #55efc4 100%);
            color: white;
        }

        .btn-warning-execution {
            background: linear-gradient(135deg, var(--warning-orange) 0%, #ffeaa7 100%);
            color: var(--dark-gray);
        }

        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin: 0 35px 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--light-gray);
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .filter-item-enhanced {
            position: relative;
        }

        .filter-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-gray);
            font-size: 0.9rem;
        }

        .filter-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }

        .filter-input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 139, 220, 0.1);
            outline: none;
        }

        .execution-table-container {
            margin: 0 35px 40px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        .table-execution {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
        }

        .table-execution thead {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        }

        .table-execution th {
            padding: 20px 18px;
            color: white;
            font-weight: 600;
            text-align: left;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        .table-execution th:after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .table-execution th:last-child:after {
            display: none;
        }

        .table-execution td {
            padding: 22px 18px;
            border-bottom: 1px solid var(--light-gray);
            vertical-align: middle;
            transition: background-color 0.3s ease;
        }

        .table-execution tbody tr {
            transition: all 0.3s ease;
        }

        .table-execution tbody tr:hover {
            background: linear-gradient(90deg, rgba(0, 139, 220, 0.05) 0%, transparent 100%);
            transform: translateX(5px);
        }

        .status-indicator-execution {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-completed {
            background: rgba(0, 184, 148, 0.1);
            color: var(--success-green);
            border: 1px solid rgba(0, 184, 148, 0.2);
        }

        .status-pending {
            background: rgba(253, 203, 110, 0.1);
            color: #e17055;
            border: 1px solid rgba(253, 203, 110, 0.2);
        }

        .status-in-progress {
            background: rgba(108, 92, 231, 0.1);
            color: var(--purple);
            border: 1px solid rgba(108, 92, 231, 0.2);
        }

        .dot-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .dot-completed {
            background: var(--success-green);
        }

        .dot-pending {
            background: #e17055;
        }

        .dot-in-progress {
            background: var(--purple);
        }

        .modal-execution {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
        }

        .modal-header-execution {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 25px 30px;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .modal-header-execution::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50px, -50px);
        }

        .modal-title-execution {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-step-indicator {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }

        .form-step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--light-gray);
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--medium-gray);
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            color: white;
            transform: scale(1.1);
        }

        .step.completed .step-circle {
            background: var(--success-green);
            border-color: var(--success-green);
            color: white;
        }

        .step-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--medium-gray);
            text-align: center;
        }

        .step.active .step-label {
            color: var(--primary-blue);
            font-weight: 700;
        }

        .hours-counter {
            background: linear-gradient(135deg, var(--light-blue) 0%, #c3e0ff 100%);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
            border: 2px solid var(--primary-blue);
            position: relative;
            overflow: hidden;
        }

        .hours-counter::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg,
                    transparent 30%,
                    rgba(255, 255, 255, 0.3) 50%,
                    transparent 70%);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        .hours-counter h4 {
            color: var(--dark-gray);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hours-display-large {
            font-size: 4rem;
            font-weight: 800;
            color: var(--primary-blue);
            line-height: 1;
            margin: 10px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hours-total {
            color: var(--medium-gray);
            font-size: 1.1rem;
            margin-top: 5px;
        }

        .validation-alert {
            background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
            border: 2px solid #e17055;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            position: relative;
            overflow: hidden;
        }

        .validation-alert::before {
            content: '⚠';
            position: absolute;
            right: -10px;
            top: -10px;
            font-size: 5rem;
            opacity: 0.2;
            color: #e17055;
        }

        .alert-content {
            position: relative;
            z-index: 2;
        }

        .alert-title {
            color: #856404;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .module-header-gradient {
                padding: 20px;
            }

            .module-title {
                font-size: 1.5rem;
            }

            .progress-summary {
                margin: -20px 20px 20px;
                padding: 20px;
            }

            .progress-stats {
                grid-template-columns: 1fr;
            }

            .action-bar-execution {
                margin: 20px;
                flex-direction: column;
                align-items: stretch;
            }

            .filter-section,
            .execution-table-container {
                margin: 0 20px 20px;
            }

            .table-execution {
                display: block;
                overflow-x: auto;
            }

            .modal-title-execution {
                font-size: 1.3rem;
            }
        }

        /* Animaciones adicionales */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilos para la validación en tiempo real */
        .valid-feedback {
            display: none;
            color: var(--success-green);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .invalid-feedback {
            display: none;
            color: var(--danger-red);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .form-control:valid~.valid-feedback,
        .form-control:invalid~.invalid-feedback {
            display: block;
        }

        .session-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 2px solid var(--light-gray);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .session-card:hover {
            border-color: var(--primary-blue);
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 139, 220, 0.1);
        }

        .session-card.completed {
            border-color: var(--success-green);
            background: linear-gradient(135deg, rgba(0, 184, 148, 0.05) 0%, transparent 100%);
        }

        .session-card.in-progress {
            border-color: var(--purple);
            background: linear-gradient(135deg, rgba(108, 92, 231, 0.05) 0%, transparent 100%);
        }

        /* Estilos modernos para botones de acción */
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            font-size: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-icon:hover::before {
            width: 100px;
            height: 100px;
        }

        .btn-icon:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .btn-icon:active {
            transform: translateY(-1px) scale(0.98);
        }

        .btn-icon i {
            position: relative;
            z-index: 1;
        }

        .btn-view {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        }

        .btn-view:hover {
            background: linear-gradient(135deg, #007acc 0%, #0092d8 100%);
        }

        .btn-edit {
            background: linear-gradient(135deg, var(--warning-orange) 0%, #ffeaa7 100%);
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #f0b95e 0%, #fdd97a 100%);
        }

        .btn-approve {
            background: linear-gradient(135deg, var(--success-green) 0%, #55efc4 100%);
        }

        .btn-approve:hover {
            background: linear-gradient(135deg, #00a884 0%, #45dfb4 100%);
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--danger-red) 0%, #fab1a0 100%);
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #d16045 0%, #f0a190 100%);
        }
    </style>

    <section class="content">
        <div class="container-fluid">
            <!-- Módulo de Ejecución de Recuperación -->
            <div class="execution-module">
                <!-- Encabezado del Módulo -->
                <div class="module-header-gradient">
                    <div class="module-title-section">
                        <h1 class="module-title">
                            <i class="fas fa-play-circle"></i>
                            Ejecución de Recuperación
                        </h1>
                    </div>
                </div>

                <br>

                <!-- Estadísticas de Progreso -->
                @if($planSeleccionado && $estadisticasPlan)
                    {{-- Estadísticas dinámicas del plan seleccionado --}}
                    <div style="padding: 0 35px;">
                        <div class="progress-stats">
                            <div class="stat-card-progress fade-in">
                                <div class="stat-icon-wrapper icon-completed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ $estadisticasPlan['horas_completadas'] }} <span>horas</span></h3>
                                    <p>Recuperación Completada</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-label">
                                            <span>Progreso</span>
                                            <span>{{ $estadisticasPlan['porcentaje_completado'] }}%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill"
                                                style="width: {{ $estadisticasPlan['porcentaje_completado'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card-progress fade-in" style="animation-delay: 0.1s;">
                                <div class="stat-icon-wrapper icon-pending">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ $estadisticasPlan['horas_pendientes'] }} <span>horas</span></h3>
                                    <p>Pendiente de Recuperación</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-label">
                                            <span>Restante</span>
                                            <span>{{ $estadisticasPlan['porcentaje_pendiente'] }}%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill"
                                                style="width: {{ $estadisticasPlan['porcentaje_pendiente'] }}%; background: linear-gradient(90deg, #e17055 0%, #fab1a0 100%);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card-progress fade-in" style="animation-delay: 0.2s;">
                                <div class="stat-icon-wrapper icon-total">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ $estadisticasPlan['total_horas'] }} <span>horas</span></h3>
                                    <p>Total a Recuperar</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-label">
                                            <span>Meta total</span>
                                            <span>100%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill"
                                                style="width: 100%; background: linear-gradient(90deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card-progress fade-in" style="animation-delay: 0.3s;">
                                <div class="stat-icon-wrapper icon-progress">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ $estadisticasPlan['sesiones_activas'] }} <span>sesiones</span></h3>
                                    <p>Sesiones Activas</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-label">
                                            <span>En progreso</span>
                                            <span>{{ $estadisticasPlan['porcentaje_sesiones'] }}%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill"
                                                style="width: {{ min(100, $estadisticasPlan['porcentaje_sesiones']) }}%; background: linear-gradient(90deg, var(--purple) 0%, #a29bfe 100%);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Estadísticas generales cuando no hay plan seleccionado --}}
                    <div style="padding: 0 35px;">
                        <div class="progress-stats">
                            <div class="stat-card-progress fade-in">
                                <div class="stat-icon-wrapper icon-completed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ count($sesionesActivas) }} <span>sesiones</span></h3>
                                    <p>Sesiones Activas</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-label">
                                            <span>Estado</span>
                                            <span>Activo</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card-progress fade-in" style="animation-delay: 0.1s;">
                                <div class="stat-icon-wrapper icon-pending">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ $horasRecuperadasHoy }} <span>horas</span></h3>
                                    <p>Recuperadas Hoy</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-label">
                                            <span>Hoy</span>
                                            <span>{{ date('d/m/Y') }}</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill"
                                                style="width: {{ $horasRecuperadasHoy > 0 ? 100 : 0 }}%; background: linear-gradient(90deg, #e17055 0%, #fab1a0 100%);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card-progress fade-in" style="animation-delay: 0.2s;">
                                <div class="stat-icon-wrapper icon-total">
                                    <i class="fas fa-list-alt"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ count($planes) }} <span>planes</span></h3>
                                    <p>Planes Registrados</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-label">
                                            <span>Total</span>
                                            <span>Todos</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill"
                                                style="width: 100%; background: linear-gradient(90deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card-progress fade-in" style="animation-delay: 0.3s;">
                                <div class="stat-icon-wrapper icon-progress">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>{{ count($sesionesEjecucion) }} <span>sesiones</span></h3>
                                    <p>Total de Sesiones</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-label">
                                            <span>Registradas</span>
                                            <span>100%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill"
                                                style="width: 100%; background: linear-gradient(90deg, var(--purple) 0%, #a29bfe 100%);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Banner de Plan Seleccionado -->
                @if(isset($planSeleccionado) && $planSeleccionado)
                    <div
                        style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 15px; padding: 25px; margin: 30px 35px; border-left: 5px solid var(--primary-blue); box-shadow: 0 5px 20px rgba(0, 139, 220, 0.15);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 15px;">
                                    Filtrando por Plan de Recuperación
                                </h5>
                                <div class="row">

                                    <div class="col-md-4">
                                        <div style="margin-bottom: 10px;">
                                            <small
                                                style="color: var(--medium-gray); display: block; font-size: 0.85rem;">Docente</small>
                                            <strong style="color: var(--dark-gray);">
                                                {{ $planSeleccionado->permiso->docente->user->last_name }},
                                                {{ $planSeleccionado->permiso->docente->user->name }}
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="margin-bottom: 10px;">
                                            <small style="color: var(--medium-gray); display: block; font-size: 0.85rem;">Horas
                                                a Recuperar</small>
                                            <strong style="color: var(--primary-blue); font-size: 1.2rem;">
                                                {{ $planSeleccionado->total_horas_recuperar }} horas
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div style="margin-bottom: 10px;">
                                            <small
                                                style="color: var(--medium-gray); display: block; font-size: 0.85rem;">Estado</small>
                                            @if($planSeleccionado->estado_plan == 'APROBADO')
                                                <span
                                                    style="background: rgba(0, 184, 148, 0.2); color: var(--success-green); padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                                    {{ $planSeleccionado->estado_plan }}
                                                </span>
                                            @else
                                                <span
                                                    style="background: rgba(0, 139, 220, 0.2); color: var(--primary-blue); padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                                    {{ $planSeleccionado->estado_plan }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Barra de Acciones -->
                <div class="action-bar-execution">
                    <div>
                        <h4 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 5px;">
                            <i class="fas fa-tasks mr-2"></i>
                            Gestión de Sesiones Activas
                        </h4>
                        <p style="color: var(--medium-gray); margin-bottom: 0; font-size: 0.95rem;">
                            {{ count($sesionesActivas) }} sesiones en progreso | {{ $horasRecuperadasHoy }} horas
                            recuperadas hoy
                        </p>
                    </div>
                </div>

                <!-- Tabla de Sesiones de Ejecución -->
                <div class="execution-table-container">
                    <table id="tablaExample2" class="table-execution table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>N° #</th>
                                <th class="none">Plan</th>
                                <th>Docente</th>
                                <th>Curso</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Horas</th>
                                <th class="none">Aula</th>
                                <th>Estado</th>
                                <th class="all">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sesionesEjecucion as $sesion)
                                <tr id="sesionRow{{ $sesion->id_sesion }}" class="fade-in"
                                    data-estado="{{ $sesion->estado_sesion }}" data-modalidad="{{ $sesion->modalidad }}"
                                    data-plan="{{ $sesion->planRecuperacion->id_plan }}">

                                    <td>
                                        <div style="color: var(--dark-gray); text-align: center;">
                                            {{ $loop->iteration }}
                                        </div>
                                    </td>

                                    <!-- Plan -->
                                    <td>
                                        <div style="font-weight: 600; color: var(--dark-gray);">
                                            Plan #{{ $sesion->planRecuperacion->id_plan }}
                                        </div>
                                        <small style="color: var(--medium-gray);">
                                            {{ $sesion->planRecuperacion->total_horas_recuperar }} horas totales
                                        </small>
                                    </td>

                                    <!-- Docente -->
                                    <td>
                                        <div style="font-weight: 600; color: var(--dark-gray);">
                                            {{ $sesion->planRecuperacion->permiso->docente->user->last_name }}
                                            {{ $sesion->planRecuperacion->permiso->docente->user->name }}
                                        </div>
                                    </td>

                                    <!-- Curso (Asignatura) -->
                                    <td>
                                        <div style="color: var(--dark-gray);">
                                            {{ $sesion->asignatura->nom_asignatura ?? 'No especificada' }}
                                        </div>
                                    </td>

                                    <!-- Fecha -->
                                    <td>
                                        <div style=" color: var(--dark-gray);">
                                            {{ \Carbon\Carbon::parse($sesion->fecha_sesion)->format('d/m/Y') }}
                                        </div>
                                    </td>

                                    <!-- Horario -->
                                    <td>
                                        <div style="color: var(--dark-gray);">
                                            @if($sesion->hora_inicio && $sesion->hora_fin)
                                                <span style="color: var(--dark-gray);">
                                                    {{ \Carbon\Carbon::parse($sesion->hora_inicio)->format('H:i') }}
                                                </span>
                                                <span style="color: var(--dark-gray);">
                                                    - {{ \Carbon\Carbon::parse($sesion->hora_fin)->format('H:i') }}
                                                </span>
                                            @else
                                                <small style="color: var(--medium-gray);">No especificado</small>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Horas -->
                                    <td>
                                        <div style="color: var(--dark-gray);">
                                            <span style="color: var(--dark-gray);">
                                                {{ $sesion->horas_recuperadas }}
                                            </span>
                                            <br>
                                            <small style="color: var(--medium-gray);">horas</small>
                                        </div>
                                    </td>



                                    <!-- Aula (hidden) -->
                                    <td class="text-center">
                                        <div style="font-weight: 500; color: var(--medium-gray);">
                                            <i class="fas fa-door-open mr-1"></i>
                                            {{ $sesion->aula ?? 'No asignada' }}
                                        </div>
                                    </td>

                                    <!-- Estado -->
                                    <td>
                                        @if($sesion->estado_sesion == 'VALIDADA')
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: rgba(0, 139, 220, 0.1); color: #008bdc; border: 1px solid rgba(0, 139, 220, 0.2);">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #008bdc;"></span>
                                                Validada
                                            </span>
                                        @elseif($sesion->estado_sesion == 'REALIZADA')
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: rgba(0, 184, 148, 0.1); color: #00b894; border: 1px solid rgba(0, 184, 148, 0.2);">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #00b894;"></span>
                                                Realizada
                                            </span>
                                        @elseif($sesion->estado_sesion == 'REPROGRAMADA')
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: rgba(255, 152, 0, 0.1); color: #ff9800; border: 1px solid rgba(255, 152, 0, 0.2);">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #ff9800;"></span>
                                                Reprogramada
                                            </span>
                                        @elseif($sesion->estado_sesion == 'CANCELADA')
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: rgba(225, 112, 85, 0.1); color: #e17055; border: 1px solid rgba(225, 112, 85, 0.2);">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #e17055;"></span>
                                                Cancelada
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: rgba(253, 203, 110, 0.1); color: #f39c12; border: 1px solid rgba(253, 203, 110, 0.2);">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #f39c12;"></span>
                                                Programada
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Acciones -->
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ url('admin/evidencia_recuperacion?sesion_id=' . $sesion->id_sesion) }}"
                                                class="btn-icon btn-view" title="Ver evidencias de esta sesión">
                                                <i class="fas fa-file-contract"></i>
                                            </a>
                                            @if($sesion->estado_sesion != 'VALIDADA')
                                                <button class="btn-icon btn-edit btn-cambiar-estado"
                                                    data-sesion-id="{{ $sesion->id_sesion }}"
                                                    data-estado-actual="{{ $sesion->estado_sesion }}" title="Cambiar estado">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                            @if(!in_array($sesion->estado_sesion, ['REALIZADA', 'CANCELADA']))
                                                <button class="btn-icon btn-warning btn-reprogramar"
                                                    data-sesion-id="{{ $sesion->id_sesion }}"
                                                    data-fecha="{{ $sesion->fecha_sesion }}"
                                                    data-hora-inicio="{{ $sesion->hora_inicio }}"
                                                    data-hora-fin="{{ $sesion->hora_fin }}" data-aula="{{ $sesion->aula }}"
                                                    title="Reprogramar sesión">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </button>
                                            @endif
                                            <button class="btn-icon btn-delete"
                                                onclick="eliminarSesion('{{ $sesion->id_sesion }}')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para validar estado de sesión -->
    <div class="modal fade" id="modalValidarSesion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Validar Estado de Sesión</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="mensajeValidacion"></p>
                    <div class="mb-3">
                        <label for="estadoSesion" class="form-label">Estado Actual:</label>
                        <input type="text" class="form-control" id="estadoActual" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nuevoEstado" class="form-label">Seleccionar Nuevo Estado:</label>
                        <select class="form-control" id="nuevoEstado">
                            <option value="">Seleccionar estado</option>
                            <option value="PROGRAMADA">Programada</option>
                            <option value="REPROGRAMADA">Reprogramada</option>
                            <option value="REALIZADA">Realizada</option>
                            <option value="CANCELADA">Cancelada</option>
                        </select>
                    </div>
                    <div class="mb-3" id="divComentario" style="display: none;">
                        <label for="comentario" class="form-label">Comentario (opcional):</label>
                        <textarea class="form-control" id="comentario" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarCambio">Confirmar Cambio</button>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL DETALLES DE SESIÓN -->
    <div class="modal fade" id="detalleSesionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-execution">
                <div class="modal-header modal-header-execution">
                    <h5 class="modal-title-execution">
                        <i class="fas fa-info-circle"></i>
                        Detalles de Sesión
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" id="detalleSesionContent">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL REPROGRAMAR SESIÓN CON PASOS -->
    <div class="modal fade" id="modalReprogramarSesion" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"> <i class="fas fa-calendar-alt mr-2"></i> Reprogramar sesión de
                        recuperación - <span id="modalStepTitle">Paso 1 de 3</span> </h5> <button type="button"
                        class="close text-white" data-dismiss="modal"> <span>&times;</span> </button>
                </div>
                <form id="formReprogramarSesion" novalidate> @csrf <input type="hidden" name="id_sesion" id="rep_id_sesion">
                    <input type="hidden" name="total_horas_asignatura" id="total_horas_asignatura">
                    <div class="modal-body"> <!-- Indicador de pasos -->
                        <div class="steps-progress mb-4">
                            <div class="step-indicator d-flex justify-content-between">
                                <div class="step active" data-step="1">
                                    <div class="step-circle">1</div>
                                    <div class="step-label">Seleccionar motivo</div>
                                </div>
                                <div class="step" data-step="2">
                                    <div class="step-circle">2</div>
                                    <div class="step-label">Confirmar asignatura</div>
                                </div>
                                <div class="step" data-step="3">
                                    <div class="step-circle">3</div>
                                    <div class="step-label">Reprogramar</div>
                                </div>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" id="stepProgressBar" style="width: 33%;"></div>
                            </div>
                        </div> <!-- Paso 1: Selección de motivo -->
                        <div class="step-content step-1 active">
                            <div class="text-center mb-4">
                                <h5>Seleccione el motivo de reprogramación</h5>
                                <p class="text-muted">Esta selección determinará el proceso a seguir</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card card-option" data-tipo="permiso">
                                        <div class="card-body text-center"> <i
                                                class="fas fa-file-contract fa-2x text-primary mb-3"></i>
                                            <h5>Permiso Institucional</h5>
                                            <p class="text-muted small"> Seleccione esta opción si cuenta con un permiso
                                                oficial </p> <button type="button"
                                                class="btn btn-outline-primary btn-sm btn-select-tipo"> Seleccionar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card card-option" data-tipo="plan">
                                        <div class="card-body text-center"> <i
                                                class="fas fa-tasks fa-2x text-success mb-3"></i>
                                            <h5>Plan de Recuperación</h5>
                                            <p class="text-muted small"> Seleccione esta opción para reprogramar dentro del
                                                plan de recuperación </p> <button type="button"
                                                class="btn btn-outline-success btn-sm btn-select-tipo"> Seleccionar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3"> <i class="fas fa-info-circle mr-2"></i>
                                <strong>Importante:</strong> La reprogramación automática calculará las horas totales
                                basándose en la asignatura seleccionada.
                            </div>
                        </div> <!-- Paso 2: Información de asignatura -->
                        <div class="step-content step-2 d-none">
                            <div class="text-center mb-4">
                                <h5>Información de la asignatura</h5>
                            </div>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-group mb-3"> <label
                                                    class="font-weight-bold text-muted">Asignatura:</label>
                                                <div class="info-value" id="infoAsignatura">-</div>
                                            </div>
                                            <div class="info-group mb-3"> <label
                                                    class="font-weight-bold text-muted">Docente:</label>
                                                <div class="info-value" id="infoDocente">-</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group mb-3"> <label class="font-weight-bold text-muted">Horas
                                                    pendientes:</label>
                                                <div class="info-value"> <span id="infoHorasPendientes">0</span> horas
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-warning mt-3"> <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Atención:</strong> El sistema calculará automáticamente las horas necesarias
                                        para completar la recuperación.
                                    </div>
                                </div>
                            </div>
                        </div> <!-- Paso 3: Reprogramación -->
                        <div class="step-content step-3 d-none">
                            <div class="text-center mb-4">
                                <h5>Configurar nueva programación</h5>
                            </div>

                            <div class="row">
                                <!-- Nueva fecha -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nueva fecha *</label>
                                        <input type="date" class="form-control" name="fecha_nueva" id="rep_fecha" required>
                                    </div>
                                </div>

                                <!-- Hora inicio -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hora inicio *</label>
                                        <input type="time" class="form-control" name="hora_inicio_nueva"
                                            id="rep_hora_inicio" required>
                                    </div>
                                </div>

                                <!-- Hora fin -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hora fin *</label>
                                        <input type="time" class="form-control" name="hora_fin_nueva" id="rep_hora_fin"
                                            required>
                                    </div>
                                </div>

                                <!-- Total horas -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Total horas calculadas</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="horas_calculadas" readonly
                                                value="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text">horas</span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            Calculado automáticamente basado en inicio y fin
                                        </small>
                                    </div>
                                </div>
                                <!-- Nueva aula -->
                                <div class="col-md-6">
                                    <div class="form-group"> <label>Nueva aula *</label> <input type="text"
                                            class="form-control" name="aula_nueva" id="rep_aula" required>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group"> <label>Motivo de reprogramación *</label> <textarea
                                    class="form-control" name="motivo" rows="2" required id="rep_motivo"></textarea> </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100 d-flex justify-content-between">
                            <div> <button type="button" class="btn btn-secondary" id="btnStepPrev" style="display: none;">
                                    <i class="fas fa-arrow-left mr-1"></i> Anterior </button> </div>
                            <div> <button type="button" class="btn btn-outline-secondary mr-2" data-dismiss="modal">
                                    Cancelar </button> <button type="button" class="btn btn-primary" id="btnStepNext">
                                    Siguiente <i class="fas fa-arrow-right ml-1"></i> </button> <button type="button"
                                    class="btn btn-success" id="btnReprogramar" style="display: none;"> <i
                                        class="fas fa-save mr-1"></i> Reprogramar </button> </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
        .step-indicator {
            position: relative;
            margin-bottom: 10px;
        }

        .step {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: bold;
            border: 3px solid #e9ecef;
            transition: all 0.3s;
        }

        .step.active .step-circle {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .step-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #007bff;
            font-weight: 600;
        }

        .card-option {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            height: 100%;
        }

        .card-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-option.selected {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.05);
        }

        .info-group {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-group:last-child {
            border-bottom: none;
        }

        .info-value {
            font-size: 16px;
            color: #495057;
        }

        #sesionUnica,
        #sesionMultiple {
            transition: all 0.3s;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/sesion_recuperacion/insert.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/sesion_recuperacion/update.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/sesion_recuperacion/delete.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/sesion_recuperacion/estado.js?v=' . time()) }}"></script>
    <script>
        // Inicialización cuando el documento está listo
        $(document).ready(function () {
            // IMPORTANTE: Reinicializar Select2 cuando se abre el modal
            $('#nuevaSesionModal').on('shown.bs.modal', function () {
                // Destruir Select2 si ya existe
                if ($('#selectPlanRecuperacion').hasClass("select2-hidden-accessible")) {
                    $('#selectPlanRecuperacion').select2('destroy');
                }

                // Reinicializar Select2 con configuración completa
                $('#selectPlanRecuperacion').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Buscar plan...',
                    allowClear: true,
                    language: 'es',
                    dropdownParent: $('#nuevaSesionModal'), // CRÍTICO: Esto hace que funcione en modales
                    width: '100%'
                });

                // Volver a adjuntar el evento change
                $('#selectPlanRecuperacion').off('change').on('change', function () {
                    cargarDetallesPlan();
                });

                // Si hay un plan pre-seleccionado, cargar sus detalles automáticamente
                @if(isset($planSeleccionado) && $planSeleccionado)
                    setTimeout(function () {
                        cargarDetallesPlan();
                    }, 300);
                @endif
                                                                                });

            // Limpiar cuando se cierra el modal
            $('#nuevaSesionModal').on('hidden.bs.modal', function () {
                // Resetear el wizard
                currentStep = 1;
                planData = null;
                sessionData = {};

                // Limpiar formulario
                $('#frmSesionInsert')[0].reset();
                $('#planSummary').hide();

                // Resetear pasos
                $('.step').removeClass('active completed');
                $('.step-content').removeClass('active').hide();
                $('#step1').addClass('active');
                $('#step1-content').addClass('active').show();

                // Resetear botones
                actualizarBotones();
            });
        });

        // Variable global para el archivo estado.js
        window.baseUrl = '{{ url('') }}';
    </script>

    <!-- Scripts específicos para sesiones de recuperación -->
    <script src="{{ asset('viewresources/admin/sesion_recuperacion/estado.js') }}"></script>
    <script src="{{ asset('viewresources/admin/sesion_recuperacion/reprogramacion.js') }}"></script>

@endsection