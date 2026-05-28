<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::withCount('students')->orderBy('name')->paginate(15);

        if ($request->expectsJson()) {
            return response()->json($classrooms);
        }

        return view('classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        return view('classrooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:classrooms,name'],
            'capacity' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $classroom = Classroom::create($validated);

        if ($request->expectsJson()) {
            return response()->json($classroom, 201);
        }

        return redirect()->route('classrooms.index')->with('success', 'Aula creada correctamente.');
    }

    public function show(Request $request, Classroom $classroom)
    {
        $classroom->load('students');

        if ($request->expectsJson()) {
            return response()->json($classroom);
        }

        return redirect()->route('classrooms.edit', $classroom);
    }

    public function edit(Classroom $classroom)
    {
        return view('classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('classrooms', 'name')->ignore($classroom->id)],
            'capacity' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $classroom->update($validated);

        if ($request->expectsJson()) {
            return response()->json($classroom->fresh());
        }

        return redirect()->route('classrooms.index')->with('success', 'Aula actualizada correctamente.');
    }

    public function destroy(Request $request, Classroom $classroom)
    {
        $classroom->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Aula eliminada correctamente.']);
        }

        return redirect()->route('classrooms.index')->with('success', 'Aula eliminada correctamente.');
    }
}
