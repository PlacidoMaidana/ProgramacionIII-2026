@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Create student</h1>

<form method="POST" action="{{ route('students.store') }}" class="card card-body">
    @csrf

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Classroom</label>
        <select name="classroom_id" class="form-select" required>
            <option value="">Select classroom</option>
            @foreach($classrooms as $classroom)
                <option value="{{ $classroom->id }}" @selected(old('classroom_id') == $classroom->id)>{{ $classroom->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
