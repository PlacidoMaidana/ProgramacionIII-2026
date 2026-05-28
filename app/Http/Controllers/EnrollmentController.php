<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $enrollments = Enrollment::with(['student.classroom', 'subject'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json($enrollments);
        }

        return view('enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        $students = Student::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('enrollments.create', compact('students', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'grade' => ['nullable', 'numeric', 'between:0,10'],
            'student_subject' => [
                Rule::unique('enrollments', 'student_id')->where(function ($query) use ($request) {
                    return $query->where('subject_id', $request->subject_id);
                }),
            ],
        ], [
            'student_subject.unique' => 'Este alumno ya esta inscrito en la materia seleccionada.',
        ]);

        unset($validated['student_subject']);

        $enrollment = Enrollment::create($validated);

        if ($request->expectsJson()) {
            return response()->json($enrollment->load(['student.classroom', 'subject']), 201);
        }

        return redirect()->route('enrollments.index')->with('success', 'Inscripcion creada correctamente.');
    }

    public function show(Request $request, Enrollment $enrollment)
    {
        $enrollment->load(['student.classroom', 'subject']);

        if ($request->expectsJson()) {
            return response()->json($enrollment);
        }

        return redirect()->route('enrollments.edit', $enrollment);
    }

    public function edit(Enrollment $enrollment)
    {
        $students = Student::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('enrollments.edit', compact('enrollment', 'students', 'subjects'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'grade' => ['nullable', 'numeric', 'between:0,10'],
        ]);

        $exists = Enrollment::where('student_id', $validated['student_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $enrollment->id)
            ->exists();

        if ($exists) {
            $message = ['subject_id' => ['Este alumno ya esta inscrito en la materia seleccionada.']];

            if ($request->expectsJson()) {
                return response()->json(['errors' => $message], 422);
            }

            return back()->withErrors($message)->withInput();
        }

        $enrollment->update($validated);

        if ($request->expectsJson()) {
            return response()->json($enrollment->fresh()->load(['student.classroom', 'subject']));
        }

        return redirect()->route('enrollments.index')->with('success', 'Inscripcion actualizada correctamente.');
    }

    public function destroy(Request $request, Enrollment $enrollment)
    {
        $enrollment->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Inscripcion eliminada correctamente.']);
        }

        return redirect()->route('enrollments.index')->with('success', 'Inscripcion eliminada correctamente.');
    }
}
