<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class EnrollementController extends Controller
{
    public function index()
    {
        return view('enrollements.index');
    }

    public function materias(): JsonResponse
    {
        $materias = [
            ['id' => 1, 'nombre' => 'Programación III'],
            ['id' => 2, 'nombre' => 'Base de Datos'],
            ['id' => 3, 'nombre' => 'Desarrollo Web'],
            ['id' => 4, 'nombre' => 'Arquitectura de Software'],
        ];

        return response()->json($materias);
    }
}
