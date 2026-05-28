<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function index(): JsonResponse
    {
        $enrollments = Enrollment::with(['student.classroom', 'subject'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()->json($enrollments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'grade'      => ['nullable', 'numeric', 'between:0,10'],
            'student_subject' => [
                Rule::unique('enrollments', 'student_id')->where(
                    fn ($query) => $query->where('subject_id', $request->subject_id)
                ),
            ],
        ], [
            'student_subject.unique' => 'Este alumno ya esta inscrito en la materia seleccionada.',
        ]);

        unset($validated['student_subject']);

        $enrollment = Enrollment::create($validated);

        return response()->json(
            $enrollment->load(['student.classroom', 'subject']),
            201
        );
    }

    public function show(Enrollment $enrollment): JsonResponse
    {
        return response()->json(
            $enrollment->load(['student.classroom', 'subject'])
        );
    }

    public function update(Request $request, Enrollment $enrollment): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'grade'      => ['nullable', 'numeric', 'between:0,10'],
        ]);

        $exists = Enrollment::where('student_id', $validated['student_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $enrollment->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['subject_id' => ['Este alumno ya esta inscrito en la materia seleccionada.']],
            ], 422);
        }

        $enrollment->update($validated);

        return response()->json(
            $enrollment->fresh()->load(['student.classroom', 'subject'])
        );
    }

    public function destroy(Enrollment $enrollment): JsonResponse
    {
        $enrollment->delete();

        return response()->json(['message' => 'Inscripcion eliminada correctamente.']);
    }
}
