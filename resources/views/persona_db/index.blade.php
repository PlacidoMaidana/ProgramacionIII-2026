@extends('layouts.app')
@section('title', 'Listado de Personas')
@section('content')
<div class="container mt-4">
    <h2>Listado de Personas</h2>
    <a href="{{ url('/db_persona/nueva') }}" class="btn btn-primary mb-3">Nuevo</a>
   

    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>E-mail</th>
                <th>Edad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personas as $persona)
            <tr>
                <td>{{ $persona->id }}</td>
                <td>{{ $persona->nombre }}</td>
                <td>{{ $persona->email }}</td>
                <td>{{ $persona->edad }}</td>
                <td>
                    <a href="{{ url('/db_persona/'.$persona->id.'/editar') }}" class="btn btn-warning btn-sm">Editar</a>
                    <a href="{{ url('/db_persona/'.$persona->id.'/eliminar') }}" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
