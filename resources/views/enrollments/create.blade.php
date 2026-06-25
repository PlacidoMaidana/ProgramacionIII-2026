@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Create enrollment</h1>

<div class="alert alert-info">
    <strong>Guia JS para principiantes:</strong>
    Este formulario usa modales para seleccionar alumno y materia.
    El JavaScript de esta vista esta al final, en la seccion <em>@section('scripts')</em>.
</div>

<form method="POST" action="{{ route('enrollments.store') }}" class="card card-body">
    @csrf

    <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <label class="form-label mb-0">Student</label>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#studentModal">
                Select from modal
            </button>
        </div>
        <select id="student_id" name="student_id" class="form-select mt-2" required>
            <option value="">Select student</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->name }} ({{ $student->email }})</option>
            @endforeach
        </select>
        <small class="text-muted">The list comes from the controller using the Student model.</small>
    </div>

    <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <label class="form-label mb-0">Subject</label>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#subjectModal">
                Select from modal
            </button>
        </div>
        <select id="subject_id" name="subject_id" class="form-select mt-2" required>
            <option value="">Select subject</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }} ({{ $subject->code }})</option>
            @endforeach
        </select>
        <small class="text-muted">Enrollment belongsTo a subject, so we store subject_id.</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Grade (0-10)</label>
        <input type="number" name="grade" min="0" max="10" step="0.01" class="form-control" value="{{ old('grade') }}">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

{{-- Modal de alumnos: lista dinamica construida desde el select para evitar duplicar miles de nodos en el DOM. --}}
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

{{-- Modal de materias: mismo patron que alumnos para mantener comportamiento consistente. --}}
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
    // 1) Tenemos dos <select> en el formulario: student_id y subject_id.
    // 2) Al abrir cada modal, creamos botones con las opciones del <select>.
    // 3) Cuando el usuario hace click en un boton del modal, copiamos ese id al <select>.
    // 4) El formulario se envia normalmente con esos valores seleccionados.

    // Esta funcion toma un select y lo "convierte" en lista de botones para el modal.
    // Parametros:
    // - selectId: id del select fuente (ej: student_id)
    // - listId: id del contenedor donde se insertan los botones
    // - itemClass: clase CSS para diferenciar alumno o materia al capturar el click
    function buildListFromSelect(selectId, listId, itemClass) {
        // Buscamos elementos del DOM por id.
        var select = document.getElementById(selectId);
        var list = document.getElementById(listId);

        // Si no encontramos alguno, salimos para evitar errores.
        if (!select || !list) {
            return;
        }

        // Limpiamos la lista para reconstruirla desde cero en cada apertura del modal.
        list.innerHTML = '';

        // Recorremos todas las opciones del select.
        Array.from(select.options).forEach(function (option) {
            // Ignoramos la opcion vacia tipo "Select student".
            if (!option.value) {
                return;
            }

            // Creamos un boton por cada opcion valida.
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'list-group-item list-group-item-action ' + itemClass;

            // Guardamos el id de la opcion para usarlo al hacer click.
            button.dataset.id = option.value;

            // Bootstrap cerrara el modal automaticamente al hacer click.
            button.dataset.bsDismiss = 'modal';

            // Texto visible para el usuario.
            button.textContent = option.textContent;

            // Agregamos el boton a la lista del modal.
            list.appendChild(button);
        });
    }

    // Evento del modal de alumnos: antes de mostrarse, generamos su lista.
    var studentModal = document.getElementById('studentModal');
    if (studentModal) {
        studentModal.addEventListener('show.bs.modal', function () {
            buildListFromSelect('student_id', 'studentModalList', 'select-student');
        });
    }

    // Evento del modal de materias: mismo flujo que alumnos.
    var subjectModal = document.getElementById('subjectModal');
    if (subjectModal) {
        subjectModal.addEventListener('show.bs.modal', function () {
            buildListFromSelect('subject_id', 'subjectModalList', 'select-subject');
        });
    }

    // Delegacion de eventos:
    // Escuchamos clicks en todo el documento y verificamos si el click vino
    // de un boton .select-student o .select-subject.
    // Esto funciona aunque los botones se creen dinamicamente.
    document.addEventListener('click', function (event) {
        // Si se clickea un alumno dentro del modal, lo pasamos al select student_id.
        var studentButton = event.target.closest('.select-student');
        if (studentButton) {
            var studentSelect = document.getElementById('student_id');
            if (studentSelect) {
                studentSelect.value = studentButton.dataset.id;
            }
            return;
        }

        // Si se clickea una materia, la pasamos al select subject_id.
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
