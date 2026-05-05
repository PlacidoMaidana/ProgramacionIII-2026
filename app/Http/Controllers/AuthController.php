<?php

// Comentario: Declaramos el namespace para ubicar este controlador dentro de la carpeta Api
namespace App\Http\Controllers;

// Comentario: Importamos el controlador base del que hereda este controlador
use App\Http\Controllers\Controller;
// Comentario: Importamos el modelo User para gestionar los usuarios en la base de datos
use App\Models\User;
// Comentario: Importamos JsonResponse para tipar los valores de retorno de cada metodo
use Illuminate\Http\JsonResponse;
// Comentario: Importamos Request para leer los datos enviados en cada peticion
use Illuminate\Http\Request;
// Comentario: Importamos Hash para encriptar la contrasena antes de guardarla
use Illuminate\Support\Facades\Hash;
// Comentario: Importamos ValidationException para lanzar errores de credenciales como errores de validacion
use Illuminate\Validation\ValidationException;

// Comentario: Controlador que maneja el registro, login, consulta del usuario autenticado y logout
class AuthController extends Controller
{
    // Comentario: Registra un nuevo usuario, genera su token y lo retorna con codigo 201 (Created)
    public function register(Request $request): JsonResponse
    {
        
        // Comentario: Validamos nombre, email unico en la tabla users y contrasena de minimo 8 caracteres
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Comentario: Creamos el usuario con la contrasena encriptada mediante bcrypt a traves de Hash::make
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Comentario: Generamos un token personal de Sanctum y obtenemos su valor en texto plano
        $token = $user->createToken('postman-token')->plainTextToken;

        // Comentario: Devolvemos el token, su tipo Bearer y los datos del usuario recien creado
        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    // Comentario: Autentica un usuario existente y retorna un nuevo token Bearer si las credenciales son correctas
    public function login(Request $request): JsonResponse
    {
        // Comentario: Validamos que se envien email y contrasena con el formato correcto
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Comentario: Buscamos el usuario por email en la base de datos
        $user = User::where('email', $credentials['email'])->first();

        // Comentario: Si el usuario no existe o la contrasena no coincide, lanzamos un error de validacion
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no son correctas.'],
            ]);
        }

        // Comentario: Generamos un nuevo token personal de Sanctum para la sesion actual
        $token = $user->createToken('postman-token')->plainTextToken;

        // Comentario: Retornamos el token y los datos del usuario autenticado
        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    // Comentario: Devuelve los datos del usuario que esta actualmente autenticado con su token
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    // Comentario: Revoca el token actual del usuario cerrando su sesion de forma segura
    public function logout(Request $request): JsonResponse
    {
        // Comentario: Eliminamos solo el token que se uso para hacer esta peticion, no todos los tokens del usuario
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesion cerrada correctamente.',
        ]);
    }
}
