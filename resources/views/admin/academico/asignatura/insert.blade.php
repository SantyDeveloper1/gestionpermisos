@extends('admin.template.layout')
@section('titleGeneral', 'Insertar Nueva Asignatura')
@section('sectionGeneral')
<style>
    .card-borde-asig {
        background: #ffffff;
        border-radius: 8px;
        border: 2px solid rgba(0, 139, 255, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 0;
    }
</style>

<section class="content">
    <div class="container-fluid">

        <div class="card card-primary card-borde-asig">
            <div class="card-header">
                <h3 class="card-title">Datos de la Asignatura</h3>
            </div>

            <div class="card-body">
                <form id="frmAsignaturaInsert" action="{{ url('admin/academico/asignatura/insert') }}" method="post">
                    @csrf

                    <div class="row">

                        <!-- Código asignatura -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Código de Asignatura:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-barcode"></i></div>
                                    </div>
                                    <input type="text" class="form-control" id="codigo_asignatura"
                                           name="codigo_asignatura" placeholder="Ej: AIS11">
                                </div>
                            </div>
                        </div>

                        <!-- Nombre asignatura -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre de la Asignatura:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-book"></i></div>
                                    </div>
                                    <input type="text" class="form-control" id="nom_asignatura"
                                           name="nom_asignatura" placeholder="Ej: Programación I">
                                </div>
                            </div>
                        </div>

                        <!-- Créditos -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Créditos:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-star"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="creditos" name="creditos"
                                           placeholder="Ej: 4">
                                </div>
                            </div>
                        </div>

                        <!-- Horas Teoría -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Horas de Teoría:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-clock"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="horas_teoria"
                                           name="horas_teoria" placeholder="Ej: 2">
                                </div>
                            </div>
                        </div>

                        <!-- Horas Práctica -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Horas de Práctica:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-tools"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="horas_practica"
                                           name="horas_practica" placeholder="Ej: 2">
                                </div>
                            </div>
                        </div>

                        <!-- CICLO -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ciclo:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-layer-group"></i></div>
                                    </div>
                                    <select class="form-control" id="IdCiclo" name="IdCiclo">
                                        <option value="" disabled selected>Seleccione...</option>
                                        @foreach($ciclos as $c)
                                            <option value="{{ $c->IdCiclo }}">
                                                {{ $c->NombreCiclo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Tipo asignatura -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo de Asignatura:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-list-alt"></i></div>
                                    </div>
                                    <select class="form-control" id="tipo" name="tipo">
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="GENERAL">General</option>
                                        <option value="ESPECIFICO">Específico</option>
                                        <option value="ESPECIALIDAD">Especialidad</option>
                                        <option value="ELECTIVO">Electivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 text-left">
                            <a href="{{ url('admin/academico/asignatura') }}" class="btn btn-danger">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                        </div>

                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-primary"
                                    onclick="sendFrmAsignaturaInsert();">
                                <i class="fa fa-save"></i> Registrar Asignatura
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
<script src="{{ asset('viewresources/admin/asignatura/insert.js?=19122025') }}"></script>
@endsection
