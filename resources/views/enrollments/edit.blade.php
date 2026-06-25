@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Edit enrollment</h1>

<div class="alert alert-info">
    <strong>Guia JS para principiantes:</strong>
    Esta vista repite el mismo patron de modales de la vista create.
    Busca el bloque JavaScript al final, en <em>@section('scripts')</em>.
</div>

<form method="POST" action="{{ route('enrollments.update', $enrollment) }}" class="card card-body">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <label class="form-label mb-0">Student</label>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#studentModal">
                Select from modal
            </button>
        </div>
        <select id="student_id" name="student_id" class="form-select mt-2" required>
            @foreach($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id', $enrollment->student_id) == $student->id)>{{ $student->name }} ({{ $student->email }})</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <label class="form-label mb-0">Subject</label>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#subjectModal">
                Select from modal
            </button>
        </div>
        <select id="subject_id" name="subject_id" class="form-select mt-2" required>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(old('subject_id', $enrollment->subject_id) == $subject->id)>{{ $subject->name }} ({{ $subject->code }})</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Grade (0-10)</label>
        <input type="number" name="grade" min="0" max="10" step="0.01" class="form-control" value="{{ old('grade', $enrollment->grade) }}">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

{{-- Modal de alumnos para edicion: seleccion asistida sin recargar la pagina. --}}
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Choose student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="studentModalList" class="list-group"></div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de materias para edicion: replica el flujo de create para facilitar la explicacion en clase. --}}
<div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subjectModalLabel">Choose subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="subjectModalList" class="list-group"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // GUIA RAPIDA (nivel inicial):
    // En editar hacemos exactamente la misma logica que en crear.
    // - Abrimos modal
    // - Mostramos botones basados en el select
    // - Al elegir, actualizamos el valor del select

    // Funcion auxiliar para convertir opciones del select en botones del modal.
    // Esto nos permite mantener una sola fuente de datos: el select del formulario.
    function buildListFromSelect(selectId, listId, itemClass) {
        var select = document.getElementById(selectId);
        var list = document.getElementById(listId);

        // Prevencion de errores: si no existe un elemento, terminamos.
        if (!select || !list) {
            return;
        }

        // Limpiamos lista para no duplicar botones.
        list.innerHTML = '';

        Array.from(select.options).forEach(function (option) {
            // Salteamos opcion vacia.
            if (!option.value) {
                return;
            }

            // Creamos boton para cada opcion valida.
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'list-group-item list-group-item-action ' + itemClass;
            button.dataset.id = option.value;

            // Cierre automatico del modal al seleccionar.
            button.dataset.bsDismiss = 'modal';
            button.textContent = option.textContent;
            list.appendChild(button);
        });
    }

    // Cuando se abre el modal de alumnos, generamos su lista.
    var studentModal = document.getElementById('studentModal');
    if (studentModal) {
        studentModal.addEventListener('show.bs.modal', function () {
            buildListFromSelect('student_id', 'studentModalList', 'select-student');
        });
    }

    // Cuando se abre el modal de materias, generamos su lista.
    var subjectModal = document.getElementById('subjectModal');
    if (subjectModal) {
        subjectModal.addEventListener('show.bs.modal', function () {
            buildListFromSelect('subject_id', 'subjectModalList', 'select-subject');
        });
    }

    // Delegacion de eventos:
    // Un solo listener maneja todos los botones creados dinamicamente.
    document.addEventListener('click', function (event) {
        // Caso 1: seleccion de alumno.
        var studentButton = event.target.closest('.select-student');
        if (studentButton) {
            var studentSelect = document.getElementById('student_id');
            if (studentSelect) {
                studentSelect.value = studentButton.dataset.id;
            }
            return;
        }

        // Caso 2: seleccion de materia.
        var subjectButton = event.target.closest('.select-subject');
        if (subjectButton) {
            var subjectSelect = document.getElementById('subject_id');
            if (subjectSelect) {
                subjectSelect.value = subjectButton.dataset.id;
            }
        }
    });
</script>
@endsection
