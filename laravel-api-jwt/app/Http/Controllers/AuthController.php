<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario y retornar el token JWT.
     */
    public function register(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Retornar errores de validación
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generar el token JWT para el usuario
        $token = JWTAuth::fromUser($user);

        // Retornar la respuesta con el token y los datos del usuario
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Autenticar al usuario y retornar el token JWT.
     */
    public function login(Request $request)
    {
        // Validar las credenciales
        $credentials = $request->only('email', 'password');

        // Validación explícita en Laravel
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        // Si la validación falla, devuelve un error 422
        if ($validator->fails()) {
            return response()->json(['error' => 'Credenciales inválidas', 'message' => $validator->errors()], 422);
        }

        // Buscar al usuario por correo
        $user = User::where('email', $credentials['email'])->first();

        // Verificar si el usuario existe
        if (!$user) {
            return response()->json(['error' => 'El Usuario NO EXISTE'], 401);
        }

        // Verificar si la contraseña proporcionada coincide con la contraseña hasheada en la base de datos
        if (Hash::check($credentials['password'], $user->password)) {
            // La contraseña es correcta, proceder a generar el token
            try {
                // Intentar generar el token
                $token = JWTAuth::fromUser($user);

                return response()->json(['token' => $token]);
            } catch (JWTException $e) {
                return response()->json(['error' => 'No se pudo crear el token'], 500);
            }
        } else {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }
    }

    /**
     * Obtener los datos del usuario autenticado.
     */
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido o no enviado'], 401);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Sesión cerrada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo cerrar la sesión'], 500);
        }
    }
}
