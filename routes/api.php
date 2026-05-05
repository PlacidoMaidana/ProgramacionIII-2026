<?php
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonasControllerApi;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// Comentario: Rutas publicas de autenticacion (no requieren token)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Comentario: Grupo de rutas protegidas: solo accesibles con un token Bearer valido de Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Comentario: Devuelve los datos del usuario actualmente autenticado
    Route::get('me', [AuthController::class, 'me']);
    // Comentario: Revoca el token actual y cierra la sesion
    Route::post('logout', [AuthController::class, 'logout']);
});


Route::get('/saludo/{nombre}', function ($nombre) {
return 'Hola, ' . $nombre . '!';
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('personas', PersonasControllerApi::class);
});