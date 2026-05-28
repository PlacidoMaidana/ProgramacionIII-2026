<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    public function index(): JsonResponse
    {
        $classrooms = Classroom::withCount('students')
            ->orderBy('name')
            ->paginate(15);

        return response()->json($classrooms);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:classrooms,name'],
            'capacity' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $classroom = Classroom::create($validated);

        return response()->json($classroom, 201);
    }

    public function show(Classroom $classroom): JsonResponse
    {
        return response()->json(
            $classroom->load('students')
        );
    }

    public function update(Request $request, Classroom $classroom): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255', Rule::unique('classrooms', 'name')->ignore($classroom->id)],
            'capacity' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $classroom->update($validated);

        return response()->json($classroom->fresh());
    }

    public function destroy(Classroom $classroom): JsonResponse
    {
        $classroom->delete();

        return response()->json(['message' => 'Aula eliminada correctamente.']);
    }
}
