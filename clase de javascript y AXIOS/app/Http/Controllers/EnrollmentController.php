<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        // with() evita el problema N+1 al cargar relaciones en bloque.
        $enrollments = Enrollment::with(['student.classroom', 'subject'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json($enrollments);
        }

        $subjects = Subject::orderBy('name')->get(['id', 'name', 'code']);
        $classrooms = Classroom::orderBy('name')->get(['id', 'name']);

        return view('enrollments.index', compact('enrollments', 'subjects', 'classrooms'));
    }

    public function create()
    {
        $students = Student::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('enrollments.create', compact('students', 'subjects'));
    }

    public function store(Request $request)
    {
        // store(): valida datos del formulario y crea una nueva inscripcion.
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
        // update(): modifica una inscripcion existente.
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
        // destroy(): elimina una inscripcion del sistema.
        $enrollment->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Inscripcion eliminada correctamente.']);
        }

        return redirect()->route('enrollments.index')->with('success', 'Inscripcion eliminada correctamente.');
    }

    // materias(): demo de filtros sobre Subject usando Eloquent y datos agregados.
    public function materias(): JsonResponse
    {
        $request = request();

        $materias = Subject::query()
            ->select(['id', 'name', 'code'])
            ->withCount('enrollments')
            ->withAvg('enrollments as avg_grade', 'grade')
            ->when($request->filled('q'), function ($query) use ($request) {
                $text = trim((string) $request->input('q'));
                $query->where(function ($inner) use ($text) {
                    $inner->where('name', 'like', "%{$text}%")
                        ->orWhere('code', 'like', "%{$text}%");
                });
            })
            ->when($request->filled('min_enrollments'), function ($query) use ($request) {
                $query->has('enrollments', '>=', (int) $request->input('min_enrollments'));
            })
            ->orderBy('name')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'nombre' => $subject->name,
                    'codigo' => $subject->code,
                    'inscriptos' => (int) $subject->enrollments_count,
                    'promedio' => $subject->avg_grade !== null ? round((float) $subject->avg_grade, 2) : null,
                ];
            })
            ->values();

        return response()->json([
            'filters' => [
                'q' => (string) $request->input('q', ''),
                'min_enrollments' => $request->input('min_enrollments'),
            ],
            'total' => $materias->count(),
            'data' => $materias,
        ]);
    }

    // data(): listado de inscripciones con filtros compuestos sobre relaciones.
    public function data(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'classroom_id' => ['nullable', 'integer', 'exists:classrooms,id'],
            'student' => ['nullable', 'string', 'max:80'],
            'min_grade' => ['nullable', 'numeric', 'between:0,10'],
            'max_grade' => ['nullable', 'numeric', 'between:0,10'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Enrollment::query()
            ->with(['student.classroom', 'subject'])
            ->when(isset($validated['subject_id']), function ($q) use ($validated) {
                $q->where('subject_id', $validated['subject_id']);
            })
            ->when(isset($validated['classroom_id']), function ($q) use ($validated) {
                $q->whereHas('student', function ($sq) use ($validated) {
                    $sq->where('classroom_id', $validated['classroom_id']);
                });
            })
            ->when(!empty($validated['student']), function ($q) use ($validated) {
                $name = trim($validated['student']);
                $q->whereHas('student', function ($sq) use ($name) {
                    $sq->where('name', 'like', "%{$name}%");
                });
            })
            ->when(isset($validated['min_grade']), function ($q) use ($validated) {
                $q->whereNotNull('grade')->where('grade', '>=', $validated['min_grade']);
            })
            ->when(isset($validated['max_grade']), function ($q) use ($validated) {
                $q->whereNotNull('grade')->where('grade', '<=', $validated['max_grade']);
            })
            ->orderBy('id', 'desc');

        $perPage = (int) ($validated['per_page'] ?? 10);
        $rows = $query->paginate($perPage);

        return response()->json([
            'filters' => [
                'subject_id' => $validated['subject_id'] ?? null,
                'classroom_id' => $validated['classroom_id'] ?? null,
                'student' => $validated['student'] ?? null,
                'min_grade' => $validated['min_grade'] ?? null,
                'max_grade' => $validated['max_grade'] ?? null,
                'per_page' => $perPage,
            ],
            'pagination' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
            'data' => collect($rows->items())->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'student' => optional($enrollment->student)->name,
                    'classroom' => optional(optional($enrollment->student)->classroom)->name,
                    'subject' => optional($enrollment->subject)->name,
                    'subject_code' => optional($enrollment->subject)->code,
                    'grade' => $enrollment->grade,
                ];
            })->values(),
        ]);
    }

    // resumen(): metricas rapidas para explicar agregaciones de Eloquent en clase.
    public function resumen(): JsonResponse
    {
        $summary = Enrollment::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('AVG(grade) as avg_grade')
            ->selectRaw('MIN(grade) as min_grade')
            ->selectRaw('MAX(grade) as max_grade')
            ->first();

        $topSubjects = Subject::query()
            ->select(['id', 'name', 'code'])
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get();

        return response()->json([
            'totals' => [
                'enrollments' => (int) ($summary->total ?? 0),
                'avg_grade' => $summary->avg_grade !== null ? round((float) $summary->avg_grade, 2) : null,
                'min_grade' => $summary->min_grade !== null ? (float) $summary->min_grade : null,
                'max_grade' => $summary->max_grade !== null ? (float) $summary->max_grade : null,
            ],
            'top_subjects' => $topSubjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                    'enrollments' => (int) $subject->enrollments_count,
                ];
            })->values(),
        ]);
    }
}
