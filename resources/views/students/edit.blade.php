@extends('layouts.app')

@section('content')
<div class="card card-body">
    <h1 class="h4 mb-4">Editar Estudiante</h1>

    <form id="studentForm">
        <input type="hidden" id="student_id" value="{{ $student->id }}">
        
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">Nombre del Estudiante</label>
                    <input type="text" id="student_name" class="form-control" value="{{ $student->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="student_email" class="form-control" value="{{ $student->email }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Aula</label>
                    <select id="classroom_id" class="form-select" required>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" @selected($student->classroom_id == $classroom->id)>{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto de Perfil (hasta 10 MB)</label>
                    <input type="file" id="student_photo" class="form-control" accept="image/*">
                    <small class="text-muted">Deja vacío si no quieres cambiar la foto</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" id="btnSaveStudent" class="btn btn-primary">Guardar Cambios</button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Foto Actual / Vista Previa</label>
                    <div id="imagePreview" class="text-center border rounded p-3" style="background-color: #f8f9fa; min-height: 250px; display: flex; align-items: center; justify-content: center;">
                        @if($student->photo)
                            <img src="{{ $student->photo }}" class="img-fluid rounded" style="max-height: 250px; max-width: 100%;">
                        @else
                            <p class="text-muted">Sin foto de perfil</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/axios/axios.min.js') }}"></script>
<script>
// Preview de imagen
document.getElementById('student_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const previewDiv = document.getElementById('imagePreview');
            previewDiv.innerHTML = `<img src="${event.target.result}" class="img-fluid rounded" style="max-height: 250px; max-width: 100%;">`;
        };
        reader.readAsDataURL(file);
    }
});

// Guardar cambios
document.getElementById('btnSaveStudent').addEventListener('click', function(e) {
    e.preventDefault();

    const studentId = document.getElementById('student_id').value;
    const name = document.getElementById('student_name').value;
    const email = document.getElementById('student_email').value;
    const classroomId = document.getElementById('classroom_id').value;
    const photoFile = document.getElementById('student_photo').files[0];
    const btn = document.getElementById('btnSaveStudent');

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('classroom_id', classroomId);
    formData.append('_method', 'PUT');
    
    if (photoFile) {
        formData.append('photo', photoFile);
    }

    btn.disabled = true;
    btn.textContent = 'Guardando...';

    axios.post(`/api/students/${studentId}`, formData)
    .then(response => {
        console.log('Alumno actualizado:', response.data);
        alert('¡Estudiante actualizado con éxito!');
        window.location.href = '{{ route("students.index") }}';
    })
    .catch(error => {
        console.error('Error completo:', error.response?.data || error.message);
        
        if (error.response?.status === 422) {
            const errors = error.response.data.errors || {};
            let errorLines = [];
            
            for (const field in errors) {
                const fieldErrors = errors[field];
                errorLines.push(`${field}: ${fieldErrors.join(', ')}`);
            }
            
            const fullMsg = errorLines.length > 0 
                ? errorLines.join('\n')
                : error.response.data.message || 'Error de validación.';
            
            alert('Error de validación:\n\n' + fullMsg);
        } else {
            alert('Error: ' + (error.message || 'Error desconocido.'));
        }
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Guardar Cambios';
    });
});
</script>
@endsection
