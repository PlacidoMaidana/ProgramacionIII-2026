@extends('layouts.app')

@section('content')
@php
    $subjectsByStudent = \Illuminate\Support\Facades\DB::table('enrollments')
        ->join('subjects', 'subjects.id', '=', 'enrollments.subject_id')
        ->select('enrollments.student_id', 'subjects.code')
        ->orderBy('subjects.name')
        ->get()
        ->groupBy('student_id');
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Students</h1>
    <a href="{{ route('students.create') }}" class="btn btn-primary">New student</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Classroom (1:N)</th>
                    <th>Subjects (N:M)</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->id }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ optional($student->classroom)->name }}</td>
                        <td>
                            @php
                                $subjects = $subjectsByStudent->get($student->id, collect());
                            @endphp

                            @forelse($subjects as $subject)
                                <span class="badge text-bg-secondary">{{ $subject->code }}</span>
                            @empty
                                <span class="text-muted">No subjects</span>
                            @endforelse
                        </td>
                        <td class="text-end">
                              <button type="button" class="btn btn-sm btn-outline-primary" id="modalMateriasButton{{ $student->id }}" data-bs-toggle="modal" data-bs-target="#studentVentModal">
                                Modal Materias
                            </button>
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $students->links() }}</div>



{{-- Modal de alumnos para edicion: seleccion asistida sin recargar la pagina. --}}
<div class="modal fade" id="studentVentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Choose student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h1 class="h5 mb-3">Materias</h1>
                <div id="materiasList"></div>
         
            </div>
        </div>
    </div>
</div>



@endsection
@section('scripts')
<script src="{{ asset('vendor/axios/axios.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>  
<script>

 // Cuando se abre el modal de alumnos, generamos su lista.
    var studentVentModal = document.getElementById('studentVentModal');
    if (studentVentModal) {
        studentVentModal.addEventListener('show.bs.modal', function () {
           
             axios.get('subjects/by-student', { params: { student_id: {{ $student->id }} } })
                .then(function (res) {
                var materias = res.data;
                var materiasDiv = document.getElementById('materiasList');

                if (!materias.length) {
                    materiasDiv.innerHTML = '<p class="text-muted">No hay materias para este alumno.</p>';
                    return;
                }

                var html = '<ul class="list-group">';
                materias.forEach(function (m) {
                    html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                    html += m.name + ' (' + m.code + ')';
                    html += '<span class="badge bg-primary rounded-pill">' + (m.grade ?? '-') + '</span>';
                    html += '</li>';
                });
                html += '</ul>';

                materiasDiv.innerHTML = html;
            })
            .catch(function (err) {
                console.error(err);
            });

        });
    }
</script>

@endsection