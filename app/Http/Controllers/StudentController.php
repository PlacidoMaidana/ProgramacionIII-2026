<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with(['classroom', 'subjects'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        if ($request->expectsJson()) {
            return response()->json($students);
        }

        return view('students.index', compact('students'));
    }

    public function create()
    {
        $classrooms = Classroom::orderBy('name')->get();

        return view('students.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
        ]);

        $student = DB::transaction(function () use ($validated) {
            return Student::create($validated);
        });

        if ($request->expectsJson()) {
            return response()->json($student->load(['classroom', 'subjects']), 201);
        }

        return redirect()->route('students.index')->with('success', 'Estudiante creado correctamente.');
    }

    public function show(Request $request, Student $student)
    {
        $student->load(['classroom', 'subjects']);

        if ($request->expectsJson()) {
            return response()->json($student);
        }

        return redirect()->route('students.edit', $student);
    }

    public function edit(Student $student)
    {
        $classrooms = Classroom::orderBy('name')->get();

        return view('students.edit', compact('student', 'classrooms'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student->id)],
        ]);

        DB::transaction(function () use ($student, $validated) {
            $student->update($validated);
        });

        if ($request->expectsJson()) {
            return response()->json($student->fresh()->load(['classroom', 'subjects']));
        }

        return redirect()->route('students.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Request $request, Student $student)
    {
        $student->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Estudiante eliminado correctamente.']);
        }

        return redirect()->route('students.index')->with('success', 'Estudiante eliminado correctamente.');
    }
}
