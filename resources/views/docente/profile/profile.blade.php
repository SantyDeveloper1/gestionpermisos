@extends('docente.template.layout')

@section('titleGeneral', 'Perfil de Usuario')

@section('sectionGeneral')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form id="userProfileForm" action="{{ route('docente.profile.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <!-- Profile Card -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile text-center">
                                <div class="mb-3 position-relative">
                                    @php
                                        $profileImagePath = $user->image
                                            ? asset('storage/' . $user->image)
                                            : asset('storage/usuarios/users.webp');
                                    @endphp

                                    <img id="profileImage" class="profile-user-img img-fluid img-circle"
                                        src="{{ $profileImagePath }}" alt="Foto de perfil"
                                        style="width: 150px; height: 150px; object-fit: cover;">

                                    <!-- Botón para cambiar imagen -->
                                    <button type="button" class="btn btn-sm btn-primary mt-2"
                                        onclick="document.getElementById('imageInput').click()">
                                        <i class="fas fa-camera mr-1"></i> Cambiar foto
                                    </button>
                                    <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;"
                                        onchange="previewImage(event)">

                                    <!-- Previsualización de imagen -->
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <small class="text-muted">Vista previa:</small>
                                        <img id="preview" class="img-fluid rounded mt-1" style="max-width: 100px;">
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <input type="text" class="form-control text-center font-weight-bold"
                                        value="{{ $user->name }} {{ $user->last_name }}" readonly>
                                </div>

                                <p class="text-muted mb-2">
                                    <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                                </p>
                                <p class="mb-2
                                {{ $user->status == 'active' ? 'bg-success text-white' : 'bg-danger text-white' }}
                                rounded px-2 py-1 d-inline-block">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ $user->status == 'active' ? 'Activo' : 'Inactivo' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 ">
                        <!-- Información detallada -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user-circle mr-1"></i> Información del Usuario
                                </h3>
                            </div>


                            <div class="card-body ">
                                <div class="row">
                                    <!-- Columna izquierda -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                <i class="fas fa-user mr-1"></i> Nombre
                                            </label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                <i class="fas fa-user mr-1"></i> Apellido
                                            </label>
                                            <input type="text" name="last_name" class="form-control"
                                                value="{{ $user->last_name }}">
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                <i class="fas fa-envelope mr-1"></i> Correo Electrónico
                                            </label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ $user->email }}">
                                            @if($user->email_verified_at)
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> Verificado
                                                </small>
                                            @else
                                                <small class="text-warning">
                                                    <i class="fas fa-exclamation-circle"></i> No verificado
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Columna derecha -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                <i class="fas fa-id-card mr-1"></i> Tipo de Documento
                                            </label>
                                            <select name="document_type" class="form-control">
                                                <option value="">Seleccione tipo de documento</option>
                                                <option value="DNI" {{ $user->document_type == 'DNI' ? 'selected' : '' }}>DNI
                                                </option>
                                                <option value="PASAPORTE" {{ $user->document_type == 'PASAPORTE' ? 'selected' : '' }}>Pasaporte</option>
                                                <option value="CE" {{ $user->document_type == 'CE' ? 'selected' : '' }}>Carné
                                                    de Extranjería</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                <i class="fas fa-hashtag mr-1"></i> Número de Documento
                                            </label>
                                            <input type="text" name="document_number" class="form-control"
                                                value="{{ $user->document_number }}" placeholder="Número de documento">
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">
                                                <i class="fas fa-venus-mars mr-1"></i> Género
                                            </label>
                                            <select name="gender" class="form-control">
                                                <option value="">Seleccione un género</option>
                                                <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Masculino
                                                </option>
                                                <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>
                                                    Femenino</option>
                                                <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Otro
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de guardar -->
                            <div class="card-footer text-right">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                                </a>
                                <button type="button" class="btn btn-primary" onclick="sendFrmProfileUpdate()">
                                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('css')
    <style>
        .profile-user-img {
            border: 3px solid #adb5bd;
            padding: 3px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-user-img:hover {
            border-color: #007bff;
            transform: scale(1.05);
        }

        .profile-default-img {
            border: 3px solid #adb5bd;
            padding: 3px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-default-img:hover {
            border-color: #007bff;
            transform: scale(1.05);
        }

        .form-group label {
            font-size: 0.9rem;
            color: #495057;
            margin-bottom: 0.3rem;
        }

        .card-outline {
            border-top: 3px solid;
        }

        .btn-change-photo {
            position: absolute;
            bottom: 10px;
            right: 10px;
            opacity: 0.8;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('viewresources/docente/profile/profile_update.js') }}"></script>
@endsection