@extends('docente.template.layout')
@section('titleGeneral', 'Panel del Docente')
@section('sectionGeneral')

<div class="teacher-dashboard">
    <!-- Encabezado con bienvenida -->
    <div class="dashboard-header">
        <div class="welcome-section">
            <h1 class="welcome-title">
                <i class="fas fa-chalkboard-teacher"></i> Bienvenido, {{ auth()->user()->name }}
            </h1>
            <p class="welcome-subtitle">Panel de control docente</p>
        </div>
        <div class="user-badge">
            <div class="badge-content">
                <i class="fas fa-user-tie"></i>
                <span class="badge-text">DOCENTE</span>
            </div>
        </div>
    </div>

    <!-- Tarjetas informativas -->
    <div class="info-cards">
        <!-- Tarjeta de información personal -->
        <div class="info-card personal-info">
            <div class="card-header">
                <i class="fas fa-user-circle"></i>
                <h3>Información Personal</h3>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <span class="info-label">
                        <i class="fas fa-envelope"></i> Email:
                    </span>
                    <span class="info-value">{{ auth()->user()->email }}</span>
                </div>
                
                @if(auth()->user()->codigo)
                <div class="info-item">
                    <span class="info-label">
                        <i class="fas fa-graduation-cap"></i> Código:
                    </span>
                    <span class="info-value badge-code">{{ auth()->user()->codigo }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">
                        <i class="fas fa-book"></i> Especialidad:
                    </span>
                    <span class="info-value">{{ auth()->user()->especialidad ?? 'No asignada' }}</span>
                </div>
                @else
                <div class="info-item">
                    <span class="info-label">
                        <i class="fas fa-graduation-cap"></i> Código:
                    </span>
                    <span class="info-value not-assigned">No asignado</span>
                </div>
                <div class="info-item">
                    <span class="info-label">
                        <i class="fas fa-book"></i> Especialidad:
                    </span>
                    <span class="info-value not-assigned">No asignada</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta de estado -->
        <div class="info-card status-card">
            <div class="card-header">
                <i class="fas fa-check-circle"></i>
                <h3>Estado de Sesión</h3>
            </div>
            <div class="card-body">
                <div class="status-indicator">
                    <div class="status-dot active"></div>
                    <div class="status-text">
                        <strong>Sesión activa</strong>
                        <small>Conectado como docente</small>
                    </div>
                </div>
                <div class="session-info">
                    <p><i class="fas fa-sign-in-alt"></i> Has iniciado sesión correctamente</p>
                    <p><i class="fas fa-clock"></i> Último acceso: {{ date('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .teacher-dashboard {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .welcome-section .welcome-title {
        color: #2c3e50;
        font-size: 28px;
        margin-bottom: 5px;
    }

    .welcome-section .welcome-subtitle {
        color: #7f8c8d;
        font-size: 16px;
    }

    .welcome-title i {
        color: #3498db;
        margin-right: 15px;
    }

    .user-badge {
        background: linear-gradient(135deg, #3498db, #2c3e50);
        padding: 12px 25px;
        border-radius: 50px;
        color: white;
    }

    .badge-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .badge-text {
        font-weight: bold;
        font-size: 16px;
    }

    .info-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-5px);
    }

    .personal-info {
        border-top: 5px solid #3498db;
    }

    .status-card {
        border-top: 5px solid #2ecc71;
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f1f1;
    }

    .card-header i {
        font-size: 24px;
        color: inherit;
    }

    .personal-info .card-header {
        color: #3498db;
    }

    .status-card .card-header {
        color: #2ecc71;
    }

    .card-header h3 {
        margin: 0;
        font-size: 20px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #555;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-value {
        font-weight: 600;
        color: #2c3e50;
    }

    .badge-code {
        background: #e3f2fd;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: bold;
        color: #1976d2;
    }

    .not-assigned {
        color: #e74c3c;
        font-style: italic;
    }

    .status-indicator {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .status-dot.active {
        background: #2ecc71;
        box-shadow: 0 0 10px rgba(46, 204, 113, 0.5);
    }

    .session-info p {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #555;
        margin: 10px 0;
    }

    .quick-actions {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .quick-actions h3 {
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .action-button {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .action-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .action-button i {
        font-size: 30px;
        margin-bottom: 10px;
    }

    .action-button span {
        font-weight: 600;
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }
        
        .info-cards {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .action-buttons {
            grid-template-columns: 1fr;
        }
        
        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
    }
</style>

@endsection