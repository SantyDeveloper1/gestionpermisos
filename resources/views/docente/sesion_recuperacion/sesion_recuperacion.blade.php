@extends('docente.template.layout')

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

        /* NUEVO ESTILO: Grid de Tarjetas */
        .execution-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            padding: 0 35px 35px;
        }

        .execution-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            position: relative;
        }

        .execution-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 139, 220, 0.15);
            border-color: var(--primary-blue);
        }

        .card-header-gradient {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .card-header-gradient::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }

        .teacher-info {
            position: relative;
            z-index: 2;
        }

        .teacher-name {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .teacher-name i {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .card-subtitle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .permission-type {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .permission-days {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .card-body {
            padding: 25px;
        }

        .course-description {
            color: var(--dark-gray);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .progress-section {
            margin-bottom: 20px;
        }

        .progress-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .progress-title span {
            font-weight: 600;
            color: var(--dark-gray);
            font-size: 0.9rem;
        }

        .progress-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-blue);
        }

        .progress-bar-mini {
            height: 8px;
            background: var(--light-gray);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .progress-fill-mini {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        }

        .session-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--light-gray);
        }

        .session-date {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 8px 15px;
            border-radius: 10px;
            font-weight: 600;
            color: var(--primary-blue);
            min-width: 110px;
            text-align: center;
        }

        .session-count {
            color: var(--medium-gray);
            font-size: 0.9rem;
        }

        .session-count strong {
            color: var(--dark-gray);
        }

        .card-footer {
            padding: 20px 25px;
            background: #f8f9fa;
            border-top: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-completed {
            background: rgba(0, 184, 148, 0.1);
            color: var(--success-green);
            border: 1px solid rgba(0, 184, 148, 0.2);
        }

        .status-in-progress {
            background: rgba(108, 92, 231, 0.1);
            color: var(--purple);
            border: 1px solid rgba(108, 92, 231, 0.2);
        }

        .status-pending {
            background: rgba(253, 203, 110, 0.1);
            color: #e17055;
            border: 1px solid rgba(253, 203, 110, 0.2);
        }

        .btn-view-details {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-view-details:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 139, 220, 0.3);
            color: white;
            text-decoration: none;
        }

        /* Filtros y búsqueda */
        .filter-bar {
            background: white;
            border-radius: 15px;
            padding: 20px 25px;
            margin: 0 35px 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--light-gray);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }

        .search-input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 139, 220, 0.1);
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--medium-gray);
            font-size: 1rem;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            background: white;
            color: var(--dark-gray);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn.active {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            color: white;
        }

        .filter-btn:hover:not(.active) {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        /* Estadísticas rápidas */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 0 35px 25px;
        }

        .stat-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--light-gray);
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 139, 220, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, var(--success-green) 0%, #55efc4 100%);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, var(--warning-orange) 0%, #ffeaa7 100%);
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, var(--purple) 0%, #a29bfe 100%);
        }

        .stat-content h4 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-gray);
        }

        .stat-content p {
            margin: 5px 0 0 0;
            color: var(--medium-gray);
            font-size: 0.9rem;
        }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--medium-gray);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--light-gray);
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: var(--dark-gray);
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .execution-cards-grid {
                grid-template-columns: 1fr;
                padding: 0 20px 25px;
            }

            .filter-bar {
                margin: 0 20px 20px;
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                min-width: 100%;
            }

            .quick-stats {
                margin: 0 20px 20px;
                grid-template-columns: 1fr;
            }

            .module-header-gradient {
                padding: 20px;
            }

            .progress-summary {
                margin: -20px 20px 20px;
                padding: 20px;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.5s ease-out;
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
                            <br>
                        </p>
                    </div>
                </div>

                <!-- Resumen de Progreso -->
                <div class="progress-summary">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 10px;">
                                <i class="fas fa-chart-line mr-2"></i>
                                Vista de Tarjetas - Sesiones Activas
                            </h3>
                            <p style="color: var(--medium-gray); margin-bottom: 0;">
                                Visualiza todas las sesiones de recuperación en un formato intuitivo
                            </p>
                        </div>
                        <!--<div class="col-md-4 text-right">
                            <a href="{{ url('sesion_recuperacion?view=table') }}" class="btn btn-primary" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); color: white; border: none; padding: 10px 25px; border-radius: 10px; font-weight: 600;">
                                <i class="fas fa-table mr-2"></i>
                                Vista de Tabla
                            </a>
                        </div>-->
                    </div>
                </div>

                <!-- Estadísticas Rápidas -->
                <div class="quick-stats">
                    <div class="stat-box fade-in-up">
                        <div class="stat-icon blue">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h4>{{ count($sesionesEjecucion) }}</h4>
                            <p>Sesiones Totales</p>
                        </div>
                    </div>
                    <div class="stat-box fade-in-up" style="animation-delay: 0.1s;">
                        <div class="stat-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h4>{{ $sesionesEjecucion->where('estado_sesion', 'VALIDADA')->count() }}</h4>
                            <p>Sesiones Validadas</p>
                        </div>
                    </div>
                    <div class="stat-box fade-in-up" style="animation-delay: 0.2s;">
                        <div class="stat-icon orange">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h4>{{ $sesionesEjecucion->where('estado_sesion', 'REALIZADA')->count() }}</h4>
                            <p>En Progreso</p>
                        </div>
                    </div>
                    <div class="stat-box fade-in-up" style="animation-delay: 0.3s;">
                        <div class="stat-icon purple">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stat-content">
                            <h4>{{ $horasRecuperadasHoy }}</h4>
                            <p>Horas Hoy</p>
                        </div>
                    </div>
                </div>

                <!-- Barra de Filtros -->
                <div class="filter-bar">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" id="searchCards" placeholder="Buscar docente, curso o asignatura...">
                    </div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">
                            <i class="fas fa-th-large"></i>
                            Todas
                        </button>
                        <button class="filter-btn" data-filter="VALIDADA">
                            <i class="fas fa-check-circle"></i>
                            Validadas
                        </button>
                        <button class="filter-btn" data-filter="REALIZADA">
                            <i class="fas fa-sync-alt"></i>
                            En Progreso
                        </button>
                        <button class="filter-btn" data-filter="PROGRAMADA">
                            <i class="fas fa-clock"></i>
                            Programadas
                        </button>
                    </div>
                </div>

                <!-- Grid de Tarjetas -->
                <div class="execution-cards-grid" id="cardsContainer">
                    @php
                        // Agrupar sesiones por permiso
                        $sesionesPorPermiso = $sesionesEjecucion->groupBy(function($sesion) {
                            return $sesion->planRecuperacion->id_permiso ?? 'sin_permiso';
                        });
                    @endphp
                    
                    @forelse($sesionesPorPermiso as $idPermiso => $sesionesDelPermiso)
                        @php
                            // Tomar la primera sesión para obtener datos del permiso
                            $primerasesion = $sesionesDelPermiso->first();
                            $plan = $primerasesion->planRecuperacion;
                            $permiso = $plan->permiso ?? null;
                            $docente = $permiso->docente ?? null;
                            
                            // Calcular horas del plan (solo sesiones completadas)
                            $horasValidadas = $plan->sesiones()->where('estado_sesion', 'VALIDADA')->sum('horas_recuperadas') ?? 0;
                            $horasRealizadas = $plan->sesiones()->where('estado_sesion', 'REALIZADA')->sum('horas_recuperadas') ?? 0;
                            $horasRecuperadas = $horasValidadas + $horasRealizadas;
                            $porcentaje = $plan->total_horas_recuperar > 0 ? round(($horasRecuperadas / $plan->total_horas_recuperar) * 100) : 0;
                            
                            // Calcular sesiones del plan
                            $totalSesionesPlan = $plan->sesiones()->count() ?? 0;
                            $sesionesCompletadas = $plan->sesiones()->whereIn('estado_sesion', ['VALIDADA', 'REALIZADA'])->count() ?? 0;
                            
                            // Determinar el estado general (prioridad: VALIDADA > REALIZADA > PROGRAMADA)
                            $estadoGeneral = 'PROGRAMADA';
                            if ($sesionesDelPermiso->contains('estado_sesion', 'VALIDADA')) {
                                $estadoGeneral = 'VALIDADA';
                            } elseif ($sesionesDelPermiso->contains('estado_sesion', 'REALIZADA')) {
                                $estadoGeneral = 'REALIZADA';
                            }
                            
                            // Obtener todas las fechas de sesiones
                            $fechasSesiones = $sesionesDelPermiso->pluck('fecha_sesion')->sort();
                            $primeraFecha = $fechasSesiones->first();
                            $ultimaFecha = $fechasSesiones->last();
                            
                            // Crear string de búsqueda con todos los cursos
                            $cursosString = $sesionesDelPermiso->map(function($s) {
                                return $s->asignatura->nom_asignatura ?? '';
                            })->filter()->implode(' ');
                        @endphp
                        
                        <div class="execution-card" 
                             data-estado="{{ $estadoGeneral }}"
                             data-docente="{{ $docente->user->last_name ?? 'N/A' }}"
                             data-docente="{{ $docente->user->name ?? 'N/A' }}"
                             data-curso="{{ $cursosString }}"
                             data-fecha="{{ $primeraFecha }}"
                             data-permiso="{{ $idPermiso }}">
                            <!-- Encabezado de la Tarjeta -->
                            <div class="card-header-gradient">
                                <div class="teacher-info">
                                    <div class="teacher-name">
                                        <i class="fas fa-user-tie"></i>
                                        {{ $docente->user->last_name ?? 'Docente no disponible' }}
                                        {{ $docente->user->name?? 'Docente no disponible' }}
                                    </div>
                                    <div class="card-subtitle">
                                        <span class="permission-type">
                                            {{ $permiso->tipoPermiso->nombre ?? 'No especificado' }}
                                        </span>
                                        <span class="permission-days">
                                            {{ $permiso->dias_permiso ?? '0' }} días
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cuerpo de la Tarjeta -->
                            <div class="card-body">
                                <!-- ID de Permiso -->
                                <div style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 10px 15px; border-radius: 10px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-id-card" style="color: var(--primary-blue); font-size: 1.1rem;"></i>
                                    <span style="font-weight: 600; color: var(--dark-gray); font-size: 0.9rem;">Permiso:</span>
                                    <span style="font-weight: 700; color: var(--primary-blue); font-size: 1rem;">{{ $idPermiso }}</span>
                                </div>

                                <!-- Resumen de Sesiones -->
                                <div class="course-description">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <strong style="color: var(--primary-blue);">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $sesionesDelPermiso->count() }} {{ $sesionesDelPermiso->count() == 1 ? 'Sesión' : 'Sesiones' }}
                                        </strong>
                                        <button class="btn btn-sm" onclick="verDetallesSesiones('{{ $idPermiso }}')" 
                                                style="background: var(--primary-blue); color: white; border: none; padding: 5px 12px; border-radius: 8px; font-size: 0.8rem; cursor: pointer;">
                                            <i class="fas fa-list mr-1"></i> Ver Lista
                                        </button>
                                    </div>
                                </div>

                                <!-- Progreso de Horas -->
                                <div class="progress-section" style="margin-top: 20px;">
                                    <div class="progress-title">
                                        <span>Progreso Total</span>
                                        <div class="progress-value">
                                            {{ $horasRecuperadas }}/{{ $plan->total_horas_recuperar }} horas
                                        </div>
                                    </div>
                                    <div class="progress-bar-mini">
                                        <div class="progress-fill-mini" style="width: {{ min($porcentaje, 100) }}%"></div>
                                    </div>
                                </div>

                                <!-- Información de Sesión -->
                                <div class="session-info">
                                    <div class="session-date">
                                        @if($primeraFecha == $ultimaFecha)
                                            {{ \Carbon\Carbon::parse($primeraFecha)->format('d/m/Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($primeraFecha)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($ultimaFecha)->format('d/m/Y') }}
                                        @endif
                                    </div>
                                    <div class="session-count">
                                        <strong>{{ $sesionesCompletadas }}/{{ $totalSesionesPlan }}</strong> sesiones
                                    </div>
                                </div>
                                
                                <!-- Datos ocultos para el modal -->
                                <div id="sesiones-data-{{ $idPermiso }}" style="display: none;">
                                    @foreach($sesionesDelPermiso as $sesion)
                                        <div class="sesion-item" 
                                             data-asignatura="{{ $sesion->asignatura->nom_asignatura ?? 'No especificada' }}"
                                             data-fecha="{{ \Carbon\Carbon::parse($sesion->fecha_sesion)->format('d/m/Y') }}"
                                             data-horas="{{ $sesion->horas_recuperadas }}"
                                             data-estado="{{ $sesion->estado_sesion }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>


                            <!-- Pie de la Tarjeta -->
                            <div class="card-footer">
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <!-- Estado del Plan -->
                                    <div>
                                        @if($plan->estado_plan == 'APROBADO')
                                            <span class="status-badge" style="background: rgba(0, 184, 148, 0.1); color: #00b894; border: 1px solid rgba(0, 184, 148, 0.2);">
                                                <i class="fas fa-check-double mr-1"></i>
                                                Aprobado
                                            </span>
                                        @elseif($plan->estado_plan == 'PRESENTADO')
                                            <span class="status-badge" style="background: rgba(0, 123, 255, 0.1); color: #007bff; border: 1px solid rgba(0, 123, 255, 0.2);">
                                                <i class="fas fa-file-alt mr-1"></i>
                                                Presentado
                                            </span>
                                        @else
                                            <span class="status-badge" style="background: rgba(255, 107, 107, 0.1); color: #ff6b6b; border: 1px solid rgba(255, 107, 107, 0.2);">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                {{ $plan->estado_plan }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ url('admin/plan_recuperacion?permiso_id=' . $idPermiso) }}" 
                                   class="btn-view-details">
                                    <i class="fas fa-eye"></i>
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    @empty
                        <!-- Estado vacío -->
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <i class="fas fa-calendar-times"></i>
                            <h4>No hay sesiones de recuperación</h4>
                            <p>No se encontraron sesiones de recuperación activas.</p>
                            <a href="{{ url('sesion_recuperacion/create') }}" class="btn btn-primary mt-3" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); color: white; border: none; padding: 10px 25px; border-radius: 10px; font-weight: 600;">
                                <i class="fas fa-plus mr-2"></i>
                                Crear Primera Sesión
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de Detalles de Sesiones -->
    <div class="modal fade" id="modalDetallesSesiones" tabindex="-1" role="dialog" aria-labelledby="modalDetallesSesionesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%); color: white; border: none;">
                    <h5 class="modal-title" id="modalDetallesSesionesLabel" style="font-weight: 700;">
                        <i class="fas fa-list-ul mr-2"></i>
                        Detalle de Sesiones
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 25px;">
                    <div id="permiso-info" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-id-card" style="color: var(--primary-blue); font-size: 1.2rem;"></i>
                            <span style="font-weight: 600; color: var(--dark-gray);">Permiso:</span>
                            <span id="modal-permiso-id" style="font-weight: 700; color: var(--primary-blue); font-size: 1.1rem;"></span>
                        </div>
                    </div>
                    
                    <div id="sesiones-list" class="table-responsive">
                        <!-- Las sesiones se cargarán aquí dinámicamente -->
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--light-gray); padding: 15px 25px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px; padding: 8px 20px;">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Filtrado por estado
            $('.filter-btn').on('click', function() {
                const filter = $(this).data('filter');
                
                // Actualizar botón activo
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                
                // Filtrar tarjetas
                $('.execution-card').each(function() {
                    if (filter === 'all' || $(this).data('estado') === filter) {
                        $(this).show().addClass('fade-in-up');
                    } else {
                        $(this).hide().removeClass('fade-in-up');
                    }
                });
                
                // Mostrar mensaje si no hay resultados
                const visibleCards = $('.execution-card:visible').length;
                if (visibleCards === 0) {
                    $('#cardsContainer').append(`
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <i class="fas fa-search"></i>
                            <h4>No se encontraron resultados</h4>
                            <p>No hay sesiones con el filtro seleccionado.</p>
                            <button class="btn btn-outline-primary mt-3" onclick="$('.filter-btn[data-filter=\"all\"]').click()">
                                <i class="fas fa-undo mr-2"></i>
                                Mostrar todas
                            </button>
                        </div>
                    `);
                } else {
                    $('#cardsContainer .empty-state').remove();
                }
            });
            
            // Búsqueda en tiempo real
            $('#searchCards').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                
                $('.execution-card').each(function() {
                    const docente = $(this).data('docente').toLowerCase();
                    const curso = $(this).data('curso').toLowerCase();
                    const fecha = $(this).data('fecha');
                    
                    const match = docente.includes(searchTerm) || 
                                  curso.includes(searchTerm) ||
                                  fecha.includes(searchTerm);
                    
                    if (match || searchTerm === '') {
                        $(this).show().addClass('fade-in-up');
                    } else {
                        $(this).hide().removeClass('fade-in-up');
                    }
                });
                
                // Mostrar mensaje si no hay resultados
                const visibleCards = $('.execution-card:visible').length;
                if (visibleCards === 0 && searchTerm !== '') {
                    if ($('#cardsContainer .empty-state').length === 0) {
                        $('#cardsContainer').append(`
                            <div class="empty-state" style="grid-column: 1 / -1;">
                                <i class="fas fa-search"></i>
                                <h4>No se encontraron coincidencias</h4>
                                <p>No hay sesiones que coincidan con "${searchTerm}"</p>
                                <button class="btn btn-outline-primary mt-3" onclick="$('#searchCards').val('').trigger('keyup')">
                                    <i class="fas fa-times mr-2"></i>
                                    Limpiar búsqueda
                                </button>
                            </div>
                        `);
                    }
                } else {
                    $('#cardsContainer .empty-state').remove();
                }
            });
            
            
            // Animación de entrada
            $('.execution-card').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });
        });
        
        // Función para mostrar detalles de sesiones en modal
        function verDetallesSesiones(permisoId) {
            // Actualizar el ID del permiso en el modal
            $('#modal-permiso-id').text(permisoId);
            
            // Obtener los datos de las sesiones desde el div oculto
            const sesionesData = $('#sesiones-data-' + permisoId);
            const sesiones = sesionesData.find('.sesion-item');
            
            // Construir la tabla de sesiones
            let tableHTML = `
                <table class="table table-hover" style="margin-bottom: 0;">
                    <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <tr>
                            <th style="border: none; padding: 12px; font-weight: 600; color: var(--dark-gray);">
                                <i class="fas fa-book mr-1" style="color: var(--primary-blue);"></i>
                                Asignatura
                            </th>
                            <th style="border: none; padding: 12px; font-weight: 600; color: var(--dark-gray); text-align: center;">
                                <i class="fas fa-calendar mr-1" style="color: var(--primary-blue);"></i>
                                Fecha
                            </th>
                            <th style="border: none; padding: 12px; font-weight: 600; color: var(--dark-gray); text-align: center;">
                                <i class="fas fa-clock mr-1" style="color: var(--primary-blue);"></i>
                                Horas
                            </th>
                            <th style="border: none; padding: 12px; font-weight: 600; color: var(--dark-gray); text-align: center;">
                                <i class="fas fa-info-circle mr-1" style="color: var(--primary-blue);"></i>
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            sesiones.each(function() {
                const asignatura = $(this).data('asignatura');
                const fecha = $(this).data('fecha');
                const horas = $(this).data('horas');
                const estado = $(this).data('estado');
                
                let estadoBadge = '';
                if (estado === 'VALIDADA') {
                    estadoBadge = '<span class="badge" style="background: rgba(0, 184, 148, 0.1); color: #00b894; border: 1px solid rgba(0, 184, 148, 0.2); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;"><i class="fas fa-check-circle mr-1"></i>VALIDADA</span>';
                } else if (estado === 'REALIZADA') {
                    estadoBadge = '<span class="badge" style="background: rgba(108, 92, 231, 0.1); color: #6c5ce7; border: 1px solid rgba(108, 92, 231, 0.2); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;"><i class="fas fa-sync-alt mr-1"></i>REALIZADA</span>';
                } else {
                    estadoBadge = '<span class="badge" style="background: rgba(253, 203, 110, 0.1); color: #e17055; border: 1px solid rgba(253, 203, 110, 0.2); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;"><i class="fas fa-clock mr-1"></i>PROGRAMADA</span>';
                }
                
                tableHTML += `
                    <tr style="transition: all 0.2s ease;">
                        <td style="padding: 15px; border-top: 1px solid #e9ecef; font-weight: 500; color: var(--dark-gray);">
                            ${asignatura}
                        </td>
                        <td style="padding: 15px; border-top: 1px solid #e9ecef; text-align: center; color: var(--medium-gray);">
                            ${fecha}
                        </td>
                        <td style="padding: 15px; border-top: 1px solid #e9ecef; text-align: center; font-weight: 600; color: var(--primary-blue);">
                            ${horas} hrs
                        </td>
                        <td style="padding: 15px; border-top: 1px solid #e9ecef; text-align: center;">
                            ${estadoBadge}
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += `
                    </tbody>
                </table>
            `;
            
            // Insertar la tabla en el modal
            $('#sesiones-list').html(tableHTML);
            
            // Mostrar el modal
            $('#modalDetallesSesiones').modal('show');
        }
    </script>
@endsection