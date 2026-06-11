@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Edit subject</h1>
<div id="subject-meta" data-subject-id="{{ $subject->id }}" data-subject-name="{{ e($subject->name) }}"></div>

<form method="POST" action="{{ route('subjects.update', $subject) }}" class="card card-body">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $subject->name) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Code</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $subject->code) }}" required>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<hr class="my-4">

<h2 class="h5 mb-3">Laboratorio JS + Axios (incremental)</h2>
<p class="text-muted mb-4">Pequenas acciones para practicar eventos, DOM, consola y consultas AJAX.</p>

<div class="card mb-3">
    <div class="card-header">Paso 1: Boton + alerta</div>
    <div class="card-body d-flex gap-2 align-items-center">
        <button type="button" id="btn-alerta" class="btn btn-sm btn-dark">Mostrar alerta</button>
        <span class="text-muted">Objetivo: entender onclick y llamada a funcion.</span>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Paso 2: Consola para debug</div>
    <div class="card-body d-flex gap-2 align-items-center">
        <button type="button" id="btn-consola" class="btn btn-sm btn-secondary">Enviar a consola</button>
        <span class="text-muted">Objetivo: usar console.log con variables.</span>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Paso 3: Checkbox que habilita boton</div>
    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="chk-activar-axios">
            <label class="form-check-label" for="chk-activar-axios">Activar consultas Axios</label>
        </div>
        <button type="button" id="btn-check-accion" class="btn btn-sm btn-primary" disabled>Accion habilitada</button>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Paso 4: Lista select + evento change</div>
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="sel-tema" class="form-label">Tema</label>
                <select id="sel-tema" class="form-select">
                    <option value="">-- Elegir --</option>
                    <option value="eventos">Eventos basicos</option>
                    <option value="dom">Manipulacion DOM</option>
                    <option value="axios">Consultas con Axios</option>
                </select>
            </div>
            <div class="col-md-8">
                <div id="out-tema" class="alert alert-light border mb-0">Aun sin seleccion.</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Paso 5: Text input (entry text) en vivo</div>
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="txt-alumno" class="form-label">Buscar alumno</label>
                <input id="txt-alumno" type="text" class="form-control" placeholder="Escribe un nombre...">
            </div>
            <div class="col-md-7">
                <div id="out-texto" class="alert alert-light border mb-0">Esperando texto...</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Paso 6: Lista local incremental</div>
    <div class="card-body">
        <div class="row g-2 align-items-end mb-2">
            <div class="col-md-5">
                <label for="txt-item" class="form-label">Nuevo item</label>
                <input id="txt-item" type="text" class="form-control" placeholder="Ej: Revisar promesas">
            </div>
            <div class="col-md-3 d-grid">
                <button type="button" id="btn-agregar-item" class="btn btn-sm btn-success">Agregar a lista</button>
            </div>
        </div>
        <ul id="lista-local" class="list-group">
            <li class="list-group-item text-muted">Sin items.</li>
        </ul>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Paso 7: Axios GET resumen</div>
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
        <button type="button" id="btn-cargar-resumen" class="btn btn-sm btn-outline-primary" disabled>Cargar resumen</button>
        <span id="out-resumen" class="text-muted">Sin consulta aun.</span>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Paso 8: Axios GET materias con filtro</div>
    <div class="card-body">
        <div class="row g-2 align-items-end mb-2">
            <div class="col-md-4">
                <label for="txt-filtro-materia" class="form-label">Filtro materia (q)</label>
                <input id="txt-filtro-materia" type="text" class="form-control" placeholder="Ej: Progra">
            </div>
            <div class="col-md-3 d-grid">
                <button type="button" id="btn-cargar-materias" class="btn btn-sm btn-outline-primary" disabled>Consultar materias</button>
            </div>
        </div>
        <ul id="lista-materias-axios" class="list-group">
            <li class="list-group-item text-muted">Sin resultados.</li>
        </ul>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Paso 9: Axios GET enrollments/data</div>
    <div class="card-body">
        <div class="row g-2 align-items-end mb-2">
            <div class="col-md-4">
                <label for="txt-student-filter" class="form-label">Alumno contiene</label>
                <input id="txt-student-filter" type="text" class="form-control" placeholder="Ej: ana">
            </div>
            <div class="col-md-2">
                <label for="txt-min-grade" class="form-label">Nota min</label>
                <input id="txt-min-grade" type="number" step="0.01" min="0" max="10" class="form-control" placeholder="0">
            </div>
            <div class="col-md-2">
                <label for="txt-max-grade" class="form-label">Nota max</label>
                <input id="txt-max-grade" type="number" step="0.01" min="0" max="10" class="form-control" placeholder="10">
            </div>
            <div class="col-md-2 d-grid">
                <button type="button" id="btn-filtrar-inscripciones" class="btn btn-sm btn-outline-primary" disabled>Filtrar</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Alumno</th>
                        <th>Aula</th>
                        <th>Materia</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody id="tbody-inscripciones">
                    <tr><td colspan="5" class="text-muted text-center">Sin datos.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/axios/axios.min.js') }}"></script>
<script>
    (function () {
        const btnAlerta = document.getElementById('btn-alerta');
        const btnConsola = document.getElementById('btn-consola');
        const subjectMeta = document.getElementById('subject-meta');
        const chkAxios = document.getElementById('chk-activar-axios');
        const btnCheckAccion = document.getElementById('btn-check-accion');
        const selTema = document.getElementById('sel-tema');
        const outTema = document.getElementById('out-tema');
        const txtAlumno = document.getElementById('txt-alumno');
        const outTexto = document.getElementById('out-texto');
        const txtItem = document.getElementById('txt-item');
        const btnAgregarItem = document.getElementById('btn-agregar-item');
        const listaLocal = document.getElementById('lista-local');

        const btnCargarResumen = document.getElementById('btn-cargar-resumen');
        const outResumen = document.getElementById('out-resumen');
        const txtFiltroMateria = document.getElementById('txt-filtro-materia');
        const btnCargarMaterias = document.getElementById('btn-cargar-materias');
        const listaMateriasAxios = document.getElementById('lista-materias-axios');
        const txtStudentFilter = document.getElementById('txt-student-filter');
        const txtMinGrade = document.getElementById('txt-min-grade');
        const txtMaxGrade = document.getElementById('txt-max-grade');
        const btnFiltrarInscripciones = document.getElementById('btn-filtrar-inscripciones');
        const tbodyInscripciones = document.getElementById('tbody-inscripciones');

        function isAxiosEnabled() {
            return chkAxios.checked;
        }

        function syncAxiosButtons() {
            const enabled = isAxiosEnabled();
            btnCheckAccion.disabled = !enabled;
            btnCargarResumen.disabled = !enabled;
            btnCargarMaterias.disabled = !enabled;
            btnFiltrarInscripciones.disabled = !enabled;
        }

        btnAlerta.addEventListener('click', function () {
            alert('Hola! Esta es una alerta simple de JavaScript.');
        });

        btnConsola.addEventListener('click', function () {
            const payload = {
                subjectId: Number(subjectMeta.dataset.subjectId),
                subjectName: subjectMeta.dataset.subjectName,
                timestamp: new Date().toISOString()
            };
            console.log('Debug payload:', payload);
        });

        chkAxios.addEventListener('change', syncAxiosButtons);

        btnCheckAccion.addEventListener('click', function () {
            alert('Perfecto, ya habilitaste acciones AJAX.');
        });

        selTema.addEventListener('change', function () {
            const value = selTema.value;
            if (!value) {
                outTema.textContent = 'Aun sin seleccion.';
                return;
            }
            outTema.textContent = 'Seleccion actual: ' + value;
        });

        txtAlumno.addEventListener('input', function () {
            outTexto.textContent = txtAlumno.value.trim()
                ? 'Texto en vivo: ' + txtAlumno.value
                : 'Esperando texto...';
        });

        btnAgregarItem.addEventListener('click', function () {
            const value = txtItem.value.trim();
            if (!value) {
                alert('Escribe un item antes de agregar.');
                return;
            }

            if (listaLocal.querySelector('.text-muted')) {
                listaLocal.innerHTML = '';
            }

            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = value;
            listaLocal.appendChild(li);
            txtItem.value = '';
            txtItem.focus();
        });

        btnCargarResumen.addEventListener('click', function () {
            if (!isAxiosEnabled()) return;
            axios.get('/enrollments/resumen')
                .then(function (res) {
                    const t = res.data.totals || {};
                    outResumen.textContent =
                        'Total=' + (t.enrollments ?? '-') +
                        ' | Prom=' + (t.avg_grade ?? '-') +
                        ' | Min=' + (t.min_grade ?? '-') +
                        ' | Max=' + (t.max_grade ?? '-');
                })
                .catch(function (err) {
                    console.error(err);
                    outResumen.textContent = 'Error al consultar resumen.';
                });
        });

        btnCargarMaterias.addEventListener('click', function () {
            if (!isAxiosEnabled()) return;
            axios.get('/enrollments/materias', {
                params: {
                    q: txtFiltroMateria.value.trim() || undefined,
                    min_enrollments: 0
                }
            })
                .then(function (res) {
                    const rows = res.data.data || [];
                    listaMateriasAxios.innerHTML = '';

                    if (!rows.length) {
                        listaMateriasAxios.innerHTML = '<li class="list-group-item text-muted">No hay materias para ese filtro.</li>';
                        return;
                    }

                    rows.forEach(function (m) {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.innerHTML = '<span>' + m.nombre + ' (' + m.codigo + ')</span>' +
                            '<span class="badge bg-secondary">Inscriptos: ' + m.inscriptos + '</span>';
                        listaMateriasAxios.appendChild(li);
                    });
                })
                .catch(function (err) {
                    console.error(err);
                    listaMateriasAxios.innerHTML = '<li class="list-group-item text-danger">Error al consultar materias.</li>';
                });
        });

        btnFiltrarInscripciones.addEventListener('click', function () {
            if (!isAxiosEnabled()) return;

            axios.get('/enrollments/data', {
                params: {
                    student: txtStudentFilter.value.trim() || undefined,
                    min_grade: txtMinGrade.value || undefined,
                    max_grade: txtMaxGrade.value || undefined,
                    per_page: 8
                }
            })
                .then(function (res) {
                    const rows = res.data.data || [];
                    tbodyInscripciones.innerHTML = '';

                    if (!rows.length) {
                        tbodyInscripciones.innerHTML = '<tr><td colspan="5" class="text-muted text-center">Sin resultados para esos filtros.</td></tr>';
                        return;
                    }

                    rows.forEach(function (row) {
                        const tr = document.createElement('tr');
                        tr.innerHTML =
                            '<td>' + row.id + '</td>' +
                            '<td>' + (row.student ?? '-') + '</td>' +
                            '<td>' + (row.classroom ?? '-') + '</td>' +
                            '<td>' + (row.subject ?? '-') + '</td>' +
                            '<td>' + (row.grade ?? '-') + '</td>';
                        tbodyInscripciones.appendChild(tr);
                    });
                })
                .catch(function (err) {
                    console.error(err);
                    tbodyInscripciones.innerHTML = '<tr><td colspan="5" class="text-danger text-center">Error al filtrar inscripciones.</td></tr>';
                });
        });

        syncAxiosButtons();
    })();
</script>
@endsection
