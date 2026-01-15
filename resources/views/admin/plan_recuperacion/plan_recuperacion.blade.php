@extends('admin.template.layout')

@section('titleGeneral', 'Gestión de Planes de Recuperación')

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
        }

        .recovery-module {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 139, 220, 0.08);
            overflow: hidden;
        }

        .module-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 16px 16px 0 0;
            position: relative;
            overflow: hidden;
        }

        .module-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(100px, -100px);
        }

        .module-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .module-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-top: 8px;
            font-weight: 400;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s ease;
            border-left: 4px solid var(--primary-blue);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            flex-shrink: 0;
        }

        .icon-presentado {
            background: linear-gradient(135deg, #0984e3 0%, #74b9ff 100%);
        }

        .icon-aprobado {
            background: linear-gradient(135deg, #00b894 0%, #55efc4 100%);
        }

        .icon-observado {
            background: linear-gradient(135deg, #e17055 0%, #fab1a0 100%);
        }

        .icon-pendiente {
            background: linear-gradient(135deg, #fdcb6e 0%, #ffeaa7 100%);
        }

        .stat-info h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-gray);
        }

        .stat-info p {
            margin: 5px 0 0;
            color: var(--medium-gray);
            font-size: 0.9rem;
        }

        .action-bar {
            background: var(--light-blue);
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-modern {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 139, 220, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 139, 220, 0.4);
            color: white;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, var(--success-green) 0%, #55efc4 100%);
            color: white;
        }

        .btn-warning-modern {
            background: linear-gradient(135deg, var(--warning-orange) 0%, #ffeaa7 100%);
            color: var(--dark-gray);
        }

        .btn-danger-modern {
            background: linear-gradient(135deg, var(--danger-red) 0%, #fab1a0 100%);
            color: white;
        }

        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        }

        .table-modern th {
            color: white;
            font-weight: 600;
            padding: 18px 16px;
            text-align: left;
            border: none;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-modern th:first-child {
            border-radius: 12px 0 0 0;
        }

        .table-modern th:last-child {
            border-radius: 0 12px 0 0;
        }

        .table-modern td {
            padding: 20px 16px;
            border-bottom: 1px solid var(--light-gray);
            vertical-align: middle;
        }

        .table-modern tbody tr {
            transition: background-color 0.3s ease;
        }

        .table-modern tbody tr:hover {
            background-color: rgba(0, 139, 220, 0.05);
        }

        .badge-modern {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-presentado {
            background: rgba(9, 132, 227, 0.1);
            color: #0984e3;
            border: 1px solid rgba(9, 132, 227, 0.2);
        }

        .badge-aprobado {
            background: rgba(0, 184, 148, 0.1);
            color: #00b894;
            border: 1px solid rgba(0, 184, 148, 0.2);
        }

        .badge-observado {
            background: rgba(225, 112, 85, 0.1);
            color: #e17055;
            border: 1px solid rgba(225, 112, 85, 0.2);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .dot-presentado {
            background: #0984e3;
        }

        .dot-aprobado {
            background: #00b894;
        }

        .dot-observado {
            background: #e17055;
        }

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

        .modal-modern {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-header-modern {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 25px 30px;
            border: none;
        }

        .modal-title-modern {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            color: white;
            opacity: 0.8;
            font-size: 1.5rem;
        }

        .modal-close:hover {
            opacity: 1;
            color: white;
        }

        .form-group-modern {
            margin-bottom: 25px;
        }

        .form-label-modern {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-gray);
            font-size: 0.95rem;
        }

        .form-control-modern {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control-modern:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 139, 220, 0.1);
            outline: none;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--medium-gray);
            font-size: 1.1rem;
        }

        .input-with-icon .form-control-modern {
            padding-left: 50px;
        }

        .permission-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-blue);
        }

        .permission-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--medium-gray);
            margin-bottom: 4px;
        }

        .info-value {
            font-weight: 600;
            color: var(--dark-gray);
            font-size: 1rem;
        }

        .hours-display {
            background: linear-gradient(135deg, var(--light-blue) 0%, #c3e0ff 100%);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .hours-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin: 10px 0;
        }

        .hours-label {
            font-size: 0.9rem;
            color: var(--medium-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .rule-alert {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            position: relative;
            overflow: hidden;
        }

        .rule-alert::before {
            content: '⚠';
            position: absolute;
            right: -20px;
            top: -20px;
            font-size: 6rem;
            opacity: 0.1;
            transform: rotate(15deg);
        }

        .rule-alert h6 {
            color: #856404;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-filter {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .filter-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        @media (max-width: 768px) {
            .module-title {
                font-size: 1.5rem;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .table-modern {
                display: block;
                overflow-x: auto;
            }

            .filter-group {
                flex-direction: column;
            }

            .filter-item {
                min-width: 100%;
            }
        }

        /* Estilos para mensajes de validación de FormValidation */
        .help-block {
            display: block;
            margin-top: 8px;
            margin-bottom: 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .has-error .form-control-modern,
        .has-error .select2-container--bootstrap4 .select2-selection {
            border-color: #e17055 !important;
            box-shadow: 0 0 0 3px rgba(225, 112, 85, 0.1) !important;
        }

        .has-success .form-control-modern,
        .has-success .select2-container--bootstrap4 .select2-selection {
            border-color: #00b894 !important;
        }

        /* Asegurar que los errores aparezcan en el modal */
        .modal-body .help-block {
            position: relative;
            z-index: 1;
        }

        .permission-card .help-block {
            margin-top: 10px;
            padding: 8px 12px;
            background: rgba(225, 112, 85, 0.05);
            border-left: 3px solid #e17055;
            border-radius: 4px;
        }

        /* Estilos para el indicador de pasos */
        .form-step-indicator {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        .form-step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--light-gray);
            z-index: 0;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
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
            font-size: 1.1rem;
            color: var(--medium-gray);
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .step-label {
            font-size: 0.85rem;
            color: var(--medium-gray);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            border-color: var(--primary-blue);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 139, 220, 0.3);
            transform: scale(1.1);
        }

        .step.active .step-label {
            color: var(--primary-blue);
            font-weight: 700;
        }

        .step.completed .step-circle {
            background: var(--success-green);
            border-color: var(--success-green);
            color: white;
        }

        .step.completed .step-label {
            color: var(--success-green);
        }

        /* Estilos para el contenido de los pasos */
        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilos para el resumen de progreso */
        .hours-display {
            text-align: center;
            padding: 20px;
        }

        .hours-display-large {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-blue);
            line-height: 1;
            margin-bottom: 8px;
        }

        .hours-total {
            color: var(--medium-gray);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>

    <section class="content">
        <div class="container-fluid">
            <!-- Módulo Principal -->
            <div class="recovery-module">
                <!-- Encabezado del Módulo -->
                <div class="module-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="module-title">
                                <i class="fas fa-calendar-check"></i>
                                Plan de Recuperación de Clases
                            </h1>
                            <p class="module-subtitle">
                                Sistema de gestión y control para la recuperación de horas académicas afectadas por permisos
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Rápidas -->
                <div class="container py-4">
                    <div class="quick-stats">
                        <div class="stat-card">
                            <div class="stat-icon icon-presentado">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="stat-info">
                                <h3>{{ $listPlanes->where('estado_plan', 'PRESENTADO')->count() }}</h3>
                                <p>Planes Presentados</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon icon-aprobado">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3>{{ $listPlanes->where('estado_plan', 'APROBADO')->count() }}</h3>
                                <p>Planes Aprobados</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon icon-observado">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3>{{ $listPlanes->where('estado_plan', 'OBSERVADO')->count() }}</h3>
                                <p>Planes Observados</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon icon-pendiente">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h3>{{ $listPlanes->where('estado_plan', 'PRESENTADO')->count() }}</h3>
                                <p>Pendientes de Aprobación</p>
                            </div>
                        </div>
                    </div>

                    <!-- Barra de Acciones -->
                    <div class="action-bar">
                        <div>
                            <h5 class="mb-0" style="color: var(--primary-blue); font-weight: 600;">
                                <i class="fas fa-list-check mr-2"></i>
                                Gestión de Planes de Recuperación
                            </h5>
                            <p class="mb-0 mt-2" style="color: var(--medium-gray); font-size: 0.9rem;">
                                Total: {{ $listPlanes->count() }} planes registrados
                            </p>
                        </div>
                        <div>
                            <button type="button" class="btn-modern btn-primary-modern" data-toggle="modal"
                                data-target="#nuevoPlanModal">
                                <i class="fas fa-plus-circle"></i>
                                Nuevo Plan
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de Planes -->
                    <div class="table-responsive px-3 pb-4">
                        <table id="tablaExample2" class="table-modern table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Permiso</th>
                                    <th>Docente</th>
                                    <th>Horas a Recuperar</th>
                                    <th>Fecha Presentación</th>
                                    <th class="all">Estado</th>
                                    <th class="all">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($listPlanes as $plan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div style="color: var(--dark-gray);">
                                                {{ $plan->permiso->tipoPermiso->nombre }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $plan->permiso->docente->user->last_name }},
                                                    {{ $plan->permiso->docente->user->name }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <span style="color: var(--dark-gray);">
                                                    {{ $plan->total_horas_recuperar }}
                                                </span><br>
                                                <small style="color: var(--medium-gray);">horas</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <span style="color: var(--dark-gray);">
                                                    {{ date('d/m/Y', strtotime($plan->fecha_presentacion)) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($plan->estado_plan == 'PRESENTADO')
                                                <span class="badge-modern badge-presentado">
                                                    <span class="status-dot dot-presentado"></span>
                                                    {{ $plan->estado_plan }}
                                                </span>
                                            @elseif($plan->estado_plan == 'APROBADO')
                                                <span class="badge-modern badge-aprobado">
                                                    <span class="status-dot dot-aprobado"></span>
                                                    {{ $plan->estado_plan }}
                                                </span>
                                            @else
                                                <span class="badge-modern badge-observado">
                                                    <span class="status-dot dot-observado"></span>
                                                    {{ $plan->estado_plan }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ url('admin/sesion_recuperacion?plan_id=' . $plan->id_plan) }}"
                                                    class="btn-icon btn-view" title="Ver sesiones de recuperación">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn-icon btn-edit" onclick="editPlan('{{ $plan->id_plan }}')"
                                                    title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon btn-delete" onclick="deletePlan('{{ $plan->id_plan }}')"
                                                    title="Eliminar">
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
        </div>
    </section>

    <style>
        /* Más aire dentro del modal */
        .modal-modern .modal-body {
            padding: 30px 35px !important;
        }

        /* Espaciado entre el borde curvo y títulos */
        .step-content {
            padding: 25px 20px;
        }

        /* Paso 3 y 4: separación superior e inferior */
        #step3-content h4,
        #step4-content h4 {
            margin-top: 15px;
            margin-bottom: 20px;
        }

        /* Contenedores internos */
        #validationResults,
        .session-card {
            padding: 25px;
            border-radius: 14px;
        }

        /* Evitar que toquen los lados del modal */
        #step3-content,
        #step4-content {
            margin-top: 15px;
            margin-bottom: 20px;
        }

        /* Mejora de legibilidad general */
        .step-content p {
            line-height: 1.5;
        }
    </style>
    <!-- MODAL NUEVO PLAN - DISEÑO MEJORADO -->
    <div class="modal fade" id="nuevoPlanModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <h5 class="modal-title-modern">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Nuevo Plan de Recuperación
                    </h5>
                    <button type="button" class="close modal-close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="frmPlanInsert" onsubmit="event.preventDefault(); sendFrmPlanInsert();">
                    @csrf
                    <div class="modal-body p-4">
                        <!-- Indicador de Pasos -->
                        <div class="form-step-indicator mb-4">
                            <div class="step active" id="stepIndicator1">
                                <div class="step-circle">1</div>
                                <div class="step-label">Plan</div>
                            </div>
                            <div class="step" id="stepIndicator2">
                                <div class="step-circle">2</div>
                                <div class="step-label">Sesión</div>
                            </div>
                            <div class="step" id="stepIndicator3">
                                <div class="step-circle">3</div>
                                <div class="step-label">Validación</div>
                            </div>
                            <div class="step" id="stepIndicator4">
                                <div class="step-circle">4</div>
                                <div class="step-label">Confirmación</div>
                            </div>
                        </div>

                        <!-- Paso 1: Selección de Permiso y Configuración del Plan -->
                        <div id="step1-content" class="step-content active">
                            <div class="form-group-modern">
                                <h6 style="color: var(--primary-blue); margin-bottom: 20px;">
                                    <i class="fas fa-file-contract mr-2"></i>
                                    Paso 1: Seleccionar Permiso y Configurar Plan
                                </h6>

                                {{-- Debug: Mostrar cantidad de permisos disponibles --}}
                                @if($permisosRecuperables->count() == 0)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>No hay permisos disponibles.</strong>
                                        Para crear un plan de recuperación, primero debe haber permisos APROBADOS con horas
                                        afectadas.
                                    </div>
                                @else
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Se encontraron <strong>{{ $permisosRecuperables->count() }}</strong> permisos
                                        disponibles
                                        para recuperación.
                                    </div>
                                @endif

                                <div class="permission-card">
                                    <div class="input-with-icon">
                                        <i class="input-icon fas fa-search"></i>
                                        <select name="id_permiso" class="form-control-modern select2" id="selectPermiso"
                                            onchange="cargarHorasPermiso()">
                                            <option value="">Buscar permiso por docente o código...</option>
                                            @foreach($permisosRecuperables as $permiso)
                                                <option value="{{ $permiso->id_permiso }}"
                                                    data-horas="{{ $permiso->horas_afectadas }}"
                                                    data-docente="{{ $permiso->docente->user->last_name }}, {{ $permiso->docente->user->name }}"
                                                    data-tipo="{{ $permiso->tipoPermiso->nombre }}"
                                                    data-periodo="{{ date('d/m/Y', strtotime($permiso->fecha_inicio)) }} - {{ date('d/m/Y', strtotime($permiso->fecha_fin)) }}"
                                                    data-fecha-fin="{{ $permiso->fecha_fin }}">
                                                    Permiso #{{ $permiso->id_permiso }} -
                                                    {{ $permiso->docente->user->last_name }},
                                                    {{ $permiso->docente->user->name }} -
                                                    {{ $permiso->tipoPermiso->nombre }}
                                                    ({{ $permiso->horas_afectadas }} horas)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Permiso Seleccionado -->
                            <div id="permisoInfo" class="permission-card" style="display: none;">
                                <h6 style="color: var(--primary-blue); margin-bottom: 15px;">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Información del Permiso Seleccionado
                                </h6>
                                <div class="permission-info">
                                    <div class="info-item">
                                        <span class="info-label">Docente:</span>
                                        <span class="info-value" id="infoDocente"></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Tipo de Permiso:</span>
                                        <span class="info-value" id="infoTipoPermiso"></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Período:</span>
                                        <span class="info-value" id="infoPeriodo"></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Horas Afectadas:</span>
                                        <span class="info-value" id="infoHorasAfectadas"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Horas a Recuperar -->
                            <!--<div class="form-group-modern mt-4">
                                                                                        <h6 style="color: var(--primary-blue); margin-bottom: 20px;">
                                                                                            <i class="fas fa-calculator mr-2"></i>
                                                                                            Horas a Recuperar
                                                                                        </h6>
                                                                                        <div class="hours-display">
                                                                                            <div class="hours-number" id="totalHorasDisplay">0</div>
                                                                                            <div class="hours-label">Horas que deben ser recuperadas</div>
                                                                                            <input type="hidden" name="total_horas_recuperar" id="totalHorasRecuperar">
                                                                                        </div>
                                                                                    </div>-->
                            <!-- Resumen del permiso seleccionado -->
                            <div class="form-group-modern mt-4">
                                <h4>Resumen del Permiso Seleccionado</h4>
                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div class="hours-display">
                                            <div class="hours-display-large" id="horasTotales">0</div>
                                            <div class="hours-total">Horas Totales</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="hours-display">
                                            <div class="hours-display-large" style="color: var(--success-green);"
                                                id="horasRecuperadas">0</div>
                                            <div class="hours-total">Recuperadas</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="hours-display">
                                            <div class="hours-display-large" style="color: #e17055;" id="horasPendientes">0
                                            </div>
                                            <div class="hours-total">Pendientes</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Hidden input for form validation -->
                                <input type="hidden" name="total_horas_recuperar" id="totalHorasRecuperar">
                            </div>

                            <!-- Configuración del Plan -->
                            <div class="form-group-modern">
                                <h6 style="color: var(--primary-blue); margin-bottom: 20px;">
                                    <i class="fas fa-cogs mr-2"></i>
                                    Configuración del Plan
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label-modern">Fecha de Presentación *</label>
                                        <div class="input-with-icon">
                                            <i class="input-icon fas fa-calendar-alt"></i>
                                            <input type="date" name="fecha_presentacion" class="form-control-modern"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-modern">Estado del Plan *</label>
                                        <select name="estado_plan" class="form-control-modern">
                                            <option value="PRESENTADO" selected>PRESENTADO</option>
                                            <option value="APROBADO">APROBADO</option>
                                            <option value="OBSERVADO">OBSERVADO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 2: Detalles de la Sesión -->
                        <div id="step2-content" class="step-content">
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
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-barcode mr-1"></i>
                                                    Código de Asignatura *
                                                </label>
                                                <div style="position: relative;">
                                                    <input type="text" id="codigoAsignatura" class="form-control-modern"
                                                        placeholder="Ej: G002, AIS11..."
                                                        style="text-transform: uppercase; padding-right: 45px;">
                                                    <button class="btn btn-primary" type="button" id="btnBuscarAsignatura"
                                                        style="position: absolute; right: 0; top: 0; height: 100%; border-radius: 0 4px 4px 0; padding: 0 12px;">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-graduation-cap mr-1"></i>
                                                    Nombre de Asignatura
                                                </label>
                                                <input type="text" id="nombreAsignatura" class="form-control-modern"
                                                    placeholder="Se completará automáticamente..." readonly
                                                    style="background-color: #f8f9fa; cursor: not-allowed;">
                                                <!-- Campo oculto para enviar el ID -->
                                                <input type="hidden" name="idAsignatura" id="idAsignatura" required>
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
                                                <input type="date" name="fecha_sesion" id="fecha_sesion"
                                                    class="form-control-modern" value="{{ date('Y-m-d') }}" required>
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
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">
                                                    <i class="fas fa-book-open mr-1"></i>
                                                    Tema a Desarrollar *
                                                </label>
                                                <textarea name="tema" id="tema" class="form-control-modern" rows="2"
                                                    required
                                                    placeholder="Ej: Introducción a Microcontroladores, Programación en C, Estructuras de Datos..."
                                                    maxlength="500"></textarea>
                                                <small class="form-text text-muted">Describa brevemente el tema que se
                                                    tratará en esta sesión</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3: Validación de Horas -->
                    <div id="step3-content" class="step-content">
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
                                        <option value="CANCELADA">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 4: Confirmación -->
                    <div id="step4-content" class="step-content">
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
                    <div class="modal-footer p-4" style="background: #f8f9fa; border-top: 1px solid #dee2e6;">
                        <button type="button" class="btn-modern" data-dismiss="modal"
                            style="background: #6c757d; color: white;">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>
                        <button type="button" class="btn-modern" id="btnPrevStep" onclick="prevStep()"
                            style="background: var(--light-gray); color: var(--dark-gray); display: none;">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Anterior
                        </button>
                        <button type="button" class="btn-modern btn-primary-modern" id="btnNextStep" onclick="nextStep()">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Siguiente
                        </button>
                        <button type="submit" class="btn-modern btn-primary-modern" id="btnSubmit" style="display: none;">
                            <i class="fas fa-save mr-2"></i>
                            Registrar Plan y Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <!-- MODAL EDITAR PLAN - DISEÑO MEJORADO -->
    <div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-modern">
                <div class="modal-header modal-header-modern">
                    <h5 class="modal-title-modern">
                        <i class="fas fa-edit mr-2"></i>
                        Cambiar Estado del Plan de Recuperación
                    </h5>
                    <button type="button" class="close modal-close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="frmPlanEdit" onsubmit="event.preventDefault(); updatePlan();">
                    @csrf
                    <input type="hidden" id="editIdPlan" name="id_plan">

                    <div class="modal-body p-4">
                        <div id="editPlanContent">
                            <!-- Estado actual -->
                            <div class="form-group mb-4">
                                <label class="form-label-modern">
                                    <i class="fas fa-info-circle mr-1"></i> Estado Actual
                                </label>
                                <div id="editEstadoActual" class="badge-modern estado-badge"
                                    style="font-size: 1rem; padding: 8px 16px;">
                                    <!-- Estado se cargará aquí -->
                                </div>
                            </div>

                            <!-- Cambiar Estado -->
                            <div class="form-group mb-4">
                                <label class="form-label-modern">
                                    <i class="fas fa-exchange-alt mr-1"></i> Cambiar Estado
                                </label>
                                <select id="editEstado" name="estado" class="form-control-modern"
                                    onchange="toggleObservacion()">
                                    <option value="">Seleccione un estado</option>
                                    <option value="PRESENTADO">PRESENTADO</option>
                                    <option value="APROBADO">APROBADO</option>
                                    <option value="OBSERVADO">OBSERVADO</option>
                                </select>
                            </div>

                            <!-- Observación (solo visible cuando estado es OBSERVADO) -->
                            <div id="observacionContainer" class="form-group mb-4" style="display: none;">
                                <label class="form-label-modern">
                                    <i class="fas fa-comment-alt mr-1"></i> Observación / Comentarios
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea id="editObservacion" name="observacion" class="form-control-modern" rows="4"
                                    placeholder="Ingrese las observaciones sobre el plan..."></textarea>
                                <small class="form-text text-muted">
                                    Campo obligatorio cuando el plan es observado.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-4" style="background: #f8f9fa; border-top: 1px solid #dee2e6;">
                        <button type="button" class="btn-modern" data-dismiss="modal"
                            style="background: #6c757d; color: white;">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn-modern btn-primary-modern">
                            <i class="fas fa-save mr-2"></i>
                            Actualizar Estado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL CONFIRMAR ENVÍO DE EMAIL PARA PLAN -->
    <div class="modal fade" id="emailConfirmModalPlan" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope mr-2"></i> Notificar cambio de estado del Plan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    ¿Deseas enviar un correo al docente informando el cambio de estado del Plan de Recuperación?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">No enviar</button>

                    <button class="btn btn-success" onclick="enviarCorreoPlan()">
                        Enviar correo
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL CONFIRMAR ENVÍO DE EMAIL -->
    <div class="modal fade" id="emailConfirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope mr-2"></i> Notificar por correo
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    ¿Deseas enviar un correo al docente informando el cambio de estado?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">No enviar</button>

                    <button class="btn btn-success" onclick="enviarCorreoPermiso()">
                        Enviar correo
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL CONFIRMAR ENVÍO DE EMAIL PARA APROBAR PLAN -->
    <div class="modal fade" id="emailConfirmModalPlan" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope mr-2"></i> Notificar por correo
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    ¿Deseas enviar un correo al docente informando que su plan de recuperación ha sido aprobado?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">No enviar</button>
                    <button class="btn btn-success" onclick="enviarCorreoPlan()">
                        Enviar correo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos para los badges de estado */
        .badge-cancelado {
            background-color: #dc3545;
            color: white;
        }
    </style>

@endsection

@section('js')
    <script src="{{ asset('viewresources/admin/plan_recuperacion/insert.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/plan_recuperacion/update.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/plan_recuperacion/delete.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/admin/plan_recuperacion/state.js?v=' . time()) }}"></script>
    <script>
        // Inicialización cuando el documento está listo
        $(document).ready(function () {
            // IMPORTANTE: Reinicializar Select2 cuando se abre el modal
            $('#nuevoPlanModal').on('shown.bs.modal', function () {
                // Destruir Select2 si ya existe
                if ($('#selectPermiso').hasClass("select2-hidden-accessible")) {
                    $('#selectPermiso').select2('destroy');
                }

                // Reinicializar Select2 con configuración completa
                $('#selectPermiso').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Buscar permiso por docente o código...',
                    allowClear: true,
                    language: 'es',
                    dropdownParent: $('#nuevoPlanModal'), // CRÍTICO: Esto hace que funcione en modales
                    width: '100%'
                });

                // Volver a adjuntar el evento change
                $('#selectPermiso').off('change').on('change', function () {
                    cargarHorasPermiso();
                });
            });

            // Limpiar cuando se cierra el modal
            $('#nuevoPlanModal').on('hidden.bs.modal', function () {
                // Resetear formulario
                $('#frmPlanInsert')[0].reset();
                $('#permisoInfo').hide();
                $('#totalHorasDisplay').text('0');
                $('#totalHorasRecuperar').val('');
                $('#nombreAsignatura').val('');
                $('#idAsignatura').val('');
                $('#codigoAsignatura').val('');

                // Resetear wizard al paso 1
                goToStep(1);

                // Resetear validación de FormValidation si existe
                if ($('#frmPlanInsert').data('formValidation')) {
                    $('#frmPlanInsert').data('formValidation').resetForm();
                }
            });
        });

        // Actualizar label del input file cuando se selecciona un archivo
        $(document).ready(function () {
            $('.custom-file-input').on('change', function () {
                var fileName = $(this).val().split('\\').pop();
                $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
            });

            // Manejar la transición entre el modal de edición y el modal de email
            $('#editPlanModal').on('hidden.bs.modal', function () {
                // Si hay un plan actualizado pendiente, mostrar el modal de email
                if (window.planActualizadoId) {
                    setTimeout(function () {
                        $('#emailConfirmModalPlan').modal('show');
                    }, 300); // Pequeño delay para asegurar que el modal anterior se cerró completamente
                }
            });

            // Limpiar la variable cuando se cierra el modal de email
            $('#emailConfirmModalPlan').on('hidden.bs.modal', function () {
                window.planActualizadoId = null;
            });
        });
    </script>

    <!-- Scripts de funcionalidad -->
    <script src="{{ asset('viewresources/admin/plan_recuperacion/aprobar.js?v=' . time()) }}"></script>
@endsection