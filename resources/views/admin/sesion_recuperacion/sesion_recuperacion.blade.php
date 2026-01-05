@extends('template.layout')

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
                        <p class="module-subtitle">
                            ...
                        </p>
                    </div>
                </div>

                <!-- Resumen de Progreso -->
                <div class="progress-summary">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 10px;">
                                <i class="fas fa-chart-line mr-2"></i>
                                Progreso General de Recuperación
                            </h3>
                            <p style="color: var(--medium-gray); margin-bottom: 0;">
                                Control acumulativo de horas recuperadas vs horas pendientes
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn-execution btn-primary-execution pulse" data-toggle="modal"
                                data-target="#nuevaSesionModal">
                                <i class="fas fa-plus-circle"></i>
                                Nueva Sesión
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Progreso -->
                <div style="padding: 0 35px;">
                    <div class="progress-stats">
                        <div class="stat-card-progress fade-in">
                            <div class="stat-icon-wrapper icon-completed">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h3>42 <span>horas</span></h3>
                                <p>Recuperación Completada</p>
                                <div class="progress-bar-container">
                                    <div class="progress-label">
                                        <span>Progreso</span>
                                        <span>70%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 70%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card-progress fade-in" style="animation-delay: 0.1s;">
                            <div class="stat-icon-wrapper icon-pending">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <h3>18 <span>horas</span></h3>
                                <p>Pendiente de Recuperación</p>
                                <div class="progress-bar-container">
                                    <div class="progress-label">
                                        <span>Restante</span>
                                        <span>30%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill"
                                            style="width: 30%; background: linear-gradient(90deg, #e17055 0%, #fab1a0 100%);">
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
                                <h3>60 <span>horas</span></h3>
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
                                <h3>12 <span>sesiones</span></h3>
                                <p>Sesiones Activas</p>
                                <div class="progress-bar-container">
                                    <div class="progress-label">
                                        <span>En progreso</span>
                                        <span>85%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill"
                                            style="width: 85%; background: linear-gradient(90deg, var(--purple) 0%, #a29bfe 100%);">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banner de Plan Seleccionado -->
                @if(isset($planSeleccionado) && $planSeleccionado)
                    <div
                        style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 15px; padding: 25px; margin: 30px 35px; border-left: 5px solid var(--primary-blue); box-shadow: 0 5px 20px rgba(0, 139, 220, 0.15);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 15px;">
                                    <i class="fas fa-filter mr-2"></i>
                                    Filtrando por Plan de Recuperación
                                </h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div style="margin-bottom: 10px;">
                                            <small style="color: var(--medium-gray); display: block; font-size: 0.85rem;">Plan
                                                ID</small>
                                            <strong
                                                style="color: var(--dark-gray); font-size: 1.1rem;">#{{ $planSeleccionado->id_plan }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="margin-bottom: 10px;">
                                            <small
                                                style="color: var(--medium-gray); display: block; font-size: 0.85rem;">Docente</small>
                                            <strong style="color: var(--dark-gray);">
                                                {{ $planSeleccionado->permiso->docente->appDocente }}
                                                {{ $planSeleccionado->permiso->docente->apmDocente }}
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
                            <div>
                                <a href="{{ url('sesion_recuperacion') }}" class="btn-execution btn-primary-execution"
                                    style="white-space: nowrap;">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Ver Todas las Sesiones
                                </a>
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
                    <div class="d-flex gap-3">
                        <button class="btn-execution btn-success-execution" onclick="generarReporte()">
                            <i class="fas fa-file-export"></i>
                            Reporte PDF
                        </button>
                        <button class="btn-execution btn-warning-execution" onclick="sincronizarSesiones()">
                            <i class="fas fa-sync-alt"></i>
                            Sincronizar
                        </button>
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
                                <th>Modalidad</th>
                                <th class="none">Semestre</th>
                                <th class="none">Aula</th>
                                <th class="none">Tipo</th>
                                <th>Estado</th>
                                <th class="all">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sesionesEjecucion as $sesion)
                                <tr class="fade-in" data-estado="{{ $sesion->estado_sesion }}"
                                    data-modalidad="{{ $sesion->modalidad }}"
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
                                        </div>
                                    </td>

                                    <!-- Curso (Asignatura) -->
                                    <td>
                                        <div style="color: var(--dark-gray);">
                                            {{ $sesion->asignatura ?? 'No especificada' }}
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

                                    <!-- Modalidad -->
                                    <td>
                                        @if($sesion->modalidad == 'PRESENCIAL')
                                            <span class="status-indicator-execution"
                                                style="background: rgba(0, 139, 220, 0.1); color: var(--primary-blue);">
                                                <i class="fas fa-building mr-1"></i>
                                                Presencial
                                            </span>
                                        @elseif($sesion->modalidad == 'VIRTUAL')
                                            <span class="status-indicator-execution"
                                                style="background: rgba(0, 206, 201, 0.1); color: var(--info-cyan);">
                                                <i class="fas fa-video mr-1"></i>
                                                Virtual
                                            </span>
                                        @else
                                            <span class="status-indicator-execution"
                                                style="background: rgba(253, 203, 110, 0.1); color: #e17055;">
                                                <i class="fas fa-star mr-1"></i>
                                                Extra
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Semestre (hidden) -->
                                    <td class="text-center">
                                        <span class="badge"
                                            style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); color: white; padding: 6px 12px; border-radius: 12px; font-size: 0.85rem;">
                                            {{ ucfirst(strtolower($sesion->semestre ?? 'N/A')) }}
                                        </span>
                                    </td>

                                    <!-- Aula (hidden) -->
                                    <td class="text-center">
                                        <div style="font-weight: 500; color: var(--medium-gray);">
                                            <i class="fas fa-door-open mr-1"></i>
                                            {{ $sesion->aula ?? 'No asignada' }}
                                        </div>
                                    </td>

                                    <!-- Tipo -->
                                    <td>
                                        @if($sesion->tipo_sesion == 'TEORIA')
                                            <span class="status-indicator-execution"
                                                style="background: rgba(108, 92, 231, 0.1); color: var(--purple);">
                                                <i class="fas fa-book mr-1"></i>
                                                Teoría
                                            </span>
                                        @elseif($sesion->tipo_sesion == 'PRACTICA')
                                            <span class="status-indicator-execution"
                                                style="background: rgba(0, 184, 148, 0.1); color: var(--success-green);">
                                                <i class="fas fa-flask mr-1"></i>
                                                Práctica
                                            </span>
                                        @else
                                            <span class="status-indicator-execution"
                                                style="background: rgba(225, 112, 85, 0.1); color: var(--danger-red);">
                                                <i class="fas fa-file-alt mr-1"></i>
                                                Examen
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Estado -->
                                    <td>
                                        @if($sesion->estado_sesion == 'VALIDADA')
                                            <span class="status-indicator-execution status-completed">
                                                <span class="dot-status dot-completed"></span>
                                                Validada
                                            </span>
                                        @elseif($sesion->estado_sesion == 'REALIZADA')
                                            <span class="status-indicator-execution status-in-progress">
                                                <span class="dot-status dot-in-progress"></span>
                                                Realizada
                                            </span>
                                        @else
                                            <span class="status-indicator-execution status-pending">
                                                <span class="dot-status dot-pending"></span>
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
                                                <button class="btn-icon btn-edit" onclick="editarSesion({{ $sesion->id_sesion }})"
                                                    title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon btn-approve"
                                                    onclick="completarSesion({{ $sesion->id_sesion }})"
                                                    title="Marcar como validada">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button class="btn-icon btn-delete"
                                                onclick="eliminarSesion({{ $sesion->id_sesion }})" title="Eliminar">
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

    <!-- MODAL NUEVA SESIÓN DE EJECUCIÓN -->
    <div class="modal fade" id="nuevaSesionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content modal-execution">
                <div class="modal-header modal-header-execution">
                    <h5 class="modal-title-execution">
                        <i class="fas fa-plus-circle"></i>
                        Nueva Sesión de Recuperación
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" style="z-index: 3;">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="frmSesionInsert" onsubmit="event.preventDefault(); registrarSesion();">
                    @csrf
                    <div class="modal-body p-4">
                        <!-- Indicador de Pasos -->
                        <div class="form-step-indicator">
                            <div class="step active" id="step1">
                                <div class="step-circle">1</div>
                                <div class="step-label">Plan</div>
                            </div>
                            <div class="step" id="step2">
                                <div class="step-circle">2</div>
                                <div class="step-label">Sesión</div>
                            </div>
                            <div class="step" id="step3">
                                <div class="step-circle">3</div>
                                <div class="step-label">Validación</div>
                            </div>
                            <div class="step" id="step4">
                                <div class="step-circle">4</div>
                                <div class="step-label">Confirmación</div>
                            </div>
                        </div>

                        <!-- Paso 1: Selección de Plan -->
                        <div id="step1-content" class="step-content active">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h4 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 20px;">
                                        <i class="fas fa-file-contract mr-2"></i>
                                        Paso 1: Seleccionar Plan de Recuperación
                                    </h4>
                                    <p style="color: var(--medium-gray);">
                                        Seleccione el plan de recuperación para el cual registrará la sesión
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Plan de Recuperación *</label>
                                        <select
                                            name="{{ isset($planSeleccionado) && $planSeleccionado ? 'plan_display' : 'id_plan_recuperacion' }}"
                                            class="form-control-modern select2" id="selectPlanRecuperacion" {{ isset($planSeleccionado) && $planSeleccionado ? '' : 'required' }}
                                            onchange="cargarDetallesPlan()" {{ isset($planSeleccionado) && $planSeleccionado ? 'disabled' : '' }}>
                                            <option value="">Buscar plan...</option>
                                            @if(isset($planSeleccionado) && $planSeleccionado)
                                                {{-- Si hay un plan seleccionado, solo mostrar ese plan --}}
                                                @php
                                                    $horasRealizadas = $planSeleccionado->sesiones()->where('estado_sesion', 'REALIZADA')->sum('horas_recuperadas') ?? 0;
                                                    $horasProgramadas = $planSeleccionado->sesiones()->where('estado_sesion', 'PROGRAMADA')->sum('horas_recuperadas') ?? 0;
                                                    $horasRecuperadas = $horasRealizadas + $horasProgramadas;
                                                    $horasPendientes = $planSeleccionado->total_horas_recuperar - $horasRecuperadas;
                                                @endphp
                                                <option value="{{ $planSeleccionado->id_plan }}"
                                                    data-horas-totales="{{ $planSeleccionado->total_horas_recuperar }}"
                                                    data-horas-realizadas="{{ $horasRealizadas }}"
                                                    data-horas-programadas="{{ $horasProgramadas }}"
                                                    data-horas-recuperadas="{{ $horasRecuperadas }}"
                                                    data-horas-pendientes="{{ $horasPendientes }}"
                                                    data-docente="{{ $planSeleccionado->permiso->docente->appDocente }} {{ $planSeleccionado->permiso->docente->apmDocente }}"
                                                    data-fecha-fin="{{ $planSeleccionado->permiso->fecha_fin }}" selected>
                                                    Plan #{{ $planSeleccionado->id_plan }} -
                                                    {{ $planSeleccionado->permiso->docente->appDocente }} -
                                                    {{ $planSeleccionado->total_horas_recuperar }} horas
                                                    ({{ $horasRecuperadas }} recuperadas)
                                                </option>
                                            @else
                                                {{-- Si no hay plan seleccionado, mostrar todos los planes activos --}}
                                                @foreach($planesActivos as $plan)
                                                    @php
                                                        $horasRealizadas = $plan->sesiones()->where('estado_sesion', 'REALIZADA')->sum('horas_recuperadas') ?? 0;
                                                        $horasProgramadas = $plan->sesiones()->where('estado_sesion', 'PROGRAMADA')->sum('horas_recuperadas') ?? 0;
                                                        $horasRecuperadas = $horasRealizadas + $horasProgramadas;
                                                        $horasPendientes = $plan->total_horas_recuperar - $horasRecuperadas;
                                                    @endphp
                                                    <option value="{{ $plan->id_plan }}"
                                                        data-horas-totales="{{ $plan->total_horas_recuperar }}"
                                                        data-horas-realizadas="{{ $horasRealizadas }}"
                                                        data-horas-programadas="{{ $horasProgramadas }}"
                                                        data-horas-recuperadas="{{ $horasRecuperadas }}"
                                                        data-horas-pendientes="{{ $horasPendientes }}"
                                                        data-docente="{{ $plan->permiso->docente->appDocente }} {{ $plan->permiso->docente->apmDocente }}"
                                                        data-fecha-fin="{{ $plan->permiso->fecha_fin }}">
                                                        Plan #{{ $plan->id_plan }} -
                                                        {{ $plan->permiso->docente->appDocente }} -
                                                        {{ $plan->total_horas_recuperar }} horas
                                                        ({{ $horasRecuperadas }} recuperadas)
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        {{-- Campo oculto para enviar el ID cuando está deshabilitado --}}
                                        @if(isset($planSeleccionado) && $planSeleccionado)
                                            <input type="hidden" name="id_plan_recuperacion"
                                                value="{{ $planSeleccionado->id_plan }}">
                                            <input type="hidden" id="hiddenHorasTotales"
                                                value="{{ $planSeleccionado->total_horas_recuperar }}">
                                            <input type="hidden" id="hiddenHorasRealizadas" value="{{ $horasRealizadas }}">
                                            <input type="hidden" id="hiddenHorasProgramadas" value="{{ $horasProgramadas }}">
                                            <input type="hidden" id="hiddenHorasRecuperadas" value="{{ $horasRecuperadas }}">
                                            <input type="hidden" id="hiddenHorasPendientes" value="{{ $horasPendientes }}">
                                            <input type="hidden" id="hiddenDocente"
                                                value="{{ $planSeleccionado->permiso->docente->appDocente }} {{ $planSeleccionado->permiso->docente->apmDocente }}">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen del Plan Seleccionado -->
                            <div id="planSummary" class="hours-counter" style="display: none;">
                                <h4>Resumen del Plan Seleccionado</h4>
                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div style="text-align: center;">
                                            <div class="hours-display-large" id="horasTotales">0</div>
                                            <div class="hours-total">Horas Totales</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="text-align: center;">
                                            <div class="hours-display-large" style="color: var(--success-green);"
                                                id="horasRecuperadas">0</div>
                                            <div class="hours-total">Recuperadas</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="text-align: center;">
                                            <div class="hours-display-large" style="color: #e17055;" id="horasPendientes">0
                                            </div>
                                            <div class="hours-total">Pendientes</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 2: Detalles de la Sesión -->
                        <div id="step2-content" class="step-content" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h4 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 20px;">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        Paso 2: Configurar Sesión de Recuperación
                                    </h4>
                                    <p style="color: var(--medium-gray);">
                                        Defina los detalles específicos de la sesión de recuperación
                                    </p>
                                </div>
                            </div>

                            <!-- Sección: Información Académica -->
                            <div class="card mb-4"
                                style="border: 2px solid var(--light-blue); border-radius: 12px; overflow: hidden;">
                                <div class="card-header"
                                    style="background: linear-gradient(135deg, var(--light-blue) 0%, #c3e0ff 100%); border-bottom: 2px solid var(--primary-blue);">
                                    <h5 style="margin: 0; color: var(--primary-blue); font-weight: 700;">
                                        <i class="fas fa-book mr-2"></i>
                                        Información Académica
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-graduation-cap mr-1"></i>
                                                    Asignatura *
                                                </label>
                                                <input type="text" name="asignatura" class="form-control-modern"
                                                    placeholder="Ej: Matemática I, Física II, Química Orgánica" required
                                                    maxlength="100">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-layer-group mr-1"></i>
                                                    Semestre *
                                                </label>
                                                <select name="semestre" class="form-control-modern" required>
                                                    <option value="">Seleccionar semestre...</option>
                                                    <option value="PRIMERO">Primero</option>
                                                    <option value="SEGUNDO">Segundo</option>
                                                    <option value="TERCERO">Tercero</option>
                                                    <option value="CUARTO">Cuarto</option>
                                                    <option value="QUINTO">Quinto</option>
                                                    <option value="SEXTO">Sexto</option>
                                                    <option value="SEPTIMO">Séptimo</option>
                                                    <option value="OCTAVO">Octavo</option>
                                                    <option value="NOVENO">Noveno</option>
                                                    <option value="DECIMO">Décimo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección: Programación de Sesión -->
                            <div class="card mb-4"
                                style="border: 2px solid #e3f2fd; border-radius: 12px; overflow: hidden;">
                                <div class="card-header"
                                    style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-bottom: 2px solid var(--primary-blue);">
                                    <h5 style="margin: 0; color: var(--primary-blue); font-weight: 700;">
                                        <i class="fas fa-calendar-check mr-2"></i>
                                        Programación de Sesión
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Fecha de Sesión *
                                                </label>
                                                <input type="date" name="fecha_sesion" class="form-control-modern"
                                                    value="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Hora Inicio *
                                                </label>
                                                <input type="time" name="hora_inicio" id="hora_inicio"
                                                    class="form-control-modern" required
                                                    onchange="calcularHorasRecuperar()">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Hora Fin *
                                                </label>
                                                <input type="time" name="hora_fin" id="hora_fin" class="form-control-modern"
                                                    required onchange="calcularHorasRecuperar()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-door-open mr-1"></i>
                                                    Aula
                                                </label>
                                                <input type="text" name="aula" class="form-control-modern"
                                                    placeholder="Ej: A-101, Lab 3, Auditorio" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-hourglass-half mr-1"></i>
                                                    Horas a Recuperar *
                                                </label>
                                                <input type="number" name="horas_recuperadas" id="horas_recuperadas"
                                                    class="form-control-modern" step="0.5" min="0.5" max="8" required
                                                    placeholder="Auto-calculado" readonly
                                                    style="background-color: #f8f9fa;">
                                                <div class="valid-feedback">Horas válidas</div>
                                                <div class="invalid-feedback">Entre 0.5 y 8 horas</div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Sección: Detalles de la Sesión -->
                            <div class="card mb-4"
                                style="border: 2px solid #f3e5f5; border-radius: 12px; overflow: hidden;">
                                <div class="card-header"
                                    style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); border-bottom: 2px solid var(--purple);">
                                    <h5 style="margin: 0; color: var(--purple); font-weight: 700;">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Detalles de la Sesión
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                    Tipo de Sesión *
                                                </label>
                                                <select name="tipo_sesion" class="form-control-modern" required>
                                                    <option value="">Seleccionar tipo...</option>
                                                    <option value="TEORIA">Teoría</option>
                                                    <option value="PRACTICA">Práctica</option>
                                                    <option value="EXAMEN">Examen</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-laptop mr-1"></i>
                                                    Modalidad *
                                                </label>
                                                <select name="modalidad" class="form-control-modern" required>
                                                    <option value="PRESENCIAL">
                                                        <i class="fas fa-building"></i> Presencial
                                                    </option>
                                                    <option value="VIRTUAL">
                                                        <i class="fas fa-video"></i> Virtual
                                                    </option>
                                                    <option value="EXTRA">
                                                        <i class="fas fa-star"></i> Extraordinaria
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 3: Validación de Horas -->
                        <div id="step3-content" class="step-content" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h4 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 20px;">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Paso 3: Validación de Acumulación
                                    </h4>
                                    <p style="color: var(--medium-gray);">
                                        El sistema validará que las horas no excedan el total del plan
                                    </p>
                                </div>
                            </div>

                            <div id="validationResults" class="hours-counter">
                                <h4>Resultado de la Validación</h4>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div
                                            style="text-align: center; padding: 20px; border-right: 2px solid var(--light-gray);">
                                            <div style="font-size: 3rem; font-weight: 700; color: var(--primary-blue);"
                                                id="horasSesion">0</div>
                                            <div style="color: var(--medium-gray); font-size: 1.1rem; margin-top: 10px;">
                                                Horas de esta sesión
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div style="text-align: center; padding: 20px;">
                                            <div style="font-size: 3rem; font-weight: 700; color: var(--success-green);"
                                                id="nuevoTotal">0</div>
                                            <div style="color: var(--medium-gray); font-size: 1.1rem; margin-top: 10px;">
                                                Nuevo total recuperado
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="validationAlert" class="validation-alert mt-4" style="display: none;">
                                    <div class="alert-content">
                                        <div class="alert-title">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Validación Requerida
                                        </div>
                                        <p style="color: #856404; margin: 0;" id="alertMessage">
                                            Las horas ingresadas exceden el total permitido para este plan
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Estado de la Sesión *</label>
                                        <select name="estado_sesion" class="form-control-modern" required>
                                            <option value="PROGRAMADA">Programada</option>
                                            <option value="REALIZADA" selected>Realizada</option>
                                            <option value="VALIDADA">Validada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 4: Confirmación -->
                        <div id="step4-content" class="step-content" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h4 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 20px;">
                                        <i class="fas fa-clipboard-check mr-2"></i>
                                        Paso 4: Confirmar Registro
                                    </h4>
                                    <p style="color: var(--medium-gray);">
                                        Revise los detalles antes de registrar la sesión
                                    </p>
                                </div>
                            </div>

                            <div class="session-card completed">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div style="font-weight: 600; color: var(--dark-gray); margin-bottom: 10px;">
                                            <i class="fas fa-user-tie mr-2"></i>
                                            <span id="confirmDocente">-</span>
                                        </div>
                                        <div style="color: var(--medium-gray); font-size: 0.9rem;">
                                            <i class="fas fa-file-contract mr-2"></i>
                                            Plan: <span id="confirmPlan">-</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="font-weight: 600; color: var(--dark-gray); margin-bottom: 10px;">
                                            <i class="fas fa-book mr-2"></i>
                                            <span id="confirmCurso">-</span>
                                        </div>
                                        <div style="color: var(--medium-gray); font-size: 0.9rem;">
                                            <i class="fas fa-clock mr-2"></i>
                                            Horario: <span id="confirmHorario">-</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="font-weight: 600; color: var(--dark-gray); margin-bottom: 10px;">
                                            <i class="fas fa-calendar mr-2"></i>
                                            <span id="confirmFecha">-</span>
                                        </div>
                                        <div style="color: var(--medium-gray); font-size: 0.9rem;">
                                            <i class="fas fa-hourglass-half mr-2"></i>
                                            Horas: <span id="confirmHoras">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div style="color: var(--medium-gray); font-size: 0.9rem;">
                                            <i class="fas fa-play-circle mr-2"></i>
                                            Estado: <span id="confirmEstado">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle"></i>
                                <strong>Nota:</strong> Una vez registrada, la sesión aparecerá en el sistema de ejecución y
                                se actualizará el progreso del plan correspondiente.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-4" style="background: #f8f9fa; border-top: 1px solid #dee2e6;">
                        <button type="button" class="btn-execution" data-dismiss="modal"
                            style="background: #6c757d; color: white;">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>
                        <button type="button" class="btn-execution" id="btnPrevStep" onclick="prevStep()"
                            style="background: var(--light-gray); color: var(--dark-gray); display: none;">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Anterior
                        </button>
                        <button type="button" class="btn-execution btn-primary-execution" id="btnNextStep"
                            onclick="nextStep()">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Siguiente
                        </button>
                        <button type="submit" class="btn-execution btn-success-execution" id="btnSubmit"
                            style="display: none;">
                            <i class="fas fa-save mr-2"></i>
                            Registrar Sesión
                        </button>
                    </div>
                </form>
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

@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/sesion_recuperacion/insert.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/sesion_recuperacion/update.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/sesion_recuperacion/delete.js?v=' . time()) }}"></script>

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
    </script>
@endsection