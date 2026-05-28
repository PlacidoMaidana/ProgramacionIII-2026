@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Create enrollment</h1>

<form method="POST" action="{{ route('enrollments.store') }}" class="card card-body">
    @csrf

    <div class="mb-3">
        <label class="form-label">Student</label>
        <select name="student_id" class="form-select" required>
            <option value="">Select student</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->name }} ({{ $student->email }})</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Subject</label>
        <select name="subject_id" class="form-select" required>
            <option value="">Select subject</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }} ({{ $subject->code }})</option>
            @endforeach
        </select>
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
@endsection
