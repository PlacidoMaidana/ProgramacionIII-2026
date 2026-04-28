<?php

namespace App\Http\Controllers;
use App\Models\Persona;

use Illuminate\Http\Request;

class PersonaController extends Controller
{
    //
    public function index()
    {
        // Simulamos datos (en la práctica vendrían de BD)
        $personas = session('personas', [
            (object)['id' => 1, 'nombre' => 'Juan', 'apellido' => 'Pérez', 'edad' => 25],
            (object)['id' => 2, 'nombre' => 'María', 'apellido' => 'Gómez', 'edad' => 30],
        ]); // si no existe, devuelve arreglo vacío
        return view('persona.index', compact('personas'));
       
    }

     public function index_db()
    {
        $personas = Persona::all();
        return view('persona_db.index', compact('personas'));
    }

     public function create()
    {
        return view('persona.form', ['persona' => null]);
    }
    
    public function create_db()
    {
        return view('persona_db.form', ['persona' => null]);
    }
    

    public function store(Request $request)
    {
      //  var_dump($request->all());
        $personas = session('personas', []);
        $nuevoId = count($personas) + 1;
        $persona = (object)[
            'id' => $nuevoId,
            'nombre' => $request->input('nombre'),
            'apellido' => $request->input('apellido'),
            'edad' => $request->input('edad'),
        ];
        $personas[] = $persona;
        session(['personas' => $personas]);
        return redirect('/personas');
    }


    public function store_db(Request $request)
    {
      //  var_dump($request->all());
        $personas =  Persona::create([
            'nombre' => $request->input('nombre'),
            'edad' => $request->input('edad'),
            'email' => $request->input('email'),
        ]);
         return redirect('/db_personas');
        
    }





    public function edit($id)
    {
        $personas = session('personas', []);
        $persona = collect($personas)->firstWhere('id', $id);
        
        if (!$persona) {
            return redirect('/personas')->with('error', 'Persona no encontrada');
        }
        return view('persona.form', compact('persona'));
    }

    public function edit_db($id)
    {
        $persona = Persona::findOrFail($id);
        
        if (!$persona) {
            return redirect('/db_personas')->with('error', 'Persona no encontrada');
        }
        return view('persona_db.form', compact('persona'));
    }

    public function update(Request $request, $id)
    {
        // var_dump($request->all()); die();
        $personas = session('personas', []);
        $index = collect($personas)->search(fn($p) => $p->id == $id);
        if ($index === false) {
            return redirect('/personas')->with('error', 'Persona no encontrada');
        }
        $personas[$index]->nombre = $request->input('nombre');
        $personas[$index]->apellido = $request->input('apellido');
        $personas[$index]->edad = $request->input('edad');
        session(['personas' => $personas]);
        return redirect('/personas');
    }
    

     public function update_db(Request $request, $id)
    {
        // var_dump($request->all()); die();
        $persona = Persona::findOrFail($id);
        $persona->nombre = $request->input('nombre');
        $persona->edad = $request->input('edad');
        $persona->email = $request->input('email');
        $persona->save();
        return redirect('/db_personas');
    }



    public function destroy($id)
    {
        $personas = session('personas', []);
        $index = collect($personas)->search(fn($p) => $p->id == $id);
        if ($index === false) {
            return redirect('/personas')->with('error', 'Persona no encontrada');
        }
        array_splice($personas, $index, 1);
        session(['personas' => $personas]);
        return redirect('/personas');
    }

    public function destroy_db($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->delete();
        return redirect('/db_personas')->with('success', 'Persona eliminada correctamente');

        }
      
    

}
