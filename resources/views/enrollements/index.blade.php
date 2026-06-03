@extends('layouts.app')
@section('title', 'Ficha de Enrollements')

@section('content')
<div class="container mt-4">
    <h2>Ficha de Enrollements</h2>
    <p class="text-muted">Ejemplo simple de AJAX con Axios.</p>

    <button id="btn-cargar-materias" class="btn btn-primary mb-3">
        Cargar materias
    </button>

    <ul id="lista-materias" class="list-group"></ul>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.getElementById('btn-cargar-materias').addEventListener('click', function () {
        axios.get('/enrollements/materias')
            .then(function (response) {
                const lista = document.getElementById('lista-materias');
                lista.innerHTML = '';

                response.data.forEach(function (materia) {
                    const item = document.createElement('li');
                    item.className = 'list-group-item';
                    item.textContent = materia.nombre;
                    lista.appendChild(item);
                });
            })
            .catch(function () {
                alert('No se pudo cargar la lista de materias.');
            });
    });
</script>
@endsection
