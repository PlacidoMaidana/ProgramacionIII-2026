@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Edit subject</h1>

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
@endsection
