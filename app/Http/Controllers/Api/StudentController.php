<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(): JsonResponse
    {
        $students = Student::with(['classroom', 'subjects'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return response()->json($students);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:students,email'],
        ]);

        $student = DB::transaction(fn () => Student::create($validated));

        return response()->json(
            $student->load(['classroom', 'subjects']),
            201
        );
    }

    public function show(Student $student): JsonResponse
    {
        return response()->json(
            $student->load(['classroom', 'subjects'])
        );
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student->id)],
        ]);

        DB::transaction(fn () => $student->update($validated));

        return response()->json(
            $student->fresh()->load(['classroom', 'subjects'])
        );
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json(['message' => 'Estudiante eliminado correctamente.']);
    }
}
