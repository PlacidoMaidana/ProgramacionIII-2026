<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Escolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('students.index') }}">Sistema Escolar</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('students.index') }}">Students</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('classrooms.index') }}">Classrooms</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('subjects.index') }}">Subjects</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('subjects.create') }}">New Subject</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('subjects.index') }}">Edit Subject</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('enrollments.index') }}">Enrollments</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Bootstrap JS es necesario para que funcionen los modales (data-bs-toggle, data-bs-dismiss, eventos show.bs.modal). --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Cada vista puede inyectar su JS especifico aqui (por ejemplo create/edit de enrollments). --}}
    @yield('scripts')
</body>
</html>
