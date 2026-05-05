<?php

namespace App\Http\Controllers;
use App\Models\Persona;
use Illuminate\Http\Request;

class PersonasControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $personas = Persona::all();
         return response()->json([
             'message' => 'Lista de personas (API)',
             'data' => $personas
          ], 200);
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $personas =  Persona::create([
            'nombre' => $request->input('nombre'),
            'edad' => $request->input('edad'),
            'email' => $request->input('email'),
        ]);
         return response()->json([
             'message' => 'Persona creada (API)',
                       ], 201);
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
            $persona = Persona::findOrFail($id);
            return response()->json([
                'message' => 'Detalle de persona (API)',
                'data' => $persona
            ], 200);
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);
        $persona->nombre = $request->input('nombre');
        $persona->edad = $request->input('edad');
        $persona->email = $request->input('email');
        $persona->save();
         return response()->json([
             'message' => 'Persona actualizada (API)',
                       ], 200);
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->delete();
         return response()->json([
             'message' => 'Persona eliminada (API)',
                       ], 200);
        //
    }
}
