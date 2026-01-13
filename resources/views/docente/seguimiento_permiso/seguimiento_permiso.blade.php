@extends('docente.template.layout')

@section('titleGeneral', 'Seguimiento de Permiso Docente')

@section('sectionGeneral')

    <style>
        :root {
            --primary-blue: #1e88e5;
            --secondary-blue: #42a5f5;
            --light-blue: #e3f2fd;
            --dark-blue: #1565c0;
            --text-dark: #333;
            --text-light: #666;
            --white: #ffffff;
            --border-color: #bbdefb;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --pending-color: #ffb74d;
        }

        /* Asegurar que el contenido no quede oculto detrás del header */
        section.content {
            padding-top: 20px !important;
        }

        .intro-section {
            background-color: var(--light-blue);
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 5px solid var(--primary-blue);
        }

        .intro-section h2 {
            color: var(--primary-blue);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .intro-section h2 i {
            font-size: 24px;
        }

        .intro-section p {
            color: var(--text-light);
            margin-bottom: 15px;
        }

        .highlight {
            background-color: var(--white);
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 600;
            color: var(--dark-blue);
            display: inline-block;
            border: 1px solid var(--border-color);
        }

        .tracking-section {
            margin-bottom: 40px;
        }

        .tracking-title {
            font-size: 22px;
            color: var(--primary-blue);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-blue);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tracking-title i {
            font-size: 24px;
        }

        .tracking-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .tracking-table th {
            background-color: var(--primary-blue);
            color: var(--white);
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
        }

        .tracking-table td {
            padding: 20px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .tracking-table tr:last-child td {
            border-bottom: none;
        }

        .tracking-table tr:nth-child(even) {
            background-color: var(--light-blue);
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .status-pending {
            background-color: var(--pending-color);
            color: #5d4037;
        }

        .status-approved {
            background-color: var(--success-color);
            color: white;
        }

        .status-review {
            background-color: var(--warning-color);
            color: white;
        }

        .action-button {
            background-color: var(--secondary-blue);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .action-button:hover {
            background-color: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .contact-section {
            background-color: var(--light-blue);
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin-top: 40px;
        }

        .contact-title {
            font-size: 22px;
            color: var(--primary-blue);
            margin-bottom: 15px;
        }

        .contact-info {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-blue);
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        .contact-button {
            background-color: var(--primary-blue);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 18px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .contact-button:hover {
            background-color: var(--dark-blue);
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .status-timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-top: 40px;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 4px;
            background-color: var(--border-color);
            z-index: 1;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            width: 20%;
        }

        .timeline-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: var(--white);
            border: 4px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 18px;
            margin-bottom: 10px;
        }

        .timeline-step.active .timeline-icon {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            color: white;
        }

        .timeline-step.completed .timeline-icon {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .timeline-step.rejected .timeline-icon {
            background-color: #f44336;
            border-color: #f44336;
            color: white;
        }

        .timeline-label {
            font-size: 14px;
            text-align: center;
            color: var(--text-light);
            font-weight: 500;
        }

        .timeline-step.active .timeline-label,
        .timeline-step.completed .timeline-label {
            color: var(--primary-blue);
            font-weight: 600;
        }

        .timeline-step.rejected .timeline-label {
            color: #f44336;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .container {
                border-radius: 8px;
            }

            .header {
                padding: 20px;
            }

            .content {
                padding: 20px;
            }

            .tracking-table {
                display: block;
                overflow-x: auto;
            }

            .status-timeline {
                flex-wrap: wrap;
                justify-content: center;
                gap: 20px;
            }

            .timeline-step {
                width: 40%;
                margin-bottom: 20px;
            }

            .status-timeline::before {
                display: none;
            }

            .contact-info {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>

    <section class="content">
        <div class="container-fluid">
            <div class="intro-section">
                <h2><i class="fas fa-info-circle"></i> Información importante</h2>
                <p>Bienvenido al sistema de seguimiento de permisos docentes. Aquí puedes consultar el estado actual de tu
                    solicitud de permiso.</p>
                <p class="highlight"><i class="fas fa-bell"></i> Recibirás una notificación cuando tu permiso sea aprobado y
                    esté listo para su uso.</p>
            </div>

            <div class="tracking-section">
                <h2 class="tracking-title"><i class="fas fa-search"></i> Consulta tu permiso docente</h2>

                <!-- Permiso -->
                <select name="permiso_id" id="permiso_id" class="form-control select2bs4">
                    <option value="">Seleccione permiso</option>
                    @foreach($permisos as $p)
                        <option value="{{ $p->id_permiso }}">{{ $p->id_permiso }} {{ $p->tipoPermiso->nombre ?? 'N/A' }}</option>
                    @endforeach
                </select>

                @php
                    // Mapeo de estados del permiso a pasos del timeline
                    $estadoActual = $permiso->estado_permiso ?? 'SOLICITADO';
                    
                    // Definir los pasos del timeline
                    $pasos = [
                        1 => ['nombre' => 'Solicitud enviada', 'icono' => 'fa-file-upload'],
                        2 => ['nombre' => 'Revisión inicial', 'icono' => 'fa-clipboard-check'],
                        3 => ['nombre' => 'Aprobación/Rechazo', 'icono' => 'fa-user-check'],
                        4 => ['nombre' => 'En Recuperación', 'icono' => 'fa-sync-alt'],
                        5 => ['nombre' => 'Completado', 'icono' => 'fa-check-circle'],
                    ];
                    
                    // Determinar qué paso está activo y el estado de cada paso
                    $pasoActivo = 1;
                    $esRechazado = $estadoActual === 'RECHAZADO';
                    
                    if ($estadoActual === 'SOLICITADO') {
                        $pasoActivo = 1;
                    } elseif ($estadoActual === 'RECHAZADO') {
                        $pasoActivo = 3; // Se detiene en Aprobación/Rechazo
                    } elseif ($estadoActual === 'APROBADO') {
                        $pasoActivo = 3;
                    } elseif ($estadoActual === 'EN_RECUPERACION') {
                        $pasoActivo = 4;
                    } elseif (in_array($estadoActual, ['RECUPERADO', 'CERRADO'])) {
                        $pasoActivo = 5;
                    }
                @endphp

                <div id="timeline-container" class="status-timeline">
                    @foreach($pasos as $numeroPaso => $paso)
                        @php
                            $claseEstado = '';
                            $textoLabel = $paso['nombre'];
                            
                            // Si está rechazado, solo los primeros 3 pasos se muestran
                            if ($esRechazado) {
                                if ($numeroPaso < 3) {
                                    $claseEstado = 'completed';
                                } elseif ($numeroPaso == 3) {
                                    $claseEstado = 'rejected'; // Nueva clase para rechazado
                                    $textoLabel = 'Rechazado'; // Cambiar texto
                                }
                                // Los pasos 4 y 5 quedan sin clase (grises/inactivos)
                            } else {
                                // Flujo normal (aprobado)
                                if ($numeroPaso < $pasoActivo) {
                                    $claseEstado = 'completed';
                                } elseif ($numeroPaso == $pasoActivo) {
                                    $claseEstado = 'active';
                                }
                                
                                // Si es paso 3 y está aprobado, cambiar texto
                                if ($numeroPaso == 3 && $estadoActual === 'APROBADO') {
                                    $textoLabel = 'Aprobado';
                                }
                            }
                        @endphp
                        <div class="timeline-step {{ $claseEstado }}">
                            <div class="timeline-icon"><i class="fas {{ $paso['icono'] }}"></i></div>
                            <div class="timeline-label">{{ $textoLabel }}</div>
                        </div>
                    @endforeach
                </div>
                <br>

                <table id="tablaExample2" class="tracking-table table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID Permiso</th>
                            <th>Tipo de Permiso</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Días</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @if($permiso)
                            <tr>
                                <td><strong>{{ $permiso->id_permiso }}</strong></td>
                                <td>{{ $permiso->tipoPermiso->nombre ?? 'N/A' }}</td>
                                <td>{{ $permiso->fecha_inicio ? $permiso->fecha_inicio->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $permiso->fecha_fin ? $permiso->fecha_fin->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $permiso->dias_permiso }} días</td>
                                <td>
                                    @php
                                        $badgeClass = 'status-pending';
                                        $estadoTexto = $permiso->estado_permiso;
                                        
                                        if ($permiso->estado_permiso === 'APROBADO') {
                                            $badgeClass = 'status-approved';
                                            $estadoTexto = 'Aprobado';
                                        } elseif ($permiso->estado_permiso === 'EN_RECUPERACION') {
                                            $badgeClass = 'status-review';
                                            $estadoTexto = 'En Recuperación';
                                        } elseif ($permiso->estado_permiso === 'RECUPERADO') {
                                            $badgeClass = 'status-approved';
                                            $estadoTexto = 'Recuperado';
                                        } elseif ($permiso->estado_permiso === 'CERRADO') {
                                            $badgeClass = 'status-approved';
                                            $estadoTexto = 'Cerrado';
                                        } elseif ($permiso->estado_permiso === 'SOLICITADO') {
                                            $badgeClass = 'status-pending';
                                            $estadoTexto = 'Solicitado';
                                        } elseif ($permiso->estado_permiso === 'RECHAZADO') {
                                            $badgeClass = 'status-review';
                                            $estadoTexto = 'Rechazado';
                                        }
                                    @endphp
                                    <span class="status-badge {{ $badgeClass }}">{{ $estadoTexto }}</span>
                                </td>
                                <td>
                                    <button class="action-button" onclick="verDetallePermiso('{{ $permiso->id_permiso }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px;">
                                    <i class="fas fa-info-circle" style="font-size: 2rem; color: var(--text-light);"></i>
                                    <p style="margin-top: 10px; color: var(--text-light);">No tienes permisos registrados</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="contact-section">
                <h3 class="contact-title">¿Necesitas ayuda con tu permiso docente?</h3>
                <div class="contact-info">
                    <i class="fas fa-phone-alt"></i>
                    <span>620 - 2333 Opción 3</span>
                </div>
                <p style="margin-bottom: 20px; color: var(--text-light);">Nuestro equipo está disponible de lunes a viernes
                    de 8:00 AM a 5:00 PM</p>
                <button class="contact-button" onclick="contactar()">
                    <i class="fas fa-comments"></i> CONTÁCTANOS
                </button>
            </div>

            <!-- MODAL ACTUALIZAR ESTADO -->
            <div id="modalActualizarEstado" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
                <div style="background-color: white; border-radius: 12px; padding: 30px; max-width: 500px; width: 90%; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);">
                    <h3 style="color: var(--primary-blue); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-edit"></i> Actualizar Estado del Permiso
                    </h3>
                    
                    <form id="formActualizarEstado">
                        @csrf
                        <input type="hidden" id="permisoIdActualizar" name="id_permiso">
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-dark);">
                                <i class="fas fa-info-circle"></i> Nuevo Estado:
                            </label>
                            <select id="nuevoEstado" name="estado_permiso" required style="width: 100%; padding: 12px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 16px;">
                                <option value="">Seleccione un estado...</option>
                                <option value="SOLICITADO">🟡 Solicitado</option>
                                <option value="APROBADO">🟢 Aprobado</option>
                                <option value="RECHAZADO">🔴 Rechazado</option>
                                <option value="EN_RECUPERACION">🔵 En Recuperación</option>
                                <option value="RECUPERADO">🟣 Recuperado</option>
                                <option value="CERRADO">⚫ Cerrado</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-dark);">
                                <i class="fas fa-comment"></i> Observación (Opcional):
                            </label>
                            <textarea id="observacion" name="observacion" rows="3" style="width: 100%; padding: 12px; border: 2px solid var(--border-color); border-radius: 6px; font-size: 16px; resize: vertical;" placeholder="Ingrese alguna observación si es necesario..."></textarea>
                        </div>

                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <button type="button" onclick="cerrarModalActualizarEstado()" style="background-color: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" style="background-color: var(--primary-blue); color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL DETALLE PERMISO -->
            <div id="modalDetallePermiso" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center; overflow-y: auto; padding: 20px;">
                <div style="background-color: white; border-radius: 12px; padding: 30px; max-width: 900px; width: 90%; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); margin: auto; max-height: 90vh; overflow-y: auto;">
                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 3px solid var(--primary-blue); padding-bottom: 15px;">
                        <h3 style="color: var(--primary-blue); margin: 0; display: flex; align-items: center; gap: 10px; font-size: 24px;">
                            <i class="fas fa-file-alt"></i> Detalle del Permiso
                        </h3>
                        <button onclick="cerrarModalDetalle()" style="background: none; border: none; font-size: 28px; color: var(--text-light); cursor: pointer; transition: color 0.3s;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Información Básica -->
                    <div style="background-color: var(--light-blue); border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                        <h4 style="color: var(--primary-blue); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-info-circle"></i> Información Básica
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                            <div>
                                <strong style="color: var(--text-dark);">Estado:</strong>
                                <p id="detalle_estado" style="margin: 5px 0 0 0;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Días de Permiso:</strong>
                                <p id="detalle_dias" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Horas Afectadas:</strong>
                                <p id="detalle_horas" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div style="background-color: #fff3e0; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #ff9800;">
                        <h4 style="color: #e65100; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-calendar-alt"></i> Fechas Importantes
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div>
                                <strong style="color: var(--text-dark);">Fecha Solicitud:</strong>
                                <p id="detalle_fecha_solicitud" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Fecha Inicio:</strong>
                                <p id="detalle_fecha_inicio" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Fecha Fin:</strong>
                                <p id="detalle_fecha_fin" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Fecha Resolución:</strong>
                                <p id="detalle_fecha_resolucion" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Docente -->
                    <div style="background-color: #e8f5e9; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #4caf50;">
                        <h4 style="color: #2e7d32; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-user"></i> Información del Docente
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div>
                                <strong style="color: var(--text-dark);">Nombres:</strong>
                                <p id="detalle_docente_nombres" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Apellidos:</strong>
                                <p id="detalle_docente_apellidos" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Documento:</strong>
                                <p id="detalle_docente_documento" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de Permiso -->
                    <div style="background-color: #f3e5f5; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #9c27b0;">
                        <h4 style="color: #6a1b9a; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-clipboard-list"></i> Tipo de Permiso
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div>
                                <strong style="color: var(--text-dark);">Nombre:</strong>
                                <p id="detalle_tipo_nombre" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Con Goce de Haber:</strong>
                                <p id="detalle_tipo_goce" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Requiere Recuperación:</strong>
                                <p id="detalle_tipo_recupero" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                        </div>
                        <div style="margin-top: 15px;">
                            <strong style="color: var(--text-dark);">Descripción:</strong>
                            <p id="detalle_tipo_descripcion" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                        </div>
                    </div>

                    <!-- Motivo y Observación -->
                    <div style="background-color: #fff; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 2px solid var(--border-color);">
                        <div style="margin-bottom: 15px;">
                            <strong style="color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-comment-dots"></i> Motivo:
                            </strong>
                            <p id="detalle_motivo" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px; line-height: 1.6;">-</p>
                        </div>
                        <div>
                            <strong style="color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-sticky-note"></i> Observación:
                            </strong>
                            <p id="detalle_observacion" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px; line-height: 1.6;">-</p>
                        </div>
                    </div>

                    <!-- Plan de Recuperación -->
                    <div id="detalle_plan_container" style="background-color: #e3f2fd; border-radius: 8px; padding: 20px; border-left: 4px solid var(--primary-blue); display: none;">
                        <h4 style="color: var(--primary-blue); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-tasks"></i> Plan de Recuperación
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                            <div>
                                <strong style="color: var(--text-dark);">Estado Plan:</strong>
                                <p id="detalle_plan_estado" style="margin: 5px 0 0 0;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Fecha Presentación:</strong>
                                <p id="detalle_plan_fecha" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                            <div>
                                <strong style="color: var(--text-dark);">Total Horas a Recuperar:</strong>
                                <p id="detalle_plan_horas" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                            </div>
                        </div>
                        <div style="margin-top: 15px;">
                            <strong style="color: var(--text-dark);">Observación del Plan:</strong>
                            <p id="detalle_plan_observacion" style="margin: 5px 0 0 0; color: var(--text-light); font-size: 16px;">-</p>
                        </div>
                    </div>

                    <!-- Botón Cerrar -->
                    <div style="margin-top: 25px; text-align: center;">
                        <button onclick="cerrarModalDetalle()" style="background-color: var(--primary-blue); color: white; border: none; padding: 12px 30px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 16px; transition: all 0.3s ease;">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script src="{{ asset('viewresources/docente/seguimiento_permiso/seguimiento.js?v=' . time()) }}"></script>
    <script src="{{ asset('viewresources/docente/seguimiento_permiso/detalle.js?v=' . time()) }}"></script>
@endsection