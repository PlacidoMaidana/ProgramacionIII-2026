<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::withCount('students')->orderBy('name')->paginate(15);

        if ($request->expectsJson()) {
            return response()->json($subjects);
        }

        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:subjects,code'],
        ]);

        $subject = Subject::create($validated);

        if ($request->expectsJson()) {
            return response()->json($subject, 201);
        }

        return redirect()->route('subjects.index')->with('success', 'Materia creada correctamente.');
    }

    public function show(Request $request, Subject $subject)
    {
        $subject->load('students');

        if ($request->expectsJson()) {
            return response()->json($subject);
        }

        return redirect()->route('subjects.edit', $subject);
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('subjects', 'code')->ignore($subject->id)],
        ]);

        $subject->update($validated);

        if ($request->expectsJson()) {
            return response()->json($subject->fresh());
        }

        return redirect()->route('subjects.index')->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Request $request, Subject $subject)
    {
        $subject->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Materia eliminada correctamente.']);
        }

        return redirect()->route('subjects.index')->with('success', 'Materia eliminada correctamente.');
    }

    public function byStudent(Request $request)
    {
        $studentId = $request->query('student_id');
        
        $materias = collect();

        if ($studentId) {
            $materias = DB::table('enrollments')
                ->join('subjects', 'subjects.id', '=', 'enrollments.subject_id')
                ->where('enrollments.student_id', $studentId)
                ->select(
                    'subjects.id',
                    'subjects.name',
                    'subjects.code',
                    'enrollments.grade'
                )
                ->orderBy('subjects.name')
                ->get();
        }

        if ($request->expectsJson()) {
            return response()->json($materias);
        }

        return view('subjects.by-student', [
            'materias' => $materias,
            'studentId' => $studentId,
        ]);
    }
}
