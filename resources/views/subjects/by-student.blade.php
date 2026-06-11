@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4 mb-3">Materias por estudiante</h1>

        <form method="GET" action="{{ route('subjects.by-student') }}" class="row g-2 mb-4">
            <div class="col-sm-8 col-md-6 col-lg-4">
                <label for="student_id" class="form-label">ID del estudiante</label>
                <input
                    type="number"
                    min="1"
                    class="form-control"
                    id="student_id"
                    name="student_id"
                    value="{{ $studentId }}"
                    required
                >
            </div>
            <div class="col-sm-4 col-md-3 col-lg-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Consultar</button>
            </div>
        </form>

        @if($studentId)
            <h2 class="h6">Resultado para estudiante ID: {{ $studentId }}</h2>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Materia</th>
                            <th>Código</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materias as $materia)
                            <tr>
                                <td>{{ $materia->id }}</td>
                                <td>{{ $materia->name }}</td>
                                <td>{{ $materia->code }}</td>
                                <td>{{ $materia->grade ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay materias para este estudiante.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
