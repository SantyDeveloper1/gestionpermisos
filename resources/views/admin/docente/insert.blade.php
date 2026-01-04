@extends('template.layout')
@section('titleGeneral', 'Insertar Nuevo Docente')

@section('sectionGeneral')
    <style>
        .card-borde-docente {
            background: #ffffff;
            border-radius: 8px;
            border: 2px solid rgba(0, 139, 255, 0.3); /* borde celeste */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* sombra suave */
            padding: 0;
        }
    </style>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-primary card-borde-docente">
                <div class="card-header">
                    <h3 class="card-title">Datos del Docente</h3>
                </div>

                <div class="card-body">

                    <form id="frmDocenteInsert" action="{{ url('docente/insert') }}" method="post">
                        @csrf
                        <div class="row">
                            <!-- Código UNAMBA -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Código UNAMBA:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                                        <input type="text" class="form-control" id="codigo_unamba"
                                               name="codigo_unamba" placeholder="Opcional">
                                    </div>
                                </div>
                            </div>
                            <!-- Grado académico -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Grado Académico:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                        <select class="form-control" id="grado_id" name="grado_id">
                                            <option disabled selected>Seleccione...</option>
                                            @foreach ($listGrados as $grado)
                                                <option value="{{ $grado->idGrados_academicos }}">
                                                    {{ $grado->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Categoría docente -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Categoría Docente:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                        <select class="form-control" id="categoria_id" name="categoria_id">
                                            <option disabled selected>Seleccione...</option>
                                            @foreach ($listCategorias as $cat)
                                                <option value="{{ $cat->idCategori_docente }}">
                                                    {{ $cat->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo contrato -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo de Contrato:</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-file-contract"></i></span>
                                        <select class="form-control" id="tipo_contrato_id" name="tipo_contrato_id">
                                            <option disabled selected>Seleccione...</option>
                                            @foreach ($listContratos as $tc)
                                                <option value="{{ $tc->idTipo_contrato }}">
                                                    {{ $tc->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6 text-left">
                                <a href="{{ url('docente') }}" class="btn btn-danger">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                            </div>

                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary"
                                        onclick="sendFrmDocenteInsert();">
                                    <i class="fa fa-save"></i> Registrar Docente
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('viewresources/docente/insert.js?=15112025') }}"></script>
@endsection
