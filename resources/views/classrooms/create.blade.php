@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Create classroom</h1>

<form method="POST" action="{{ route('classrooms.store') }}" class="card card-body">
    @csrf

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Capacity</label>
        <input type="number" name="capacity" min="1" max="120" class="form-control" value="{{ old('capacity', 30) }}" required>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
