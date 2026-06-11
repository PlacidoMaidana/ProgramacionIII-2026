@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Enrollments</h1>
    <a href="{{ route('enrollments.create') }}" class="btn btn-primary">New enrollment</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Classroom (1:N)</th>
                    <th>Subject (N:M)</th>
                    <th>Grade</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->id }}</td>
                        <td>{{ optional($enrollment->student)->name }}</td>
                        <td>{{ optional(optional($enrollment->student)->classroom)->name }}</td>
                        <td>{{ optional($enrollment->subject)->name }} ({{ optional($enrollment->subject)->code }})</td>
                        <td>{{ $enrollment->grade ?? '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('enrollments.edit', $enrollment) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('enrollments.destroy', $enrollment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">No enrollments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $enrollments->links() }}</div>

<hr class="my-4">

<h2 class="h5">Demo AJAX con Axios + Filtros Eloquent</h2>
<p class="text-muted">Consultas GET a /enrollments/resumen, /enrollments/data y /enrollments/materias.</p>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="f-subject" class="form-label mb-1">Materia</label>
                <select id="f-subject" class="form-select">
                    <option value="">Todas</option>
                    @foreach(($subjects ?? collect()) as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="f-classroom" class="form-label mb-1">Aula</label>
                <select id="f-classroom" class="form-select">
                    <option value="">Todas</option>
                    @foreach(($classrooms ?? collect()) as $classroom)
                        <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="f-min-grade" class="form-label mb-1">Nota min</label>
                <input id="f-min-grade" type="number" class="form-control" min="0" max="10" step="0.01" placeholder="0">
            </div>
            <div class="col-md-2">
                <label for="f-max-grade" class="form-label mb-1">Nota max</label>
                <input id="f-max-grade" type="number" class="form-control" min="0" max="10" step="0.01" placeholder="10">
            </div>
            <div class="col-md-2">
                <label for="f-student" class="form-label mb-1">Alumno</label>
                <input id="f-student" type="text" class="form-control" placeholder="Nombre...">
            </div>
            <div class="col-12 d-flex gap-2 mt-2">
                <button id="btn-consultar" class="btn btn-primary">Consultar</button>
                <button id="btn-limpiar" class="btn btn-outline-secondary">Limpiar</button>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3" id="resumen-box">
    <div class="col-md-3"><div class="card"><div class="card-body"><small>Total inscripciones</small><h4 id="k-total">-</h4></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><small>Promedio</small><h4 id="k-prom">-</h4></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><small>Nota minima</small><h4 id="k-min">-</h4></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><small>Nota maxima</small><h4 id="k-max">-</h4></div></div></div>
</div>

<div class="card mb-3">
    <div class="card-header">Resultados filtrados (Eloquent)</div>
    <div class="table-responsive">
        <table class="table table-sm table-striped mb-0" id="tabla-filtrada">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Alumno</th>
                    <th>Aula</th>
                    <th>Materia</th>
                    <th>Nota</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="5" class="text-muted text-center py-3">Sin datos.</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Materias (consulta agregada)</div>
    <ul id="lista-materias" class="list-group list-group-flush">
        <li class="list-group-item text-muted">Sin datos.</li>
    </ul>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/axios/axios.min.js') }}"></script>
<script>
    function readFilters() {
        return {
            subject_id: document.getElementById('f-subject').value || undefined,
            classroom_id: document.getElementById('f-classroom').value || undefined,
            min_grade: document.getElementById('f-min-grade').value || undefined,
            max_grade: document.getElementById('f-max-grade').value || undefined,
            student: document.getElementById('f-student').value.trim() || undefined,
            per_page: 10,
        };
    }

    function renderResumen(payload) {
        document.getElementById('k-total').textContent = payload.totals.enrollments;
        document.getElementById('k-prom').textContent = payload.totals.avg_grade ?? '-';
        document.getElementById('k-min').textContent = payload.totals.min_grade ?? '-';
        document.getElementById('k-max').textContent = payload.totals.max_grade ?? '-';
    }

    function renderData(payload) {
        const tbody = document.querySelector('#tabla-filtrada tbody');
        tbody.innerHTML = '';

        if (!payload.data.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-muted text-center py-3">Sin resultados para los filtros.</td></tr>';
            return;
        }

        payload.data.forEach(function (row) {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + row.id + '</td>' +
                '<td>' + (row.student ?? '-') + '</td>' +
                '<td>' + (row.classroom ?? '-') + '</td>' +
                '<td>' + (row.subject ?? '-') + ' (' + (row.subject_code ?? '-') + ')</td>' +
                '<td>' + (row.grade ?? '-') + '</td>';
            tbody.appendChild(tr);
        });
    }

    function renderMaterias(payload) {
        const lista = document.getElementById('lista-materias');
        lista.innerHTML = '';

        if (!payload.data.length) {
            lista.innerHTML = '<li class="list-group-item text-muted">No hay materias para esos filtros.</li>';
            return;
        }

        payload.data.forEach(function (materia) {
            const item = document.createElement('li');
            item.className = 'list-group-item d-flex justify-content-between align-items-center';
            item.innerHTML =
                '<span>' + materia.nombre + ' (' + materia.codigo + ')</span>' +
                '<span class="badge bg-secondary">Inscriptos: ' + materia.inscriptos + ' | Prom: ' + (materia.promedio ?? '-') + '</span>';
            lista.appendChild(item);
        });
    }

    function cargarTodo() {
        const filters = readFilters();

        Promise.all([
            axios.get('/enrollments/resumen'),
            axios.get('/enrollments/data', { params: filters }),
            axios.get('/enrollments/materias', {
                params: {
                    q: filters.student,
                    min_enrollments: 0,
                }
            })
        ])
            .then(function (responses) {
                renderResumen(responses[0].data);
                renderData(responses[1].data);
                renderMaterias(responses[2].data);
            })
            .catch(function (error) {
                console.error(error);
                alert('No se pudieron cargar los datos de la demo AJAX.');
            });
    }

    document.getElementById('btn-consultar').addEventListener('click', cargarTodo);

    document.getElementById('btn-limpiar').addEventListener('click', function () {
        document.getElementById('f-subject').value = '';
        document.getElementById('f-classroom').value = '';
        document.getElementById('f-min-grade').value = '';
        document.getElementById('f-max-grade').value = '';
        document.getElementById('f-student').value = '';
        cargarTodo();
    });

    cargarTodo();
</script>
@endsection
