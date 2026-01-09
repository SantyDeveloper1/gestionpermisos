@extends('docente.template.layout')
@section('titleGeneral', 'Página principal...')
@section('sectionGeneral')
    <h1>📚 Panel del Docente</h1>

    @auth
        <p>Has iniciado sesión correctamente.</p>

        <hr>

        <h3>👤 Docente logueado:</h3>
        <ul>
            <li><strong>ID:</strong> {{ auth()->user()->id }}</li>
            <li><strong>Nombre:</strong> {{ auth()->user()->name }}</li>
            <li><strong>Email:</strong> {{ auth()->user()->email }}</li>
            @if(auth()->user()->codigo)
                <li><strong>Código Docente:</strong> {{ auth()->user()->codigo }}</li>
                <li><strong>Especialidad:</strong> {{ auth()->user()->especialidad ?? 'No asignada' }}</li>
            @else
                <li><strong>Código Docente:</strong> <em>No asignado</em></li>
                <li><strong>Especialidad:</strong> <em>No asignada</em></li>
            @endif
        </ul>

        <p style="color: blue; font-weight: bold;">✅ Rol: DOCENTE</p>
    @endauth
@endsection