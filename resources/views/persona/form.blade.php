@extends('layouts.app')
@section('title', $persona ? 'Editar Persona' : 'Nueva Persona')
@section('content')
<div class="container mt-4">
    <h2>{{ $persona ? 'Editar Persona' : 'Nueva Persona' }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ $persona ? url('/persona/'.$persona->id) : url('/persona') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre"
                   value="{{ $persona->nombre ?? '' }}" placeholder="Ingrese su nombre">
        </div>

        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido"
                   value="{{ $persona->apellido ?? '' }}" placeholder="Ingrese su apellido">
        </div>

        <div class="mb-3">
            <label for="edad" class="form-label">Edad</label>
            <input type="number" class="form-control" id="edad" name="edad"
                   value="{{ $persona->edad ?? '' }}" placeholder="Ingrese su edad">
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $persona ? 'Actualizar' : 'Guardar' }}
        </button>
    </form>
</div>
@endsection