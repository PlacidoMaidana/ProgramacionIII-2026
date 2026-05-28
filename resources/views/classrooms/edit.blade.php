@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Edit classroom</h1>

<form method="POST" action="{{ route('classrooms.update', $classroom) }}" class="card card-body">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $classroom->name) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Capacity</label>
        <input type="number" name="capacity" min="1" max="120" class="form-control" value="{{ old('capacity', $classroom->capacity) }}" required>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
