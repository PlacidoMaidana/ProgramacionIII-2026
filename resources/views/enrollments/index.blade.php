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
@endsection
