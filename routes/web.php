<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/palomas', function () {
    return view('segunda_vista');
})->name('palomas');


Route::get('/hola', function () {
return '<p><strong>Salud Pública:</strong> Son vectores potenciales de enfermedades y parásitos si no se controla su población en áreas densamente pobladas.</p>            
';
});
Route::get('/saludo/{nombre}', function ($nombre) {
return 'Hola, ' . $nombre . '!';
});
Route::get('/calcula/{numero}', function ($numero) {

if (isset($numero)) {
    echo 'El parámetro debe ser un número.';
    $n=$numero;

    echo ' <table class="table table-striped table-inverse table-responsive">
        <thead class="thead-inverse">
            <tr>
                <th>i</th>
                <th>i*n</th>
               
            </tr>
            </thead>
            <tbody>';

            for ($i=1; $i<=10; $i++) {      
                echo '<tr>
                    <td scope="row">'.$i.'</td>'
                    .'<td>'.$i*$n.'</td> </tr>';
            }

           echo '</tbody>   </table>';


}else {
    echo 'El parámetro debe ser un número.';
}

});

Route::get('/db_personas', [App\Http\Controllers\PersonaController::class, 'index_db']);
Route::get('/db_persona/nueva', [App\Http\Controllers\PersonaController::class, 'create_db']);
Route::post('/db_persona', [App\Http\Controllers\PersonaController::class, 'store_db']);
Route::get('/db_persona/{id}/editar', [App\Http\Controllers\PersonaController::class, 'edit_db']);
Route::post('db_persona/{id}', [App\Http\Controllers\PersonaController::class, 'update_db']);
Route::get('db_persona/{id}/eliminar', [App\Http\Controllers\PersonaController::class, 'destroy_db']);


Route::get('/personas', [App\Http\Controllers\PersonaController::class, 'index']);


Route::get('/persona/nueva', [App\Http\Controllers\PersonaController::class, 'create']);
Route::post('/persona', [App\Http\Controllers\PersonaController::class, 'store']);
Route::get('/persona/{id}/editar', [App\Http\Controllers\PersonaController::class, 'edit']);
Route::post('/persona/{id}', [App\Http\Controllers\PersonaController::class, 'update']);
Route::get('/persona/{id}/eliminar', [App\Http\Controllers\PersonaController::class, 'destroy']);