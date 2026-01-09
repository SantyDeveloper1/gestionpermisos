@extends('docente.template.layout')

@section('titleGeneral', 'Gestión de Evidencias de Recuperación')

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
            --purple: #6c5ce7;
            --teal: #00cec9;
            --dark-gray: #2d3436;
            --medium-gray: #636e72;
            --light-gray: #dfe6e9;
            --yellow: #fdcb6e;
            --indigo: #5c6bc0;
        }

        .card-borde {
            background: #ffffff;
            border-radius: 10px;
            border: 2px solid rgba(0, 139, 220, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .thead-custom th {
            background: linear-gradient(135deg, #008BDC 0%, #00A3E8 100%);
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .table-responsive {
            border-radius: 12px;
        }

        .evidence-module {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 139, 220, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .module-header-evidence {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            padding: 30px 35px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .module-header-evidence::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="rgba(255,255,255,0.05)" d="M0,0 L100,0 L100,100 Z"/></svg>');
            background-size: cover;
        }

        .module-title-evidence {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 2;
        }

        .module-subtitle-evidence {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 400;
            margin: 0;
            position: relative;
            z-index: 2;
        }

        .evidence-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 35px;
        }

        .stat-card-evidence {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .stat-card-evidence:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .stat-icon-evidence {
            width: 65px;
            height: 65px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            flex-shrink: 0;
        }

        .icon-acta {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        }

        .icon-asistencia {
            background: linear-gradient(135deg, var(--success-green) 0%, #55efc4 100%);
        }

        .icon-captura {
            background: linear-gradient(135deg, var(--purple) 0%, #a29bfe 100%);
        }

        .icon-otro {
            background: linear-gradient(135deg, var(--warning-orange) 0%, #ffeaa7 100%);
        }

        .stat-content-evidence h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: var(--dark-gray);
        }

        .stat-content-evidence p {
            margin: 0;
            color: var(--medium-gray);
            font-size: 0.95rem;
        }

        .upload-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 30px;
            margin: 0 35px 30px;
            border: 2px dashed rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .upload-section::before {
            content: '📁';
            position: absolute;
            right: -30px;
            bottom: -30px;
            font-size: 8rem;
            opacity: 0.1;
            transform: rotate(15deg);
        }

        .upload-title {
            color: var(--primary-blue);
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
        }

        .upload-zone {
            border: 3px dashed var(--primary-blue);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .upload-zone:hover {
            background: var(--light-blue);
            transform: scale(1.02);
        }

        .upload-zone.dragover {
            background: var(--light-blue);
            border-color: var(--success-green);
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 12px;
        }

        .upload-text h4 {
            color: var(--dark-gray);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .upload-text p {
            color: var(--medium-gray);
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .file-preview-container {
            display: none;
            margin-top: 20px;
            animation: fadeIn 0.5s ease;
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

        .file-preview {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .file-icon {
            font-size: 2rem;
            color: var(--primary-blue);
        }

        .file-info {
            flex: 1;
        }

        .file-name {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 5px;
            word-break: break-all;
        }

        .file-size {
            color: var(--medium-gray);
            font-size: 0.85rem;
        }

        .file-remove {
            color: var(--danger-red);
            cursor: pointer;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .file-remove:hover {
            transform: scale(1.2);
        }

        .form-grid-evidence {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .form-group-evidence {
            position: relative;
        }

        .form-label-evidence {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-gray);
            font-size: 0.95rem;
        }

        .form-control-evidence {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 1rem;
            line-height: 1.5;
            transition: all 0.3s ease;
            background: white;
            color: var(--dark-gray);
        }

        .form-control-evidence.select2 {
            padding: 0;
        }

        select.form-control-evidence {
            height: auto;
            min-height: 48px;
            appearance: auto;
        }

        .form-control-evidence:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 139, 220, 0.1);
            outline: none;
        }

        .btn-evidence {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary-evidence {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 139, 220, 0.3);
        }

        .btn-primary-evidence:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 139, 220, 0.4);
        }

        .btn-success-evidence {
            background: linear-gradient(135deg, var(--success-green) 0%, #55efc4 100%);
            color: white;
        }

        .table-evidence-container {
            margin: 0 35px 40px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        .table-evidence {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
        }

        .table-evidence thead {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        }

        .table-evidence th {
            padding: 20px 18px;
            color: white;
            font-weight: 600;
            text-align: left;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-evidence th:first-child {
            border-radius: 12px 0 0 0;
        }

        .table-evidence th:last-child {
            border-radius: 0 12px 0 0;
        }

        .table-evidence td {
            padding: 22px 18px;
            border-bottom: 1px solid var(--light-gray);
            vertical-align: middle;
        }

        .table-evidence tbody tr {
            transition: all 0.3s ease;
        }

        .table-evidence tbody tr:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
        }

        .evidence-type-badge {
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

        .badge-acta {
            background: rgba(0, 139, 220, 0.1);
            color: var(--primary-blue);
            border: 1px solid rgba(0, 139, 220, 0.2);
        }

        .badge-asistencia {
            background: rgba(0, 184, 148, 0.1);
            color: var(--success-green);
            border: 1px solid rgba(0, 184, 148, 0.2);
        }

        .badge-captura {
            background: rgba(108, 92, 231, 0.1);
            color: var(--purple);
            border: 1px solid rgba(108, 92, 231, 0.2);
        }

        .badge-otro {
            background: rgba(253, 203, 110, 0.1);
            color: #e17055;
            border: 1px solid rgba(253, 203, 110, 0.2);
        }

        .evidence-file {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .file-icon-table {
            font-size: 1.8rem;
            color: var(--primary-blue);
        }

        .file-info-table {
            flex: 1;
        }

        .file-name-table {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 3px;
            word-break: break-all;
        }

        .file-meta {
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
            color: var(--medium-gray);
        }

        .action-buttons-evidence {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
        }

        .btn-action-evidence {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-action-evidence:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-view-evidence {
            background: var(--primary-blue);
        }

        .btn-download-evidence {
            background: var(--success-green);
        }

        .btn-delete-evidence {
            background: var(--danger-red);
        }

        .modal-evidence {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.2);
        }

        .modal-header-evidence {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 25px 30px;
            border: none;
        }

        .modal-title-evidence {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .evidence-preview {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px solid var(--light-gray);
        }

        .preview-container {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--medium-gray);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-text h4 {
            color: var(--dark-gray);
            margin-bottom: 10px;
            font-size: 1.3rem;
        }

        .empty-text p {
            margin-bottom: 20px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        @media (max-width: 768px) {
            .module-header-evidence {
                padding: 20px;
            }

            .module-title-evidence {
                font-size: 1.5rem;
            }

            .evidence-stats {
                margin: 20px;
                grid-template-columns: 1fr;
            }

            .upload-section {
                margin: 0 20px 20px;
                padding: 20px;
            }

            .table-evidence-container {
                margin: 0 20px 20px;
            }

            .table-evidence {
                display: block;
                overflow-x: auto;
            }

            .form-grid-evidence {
                grid-template-columns: 1fr;
            }
        }

        /* Animaciones personalizadas */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slide-in {
            animation: slideIn 0.5s ease;
        }

        /* Estilos para el drag and drop */
        .drop-area {
            position: relative;
        }

        .drop-overlay {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 139, 220, 0.9);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            z-index: 10;
        }

        /* Estilos para diferentes tipos de archivos */
        .file-pdf .file-icon {
            color: #e74c3c;
        }

        .file-image .file-icon {
            color: #9b59b6;
        }

        .file-word .file-icon {
            color: #2980b9;
        }

        .file-excel .file-icon {
            color: #27ae60;
        }

        .file-generic .file-icon {
            color: #95a5a6;
        }
    </style>

    <section class="content">
        <div class="container-fluid">
            <!-- Módulo de Gestión de Evidencias -->
            <div class="evidence-module">
                <!-- Encabezado del Módulo -->
                <div class="module-header-evidence">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="module-title-evidence">
                                <i class="fas fa-file-contract"></i>
                                Gestión de Evidencias de Recuperación
                            </h1>
                            <p class="module-subtitle-evidence">
                                Sistema de carga y gestión de evidencias sustentatorias de sesiones de recuperación
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="mb-3">
                                <span class="evidence-type-badge badge-acta">
                                    <i class="fas fa-file-signature"></i>
                                    Acta
                                </span>
                                <span class="evidence-type-badge badge-asistencia ml-2">
                                    <i class="fas fa-clipboard-list"></i>
                                    Asistencia
                                </span>
                                <span class="evidence-type-badge badge-captura ml-2">
                                    <i class="fas fa-camera"></i>
                                    Captura
                                </span>
                                <span class="evidence-type-badge badge-otro ml-2">
                                    <i class="fas fa-file-alt"></i>
                                    Otro
                                </span>
                            </div>
                            <small class="text-white opacity-75">Sustento obligatorio para todas las sesiones</small>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Evidencias -->
                <div class="evidence-stats">
                    <div class="stat-card-evidence">
                        <div class="stat-icon-evidence icon-acta">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div class="stat-content-evidence">
                            <h3>{{ $totalActas }}</h3>
                            <p>Actas Registradas</p>
                        </div>
                    </div>
                    <div class="stat-card-evidence">
                        <div class="stat-icon-evidence icon-asistencia">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-content-evidence">
                            <h3>{{ $totalAsistencias }}</h3>
                            <p>Listas de Asistencia</p>
                        </div>
                    </div>
                    <div class="stat-card-evidence">
                        <div class="stat-icon-evidence icon-captura">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="stat-content-evidence">
                            <h3>{{ $totalCapturas }}</h3>
                            <p>Capturas de Pantalla</p>
                        </div>
                    </div>
                    <div class="stat-card-evidence">
                        <div class="stat-icon-evidence icon-otro">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content-evidence">
                            <h3>{{ $totalOtros }}</h3>
                            <p>Otros Documentos</p>
                        </div>
                    </div>
                </div>

                <!-- Banner de Sesión Seleccionada -->
                @if(isset($sesionSeleccionada) && $sesionSeleccionada)
                    <div
                        style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 15px; padding: 25px; margin: 30px 35px; border-left: 5px solid var(--primary-blue); box-shadow: 0 5px 20px rgba(0, 139, 220, 0.15);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 style="color: var(--primary-blue); font-weight: 700; margin-bottom: 15px;">
                                    <i class="fas fa-filter mr-2"></i>
                                    Filtrando Evidencias por Sesión
                                </h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div style="margin-bottom: 10px;">
                                            <small style="color: var(--medium-gray); display: block; font-size: 0.85rem;">Sesión
                                                ID</small>
                                            <strong
                                                style="color: var(--dark-gray); font-size: 1.1rem;">#{{ $sesionSeleccionada->id_sesion }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="margin-bottom: 10px;">
                                            <small
                                                style="color: var(--medium-gray); display: block; font-size: 0.85rem;">Docente</small>
                                            <strong style="color: var(--dark-gray);">
                                                {{ $sesionSeleccionada->planRecuperacion->permiso->docente->user->last_name }}, 
                                                {{ $sesionSeleccionada->planRecuperacion->permiso->docente->user->name }}
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div style="margin-bottom: 10px;">
                                            <small
                                                style="color: var(--medium-gray); display: block; font-size: 0.85rem;">Fecha</small>
                                            <strong style="color: var(--primary-blue);">
                                                {{ \Carbon\Carbon::parse($sesionSeleccionada->fecha_sesion)->format('d/m/Y') }}
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="margin-bottom: 10px;">
                                            <small style="color: var(--medium-gray); display: block; font-size: 0.85rem;">Horas
                                                Recuperadas</small>
                                            <strong style="color: var(--success-green); font-size: 1.2rem;">
                                                {{ $sesionSeleccionada->horas_recuperadas }} horas
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="{{ url('evidencia_recuperacion') }}" class="btn-evidence btn-primary-evidence"
                                    style="white-space: nowrap;">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Ver Todas las Evidencias
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Sección de Carga de Evidencias -->
                <div class="upload-section">
                    <div class="upload-title">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Cargar Nueva Evidencia
                    </div>

                    <!-- Zona de Drag & Drop -->
                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">
                            <h4>Arrastra y suelta tus archivos aquí</h4>
                            <p>o haz clic para seleccionar archivos</p>
                            <input type="file" id="fileInput" multiple style="display: none;"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                            <button type="button" class="btn-evidence btn-primary-evidence"
                                onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-folder-open"></i>
                                Seleccionar Archivos
                            </button>
                            <p class="mt-3" style="font-size: 0.85rem; color: var(--medium-gray);">
                                Formatos aceptados: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX (Máx. 10MB por archivo)
                            </p>
                        </div>
                    </div>

                    <!-- Previsualización de Archivos -->
                    <div id="filePreviewContainer" class="file-preview-container">
                        <h5 style="color: var(--dark-gray); margin-bottom: 15px; font-weight: 600;">
                            <i class="fas fa-file-alt mr-2"></i>
                            Archivos Seleccionados
                        </h5>
                        <div id="filePreviews"></div>
                    </div>

                    <!-- Formulario de Metadata -->
                    <form id="frmEvidenciaInsert" onsubmit="event.preventDefault(); submitEvidence();">
                        @csrf
                        <input type="hidden" name="id_sesion" id="id_sesion" value="{{ request('sesion_id') }}">

                        <div class="form-grid-evidence">
                            <div class="form-group form-group-evidence">
                                <label class="form-label-evidence">Tipo de Evidencia *</label>
                                <select name="tipo_evidencia" class="form-control form-control-evidence">
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="ACTA">Acta de Recuperación</option>
                                    <option value="ASISTENCIA">Lista de Asistencia</option>
                                    <option value="CAPTURA">Captura de Pantalla</option>
                                    <option value="OTRO">Otro Documento</option>
                                </select>
                            </div>

                            <div class="form-group form-group-evidence">
                                <label class="form-label-evidence">Sesión de Recuperación *</label>
                                <select name="id_sesion_select" class="form-control form-control-evidence select2">
                                    <option value="">Buscar sesión...</option>
                                    @foreach($sesiones as $sesion)
                                        <option value="{{ $sesion->id_sesion }}" {{ request('sesion_id') == $sesion->id_sesion ? 'selected' : '' }}>
                                            Sesión #{{ $sesion->id_sesion }} -
                                            {{ $sesion->planRecuperacion->permiso->docente->apellido_paterno }} -
                                            {{ $sesion->fecha_sesion }} ({{ $sesion->horas_recuperadas }} horas)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group form-group-evidence">
                                <label class="form-label-evidence">Descripción (Opcional)</label>
                                <input type="text" name="descripcion" class="form-control form-control-evidence"
                                    placeholder="Breve descripción del archivo...">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn-evidence btn-success-evidence">
                                <i class="fas fa-save"></i>
                                Guardar Evidencia
                            </button>
                            <button type="button" class="btn-evidence" onclick="clearForm()"
                                style="background: var(--light-gray); color: var(--dark-gray); margin-left: 10px;">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de Evidencias -->
                <div class="table-evidence-container">
                    @if($evidencias->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-file-import"></i>
                            </div>
                            <div class="empty-text">
                                <h4>No hay evidencias registradas</h4>
                                <p>Comienza cargando evidencias de recuperación para las sesiones realizadas.</p>
                                <button class="btn-evidence btn-primary-evidence" onclick="scrollToUpload()">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>
                                    Cargar Primera Evidencia
                                </button>
                            </div>
                        </div>
                    @else
                        <table id="tablaExample2" class="table-evidence table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Sesión</th>
                                    <th>Tipo</th>
                                    <th>Archivo</th>
                                    <th>Fecha Subida</th>
                                    <th class="all">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evidencias as $evidencia)
                                    <tr class="slide-in" data-type="{{ $evidencia->tipo_evidencia }}">
                                        <td>
                                            <div style="text-align: center; color: var(--dark-gray); font-weight: 600;">
                                                {{ $loop->iteration }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong style="color: var(--dark-gray);">
                                                    Sesión #{{ $evidencia->sesionRecuperacion->id_sesion }}
                                                </strong><br>
                                                <small style="color: var(--medium-gray);">
                                                    {{ $evidencia->sesionRecuperacion->planRecuperacion->permiso->docente->apellido_paterno }}
                                                    - {{ $evidencia->sesionRecuperacion->fecha_sesion }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($evidencia->tipo_evidencia == 'ACTA')
                                                <span class="evidence-type-badge badge-acta">
                                                    <i class="fas fa-file-signature"></i>
                                                    Acta
                                                </span>
                                            @elseif($evidencia->tipo_evidencia == 'ASISTENCIA')
                                                <span class="evidence-type-badge badge-asistencia">
                                                    <i class="fas fa-clipboard-list"></i>
                                                    Asistencia
                                                </span>
                                            @elseif($evidencia->tipo_evidencia == 'CAPTURA')
                                                <span class="evidence-type-badge badge-captura">
                                                    <i class="fas fa-camera"></i>
                                                    Captura
                                                </span>
                                            @else
                                                <span class="evidence-type-badge badge-otro">
                                                    <i class="fas fa-file-alt"></i>
                                                    Otro
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="evidence-file">
                                                @php
                                                    $fileExt = pathinfo($evidencia->archivo, PATHINFO_EXTENSION);
                                                    $fileClass = 'file-generic';
                                                    $fileIcon = 'fa-file';

                                                    if (in_array(strtolower($fileExt), ['pdf'])) {
                                                        $fileClass = 'file-pdf';
                                                        $fileIcon = 'fa-file-pdf';
                                                    } elseif (in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif'])) {
                                                        $fileClass = 'file-image';
                                                        $fileIcon = 'fa-file-image';
                                                    } elseif (in_array(strtolower($fileExt), ['doc', 'docx'])) {
                                                        $fileClass = 'file-word';
                                                        $fileIcon = 'fa-file-word';
                                                    } elseif (in_array(strtolower($fileExt), ['xls', 'xlsx'])) {
                                                        $fileClass = 'file-excel';
                                                        $fileIcon = 'fa-file-excel';
                                                    }
                                                @endphp

                                                <div class="file-icon-table {{ $fileClass }}">
                                                    <i class="fas {{ $fileIcon }}"></i>
                                                </div>
                                                <div class="file-info-table">
                                                    <div class="file-name-table">{{ basename($evidencia->archivo) }}</div>
                                                    <div class="file-meta">
                                                        <span><i class="fas fa-calendar"></i>
                                                            {{ $evidencia->fecha_subida->format('d/m/Y') }}</span>
                                                        <span><i class="fas fa-clock"></i>
                                                            {{ $evidencia->fecha_subida->format('H:i') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $evidencia->fecha_subida->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-evidence">
                                                @php
                                                    $fileExt = strtolower(pathinfo($evidencia->archivo, PATHINFO_EXTENSION));
                                                    $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']);
                                                    $verUrl = $isImage 
                                                        ? url('docente/evidencia_recuperacion/ver/' . $evidencia->id_evidencia)
                                                        : asset($evidencia->archivo);
                                                @endphp
                                                <a href="{{ $verUrl }}" 
                                                   class="btn-action-evidence btn-view-evidence" 
                                                   title="Ver evidencia"
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn-action-evidence btn-download-evidence"
                                                    onclick="downloadEvidence('{{ $evidencia->id_evidencia }}')"
                                                    title="Descargar archivo">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button class="btn-action-evidence btn-delete-evidence"
                                                    onclick="deleteEvidencia('{{ $evidencia->id_evidencia }}')"
                                                    title="Eliminar evidencia">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Paginación -->
                        @if($evidencias->hasPages())
                            <div class="p-4 border-top">
                                {{ $evidencias->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL VISOR DE EVIDENCIAS -->
    <div class="modal fade" id="viewEvidenceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content modal-evidence">
                <div class="modal-header modal-header-evidence">
                    <h5 class="modal-title-evidence">
                        <i class="fas fa-search"></i>
                        Visualizar Evidencia
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="evidence-preview">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 id="evidenceTitle" style="color: var(--dark-gray); font-weight: 700; margin: 0;">
                                Cargando...
                            </h5>
                            <div class="action-buttons-evidence">
                                <button class="btn-action-evidence btn-download-evidence" id="btnDownloadModal">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>

                        <div id="evidenceContent" class="preview-container">
                            <!-- Contenido cargado dinámicamente -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-evidence">
                                <label class="form-label-evidence">Tipo de Evidencia</label>
                                <input type="text" class="form-control-evidence" id="viewType" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-evidence">
                                <label class="form-label-evidence">Sesión Relacionada</label>
                                <input type="text" class="form-control-evidence" id="viewSession" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group-evidence">
                                <label class="form-label-evidence">Fecha de Subida</label>
                                <input type="text" class="form-control-evidence" id="viewUploadDate" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-evidence">
                                <label class="form-label-evidence">Docente</label>
                                <input type="text" class="form-control-evidence" id="viewTeacher" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top">
                    <button type="button" class="btn-evidence" data-dismiss="modal"
                        style="background: var(--light-gray); color: var(--dark-gray);">
                        <i class="fas fa-times mr-2"></i>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('viewresources/docente/evidencia/insert.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/docente/evidencia/delete.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/docente/evidencia/preview_evidencia.js?v=' . time()) }}"></script>
    <script>
        // Inicialización
        $(document).ready(function () {
            // Las funciones de carga de archivos están en insert.js
        });


        // Utilidades
        function initializeSelect2() {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Seleccione una opción',
                allowClear: true
            });
        }

        function scrollToUpload() {
            document.querySelector('.upload-section').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    </script>
@endsection